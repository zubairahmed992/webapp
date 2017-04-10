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
    }

    #---------------------------------   

    public function getNew() {
        $class = $this->class;
        $c = new $class();
        $c->setCreatedAt(new \DateTime('now'));
        return $c;
    }

    #---------------------------------   

    public function createNew($title, $desc, $json) {
        $class = $this->class;
        $c = new $class();
        $c->setTitle($title);
        $c->setDescription($desc);
        $c->setSpecsJson($json);
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
        $entity->fill($parsed);
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

    ######################################################################################
    ##################################### Fit Model Dynamic Calculations #################
    #####################################################################################
    
    #-------------------> Dynamic calculations     
    public function dynamicCalculations($decoded) {
        $specs_obj = $this->find($decoded['pk']);
        $specs = json_decode($specs_obj->getSpecsJson(), true);
        #-----------------------------        
        if (!array_key_exists('fit_point_stretch', $specs)) {
            $specs['fit_point_stretch'] = $specs_obj->getFitPointStretchArray();
        }#-----------------------------
        if ($decoded['name'] == 'horizontal_stretch' || $decoded['name'] == 'vertical_stretch') {
            $specs[$decoded['name']] = $decoded['value'];
            $specs = $this->generate_specs_for_stretch($specs, $decoded['name']); #~~~~~~~~>1
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
        } else {
            return array(
                'message' => 'Nothing to update!',
                'message_type' => 'error',
                'success' => true,
            );
        }        
        $specs_obj->setUndoSpecsJson($specs_obj->getSpecsJson());
        $specs_obj->setSpecsJson(json_encode($specs));
        return $this->update($specs_obj);
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

        $specs['sizes'][$fm_size][$fp][$attrib] = $value;
        $ratio = $value / $specs['sizes'][$fm_size][$fp]['fit_model'];

        #--------- calculate grade rule
        foreach ($specs['sizes'] as $size => $fit_points) {
            $specs['sizes'][$size][$fp][$attrib] = $ratio * $fit_points[$fp]['fit_model'];
        }
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
        #---> reverse order if size is smaller than fit model size
        $size_keys = $pointer < 0 ? array_reverse(array_keys($specs['sizes'])) : array_keys($specs['sizes']);
        $target_pointer = false;
        $prev_size_title = null;
        #---> grade rule for fit model size
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
        $specs = $this->compute_grade_rule($specs, $fit_model_obj);
        $specs = $this->compute_stretch($specs);
        #------------- compute ranges for all sizes
        return $this->compute_all_ranges($specs, $fit_model_obj);
    }

    ###################################################################


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
            $grade_rule = $specs['sizes'][$fit_model_obj->getSize()][$fit_point]['grade_rule_stretch'];
            $fit_model_ratio['fit_model_measurement'][$fit_point]['min_calc'] = $fit_model_fit_points[$fit_point] > 0 ? $fit_model_fit_points[$fit_point] - (2.5 * $grade_rule) : 0;
            $fit_model_ratio['fit_model_measurement'][$fit_point]['ideal_low'] = $fit_model_fit_points[$fit_point] - (0.5 * $grade_rule);
            $fit_model_ratio['fit_model_measurement'][$fit_point]['fit_model'] = $fit_model_fit_points[$fit_point];
            $fit_model_ratio['fit_model_measurement'][$fit_point]['ideal_high'] = $fit_model_fit_points[$fit_point] + (0.5 * $grade_rule);
            $fit_model_ratio['fit_model_measurement'][$fit_point]['max_calc'] = $fit_model_fit_points[$fit_point] > 0 ? $fit_model_fit_points[$fit_point] + (2.5 * $grade_rule) : 0;
            #---------------> Calculate ratios
            $fit_model_ratio[$fit_point]['fit_model'] = ($measure['garment_stretch'] > 0 ) ? ($fit_model_fit_points[$fit_point] / $measure['garment_stretch']) : 0;
            $fit_model_ratio[$fit_point]['min_calc'] = ($fit_model_ratio['fit_model_measurement'][$fit_point]['min_calc'] / $fit_model_fit_points[$fit_point]);
            $fit_model_ratio[$fit_point]['ideal_low'] = ($fit_model_ratio['fit_model_measurement'][$fit_point]['ideal_low'] / $fit_model_fit_points[$fit_point]);
            $fit_model_ratio[$fit_point]['ideal_high'] = ($fit_model_ratio['fit_model_measurement'][$fit_point]['ideal_high'] / $fit_model_fit_points[$fit_point]);
            $fit_model_ratio[$fit_point]['max_calc'] = ($fit_model_ratio['fit_model_measurement'][$fit_point]['max_calc'] / $fit_model_fit_points[$fit_point]);
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
            $specs['sizes'][$s][$fp]['fit_model'];
            $specs['sizes'][$s][$fp]['grade_rule'] = $avg_grade_rule;
            $specs['sizes'][$s][$fp]['grade_rule_stretch'] = $avg_grade_rule + ($avg_grade_rule * $specs['sizes'][$s][$fp]['stretch_percentage'] / 100);
            $specs['sizes'][$s][$fp]['min_calc'] = $specs['sizes'][$s][$fp]['fit_model'] - (2.5 * $avg_grade_rule);
            $specs['sizes'][$s][$fp]['ideal_low'] = $specs['sizes'][$s][$fp]['fit_model'] - (0.5 * $avg_grade_rule);
            $specs['sizes'][$s][$fp]['ideal_high'] = $specs['sizes'][$s][$fp]['fit_model'] + (0.5 * $avg_grade_rule);
            $specs['sizes'][$s][$fp]['max_calc'] = $specs['sizes'][$s][$fp]['fit_model'] + (2.5 * $avg_grade_rule);
        }
        return $specs['sizes'][$s][$fp];
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
            if (array_key_exists('fit_model_size', $specs)) {
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
                if ($size != $fit_model_obj->getSize()) {#---> exclude measurement for fit model size
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
        $fp_specs['min_actual'] = $fp_specs['min_actual'] > 0 ? $fp_specs['min_actual'] : $fp_specs['min_calc'];
        $fp_specs['max_actual'] = $fp_specs['max_actual'] > 0 ? $fp_specs['max_actual'] : $fp_specs['max_calc'];
        return $fp_specs;
    }

    ########################################################################
    ############################## Product Creation ########################
    ########################################################################

    public function create_product($id) {
        $specs = $this->find($id);
        $data = json_decode($specs->getSpecsJson(), true);
        $clothing_type = $this->container->get("admin.helper.clothingtype")->findOneByGenderNameCSV(strtolower($data['gender']), strtolower($data['clothing_type']));
        $brand = $this->container->get('admin.helper.brand')->findOneByName($data['brand']);
        $product = new Product;
        $product->setBrand($brand);
        $product->setClothingType($clothing_type);
        $product->setName(array_key_exists('name', $data) ? $data['name'] : '');
        $product->setName(array_key_exists('control_number', $data) ? $data['control_number'] : '');
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
        $product->setLayering(array_key_exists('layring', $data) ? $data['layring'] : $data['layering']);
        $product->setFitPriority(array_key_exists('fit_priority', $data) ? json_encode($data['fit_priority']) : 'NULL' );
        $product->setFabricContent(json_encode(array_key_exists('fabric_content', $data) ? $data['fabric_content'] : ''));
        $product->setDisabled(false);
        $product->setSizeTitleType($data['size_title_type']);
        #------------------------
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();
        $this->create_product_sizes($product, $data);
        $this->create_product_colors($data, $product);
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
                $ps->setBodyType($data['body_type']);
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
        foreach ($csv_files as $k => $v) {
            $csv_file = $this->find($v->getId());
            $csv_file_path[$v->getId()] = $csv_file->getWebPath();
        }
        return $csv_file_path;
    }

}

