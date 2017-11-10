<?php

namespace LoveThatFit\ProductIntakeBundle\Entity;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ProductSize;
use LoveThatFit\AdminBundle\Entity\ProductSizeMeasurement;
use LoveThatFit\AdminBundle\Entity\ProductColor;

class ProductSpecificationHelper {

    protected $conf;
    protected $size_config;
    protected $dispatcher;

    /**
     * @var EntityManager 
     */
    private $em;

    /**
     * @var EntityRepository
     */
    protected $repo;

    /**
     * @var string
     */
    protected $class;
    private $container;

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, Container $container) {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
        $conf_yml = new Parser();
        $this->conf = $conf_yml->parse(file_get_contents('../src/LoveThatFit/ProductIntakeBundle/Resources/config/specs_config.yml'));
        $this->size_config = $conf_yml->parse(file_get_contents('../app/config/sizes_ltf.yml'));     

    }

    #---------------------------------   

    public function getNew() {
        $class = $this->class;
        $c = new $class();
        $c->setCreatedAt(new \DateTime('now'));
        return $c;
    }

    #---------------------------------   

    public function createNew($title, $desc, $data) {
        $brand =  $this->container->get('admin.helper.brand')->findOneByName($data['brand']);        
        $class = $this->class;
        $c = new $class();
        $c->setTitle($title);
        $c->setDescription($desc);
        $c->setSpecsJson(json_encode($data));        
        $c->setBrand($brand);
        $c->setCreatedAt(new \DateTime('now'));
        $this->save($c);
        return $c;
    }

    #---------------------------------   

    public function save($entity) {
        $entity->setUpdatedAt(new \DateTime('now'));
        $this->em->persist($entity);
        $this->em->flush();
    }

    #---------------------------------   

    public function delete($id) {

        $entity = $this->repo->find($id);
        $title = $entity->getTitle();
        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();
            return array(
                'message' => 'The product specs for ' . $title . ' has been Deleted!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {

            return array(
                'message' => 'Product specs not found!',
                'message_type' => 'warning',
                'success' => false,
            );
        }
    }

    #---------------------------------       

    public function update($entity) {
        $title = $entity->getTitle();
        $entity->setUpdatedAt(new \DateTime('now'));
        $this->em->persist($entity);
        $this->em->flush();

        return array(
            'message' => 'The product specs for ' . $title . ' has been Updated!',
            'message_type' => 'success',
            'success' => true,
        );
    }

    #---------------------------#---------------------------

    public function updateAndFill($id, $posted) {
        $entity = $this->find($id);
        $parsed = $this->posted_values_to_array($posted);
        $entity->fill($parsed, true);
        return $this->update($entity);
    }
    

    #----------------------------------------------
    #---------------------------

    private function posted_values_to_array($posted) {
        $output = array();
        foreach ($posted as $key => $value) {
            $sizes = explode('-', $key); //[sizes-XS-neck-garment_dimension]
            $array_length = count($sizes);
            if ($array_length == '2') {
                $output[$sizes[0]][$sizes[1]] = $value;
            } elseif ($array_length == '4') {
                $output['sizes'][$sizes[1]][$sizes[2]][$sizes[3]] = $value;
            } else {
                $output[$key] = $value;
            }
        }
        return $output;
    }

    ###############################################

    public function find($id) {
        return $this->repo->find($id);
    }

    #--------------------

    public function findAll() {
        return $this->repo->findAll();
    }

    #----------------------

    public function findOneByTitle($title) {
        return $this->repo->findOneByTitle($title);
    }

    #-----------------------------------------------

    public function getFitPointArray() {
        $fp = array();
        foreach ($this->conf['fit_points'] as $fpk => $fpv) {
            $fp[$fpv['axis']][$fpk] = $fpv['title'];
        }
        return $fp;
    }
#-----------------------------------------------
    public function getFitModelMeasurements($id) {
        $ps = $this->repo->find($id);
        if(!$ps) {return false;}
        $parsed_data = json_decode($ps->getSpecsJson(), true);
        $fm_specs = array();
        $fm_specs=$parsed_data;
        foreach ($parsed_data['sizes'] as $size => $fp) {
            foreach ($fp as $fpk => $fpv) {
                $fm_specs['sizes'][$size][$fpk] = $fpv['fit_model'];
            }
        }
        return $fm_specs;
    }
    ######################################################################################
    ##################################### Fit Model Dynamic Calculations #################
    #####################################################################################
    
    #-------------------> Dynamic calculations     
    public function dynamicCalculations($decoded) {
//        $validation = $this->validateProdoctValues($decoded, $specs_value=null);
//           if(!$validation['status']) {
//               return $validation;
//           }
        $specs_obj = $this->find($decoded['pk']);
        $specs = json_decode($specs_obj->getSpecsJson(), true);
        #-----------------------------        
        if (!array_key_exists('fit_point_stretch', $specs)) {
            $specs['fit_point_stretch'] = $specs_obj->getFitPointStretchArray();
        }#-----------------------------
        if ($decoded['name'] == 'horizontal_stretch' || $decoded['name'] == 'vertical_stretch') {
            $specs[$decoded['name']] = $decoded['value'];           
            $specs = $this->generate_specs_for_stretch($specs, $decoded['name']); #~~~~~~~~>1
        } elseif ($decoded['name'] == 'max_horizontal_stretch') {    #~~~~~~~~>2
            $specs[$decoded['name']] = $decoded['value'];    
            $specs['horizontal_stretch'] = $specs['horizontal_stretch'] == 0 ? $decoded['value'] / 3 : $specs['horizontal_stretch'];
        } elseif( $decoded['name'] == 'max_vertical_stretch') {
            $specs[$decoded['name']] = $decoded['value'];    
            $specs['vertical_stretch'] = $specs['vertical_stretch'] == 0 ? $decoded['value'] / 3 : $specs['vertical_stretch'];
        } elseif (strpos($decoded['name'], 'fit_point_stretch') !== false) {    #~~~~~~~~>2
            $fit_point_stretch_array = explode('-', $decoded['name']);
            $specs['fit_point_stretch'][$fit_point_stretch_array[1]] = $decoded['value'];
            $specs = $this->generate_specs_for_fit_point_stretch($specs, $fit_point_stretch_array[1]);
        } elseif (strpos($decoded['name'], 'actual') !== false) { #~~~~~~~~>3
            $specs = $this->generate_specs_for_actual($specs, $decoded['name'], $decoded['value']);
        } elseif (strpos($decoded['name'], 'grade_rule') !== false) {   #~~~~~~~~>4
            $specs = $this->generate_specs_for_grade_rule($specs, $decoded['name'], $decoded['value']);
        } elseif (strpos($decoded['name'], 'garment_dimension') !== false) {    #~~~~~~~~>5
            $specs = $this->generate_specs_for_garment_dimension($specs, $decoded['name'], $decoded['value']);
        } elseif (strpos($decoded['name'], 'fit_model_size') !== false) { #~~~~~~~~>6
            $specs['fit_model_size'] = $decoded['value'];
            $specs = $this->generate_specs_for_fit_model_size($specs);
        } elseif (strpos($decoded['name'], 'remove_fit_point') !== false) { #~~~~~~~~>7            
            $specs = $this->remove_fit_point($specs, $decoded);
        } elseif (strpos($decoded['name'], 'add_new_fit_point') !== false) { #~~~~~~~~>8            
            $specs = $this->add_new_fit_point($specs, $decoded);            
        } elseif (strpos($decoded['name'], 'remove_size') !== false) { #~~~~~~~~>7            
            $specs = $this->remove_size($specs, $decoded);            
        }  else {
            return array(
                'message' => 'Nothing to update!',
                'message_type' => 'error',
                'success' => false,
            );
        }
//        $specs['status'] = false;
//        $specs['error'] = $specs;
//        return  $specs;
        $validate_specs = $this->validateProdoctValues($decoded, $specs);
        if(!$validate_specs['status']) {
            return $validate_specs;
        }

        if ($specs){
            $specs_obj->setUndoSpecsJson($specs_obj->getSpecsJson());
            $specs_obj->setSpecsJson(json_encode($specs));
            return $this->update($specs_obj);
        }else{
            return array(
                'message' => 'Nothing to update!',
                'message_type' => 'error',
                'success' => false,
            );
        }
    }
    //------------------------- Validation Product Specification values

    private function validateProdoctValues($decoded, $specs_value){
        $validation['status'] = true;
        $fit_point_explode = explode("-", $decoded['name']);
        $fit_model_selected = '';
        $specs_obj = $this->find($decoded['pk']);
        $specs = json_decode($specs_obj->getSpecsJson(), true);
        if(isset($specs['fit_model_size'])){
            $fit_model_selected_size= $specs['fit_model_size']==null?null:$this->container->get('productIntake.fit_model_measurement')->find($specs['fit_model_size']);
            $fit_model_selected = $fit_model_selected_size?$fit_model_selected_size->getSize():null;
        }

        //------------ Apply Ac # 1 and AC # 2 Validation Rule
        if(count($fit_point_explode) == 4){
            if ($fit_point_explode[3] == 'max_actual' || $fit_point_explode[3] == 'garment_dimension') {
                if( $specs_value[$fit_point_explode[0]][$fit_point_explode[1]][$fit_point_explode[2]]['max_actual'] > $specs_value[$fit_point_explode[0]][$fit_point_explode[1]][$fit_point_explode[2]]['garment_stretch']){
                    $validation['error'] = ' Max_actual it must be less than Garment Stretch Dimension';
                    $validation['status'] = false;
                }
            }
        }

        //------------- Apply AC # 3 Validation Rule
        
        if ($decoded['name'] == 'horizontal_stretch' || $decoded['name'] == 'vertical_stretch'){
            $axis = $decoded['name'] == 'horizontal_stretch' ? 'x' : 'y';
            $fpa = $this->getFitPointArray();
            foreach($specs_value['sizes'][$fit_model_selected] as $fit_point_key => $fit_point_name){
                if (array_key_exists($fit_point_key, $fpa[$axis])) {#--------> for over all horiz stretch
                    if ($specs_value['sizes'][$fit_model_selected][$fit_point_key]['max_actual'] > $specs_value['sizes'][$fit_model_selected][$fit_point_key]['garment_stretch']) {
                        $validation['error'] = $fit_point_key . ' Max Actual cannot be greater than Garment Stretch Dimension';
                        $validation['status'] = false;
                    }
                }
            }
        }
        #---------------------------------
        if($fit_point_explode[0] == 'fit_point_stretch'){            
            if ($specs_value['sizes'][$fit_model_selected][$fit_point_explode[1]]['max_actual'] > $specs_value['sizes'][$fit_model_selected][$fit_point_explode[1]]['garment_stretch']) {
                        $validation['error'] = $fit_point_explode[0] . ' Max Actual cannot be greater than Garment Stretch Dimension';
                        $validation['status'] = false;
            }            
        }
        
        #--------------------------------------------------------------------------
        if (count($fit_point_explode) == 4) {
            //3. Ranges are sequential (ex. Fit Model Dimension, Min Actual, Max Actual, Ideal High, Ideal Low, Garment Dimensions should all be smaller than the same value in the next size up. i.e. Fit Model Bust in size S should be smaller than in size M.)
            if ($fit_point_explode[3] == 'min_actual' || $fit_point_explode[3] == 'max_actual') {
                foreach ($specs_value['sizes'] as $key => $product_size_value) {
                    $size_title = array_keys($specs_value['sizes']);
                    $size_count = count($size_title);
                    foreach ($product_size_value as $key1 => $value) {
                        if (array_key_exists($key1, $specs_value['fit_priority']) && $specs_value['fit_priority'][$key1] > 0) {
                            //3. Ranges are sequential (ex. Fit Model Dimension, Min Actual, Max Actual, Ideal High, Ideal Low, Garment Dimensions should all be smaller than the same value in the next size up. i.e. Fit Model Bust in size S should be smaller than in size M.)
                            if ($fit_point_explode[2] == $key1) { # only specific fit point that is being address
                                $find_next_elements = array_search($key, $size_title) + 1;
                                $next_size_title = ($find_next_elements < $size_count) ? $size_title[$find_next_elements] : null;
                                $next_array_elements = (in_array($next_size_title, $size_title)) ? $specs_value['sizes'][$next_size_title] : null;
                                if ($next_array_elements) {
                                    //------------- AC # 4 and 5
                                    if (($next_array_elements[$key1]['min_actual'] - $value['max_actual']) > 0) {
                                        $validation['error'][$key1] = ' ~~Max Actual for ' . $key . ' for ' . $key1 . ' is less than Min Actual for ' . $next_size_title . ' for ' . $key1;
                                        $validation['status'] = false;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $validation;


    }
    #------------------->1 Overall Stretch >>>>>>>>>>>>>>>>>>>>>>>>>>>
    private function generate_specs_for_stretch($specs, $stretch_type) {
        $axis = $stretch_type == 'horizontal_stretch' ? 'x' : 'y';
        $fpa = $this->getFitPointArray();
        #--------- calculate stretch
        foreach ($specs['sizes'] as $size => $fit_points) {
            foreach ($fit_points as $fpk => $fpv) {
                if (array_key_exists($fpk, $specs['fit_point_stretch']) && !$specs['fit_point_stretch'][$fpk] > 0) {
                    if (array_key_exists($fpk, $fpa[$axis]) && $specs[$stretch_type] > 0) {#--------> for over all horiz stretch                        
                        $specs['sizes'][$size][$fpk]['stretch_percentage'] = $specs[$stretch_type];
                        $specs['sizes'][$size][$fpk]['garment_stretch'] = $fpv['garment_dimension'] + ($fpv['garment_dimension'] * $specs[$stretch_type] / 100);
                        $specs['sizes'][$size][$fpk]['grade_rule_stretch'] = $fpv['grade_rule'] + ($fpv['grade_rule'] * $specs[$stretch_type] / 100);
                    }
                }
            }
        }
        #------------- compute ranges for all sizes
        return $this->compute_all_ranges($specs);
    }

    #------------------->2 Stretch for individual Fitpoint >>>>>>>>>>>>>>>>>>>>>>>>>>>
    private function generate_specs_for_fit_point_stretch($specs, $fp_target) {
        foreach ($specs['sizes'] as $size => $fit_points) {
            $us = $fit_points[$fp_target];
            $us['stretch_percentage'] = $specs['fit_point_stretch'][$fp_target];
            $us['garment_stretch'] = $this->get_garment_stretch($us);
            $us['grade_rule_stretch'] = $this->get_grade_rule_stretch($us);
            $specs['sizes'][$size][$fp_target] = $us;
        }
        #------------- compute ranges for all sizes
        return $this->compute_all_ranges($specs);
    }

    #------------------->3 Actual Max/Min >>>>>>>>>>>>>>>>>>>>>>>>>>>
    private function generate_specs_for_actual($specs, $target, $value) {
        $str = explode('-', $target);
        #calculate ratio sizes-6-bust-min_actual
        $fp = $str[2];
        $fm_size = $str[1];
        $attrib = $str[3];

        #$specs['sizes'][$fm_size][$fp][$attrib] = $value;
        $attrib_calc = str_replace("actual", "calc", $attrib);
        $ratio = $value / $specs['sizes'][$fm_size][$fp]['fit_model'];
        
        #--------- calculate grade rule
        if (number_format((float) $specs['sizes'][$fm_size][$fp][$attrib_calc], 2, '.', '') == number_format((float) $value, 2, '.', '')) {
            foreach ($specs['sizes'] as $size => $fit_points) {
                $specs['sizes'][$size][$fp][$attrib] = $specs['sizes'][$size][$fp][$attrib_calc];
            }
        } else {
            foreach ($specs['sizes'] as $size => $fit_points) {
                $specs['sizes'][$size][$fp][$attrib] = $ratio * $fit_points[$fp]['fit_model'];
            }
        }
//                  if (strpos(strtolower($attrib), 'min') !== false) {
//                    $specs['sizes'][$size][$fp][$attrib] = $fit_points[$fp]['fit_model'] - (2.5 * ($fit_points[$fp]['ideal_high'] - $fit_points[$fp]['ideal_low']));
//                } else {
//                    $specs['sizes'][$size][$fp][$attrib] = $fit_points[$fp]['fit_model'] + (2.5 * ($fit_points[$fp]['ideal_high'] - $fit_points[$fp]['ideal_low']));
//                }

        return $specs;
    }

    #------------------->4 Grade Rule >>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function generate_specs_for_grade_rule($specs, $target, $value) {
        $attrib = $this->break_target_params($target, $value);
        $fit_model_obj = $this->container->get('productIntake.fit_model_measurement')->find($specs['fit_model_size']);
        $specs['fit_model_size_title'] = $fit_model_obj->getSize();

        $pointer = $this->get_fit_model_size_pointer($specs, $attrib['size']);
        #$fit_model_ratio = $this->calculate_fit_model_ratio($specs);
        $fit_model_ratio = $this->compute_fit_model_ratio($specs);
        #---> reverse order if size is smaller than fit model size (This way it will always calculate from target opposite to fit model size till the end)
        $size_keys = $pointer < 0 ? array_reverse(array_keys($specs['sizes'])) : array_keys($specs['sizes']);
        $target_pointer = false;
        $prev_size_title = null;
        #---> grade rule for target size 
        $specs['sizes'][$attrib['size']][$attrib['fit_point']]['grade_rule'] = $value;
        $specs['sizes'][$attrib['size']][$attrib['fit_point']]['grade_rule_stretch'] = $this->get_grade_rule_stretch($specs['sizes'][$attrib['size']][$attrib['fit_point']]);
        
        foreach ($size_keys as $size) {
            if ($size == $attrib['size'] || $target_pointer == true) { # start calculation from the target size
                $target_pointer = true;

                if ($pointer > 0) {#---> garment_dimension = garment_dimension + grade_rule if size is smaller than fit model size
                    $specs['sizes'][$size][$attrib['fit_point']]['garment_dimension'] = $specs['sizes'][$prev_size_title][$attrib['fit_point']]['garment_dimension'] + $specs['sizes'][$size][$attrib['fit_point']]['grade_rule'];
                } else {#---> garment_dimension = garment_dimension - grade_rule if size is smaller than fit model size
                    $specs['sizes'][$size][$attrib['fit_point']]['garment_dimension'] = $specs['sizes'][$prev_size_title][$attrib['fit_point']]['garment_dimension'] - $specs['sizes'][$size][$attrib['fit_point']]['grade_rule'];
                }
                
                #---> garment_stretch = garment_dimension +(garment_dimension * stretch_percentage / 100)
                $specs['sizes'][$size][$attrib['fit_point']]['garment_stretch'] = $this->get_garment_stretch($specs['sizes'][$size][$attrib['fit_point']]);
                #~~~~~~> require to do related calculations for ranges
                $specs['sizes'][$size][$attrib['fit_point']] = $this->compute_ranges_for_fit_point($specs['sizes'][$size][$attrib['fit_point']], $fit_model_ratio[$attrib['fit_point']]);
            }
            $prev_size_title = $size;
        }
        $specs['sizes'][$specs['fit_model_size_title']][$attrib['fit_point']] = $this->reset_fit_model_size_grade_rule($specs, $attrib);
        return $specs;
    }

    #------------------->5 Garment Dimension >>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function generate_specs_for_garment_dimension($specs, $target_str, $value) {
        # here the target size is always the fit model size
        # garment dimension can only be editable in Fit Model size
        $str = explode('-', $target_str);        #sizes-6-bust-garment_dimension
        $target = array('fit_point' => $str[2], 'size' => $str[1], 'value' => $value);
        $specs['sizes'][$target['size']][$target['fit_point']]['garment_dimension'] = $target['value'];
        $specs['sizes'][$target['size']][$target['fit_point']]['garment_stretch'] = $specs['sizes'][$target['size']][$target['fit_point']]['garment_dimension'] + ($specs['sizes'][$target['size']][$target['fit_point']]['garment_dimension'] * $specs['sizes'][$target['size']][$target['fit_point']]['stretch_percentage'] / 100);
        #$fit_model_ratio = $this->calculate_fit_model_ratio($specs);
        $fit_model_ratio = $this->compute_fit_model_ratio($specs);
        # calculated ranges for fit model size
        $specs['sizes'][$target['size']] = $fit_model_ratio['fit_model_measurement'];
        #calculate ranges for bigger sizes
        $specs = $this->reset_garment_dimension($specs, $fit_model_ratio, $target);
        #calculate ranges for smaller sizes
        $specs = $this->reset_garment_dimension($specs, $fit_model_ratio, $target, 'reverse');
        return $specs;
    }

    #------------------->6 Fit Model Size >>>>>>>>>>>>>>>>>>>>>>>>>>>
    private function generate_specs_for_fit_model_size($specs) {
        $fit_model_obj = $this->container->get('productIntake.fit_model_measurement')->find($specs['fit_model_size']);
        $specs['fit_model_size_title']=$fit_model_obj->getSize();
        $specs = $this->add_fitpoints_from_fit_model($specs, $fit_model_obj); #--> additional fit points
        $specs = $this->compute_grade_rule($specs, $fit_model_obj);
        $specs = $this->compute_stretch($specs);
        #------------- compute ranges for all sizes
        return $this->compute_all_ranges($specs, $fit_model_obj);        
    }
    #------------------------------------------
    private function add_fitpoints_from_fit_model($specs, $fit_model) {
        $fm = json_decode($fit_model->getMeasurementJson());
        foreach ($fm as $fm_k => $fm_v) {
            if ($fm_v > 0) {
                foreach ($specs['sizes'] as $size => $fit_points) {
                    if (!array_key_exists($fm_k, $specs['sizes'][$size])) {
                        $specs['sizes'][$size][$fm_k]=$this->get_fit_point_init_measurements($fm_v);
                    }
                }
            }
        }
        return $specs;
    }
    #-------------------------------------------------
    private function get_fit_point_init_measurements($fit_model=0, $garment_dimension=0){
        return array(
            'garment_dimension' => $garment_dimension, 
            'stretch_percentage' => 0, 
            'garment_stretch' => 0, 
            'grade_rule' => 0, 
            'grade_rule_stretch' => 0, 
            'min_calc' => 0, 
            'max_calc' => 0, 
            'min_actual' => 0, 
            'max_actual' => 0, 
            'ideal_low' => 0, 
            'ideal_high' => 0, 
            'fit_model' => $fit_model, 
            'prev_garment_dimension' => 0, 
            'grade_rule' => 0
            );
    }
    #------------------->6 Fit Model Size >>>>>>>>>>>>>>>>>>>>>>>>>>>
    private function remove_fit_point($specs, $decoded) {
        $fp = str_replace("remove_fit_point_", "", $decoded['name']);
        foreach ($specs['sizes'] as $size => $fit_points) {
            if (array_key_exists($fp, $specs['sizes'][$size])) {
                unset($specs['sizes'][$size][$fp]);
            }
        }
        if (array_key_exists($fp, $specs['fit_point_stretch'])) {
            unset($specs['fit_point_stretch'][$fp]);
        }
        return $specs;
    }
    #------------------->#------------------->
     private function remove_size($specs, $decoded) {
        $s = str_replace("remove_size_", "", $decoded['name']);         
        foreach ($specs['sizes'] as $size => $fit_points) {
            if ($size==$s) {
                unset($specs['sizes'][$s]);
            }
        }
        return $specs;
    }

    #------------------->8 add new Fit Point >>>>>>>>>>>>>>>>>>>>>>>>>>>
    private function add_new_fit_point($specs, $decoded) {
        foreach ($specs['sizes'] as $size=>$fp) {
            if (array_key_exists($decoded['value'], $specs['sizes'][$size])) {
                return null;
            }else{
                #this should check if the fit point belongs to horizontal or vertical or should take an input
                $stretch = $specs['horizontal_stretch']; 
                $specs['sizes'][$size][$decoded['value']] = $this->get_empty_fit_point($stretch);
                #$specs['fit_point_stretch'][$decoded['value']]=$stretch;
            }
        }
        return $specs;
    }

    private function get_empty_fit_point($strtech=0) {
        return array('garment_dimension' => 0,
            'stretch_percentage' => $strtech,
            'garment_stretch' => 0,
            'grade_rule' => 0,
            'grade_rule_stretch' => 0,
            'min_calc' => 0,
            'max_calc' => 0,
            'min_actual' => 0,
            'max_actual' => 0,
            'ideal_low' => 0,
            'ideal_high' => 0,
            'fit_model' => 0,
            'grade_rule' => 0,
            'original_value' => 0,
        );
    }
    
    ###################################################################
#-------------------> Dynamic calculations     
    public function dynamicChange($decoded) {
        $specs_obj = $this->find($decoded['pk']);
        $specs = json_decode($specs_obj->getSpecsJson(), true);
        #----------------
        switch ($decoded['type']) {
            case 'double_thigh_grade_rule_min_max': #change---> 1
                $specs = $this->double_thigh_grade_rule_min_max($specs);
                break;
            default:
                $specs = null;
                break;
        }
        
        #----------------
        if ($specs) {
            $specs_obj->setUndoSpecsJson($specs_obj->getSpecsJson());
            $specs_obj->setSpecsJson(json_encode($specs));
            return $this->update($specs_obj);
        } else {
            return array(
                'message' => 'Nothing to update!',
                'message_type' => 'error',
                'success' => false,
            );
        }
    }

#change---> 1
    private function double_thigh_grade_rule_min_max($specs) {
        foreach ($specs['sizes'] as $size => $fit_points ) {
            foreach ($fit_points as $fp => $measure ) {
                if(strpos($fp,'thigh')){
                    $specs['sizes'][$size][$fp]['min_calc'] = $this->to_frac($measure['fit_model'] - ($measure['grade_rule_stretch'] * 2.5 * 2));
                    $specs['sizes'][$size][$fp]['max_calc'] = $this->to_frac($measure['fit_model'] + ($measure['grade_rule_stretch'] * 2.5 * 2));
                }
            }
        }
        return $specs;
    }
    #----------------> return array of fit model ratio to garment dimension
    private function compute_fit_model_ratio($specs, $fit_model_obj = null) {
        if ($fit_model_obj == null) {
            if (!array_key_exists('fit_model_size', $specs)) {
                return null;
            }
            $fit_model_obj = $this->container->get('productIntake.fit_model_measurement')->find($specs['fit_model_size']);
        }
        $fit_model_ratio = array();
        $fit_model_fit_points = json_decode($fit_model_obj->getMeasurementJson(), true);

        #--------- calculate fit model ratio        
        foreach ($specs['sizes'][$fit_model_obj->getSize()] as $fit_point => $measure) {
            $fit_model_ratio['fit_model_measurement'][$fit_point] = $specs['sizes'][$fit_model_obj->getSize()][$fit_point];
            $fit_model_ratio['fit_model_measurement'][$fit_point]['fit_model'] = array_key_exists($fit_point, $fit_model_fit_points)?$fit_model_fit_points[$fit_point]:0;
            $fit_model_ratio['fit_model_measurement'][$fit_point] = $this->grade_rule_calculations_for_fit_model($fit_model_ratio['fit_model_measurement'][$fit_point]);

            #---------------> Calculate ratios
            
        if (!array_key_exists($fit_point, $fit_model_fit_points) || $fit_model_fit_points[$fit_point] == 0) {
                $fit_model_ratio[$fit_point]['min_calc'] = 0;
                $fit_model_ratio[$fit_point]['ideal_low'] = 0;
                $fit_model_ratio[$fit_point]['ideal_high'] = 0;
                $fit_model_ratio[$fit_point]['max_calc'] = 0;            
                $fit_model_ratio[$fit_point]['fit_model'] = 0;
            }else{
                $fit_model_ratio[$fit_point]['min_calc'] = ($fit_model_ratio['fit_model_measurement'][$fit_point]['min_calc'] / $fit_model_fit_points[$fit_point]);
                $fit_model_ratio[$fit_point]['ideal_low'] = ($fit_model_ratio['fit_model_measurement'][$fit_point]['ideal_low'] / $fit_model_fit_points[$fit_point]);
                $fit_model_ratio[$fit_point]['ideal_high'] = ($fit_model_ratio['fit_model_measurement'][$fit_point]['ideal_high'] / $fit_model_fit_points[$fit_point]);
                $fit_model_ratio[$fit_point]['max_calc'] = ($fit_model_ratio['fit_model_measurement'][$fit_point]['max_calc'] / $fit_model_fit_points[$fit_point]);
                $fit_model_ratio[$fit_point]['fit_model'] = ($measure['garment_stretch'] > 0 ) ? ($fit_model_fit_points[$fit_point] / $measure['garment_stretch']) : 0;
            }
        }
        return $fit_model_ratio;
    }

    #----------------> calculate average of adjuscent sizes grade rule for fit model
    private function reset_fit_model_size_grade_rule($specs, $target) {
        $tracker = $this->get_fit_model_size_tracker($specs);
        $s = $tracker['fit_model'];
        $fp = $target['fit_point'];
        $fm_grade_rule = $specs['sizes'][$s][$fp]['grade_rule'];        
        #----------------->
        if (array_key_exists('prev', $tracker) && array_key_exists('next', $tracker) && $tracker['prev'] != null && $tracker['next'] != null) {
            $avg_grade_rule = ($specs['sizes'][$tracker['prev']][$fp]['grade_rule'] + $specs['sizes'][$tracker['next']][$fp]['grade_rule']) / 2;
        } elseif (array_key_exists('prev', $tracker) && $tracker['prev'] != null && (!array_key_exists('next', $tracker) || (array_key_exists('next', $tracker) && $tracker['next'] == null))) {
            $avg_grade_rule = $specs['sizes'][$tracker['prev']][$fp]['grade_rule'];
        } elseif (array_key_exists('next', $tracker) && $tracker['next'] != null && (!array_key_exists('prev', $tracker) || (array_key_exists('prev', $tracker) && $tracker['prev'] == null))) {
            $avg_grade_rule = $specs['sizes'][$tracker['next']][$fp]['grade_rule'];
        }
        #---------------->
        if ($fm_grade_rule != $avg_grade_rule) {            
            $specs['sizes'][$s][$fp]['grade_rule'] = $avg_grade_rule;
            $specs['sizes'][$s][$fp] = $this->grade_rule_calculations_for_fit_model($specs['sizes'][$s][$fp]);            
        }
        return $specs['sizes'][$s][$fp];
    }
    #---> grade rule for fit model size perform just using grade rule without stretch <-------..
    private function grade_rule_calculations_for_fit_model($fp) {
        if ($fp['garment_dimension'] == 0) {
            return $fp;
        }
        $gr_value = $fp['grade_rule'] + ($fp['grade_rule'] * $fp['stretch_percentage'] / 100);
        $fp['grade_rule_stretch'] = $gr_value;
        $fp['min_calc'] = $fp['fit_model'] - (2.5 * $gr_value);
        $fp['ideal_low'] = $fp['fit_model'] - (0.5 * $gr_value);
        $fp['ideal_high'] = $fp['fit_model'] + (0.5 * $gr_value);
        $fp['max_calc'] = $fp['fit_model'] + (2.5 * $gr_value);
        #$fp['max_actual'] = $fp['max_calc'];
        #$fp['min_actual'] = $fp['min_calc']; # disabled for old products import
        $fp['max_actual'] = floatval($fp['max_actual'])>0?$fp['max_actual']:$fp['max_calc'];
        $fp['min_actual'] = floatval($fp['min_calc'])>0?$fp['min_calc']:$fp['min_calc'];
        return $fp;
    }

    #--------------> parse & create array against params
    private function break_target_params($params, $value = null) {
        $str = explode('-', $params);   #sizes-6-bust-min_actual        
        return array('size' => $str[1], 'fit_point' => $str[2], 'attribute' => $str[3], 'value' => $value);
    }

    #--------------> array of fit model & adjuscent (prev & next) sizes
    private function get_fit_model_size_tracker($specs) {
        $size_keys = array_keys($specs['sizes']);
        $pointer = -1;
        $tracker = array();
        foreach ($size_keys as $size_title) {
            if ($size_title == $specs['fit_model_size_title']) {
                $pointer = 0;
                $tracker['fit_model'] = $size_title;
            } else {
                if ($pointer == 0) {
                    $pointer = 1;
                    $tracker['next'] = $size_title;
                } elseif ($pointer == -1) {
                    $tracker['prev'] = $size_title;
                }
            }
        }
        return $tracker;
    }

    #------------->
    private function get_tracking_specs($specs, $target_size = null) {
        $size_keys = array_keys($specs['sizes']);
        $pointer = -1;
        $tracker = array();
        foreach ($size_keys as $size_title) {
            if ($size_title == $specs['fit_model_size_title']) {
                $pointer = 0;
                $tracker['fit_model']['fm'] = $size_title;
            } else {
                if ($pointer == 0) {
                    $pointer = 1;
                    $tracker['fit_model']['next'] = $size_title;
                } elseif ($pointer == -1) {
                    $tracker['fit_model']['prev'] = $size_title;
                }
            }
            if ($target_size != null && $size_title == $target_size) {
                $tracker['target']['size'] = $target_size;
                $tracker['target']['pointer'] = $pointer;
                $tracker['target']['adjucent'] = $tracker['fit_model']['prev'] == $size_title || $tracker['fit_model']['next'] == $size_title ? true : false;
            }
        }
        return $tracker;
    }

    #------------>return -1 if target size smaller than fit model size else return 1
    private function get_fit_model_size_pointer($specs, $target_size) {
        $pointer = -1;
        $size_keys = array_keys($specs['sizes']);
        foreach ($size_keys as $size_title) {
            if ($size_title == $specs['fit_model_size_title']) {
                $pointer = 0;
            } else {
                $pointer = $pointer == 0 ? 1 : $pointer;
            }
            if ($size_title == $target_size) {
                break;
            }
        }
        return $pointer;
    }

    #------------> calculate garment dimension by grade rule
    private function reset_garment_dimension($specs, $fit_model_ratio, $target, $directions = 'forward') {
        # here the target size is always the fit model size
        $size_keys = array_keys($specs['sizes']);
        $size_keys = $directions == 'reverse' ? array_reverse($size_keys) : $size_keys;
        $target_pointer = false;
        $prev_size_title = null;

        foreach ($size_keys as $size) {
            if ($size == $target['size']) {
                $target_pointer = true;
            } else {
                if ($target_pointer == true) {
                    if ($directions == 'reverse') {
                        $specs['sizes'][$size][$target['fit_point']]['garment_dimension'] = $specs['sizes'][$prev_size_title][$target['fit_point']]['garment_dimension'] - $specs['sizes'][$size][$target['fit_point']]['grade_rule'];
                    } else {
                        $specs['sizes'][$size][$target['fit_point']]['garment_dimension'] = $specs['sizes'][$prev_size_title][$target['fit_point']]['garment_dimension'] + $specs['sizes'][$size][$target['fit_point']]['grade_rule'];
                    }
                    $specs['sizes'][$size][$target['fit_point']]['garment_stretch'] = $this->get_garment_stretch($specs['sizes'][$size][$target['fit_point']]);
                    $specs['sizes'][$size][$target['fit_point']] = $this->compute_ranges_for_fit_point($specs['sizes'][$size][$target['fit_point']], $fit_model_ratio[$target['fit_point']]);
                }
            }
            $prev_size_title = $size;
        }
        return $specs;
    }

    #------------>  calculate garment strtech 
    private function get_garment_stretch($fp) {
        return $fp['garment_dimension'] + ($fp['garment_dimension'] * $fp['stretch_percentage'] / 100);
    }
    #------------>  calculate garment strtech 
    private function get_grade_rule_stretch($fp) {
        return $fp['grade_rule'] + ($fp['grade_rule'] * $fp['stretch_percentage'] / 100);                
    }

    #-------------------------------------------------------------
    private function compute_grade_rule($specs, $fm_obj) {
        $size_keys = array_keys($specs['sizes']);
        $tracker['fit_model'] = $fm_obj->getSize();
        $prev = null;
        $fm_pass = false;
        #--------> from next to fit model size to the largest size
        foreach ($size_keys as $sk) {
            if ($fm_pass == true) {
                foreach ($specs['sizes'][$sk] as $fp => $fpm) {
                    if(array_key_exists($fp, $specs['sizes'][$sk]) && array_key_exists($fp, $specs['sizes'][$prev]))
                        $specs['sizes'][$sk][$fp]['grade_rule'] = $specs['sizes'][$sk][$fp]['garment_dimension'] - $specs['sizes'][$prev][$fp]['garment_dimension'];
                }
            } else {
                if ($sk == $tracker['fit_model']) {
                    $tracker['prev'] = $prev;
                    $fm_pass = true;
                }
            }
            $prev = $sk;
        }

        #----------  from next to fit model size to the smallest size
        $size_keys = array_reverse($size_keys);
        $prev = null;
        $fm_pass = false;
        foreach ($size_keys as $sk) {
            if ($fm_pass == true) {
                foreach ($specs['sizes'][$sk] as $fp => $fpm) {
                    $specs['sizes'][$sk][$fp]['grade_rule'] = $specs['sizes'][$prev][$fp]['garment_dimension'] - $specs['sizes'][$sk][$fp]['garment_dimension'];
                }
            } else {
                if ($sk == $tracker['fit_model']) {
                    $tracker['next'] = $prev;
                    $fm_pass = true;
                }
            }
            $prev = $sk;
        }
                
        return $this->compute_fit_model_grade_rule($specs);
    }
    #------------------------------------
    private function compute_fit_model_grade_rule($specs){
        $tracker = $this->get_fit_model_size_tracker($specs);
        if (array_key_exists('prev', $tracker) && array_key_exists('next', $tracker) && $tracker['prev']!=null && $tracker['next']!=null){
            foreach ($specs['sizes'][$tracker['fit_model']] as $fp => $fpm) {
                $specs['sizes'][$tracker['fit_model']][$fp]['grade_rule'] = ($specs['sizes'][$tracker['prev']][$fp]['grade_rule'] + $specs['sizes'][$tracker['next']][$fp]['grade_rule']) / 2;
            }
        }elseif (array_key_exists('prev', $tracker) && $tracker['prev']!=null && (!array_key_exists('next', $tracker) || (array_key_exists('next', $tracker) && $tracker['next']==null))){
            foreach ($specs['sizes'][$tracker['fit_model']] as $fp => $fpm) {
                $specs['sizes'][$tracker['fit_model']][$fp]['grade_rule'] = $specs['sizes'][$tracker['prev']][$fp]['grade_rule'];                
            }
        }elseif (array_key_exists('next', $tracker) && $tracker['next']!=null  && (!array_key_exists('prev', $tracker)  || (array_key_exists('prev', $tracker)  && $tracker['prev']==null))){            
            foreach ($specs['sizes'][$tracker['fit_model']] as $fp => $fpm) {
                $specs['sizes'][$tracker['fit_model']][$fp]['grade_rule'] = $specs['sizes'][$tracker['next']][$fp]['grade_rule'];            
            }
        }
        return $specs;
    }
    #-----------------------------------------------------
    private function compute_stretch($specs) {
        $fpa = $this->getFitPointArray();
        !array_key_exists('fit_point_stretch', $specs) ? $specs['fit_point_stretch'] = array() : '';
        #--------- calculate stretch
        foreach ($specs['sizes'] as $size => $fit_points) {
            foreach ($fit_points as $fpk => $fpv) {
                $us = $fpv;
                $us['stretch_percentage'] = 0;
                #---------------> stretch calculation
                if (array_key_exists($fpk, $specs['fit_point_stretch']) && $specs['fit_point_stretch'][$fpk] > 0) { #--------> for individual fit point
                    $us['stretch_percentage'] = $specs['fit_point_stretch'][$fpk];
                } else { #--------> for over all horiz/vertical stretch
                    if (array_key_exists($fpk, $fpa['x']) && $specs['horizontal_stretch'] > 0) {
                        $us['stretch_percentage'] = $specs['horizontal_stretch'];
                    } elseif (array_key_exists($fpk, $fpa['y']) && $specs['vertical_stretch'] > 0) {
                        $us['stretch_percentage'] = $specs['vertical_stretch'];
                    }
                }
                $us['garment_stretch'] = $this->get_garment_stretch($us);                
                $us['grade_rule_stretch'] = $this->get_grade_rule_stretch($us);
                #--------------------------                
                $specs['sizes'][$size][$fpk] = $us;
            }
        }
        return $specs;
    }

    #------------------------------------------------------
    private function compute_all_ranges($specs, $fit_model_obj = null) {
        if ($fit_model_obj == null) {
            if (array_key_exists('fit_model_size', $specs) && strlen($specs['fit_model_size'])>0) {
                $fit_model_obj = $this->container->get('productIntake.fit_model_measurement')->find($specs['fit_model_size']);
            } else {
                return $specs; # if fit model has not been set yet
            }
        }
        $fit_model_ratio = $this->compute_fit_model_ratio($specs, $fit_model_obj);
        #--------- copy ranges for fit model size
        $specs['sizes'][$fit_model_obj->getSize()] = $fit_model_ratio['fit_model_measurement'];
        
        #---------------------------------> calculate ranges
        foreach ($specs['sizes'] as $size => $fit_points) {
            foreach ($fit_points as $fpk => $fpv) {
                if ($size != $fit_model_obj->getSize() && array_key_exists($fpk, $fit_model_ratio)) {#---> exclude measurement for fit model size
                    $specs['sizes'][$size][$fpk] = $this->compute_ranges_for_fit_point($specs['sizes'][$size][$fpk], $fit_model_ratio[$fpk]);
                }
            }
        }
        return $specs;
    }

    #------------------------------------------------------
    private function compute_ranges_for_fit_point($fp_specs, $ratio) {
        $fp_specs['fit_model'] = $fp_specs['garment_stretch'] * $ratio['fit_model'];
        $fp_specs['ideal_low'] = $fp_specs['fit_model'] * $ratio['ideal_low'];
        $fp_specs['ideal_high'] = $fp_specs['fit_model'] * $ratio['ideal_high'];
        $fp_specs['min_calc'] = $fp_specs['fit_model'] - (2.5 * ($fp_specs['ideal_high'] - $fp_specs['ideal_low']));
        $fp_specs['max_calc'] = $fp_specs['fit_model'] + (2.5 * ($fp_specs['ideal_high'] - $fp_specs['ideal_low']));
        #$fp_specs['min_actual'] = $fp_specs['min_calc'];
        #$fp_specs['max_actual'] = $fp_specs['max_calc'];        
        $fp_specs['min_actual'] = floatval($fp_specs['min_actual']) > 0 ? $fp_specs['min_actual'] : $fp_specs['min_calc'];
        $fp_specs['max_actual'] = floatval($fp_specs['max_actual']) > 0 ? $fp_specs['max_actual'] : $fp_specs['max_calc'];        
        return $fp_specs;
    }

    ########################################################################
    ############################## Specification Validation ########################
    ########################################################################

    public function validateSpecification($id) {
        $ps = $this->find($id);
        $parsed_data = json_decode($ps->getSpecsJson(), true);
        $validation_rule = json_decode($ps->getValidationJson(), true);
        $result = array();
        $size_title = array_keys($parsed_data['sizes']);
        $size_count = count($size_title);

        #----------------- AC#13 & 14 Wrong Fit Priority
        if (!(isset($validation_rule) && array_key_exists('fit_priority_assigned_to_an_incorrect_garment', $validation_rule))) {                                        
            # clothing type & attributes array --------->
            $clothing_type_attributes = $this->container->get('admin.helper.product.specification')->getAttributesFor($parsed_data['clothing_type']);
            if (is_array($clothing_type_attributes)) {
                foreach ($parsed_data['fit_priority'] as $fp => $fpp) {
                    if ($fpp > 0 && is_array($clothing_type_attributes) && !array_key_exists($fp, $clothing_type_attributes)) {
                        $result['fit_priority_assigned_to_an_incorrect_garment'][$fp] = $parsed_data['clothing_type'] . " should not have " . $fp . " (".$fpp."%) in the fit priority";
                    }
                }
            }else{
                $result['fit_priority_assigned_to_an_incorrect_garment']['clothing_type'] = "Clothing Type '". $parsed_data['clothing_type'] . "' dose not match any attribute lists";
            }
        }
        #---------------------------------- 
        foreach ($parsed_data['sizes'] as $current_size_title => $current_size) {
            $next_index = array_search($current_size_title, $size_title) + 1;
            $next_size_title = ($next_index < $size_count) ? $size_title[$next_index] : null;
            $next_size = (in_array($next_size_title, $size_title)) ? $parsed_data['sizes'][$next_size_title] : null;
            foreach ($current_size as $fp_title => $fp) {
                if(array_key_exists($fp_title, $parsed_data['fit_priority']) && $parsed_data['fit_priority'][$fp_title]>0){
                #-------- AC#12 Range Sequence ----------
                if ($next_size) {
                if (!(isset($validation_rule) && array_key_exists('sequential', $validation_rule))) {
                    
                        if ($fp['garment_dimension'] > $next_size[$fp_title]['garment_dimension']) {                            
                            $result['sequential'][$current_size_title][$fp_title] = $this->ac12Msg('Garment Dimension', $fp_title, $current_size_title, $next_size_title);                                                                                    
                        }
                        if ($fp['fit_model'] > $next_size[$fp_title]['fit_model']) {
                            $result['sequential'][$current_size_title][$fp_title] = $this->ac12Msg('Fit Model', $fp_title, $current_size_title, $next_size_title);                            
                        }
                        if ($fp['min_actual'] > $next_size[$fp_title]['min_actual']) {
                            $result['sequential'][$current_size_title][$fp_title] = $this->ac12Msg('Min Actual', $fp_title, $current_size_title, $next_size_title);
                        }
                        
                        if ($fp['max_actual'] > $next_size[$fp_title]['max_actual']) {
                            $result['sequential'][$current_size_title][$fp_title] = $this->ac12Msg('Max Actual', $fp_title, $current_size_title, $next_size_title);
                        }
                        if ($fp['ideal_high'] > $next_size[$fp_title]['ideal_high']) {
                            $result['sequential'][$current_size_title][$fp_title] = $this->ac12Msg('Ideal High', $fp_title, $current_size_title, $next_size_title);
                        }
                        if ($fp['ideal_low'] > $next_size[$fp_title]['ideal_low']) {
                            $result['sequential'][$current_size_title][$fp_title] = $this->ac12Msg('Ideal low', $fp_title, $current_size_title, $next_size_title);
                        }

                    }
                    # AC# 15 & 16 Grade rules become generally larger as the sizes increase within a certain % tolerance (Ex. if there is a size run of S, M, L, XL and grade rules for S-M-L are all 2" but it changes to 1" for L-XL, this should be called out. It is possible, but we want to check it.)
                    if (!(isset($validation_rule) && array_key_exists('grade_rules_become_generally_larger', $validation_rule) )) {
                        if ($fp['grade_rule'] > $next_size[$fp_title]['grade_rule']) {
                            $result['grade_rules_become_generally_larger'][$current_size_title][$fp_title] = 'Grade rules for ' . $fp_title . ' of size ' . $current_size_title . ' (' . $fp['grade_rule'] . ')' . ' decrease in size ' . $next_size_title . ' (' . $next_size[$fp_title]['grade_rule'] . ')';
                        }
                    }
                  
                
                  #-------- Have general guide for Fit Model Body proportions: --Flag if bust to waist ratio is more than 11" --Flag if waist to hip is more than 12"
                #------------ AC# 17 & 18
                    if (!(isset($validation_rule) && array_key_exists('bust_to_waist_ratio', $validation_rule) )) {
                        if (isset($current_size["waist"]['fit_model']) && $fp_title == 'bust') {
                            $bust_waist = $fp["fit_model"] - $current_size["waist"]['fit_model'];
                            if ($bust_waist > 11) {
                                $result['bust_to_waist_ratio'][$key][$fp_title] = $bust_waist . ' bust to waist ratio is more than 11';
                            }
                        }
                    }
                    #--------------------- AC# 19 & 20
                    if (!(isset($validation_rule) && array_key_exists('bust_to_hip_ratio', $validation_rule))) {
                        if (isset($current_size["waist"]['fit_model']) && $fp_title == 'hip') {
                            $bust_hip = $current_size["waist"]['fit_model'] - $fp["fit_model"];
                            if ($bust_hip > 12) {
                                $result['bust_to_hip_ratio'][$key][$fp_title] = $bust_hip . ' waist to hip is more than 12';
                            }
                        }
                    }

                    #------------------AC# 21
                    #Need a tolerance of + or - 0.25" that if the garment dimension increased by 2" from one size to the next (i.e. has a 2" grade rule) then the fit model body dimension for that fit point should increase by 2" + or - 0.25".
                    
                    if(! (isset($validation_rule) && array_key_exists('tolerance', $validation_rule) )){                        
                        $garment_dimension_difference = $next_size[$fp_title]['garment_dimension'] - $fp['garment_dimension'];
                        
                        if (!($garment_dimension_difference <= ($fp['grade_rule'] + 0.25) && $garment_dimension_difference >= ($fp['grade_rule'] - 0.25))) {
                            $result['tolerance'][$current_size_title][$fp_title] = 'Fit Model ' . $fp_title . ' did not increase by 2.25" from size ' . $current_size_title . ' to ' . $next_size_title;
                            }
                    }
                }
                
                        }
            }
        }
        return $result;

        return new Response(json_encode($result));
    }
    private function ac12Msg($range, $fp, $cs, $ns) {
        return $range . ' for size ' . $cs . '  ' . $fp . ' is less than size ' . $ns;
    }
#--------------------------------------------------
    public function range_validation_message($range, $fp, $current, $next, $specs) {
        #if ($specs[$current][$fp][$range] > $specs[$next][$fp][$range]) {
           return $this->snake_camel($range) . ' ' . $this->snake_camel($fp) . ' of size ' . $current . ' (' . $specs[$current][$fp][$range] . ') should be greater than ' . $this->snake_camel($range) . ' ' . $this->snake_camel($fp) . ' of size ' . $next . ' (' . $specs[$next][$fp][$range] . ')';
        #}
    }

    private function snake_camel($str){
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $str)));
    }
    ########################################################################
    ############################## Product Creation ########################
    ########################################################################

    public function create_product($id) {
        $specs = $this->find($id);
        $data = json_decode($specs->getSpecsJson(), true);
        $clothing_type = $this->container->get("admin.helper.clothingtype")->findOneByGenderNameCSV(strtolower($data['gender']), strtolower($data['clothing_type']));
        
        if($clothing_type==null)
            return array('success'=>false, 'message'=>'Clothing type not found');
        
        $brand = $this->container->get('admin.helper.brand')->findOneByName($data['brand']);
        if($brand==null)
            return array('success'=>false, 'message'=>'Brand not found');
        
        $product = new Product;
        $product->setBrand($brand);
        $product->setClothingType($clothing_type);
        $product->setName(array_key_exists('style_name', $data) ? $data['style_name'] : '');
        $product->setControlNumber(array_key_exists('style_id_number', $data) ? $data['style_id_number'] : '');
        $product->setDescription(array_key_exists('description', $data) ? $data['description'] : '');
        $product->setStretchType(array_key_exists('stretch_type', $data) ? $data['stretch_type'] : '');
        $product->setHorizontalStretch($data['horizontal_stretch']);
        $product->setVerticalStretch($data['vertical_stretch']);
        $product->setCreatedAt(new \DateTime('now'));
        $product->setUpdatedAt(new \DateTime('now'));
        $product->setGender($data['gender']);
        $product->setStylingType($data['styling_type']);
        $product->setNeckline(array_key_exists('neck_line', $data) ? $data['neck_line'] : $data['neckline']);
        $product->setSleeveStyling($data['sleeve_styling']);
        $product->setRise($data['rise']);
        $product->setHemLength($data['hem_length']);
        $product->setFabricWeight($data['fabric_weight']);
        $product->setStructuralDetail($data['structural_detail']);
        $product->setFitType($data['fit_type']);
        $product->setStatus('pending');
        $product->setLayering(array_key_exists('layring', $data) ? $data['layring'] : $data['layering']);
        $product->setFitPriority(array_key_exists('fit_priority', $data) ? json_encode($data['fit_priority']) : 'NULL' );
        $product->setFabricContent(json_encode(array_key_exists('fabric_content', $data) ? $data['fabric_content'] : ''));
        $product->setDisabled(true);
        $product->setDeleted(false);
        $product->setSizeTitleType($data['size_title_type']);
        #------------------------
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();
        $this->create_product_sizes($product, $data);
        $this->create_product_colors($data, $product);
        return array('success'=>true, 'message'=>'Product Created');
    }

    #------------------------------------------------------------
    private function create_product_sizes($product, $data) {
        $em = $this->getDoctrine()->getManager();
        $size_titles = $this->container->get('admin.helper.size')->getSizeArray($data['gender'], $data['size_title_type']);
        $i = 1;
        foreach ($size_titles['regular'] as $size_title => $value) {
            if (array_key_exists($size_title, $data['sizes'])) {
                $ps = new ProductSize();
                $ps->setTitle($size_title);
                $ps->setProduct($product);
                $ps->setBodyType(ucfirst($data['body_type']));
                $ps->setIndexValue($i);
                $em->persist($ps);
                $em->flush();
                $this->create_product_size_measurements($ps, $data['sizes'][$size_title]);
            }
            $i++;
        }
        return $product;
    }

    #------------------------------------------------------
    private function create_product_size_measurements($size, $data) {
        $em = $this->getDoctrine()->getManager();
        foreach ($data as $key => $value) {
            if ($key != 'key') {
                $psm = new ProductSizeMeasurement;
                $psm->setTitle($key);
                $psm->setProductSize($size);
                array_key_exists('garment_dimension', $value) ? $psm->setGarmentMeasurementFlat($value['garment_dimension']) : null;
                array_key_exists('garment_stretch', $value) ? $psm->setGarmentMeasurementStretchFit($value['garment_stretch']) : null;
                $psm->setMaxBodyMeasurement($value['max_actual']);
                $psm->setIdealBodySizeHigh($value['ideal_high']);
                $psm->setIdealBodySizeLow($value['ideal_low']);
                $psm->setMinBodyMeasurement($value['min_actual']);
                $psm->setFitModelMeasurement($value['fit_model']);
                $psm->setMaxCalculated($value['max_calc']);
                $psm->setMinCalculated($value['min_calc']);
                $psm->setGradeRule($value['grade_rule']);
                $em->persist($psm);
                $em->flush();
            }
        }
        return;
    }

    #------------------------------------------------------------
    private function create_product_colors($data, $product) {
        $color_names = explode(",", $data['colors']);
        $em = $this->getDoctrine()->getManager();
        foreach ($color_names as $cn) {
            $pc = new ProductColor();
            $pc->setTitle(trim($cn));
            $pc->setProduct($product);
            $em->persist($pc);
            $em->flush();
        }
        return $product;
    }
    
    #######################################################################
    
    public function getDoctrine() {
        return $this->container->get('doctrine');
    }

    #---------------------- CSV File Downlod Links
    public function csvDownloads($csv_files) {
        $csv_file_path = array();
        if(count($csv_files) == 1){
             $csv_file_path[$csv_files->getId()] = $csv_files->getWebPath();
        } else {
            foreach ($csv_files as $k => $v) {
                $csv_file = $this->find($v->getId());
                $csv_file_path[$v->getId()] = $csv_file->getWebPath();
            }
        }
        return $csv_file_path;
    }

    private function replace_with_new_fitpoint($fp_array, $nfp) {        
        foreach ($fp_array as $fp => $v) {
            if (array_key_exists($fp, $nfp)){
                $fp_array[$nfp[$fp]]=$v;
                unset($fp_array[$fp]);
            }
        }
        return $fp_array;
    }
    
    //--------------------------- get deaitls of Existing Product
    public function getExistingProductDetails( $id )
    {       
        $new_fp = array('hip' => 'low_hip', 'thigh' => 'high_thigh', 'central_front_waist' => 'cf_waist');
        $data = $this->container->get('service.repo')->getExistingProductDetails($id); 
//        $product_sizes_sorted = $this->product_size_sorting($data[0][0]['gender'],$data[0][0]['size_title_type'], $data[0][0]['product_sizes']);
//        echo "<pre>";
//        print_R($data[0][0]['product_sizes']);
//        die;
        $data1['product_id'] = $id;
        $data1['clothing_type']=$data[0]['clothing_type'];
        $data1['brand']=$data[0]['brand'];
        $data1['style_id_number']=$data[0][0]['control_number'];
        $data1['style_name']=$data[0][0]['name'];
        $data1['gender']=$data[0][0]['gender'];
        $data1['description']=$data[0][0]['description'];
        $data1['hem_length']=$data[0][0]['hem_length'];
        $data1['neckline']=$data[0][0]['neckline'];
        $data1['sleeve_styling']=$data[0][0]['sleeve_styling'];
        $data1['rise']=$data[0][0]['rise'];
        $data1['stretch_type']=$data[0][0]['stretch_type'];
        $data1['horizontal_stretch']=$data[0][0]['horizontal_stretch'];
        $data1['vertical_stretch']=$data[0][0]['vertical_stretch'];
        $data1['fabric_weight']=$data[0][0]['fabric_weight'];
        $data1['layering']=$data[0][0]['layering'];
        $data1['structural_detail']=$data[0][0]['structural_detail'];
        $data1['fit_type']=$data[0][0]['fit_type'];          
        $data1['fit_priority']=json_decode($data[0][0]['fit_priority'], true);
        $data1['fabric_content']=json_decode($data[0][0]['fabric_content']);
        $data1['size_title_type']=$data[0][0]['size_title_type'];
        $data1['control_number']=$data[0][0]['control_number'];
        $data1['styling_type']=$data[0][0]['styling_type'];
        $data1['colors']='';
        $data1['mapping_title']=$data[0]['clothing_type'];
        $data1['mapping_description']=$data[0]['clothing_type'];       
        $data1['measuring_unit']='inch';       
        $data1['fit_priority'] = $this->replace_with_new_fitpoint($data1['fit_priority'], $new_fp); #replacing old fit point with new
        //------------- Return Product Sizes Sorted Form
        $product_sizes_sorted = $this->product_size_sorting($data[0][0]['gender'],$data[0][0]['size_title_type'], $data[0][0]['product_sizes']);
        foreach ($product_sizes_sorted as $key => $product_size_value) {                  
                 foreach ($product_size_value['product_size_measurements'] as  $value) {  
                     $fp = array_key_exists($value['title'], $new_fp) ? $new_fp[$value['title']] : $value['title'];#replacing old fit point with new
                    $stretch_percentage = ($value['garment_measurement_stretch_fit'] == 0)? 0 :(($value['garment_measurement_stretch_fit'] - $value['garment_measurement_flat'])/$value['garment_measurement_flat'])*100;
                    $data1['sizes'][$product_size_value['title']][$fp]['fit_model'] = $value['fit_model_measurement'];
                    $data1['sizes'][$product_size_value['title']][$fp]['garment_dimension'] = $value['garment_measurement_flat'];
                    $data1['sizes'][$product_size_value['title']][$fp]['garment_stretch'] = $value['garment_measurement_stretch_fit'];
                    $data1['sizes'][$product_size_value['title']][$fp]['grade_rule'] = $value['grade_rule'];
                    #$data1['sizes'][$product_size_value['title']][$fp]['grade_rule_stretch'] = $value['horizontal_stretch'];
                    #$data1['sizes'][$product_size_value['title']][$fp]['grade_rule_stretch'] = $value['vertical_stretch'];                
                    $data1['sizes'][$product_size_value['title']][$fp]['stretch_percentage'] = $stretch_percentage;//$value['stretch_type_percentage'];
                    $data1['sizes'][$product_size_value['title']][$fp]['ideal_high'] = $value['ideal_body_size_high'];
                    $data1['sizes'][$product_size_value['title']][$fp]['ideal_low'] = $value['ideal_body_size_low'];
                    $data1['sizes'][$product_size_value['title']][$fp]['max_actual'] = $value['max_body_measurement'];
                    $data1['sizes'][$product_size_value['title']][$fp]['max_calc'] = $value['max_calculated'];
                    $data1['sizes'][$product_size_value['title']][$fp]['min_actual'] = $value['min_body_measurement'];
                    $data1['sizes'][$product_size_value['title']][$fp]['min_calc'] = $value['min_calculated'];
                    $data1['body_type'] = $product_size_value['body_type'];
                    $data1['fit_point_stretch'][$fp] = $stretch_percentage;
                }
             }
            foreach ($data[0][0]['product_colors'] as $key => $product_color_value) {                  
               $colors['colors'][$product_color_value['title']] = $product_color_value['title'];
            }
            $data1['colors'] = implode(',',$colors['colors']);            
          //  $data1['fit_point_stretch'] = array_flip(array_keys(reset($data1['sizes'])));          
            $brand =  $this->container->get('admin.helper.brand')->findOneByName($data1['brand']); 
            $class = $this->class;
            $c = new $class();
            $c->setTitle("Set title");
            $c->setDescription($data1['description']);
            $c->setSpecsJson(json_encode($data1));        
            $c->setBrand($brand);            
            $c->setBrandName($data1['brand']);
            $c->setStyleName($data1['style_name']);
            $c->setClothingType($data1['clothing_type']);
            $c->setStyleIdNumber($data1['style_id_number']);            
            $c->setCreatedAt(new \DateTime('now'));
            $this->save($c);
            return true;
        
    }
      //--------------------------- get Product Size Measurments
    public function getProductSizeMeasurments($sizemeasurements, $id )
    {       
        $data = $this->container->get('service.repo')->getExistingProductDetails($id);  
         //------------- Product Size Measurement Get Titles   
        foreach ($data[0][0]['product_sizes'] as $key => $product_size_value) { 
            foreach ($product_size_value['product_size_measurements'] as  $value) {             
                $size_measurements_title[$product_size_value['title']][$value['title']] = $value['id'];
                $new_fp_size_measurements[$product_size_value['title']] = $product_size_value['id']; 
            }           
        }       
        foreach ($data[0][0]['product_sizes'] as $key => $product_size_value) { 
            foreach ($product_size_value['product_size_measurements'] as  $fp_title=>$value) {             
                if(!array_key_exists($value['title'],$sizemeasurements['sizes'][$product_size_value['title']])){
                    $this->container->get('admin.helper.productsizemeasurement')->delete($value['id']);
                }
            }           
        }       
        
        foreach ($sizemeasurements['sizes'] as $key => $product_size_mesurements) {            
            foreach ($product_size_mesurements as $key_val => $value) { 
                $size_measurements_title_key[] = (isset($size_measurements_title[$key])) ? $size_measurements_title[$key] : null;
                  if( array_key_exists( $key_val,$size_measurements_title_key ) ){
                    $psm = $this->container->get('admin.helper.productsizemeasurement')->find($size_measurements_title[$key][$key_val]);
                    $psm->setGarmentMeasurementFlat($value['garment_dimension']);
                    $psm->setGarmentMeasurementStretchFit($value['garment_stretch']);
                    $psm->setMaxBodyMeasurement($value['max_actual']);
                    $psm->setIdealBodySizeHigh($value['ideal_high']);
                    $psm->setIdealBodySizeLow($value['ideal_low']);
                    $psm->setMinBodyMeasurement($value['min_actual']);
                    $psm->setFitModelMeasurement($value['fit_model']);
                    $psm->setMaxCalculated($value['max_calc']);
                    $psm->setMinCalculated($value['min_calc']);
                    $psm->setGradeRule($value['grade_rule']);
                    $this->container->get('admin.helper.productsizemeasurement')->update($psm);
                } else {      
                    $size_id = $this->container->get('admin.helper.productsizes')->findSizeByProductTitle($key,$id);
                    $psm = new ProductSizeMeasurement;                                         
                    $psm->setProductSize($size_id);
                    $psm->setTitle($key_val);
                    $psm->setGarmentMeasurementFlat($value['garment_dimension']);
                    $psm->setGarmentMeasurementStretchFit($value['garment_stretch']);
                    $psm->setMaxBodyMeasurement($value['max_actual']);
                    $psm->setIdealBodySizeHigh($value['ideal_high']);
                    $psm->setIdealBodySizeLow($value['ideal_low']);
                    $psm->setMinBodyMeasurement($value['min_actual']);
                    $psm->setFitModelMeasurement($value['fit_model']);
                    $psm->setMaxCalculated($value['max_calc']);
                    $psm->setMinCalculated($value['min_calc']);
                    $psm->setGradeRule($value['grade_rule']);
                    $this->container->get('admin.helper.productsizemeasurement')->update($psm);
                }
            }
        }
            
            
        
        $data1= '';
//        foreach ($data[0][0]['product_sizes'] as $key => $product_size_value) { 
//            foreach ($product_size_value['product_size_measurements'] as  $value) { 
//                               
//              //  $data1['sizes'][$product_size_value['title']][$value['title']]['id'] = $value['id'];
//                if( array_key_exists($value['title'],array_flip(array_keys(reset($sizemeasurements['sizes'])))) ){
//              //   array_key_exists($value['title'],array_flip(array_keys(reset($sizemeasurements['sizes'])))) ? $psm = $this->container->get('admin.helper.productsizemeasurement')->find($value['id']) : $psm = new ProductSizeMeasurement;  
//                    $psm = $this->container->get('admin.helper.productsizemeasurement')->find($value['id']);
//                    array_key_exists('garment_dimension', $sizemeasurements['sizes'][$product_size_value['title']][$value['title']]) ? $psm->setGarmentMeasurementFlat($sizemeasurements['sizes'][$product_size_value['title']][$value['title']]['garment_dimension']) : null;
//                    array_key_exists('garment_stretch', $sizemeasurements['sizes'][$product_size_value['title']][$value['title']]) ? $psm->setGarmentMeasurementStretchFit($sizemeasurements['sizes'][$product_size_value['title']][$value['title']]['garment_stretch']) : null;
//                    $psm->setMaxBodyMeasurement($sizemeasurements['sizes'][$product_size_value['title']][$value['title']]['max_actual']);
//                    $psm->setIdealBodySizeHigh($sizemeasurements['sizes'][$product_size_value['title']][$value['title']]['ideal_high']);
//                    $psm->setIdealBodySizeLow($sizemeasurements['sizes'][$product_size_value['title']][$value['title']]['ideal_low']);
//                    $psm->setMinBodyMeasurement($sizemeasurements['sizes'][$product_size_value['title']][$value['title']]['min_actual']);
//                    $psm->setFitModelMeasurement($sizemeasurements['sizes'][$product_size_value['title']][$value['title']]['fit_model']);
//                    $psm->setMaxCalculated($sizemeasurements['sizes'][$product_size_value['title']][$value['title']]['max_calc']);
//                    $psm->setMinCalculated($sizemeasurements['sizes'][$product_size_value['title']][$value['title']]['min_calc']);
//                    $psm->setGradeRule($sizemeasurements['sizes'][$product_size_value['title']][$value['title']]['grade_rule']);
//                    $this->container->get('admin.helper.productsizemeasurement')->update($psm);
//                } else{
//                    $psm = new ProductSizeMeasurement;                                         
//                    array_key_exists('garment_dimension', $sizemeasurements['sizes'][$product_size_value['title']][$value['title']]) ? $psm->setGarmentMeasurementFlat($sizemeasurements['sizes'][$product_size_value['title']][$value['title']]['garment_dimension']) : null;
//                    array_key_exists('garment_stretch', $sizemeasurements['sizes'][$product_size_value['title']][$value['title']]) ? $psm->setGarmentMeasurementStretchFit($sizemeasurements['sizes'][$product_size_value['title']][$value['title']]['garment_stretch']) : null;
//                    $psm->setMaxBodyMeasurement($sizemeasurements['sizes'][$product_size_value['title']][$value['title']]['max_actual']);
//                    $psm->setIdealBodySizeHigh($sizemeasurements['sizes'][$product_size_value['title']][$value['title']]['ideal_high']);
//                    $psm->setIdealBodySizeLow($sizemeasurements['sizes'][$product_size_value['title']][$value['title']]['ideal_low']);
//                    $psm->setMinBodyMeasurement($sizemeasurements['sizes'][$product_size_value['title']][$value['title']]['min_actual']);
//                    $psm->setFitModelMeasurement($sizemeasurements['sizes'][$product_size_value['title']][$value['title']]['fit_model']);
//                    $psm->setMaxCalculated($sizemeasurements['sizes'][$product_size_value['title']][$value['title']]['max_calc']);
//                    $psm->setMinCalculated($sizemeasurements['sizes'][$product_size_value['title']][$value['title']]['min_calc']);
//                    $psm->setGradeRule($sizemeasurements['sizes'][$product_size_value['title']][$value['title']]['grade_rule']);
//                    $this->container->get('admin.helper.productsizemeasurement')->save($psm);
//                }
//            }
        
        return $data1;
    }
    
   //----------------- Product Sizes Sorting any formating using Config file sizes_ltf.yml based on index  
    public function product_size_sorting($gender, $size_title, $product_size_array) {
         $size_config = $this->size_config;
         $gender_title = ($gender == 'f' ? 'woman':'men');
         //return $gender_title;
         $size_index_array =  $size_config['constants']['size_titles'][$gender_title][$size_title];
         $product_size_title_array = array_flip(array_column($product_size_array, 'title'));
        // return  $product_size_array;
         $product_size_title_sorted = array_intersect_key($size_index_array, $product_size_title_array );
      // return array_intersect_key($size_index_array, $product_size_title_array );         
        //----------- Filter the Array baseb on array key 
        foreach($product_size_title_sorted as $key => $size_title ){
          $size_title_key =  array_search($size_title['title'], array_column($product_size_array, 'title'));
          $product_sorted_size_tilte[] = $product_size_array[$size_title_key]; 
        }
        return $product_sorted_size_tilte;
    }
    
     #------------------------------------------------------------------
    
     function to_frac($number, $denominator = 16) {
        $x = floor($number * $denominator);
        return $x / $denominator;         
    }
}

