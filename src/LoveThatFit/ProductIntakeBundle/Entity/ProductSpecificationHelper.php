<?php

namespace LoveThatFit\ProductIntakeBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;

class ProductSpecificationHelper {

    protected $conf;
    protected $dispatcher;

    /**
     * @var EntityManager 
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repo;

    /**
     * @var string
     */
    protected $class;

     private $container;

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class,Container $container) {
         $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
        $conf_yml = new Parser();
        $this->conf = $conf_yml->parse(file_get_contents('../src/LoveThatFit/ProductIntakeBundle/Resources/config/specs_config.yml'));
    }
    //-------------------------Create --------------------------------------------   
public function getNew() {
        $class = $this->class;
        $c = new $class();
        $c->setCreatedAt(new \DateTime('now'));        
        return  $c;        
    }
    
    public function createNew($title, $desc, $json) {
        $class = $this->class;
        $c = new $class();
        $c->setTitle($title);
        $c->setDescription($desc);
        $c->setSpecsJson($json);
        $c->setCreatedAt(new \DateTime('now'));                   
        $this->save($c);
        return  $c;        
    }

//--------------------------Save ----------------------------------------------------------------

    public function save($entity) {       
        $entity->setUpdatedAt(new \DateTime('now'));        
        $this->em->persist($entity);
        $this->em->flush();        
    }

    
//------------------------------------------------------

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
   
//----------------------Find ProductSpecifications By ID----------------------------------------------------------------

    public function find($id) {
        return $this->repo->find($id);
    }
   #--------------------Find All ProductSpecifications---------------------------------------------------------------------------------
  public function findAll(){
  return $this->repo->findAll();      
    }
#----------------------Find By title----------------------------------------------------------------
    public function findOneByTitle($title) {
        return $this->repo->findOneByTitle($title);
    }
    
    #------------------------
    public function getFitPointArray(){
        $fp=array();
        foreach ($this->conf['fit_points'] as $fpk => $fpv) {            
            $fp[$fpv['axis']][$fpk]=$fpv['title'];
        }
        return $fp;
    }
  
    #-----------------------------------------------
    #-----------------------------------------------
    #-----------------------------------------------
    
    public function generate($specs) {
        $specs_updated = $specs;
        $prev_size_key = null; 
        $size_no = 1;
        $fit_model_obj = $this->container->get('productIntake.fit_model_measurement')->find($specs['fit_model_size']);
        $fit_model_ratio = array();
        $fit_model_fit_points = json_decode($fit_model_obj->getMeasurementJson(), true);        
        $fpa = $this->getFitPointArray();
        !array_key_exists('fit_point_stretch', $specs)?$specs['fit_point_stretch']=array():'';
        #--------- calculate grade rule
        foreach ($specs['sizes'] as $size => $fit_points) {
            foreach ($fit_points as $fpk => $fpv) {
                $us = array('garment_dimension' => $fpv['garment_dimension'], 'stretch_percentage' => 0, 'stretch_value' => 0, 'garment_stretch' => 0, 'grade_rule' => 0, 'grade_rule_stretch' => 0, 'min_calc' => 0, 'min_actual' => 0, 'ideal_low' => 0, 'fit_model' => 0, 'ideal_high' => 0, 'max_calc' => 0, 'max_actual' => 0);

                #---------------> stretch calculation
                if(array_key_exists($fpk, $specs['fit_point_stretch']) && $specs['fit_point_stretch'][$fpk]>0){ #--------> for individual fit point
                    $us['stretch_percentage']=$specs['fit_point_stretch'][$fpk];
                }else{ #--------> for over all horiz/vertical stretch
                    if(array_key_exists($fpk, $fpa['x']) && $specs['horizontal_stretch'] > 0){
                        $us['stretch_percentage'] = $specs['horizontal_stretch'];                    
                    }elseif(array_key_exists($fpk, $fpa['y']) && $specs['vertical_stretch'] > 0){
                        $us['stretch_percentage'] = $specs['vertical_stretch'] ;
                    }
                }
                    $us['garment_stretch'] = $us['garment_dimension'] + ($us['garment_dimension'] * $us['stretch_percentage']/100);

                #-----------> grade rule
                if (!is_null($prev_size_key)) {#----------> for all the sizes after first                    
                    $us['grade_rule'] = array_key_exists($fpk, $specs_updated['sizes'][$prev_size_key])? $fpv['garment_dimension'] - $specs_updated['sizes'][$prev_size_key][$fpk]['garment_dimension']:0;                                         
                    $us['grade_rule_stretch'] = $us['grade_rule'] + ($us['grade_rule'] * $us['stretch_percentage']/100);    
                }   
                if ($size_no==2){ #----------> for first size only
                    $specs_updated['sizes'][$prev_size_key][$fpk]['grade_rule'] = $us['grade_rule'];    
                    $specs_updated['sizes'][$prev_size_key][$fpk]['grade_rule_stretch'] =$us['grade_rule_stretch'];
                    #$specs_updated['sizes'][$prev_size_key][$fpk]['garment_stretch'] = $us['garment_dimension'] + ($us['garment_dimension'] * $us['stretch_percentage']/100);
                }
                #--------------------------                
                $specs_updated['sizes'][$size][$fpk] = $us;
                
            }
            $prev_size_key = $size;
            $size_no = $size_no + 1;
        }
        
        #--------- calculate fit model ratio        
        foreach ($specs_updated['sizes'][$fit_model_obj->getSize()] as $fit_point => $measure) {                    
            $grade_rule = $specs_updated['sizes'][$fit_model_obj->getSize()][$fit_point]['grade_rule_stretch'];                    
            $specs_updated['sizes'][$fit_model_obj->getSize()][$fit_point]['min_calc'] = $fit_model_fit_points[$fit_point] > 0 ? $fit_model_fit_points[$fit_point] - (2.5 * $grade_rule) : 0;
            $specs_updated['sizes'][$fit_model_obj->getSize()][$fit_point]['min_actual'] = $specs_updated['sizes'][$fit_model_obj->getSize()][$fit_point]['min_calc'];
            $specs_updated['sizes'][$fit_model_obj->getSize()][$fit_point]['ideal_low'] = $fit_model_fit_points[$fit_point] - (0.5 * $grade_rule);                    
            $specs_updated['sizes'][$fit_model_obj->getSize()][$fit_point]['fit_model'] = $fit_model_fit_points[$fit_point];
            $specs_updated['sizes'][$fit_model_obj->getSize()][$fit_point]['ideal_high'] = $fit_model_fit_points[$fit_point] + (0.5 * $grade_rule);                    
            $specs_updated['sizes'][$fit_model_obj->getSize()][$fit_point]['max_calc'] = $fit_model_fit_points[$fit_point] > 0 ? $fit_model_fit_points[$fit_point] + (2.5 * $grade_rule) : 0;
            $specs_updated['sizes'][$fit_model_obj->getSize()][$fit_point]['max_actual'] = $specs_updated['sizes'][$fit_model_obj->getSize()][$fit_point]['max_calc'];

            #---------------> Calculate ratios
            $fit_model_ratio[$fit_point]['fit_model'] = ($measure['garment_stretch'] > 0 ) ? ($fit_model_fit_points[$fit_point] / $measure['garment_stretch']) : 0;            
            $fit_model_ratio[$fit_point]['min_calc'] = ($fit_model_fit_points[$fit_point] > 0 ) ? ($specs_updated['sizes'][$fit_model_obj->getSize()][$fit_point]['min_calc'] / $fit_model_fit_points[$fit_point]) : 0;            
            $fit_model_ratio[$fit_point]['ideal_low'] =($fit_model_fit_points[$fit_point] > 0 ) ? ($specs_updated['sizes'][$fit_model_obj->getSize()][$fit_point]['ideal_low'] / $fit_model_fit_points[$fit_point]) : 0;                        
            $fit_model_ratio[$fit_point]['ideal_high'] = ($fit_model_fit_points[$fit_point] > 0 ) ? ($specs_updated['sizes'][$fit_model_obj->getSize()][$fit_point]['ideal_high'] / $fit_model_fit_points[$fit_point]) : 0;            
            $fit_model_ratio[$fit_point]['max_calc'] = ($fit_model_fit_points[$fit_point] > 0 ) ? ($specs_updated['sizes'][$fit_model_obj->getSize()][$fit_point]['max_calc'] / $fit_model_fit_points[$fit_point]) : 0;            
        }
        
        #---------------------------------> calculate ranges

        foreach ($specs_updated['sizes'] as $size => $fit_points) {
            foreach ($fit_points as $fpk => $fpv) {
                if ($size != $fit_model_obj->getSize()) {
                    $fit_model_measurement = array_key_exists($fpk, $fit_model_ratio)?$fpv['garment_stretch'] * $fit_model_ratio[$fpk]['fit_model']:0;
                    $specs_updated['sizes'][$size][$fpk]['fit_model'] = $fit_model_measurement;
                    $specs_updated['sizes'][$size][$fpk]['min_calc'] = array_key_exists($fpk, $fit_model_ratio)?$fit_model_measurement * $fit_model_ratio[$fpk]['min_calc']:0;
                    $specs_updated['sizes'][$size][$fpk]['min_actual'] =$specs_updated['sizes'][$size][$fpk]['min_calc'] ;
                    $specs_updated['sizes'][$size][$fpk]['ideal_low'] = array_key_exists($fpk, $fit_model_ratio)?$fit_model_measurement * $fit_model_ratio[$fpk]['ideal_low']:0;
                    $specs_updated['sizes'][$size][$fpk]['ideal_high'] = array_key_exists($fpk, $fit_model_ratio)? $fit_model_measurement * $fit_model_ratio[$fpk]['ideal_high']:0;
                    $specs_updated['sizes'][$size][$fpk]['max_calc'] = array_key_exists($fpk, $fit_model_ratio)?$fit_model_measurement * $fit_model_ratio[$fpk]['max_calc']:0;
                    $specs_updated['sizes'][$size][$fpk]['max_actual'] =$specs_updated['sizes'][$size][$fpk]['max_calc'] ;
                }
            }
        }
        return $specs_updated;        
    }  
    

    ######################################################################################
    ##################################### Fit Model Dynamic Calculations #################
    #####################################################################################
    
    #-------------------> Calculate Fit Model ratio
    private function calculate_fit_model_ratio($specs){        
        if(!array_key_exists('fit_model_size', $specs))
            return null;
        $fit_model_obj = $this->container->get('productIntake.fit_model_measurement')->find($specs['fit_model_size']);
        $fit_model_ratio = array();
        $fit_model_fit_points = json_decode($fit_model_obj->getMeasurementJson(), true);
                #--------- calculate fit model ratio        
        foreach ($specs['sizes'][$fit_model_obj->getSize()] as $fit_point => $measure) {                    
            #grade rule stretch value -------------
            $grade_rule = $specs['sizes'][$fit_model_obj->getSize()][$fit_point]['grade_rule'];   
            $grade_rule = $grade_rule + ($grade_rule * ($specs['sizes'][$fit_model_obj->getSize()][$fit_point]['stretch_percentage']/100));
            #fit model measurement ---------------------
            $specs['sizes'][$fit_model_obj->getSize()][$fit_point]['min_calc'] = $fit_model_fit_points[$fit_point] > 0 ? $fit_model_fit_points[$fit_point] - (2.5 * $grade_rule) : 0;
            $specs['sizes'][$fit_model_obj->getSize()][$fit_point]['min_actual'] = $specs['sizes'][$fit_model_obj->getSize()][$fit_point]['min_calc'];
            $specs['sizes'][$fit_model_obj->getSize()][$fit_point]['ideal_low'] = $fit_model_fit_points[$fit_point] - (0.5 * $grade_rule);                    
            $specs['sizes'][$fit_model_obj->getSize()][$fit_point]['fit_model'] = $fit_model_fit_points[$fit_point];
            $specs['sizes'][$fit_model_obj->getSize()][$fit_point]['ideal_high'] = $fit_model_fit_points[$fit_point] + (0.5 * $grade_rule);                    
            $specs['sizes'][$fit_model_obj->getSize()][$fit_point]['max_calc'] = $fit_model_fit_points[$fit_point] > 0 ? $fit_model_fit_points[$fit_point] + (2.5 * $grade_rule) : 0;
            $specs['sizes'][$fit_model_obj->getSize()][$fit_point]['max_actual'] = $specs['sizes'][$fit_model_obj->getSize()][$fit_point]['max_calc'];

            #---------------> Calculate ratios
            $fit_model_ratio[$fit_point]['fit_model'] = ($measure['garment_stretch'] > 0 ) ? ($fit_model_fit_points[$fit_point] / $measure['garment_stretch']) : 0;            
            $fit_model_ratio[$fit_point]['min_calc'] = ($fit_model_fit_points[$fit_point] > 0 ) ? ($specs['sizes'][$fit_model_obj->getSize()][$fit_point]['min_calc'] / $fit_model_fit_points[$fit_point]) : 0;            
            $fit_model_ratio[$fit_point]['ideal_low'] =($fit_model_fit_points[$fit_point] > 0 ) ? ($specs['sizes'][$fit_model_obj->getSize()][$fit_point]['ideal_low'] / $fit_model_fit_points[$fit_point]) : 0;                        
            $fit_model_ratio[$fit_point]['ideal_high'] = ($fit_model_fit_points[$fit_point] > 0 ) ? ($specs['sizes'][$fit_model_obj->getSize()][$fit_point]['ideal_high'] / $fit_model_fit_points[$fit_point]) : 0;            
            $fit_model_ratio[$fit_point]['max_calc'] = ($fit_model_fit_points[$fit_point] > 0 ) ? ($specs['sizes'][$fit_model_obj->getSize()][$fit_point]['max_calc'] / $fit_model_fit_points[$fit_point]) : 0;            
        }
        return $fit_model_ratio;
    }
    #-------------------> Dynamic calculations 
    public function dynamicCalculations($decoded){
        $specs_obj = $this->find($decoded['pk']);    
        $specs = json_decode($specs_obj->getSpecsJson(),true);
        
        if ($decoded['name']=='horizontal_stretch' || $decoded['name']=='vertical_stretch'){            
            $specs[$decoded['name']] = $decoded['value']; 
            $specs = $this->generate_specs_for_stretch($specs, $decoded['name']); 
        }elseif(strpos($decoded['name'],'fit_point_stretch' ) !== false){
            $fit_point_stretch_array = explode('-', $decoded['name']);            
            $specs['fit_point_stretch'][$fit_point_stretch_array[1]] = $decoded['value'];
            $specs = $this->generate_specs_for_fit_point_stretch($specs, $fit_point_stretch_array[1]);
        }elseif(strpos($decoded['name'],'actual' ) !== false){            
            $specs = $this->generate_specs_for_actual($specs, $decoded['name'], $decoded['value']);
        }elseif(strpos($decoded['name'],'grade_rule' ) !== false){            
            $specs = $this->generate_specs_for_grade_rule($specs, $decoded['name'], $decoded['value']);
        }elseif(strpos($decoded['name'],'garment_dimension' ) !== false){            
            $specs = $this->generate_specs_for_garment_dimension($specs, $decoded['name'], $decoded['value']);
        }elseif(strpos($decoded['name'],'fit_model_size' ) !== false){            
            $specs = $this->generate_specs_for_fit_model_size($specs, $decoded['value']);
        }else{
              return array(
                'message' => 'Nothing to update!',
                'message_type' => 'error',
                'success' => true,
            );
        }
       
        $str = explode('-', $decoded['name']);
        $target_fp = $str[2];
       
        $this->strip_to_fitpoint($specs,$target_fp);
        $specs_obj->setUndoSpecsJson($specs_obj->getSpecsJson());
        $specs_obj->setSpecsJson(json_encode($specs));
        return $this->update($specs_obj);          
    }
    #------------------->1 Overall Stretch
    private function generate_specs_for_stretch($specs, $stretch_type) {
        $specs_updated = $specs;
        $prev_size_key = null;
        $size_no = 1;
        
        $axis=$stretch_type=='horizontal_stretch'?'x':'y';
        
        $fpa = $this->getFitPointArray();
        #--------- calculate grade rule
        foreach ($specs['sizes'] as $size => $fit_points) {
            foreach ($fit_points as $fpk => $fpv) {
                if (array_key_exists($fpk, $specs['fit_point_stretch']) && !$specs['fit_point_stretch'][$fpk] > 0) {
                    #---------------> stretch calculation
                    if (array_key_exists($fpk, $fpa[$axis]) && $specs[$stretch_type] > 0) {#--------> for over all horiz stretch
                        $us = array('garment_dimension' => $fpv['garment_dimension'], 'stretch_percentage' => 0, 'stretch_value' => 0, 'garment_stretch' => 0, 'grade_rule' => 0, 'grade_rule_stretch' => 0, 'min_calc' => 0, 'min_actual' => 0, 'ideal_low' => 0, 'fit_model' => 0, 'ideal_high' => 0, 'max_calc' => 0, 'max_actual' => 0);
                        $us['stretch_percentage'] = $specs[$stretch_type];
                        $us['garment_stretch'] = $us['garment_dimension'] + ($us['garment_dimension'] * $us['stretch_percentage'] / 100);
                        #-----------> grade rule
                        if (!is_null($prev_size_key)) {#----------> for all the sizes after first                    
                            $us['grade_rule'] = array_key_exists($fpk, $specs_updated['sizes'][$prev_size_key]) ? $fpv['garment_dimension'] - $specs_updated['sizes'][$prev_size_key][$fpk]['garment_dimension'] : 0;
                            $us['grade_rule_stretch'] = $us['grade_rule'] + ($us['grade_rule'] * $us['stretch_percentage']/100);
                        }
                        if ($size_no == 2) { #----------> for first size only
                            $specs_updated['sizes'][$prev_size_key][$fpk]['grade_rule'] = $us['grade_rule'];
                            $specs_updated['sizes'][$prev_size_key][$fpk]['grade_rule_stretch'] = $us['grade_rule_stretch'];
                        }
                        #--------------------------                
                        $specs_updated['sizes'][$size][$fpk] = $us;
                    }
                }
            }
            $prev_size_key = $size;
            $size_no = $size_no + 1;
        }
        $fit_model_ratio = $this->calculate_fit_model_ratio($specs);

        #---------------------------------> calculate ranges
        foreach ($specs_updated['sizes'] as $size => $fit_points) {
            foreach ($fit_points as $fpk => $fpv) {
                if (array_key_exists($fpk, $fpa[$axis]) && $specs[$stretch_type] > 0) {
                    $fit_model_measurement = array_key_exists($fpk, $fit_model_ratio) ? $fpv['garment_stretch'] * $fit_model_ratio[$fpk]['fit_model'] : 0;
                    $specs_updated['sizes'][$size][$fpk]['fit_model'] = $fit_model_measurement;
                    $specs_updated['sizes'][$size][$fpk]['min_calc'] = array_key_exists($fpk, $fit_model_ratio) ? $fit_model_measurement * $fit_model_ratio[$fpk]['min_calc'] : 0;
                    $specs_updated['sizes'][$size][$fpk]['min_actual'] = $specs_updated['sizes'][$size][$fpk]['min_calc'];
                    $specs_updated['sizes'][$size][$fpk]['ideal_low'] = array_key_exists($fpk, $fit_model_ratio) ? $fit_model_measurement * $fit_model_ratio[$fpk]['ideal_low'] : 0;
                    $specs_updated['sizes'][$size][$fpk]['ideal_high'] = array_key_exists($fpk, $fit_model_ratio) ? $fit_model_measurement * $fit_model_ratio[$fpk]['ideal_high'] : 0;
                    $specs_updated['sizes'][$size][$fpk]['max_calc'] = array_key_exists($fpk, $fit_model_ratio) ? $fit_model_measurement * $fit_model_ratio[$fpk]['max_calc'] : 0;
                    $specs_updated['sizes'][$size][$fpk]['max_actual'] = $specs_updated['sizes'][$size][$fpk]['max_calc'];
                }
            }
        }
        return $specs_updated;
    }
    #------------------->2 Fitpoint Stretch
    private function generate_specs_for_fit_point_stretch($specs, $fp_target) {
        $specs_updated = $specs;
        $prev_size_key = null;
        $size_no = 1;
        
        #--------- calculate grade rule
        foreach ($specs['sizes'] as $size => $fit_points) {
            $us = array('garment_dimension' => $fit_points[$fp_target]['garment_dimension'], 'stretch_percentage' => 0, 'stretch_value' => 0, 'garment_stretch' => 0, 'grade_rule' => 0, 'grade_rule_stretch' => 0, 'min_calc' => 0, 'min_actual' => 0, 'ideal_low' => 0, 'fit_model' => 0, 'ideal_high' => 0, 'max_calc' => 0, 'max_actual' => 0);
            $us['stretch_percentage'] = $specs['fit_point_stretch'][$fp_target];
            $us['garment_stretch'] = $us['garment_dimension'] + ($us['garment_dimension'] * $us['stretch_percentage'] / 100);            
            #-----------> grade rule
            if (!is_null($prev_size_key)) {#----------> for all the sizes after first                    
                $us['grade_rule'] = $us['garment_dimension'] - $specs_updated['sizes'][$prev_size_key][$fp_target]['garment_dimension'];                        
                $us['grade_rule_stretch'] = $us['grade_rule'] + ($us['grade_rule'] * $us['stretch_percentage']/100);
            }
            if ($size_no == 2) { #----------> for first size only
                $specs_updated['sizes'][$prev_size_key][$fp_target]['grade_rule'] = $us['grade_rule'];                        
                $specs_updated['sizes'][$prev_size_key][$fp_target]['grade_rule_stretch'] = $us['grade_rule_stretch'];
            }
            #--------------------------                
            $specs_updated['sizes'][$size][$fp_target] = $us;                    
            
            $prev_size_key = $size;
            $size_no = $size_no + 1;
        }
        $fit_model_ratio = $this->calculate_fit_model_ratio($specs);

        #---------------------------------> calculate ranges
        foreach ($specs_updated['sizes'] as $size => $fit_points) {
            $fit_model_measurement = $fit_points[$fp_target]['garment_stretch'] * $fit_model_ratio[$fp_target]['fit_model'];
            $specs_updated['sizes'][$size][$fp_target]['fit_model'] = $fit_model_measurement;
            $specs_updated['sizes'][$size][$fp_target]['min_calc'] = array_key_exists($fp_target, $fit_model_ratio) ? $fit_model_measurement * $fit_model_ratio[$fp_target]['min_calc'] : 0;
            $specs_updated['sizes'][$size][$fp_target]['min_actual'] = $specs_updated['sizes'][$size][$fp_target]['min_calc'];
            $specs_updated['sizes'][$size][$fp_target]['ideal_low'] = array_key_exists($fp_target, $fit_model_ratio) ? $fit_model_measurement * $fit_model_ratio[$fp_target]['ideal_low'] : 0;
            $specs_updated['sizes'][$size][$fp_target]['ideal_high'] = array_key_exists($fp_target, $fit_model_ratio) ? $fit_model_measurement * $fit_model_ratio[$fp_target]['ideal_high'] : 0;
            $specs_updated['sizes'][$size][$fp_target]['max_calc'] = array_key_exists($fp_target, $fit_model_ratio) ? $fit_model_measurement * $fit_model_ratio[$fp_target]['max_calc'] : 0;
            $specs_updated['sizes'][$size][$fp_target]['max_actual'] = $specs_updated['sizes'][$size][$fp_target]['max_calc'];
        }
        return $specs_updated;
    }     
    #------------------->3 Actual Max/Min 
    private function generate_specs_for_actual($specs, $target, $value) {        
        $str=explode('-', $target);
        #calculate ratio sizes-6-bust-min_actual
        $fp=$str[2];
        $fm_size=$str[1];
        $attrib=$str[3];
        
        $specs['sizes'][$fm_size][$fp][$attrib] = $value;
        $ratio = $value/$specs['sizes'][$fm_size][$fp]['fit_model'];
                
        #--------- calculate grade rule
        foreach ($specs['sizes'] as $size => $fit_points) {            
            $specs['sizes'][$size][$fp][$attrib] = $ratio * $fit_points[$fp]['fit_model'];                                    
        }
        return $specs;
    }    
    #------------------->4 Grade Rule
    private function generate_specs_for_grade_rule($specs, $target, $value) { 
        $str = explode('-', $target);     
        $target_size = $str[1];
        $target_fp = $str[2];

       $fit_model_obj = $this->container->get('productIntake.fit_model_measurement')->find($specs['fit_model_size']);
        $size_keys = array_keys($specs['sizes']);               
        $pointer=-1;     
        foreach ($size_keys as $size_title) {            
            if($size_title==$fit_model_obj->getSize()){
                $pointer=0;                
            }else{
                $pointer=$pointer==0?1:$pointer;
            }            
            if ($size_title==$target_size){             
                   break;
            }
        }
        
        switch ($pointer) {
            case -1:
                    $specs=$this->generate_specs_for_grade_rule_minus($specs, $target, $value);        
                break;
            case 0:
                   $specs= $this->generate_specs_for_grade_rule_minus($specs, $target, $value);        
                    $specs= $this->generate_specs_for_grade_rule_plus($specs, $target, $value);
                break;
            case 1:
                    $specs=$this->generate_specs_for_grade_rule_plus($specs, $target, $value);        
                
                break;
        }

        
        #return $this->strip_to_fitpoint($specs, $target_fp);
        
        return $specs;
    }
    #------------------->4a Grade Rule Smaller than fit model size
    private function generate_specs_for_grade_rule_minus($specs, $target, $value) {
        $str = explode('-', $target);        #calculate ratio sizes-6-bust-grade_rule
        $target_fp = $str[2];
        $target_size = $str[1];
        $target_attrib = $str[3];        
        #-------------> if size is before or after fit model size
        #$fit_model_obj = $this->container->get('productIntake.fit_model_measurement')->find($specs['fit_model_size']);
        $fit_model_ratio = $this->calculate_fit_model_ratio($specs);
        
        $size_keys = array_keys($specs['sizes']);               
        #-------------> if size is before the fit model size
        $size_keys = array_reverse($size_keys);
        $target_pointer = false;
        $prev_size_title = null;
                
        foreach ($size_keys as $size) {                        
            if ($size==$target_size){             
                $target_pointer = true;
                $specs['sizes'][$size][$target_fp]['grade_rule'] = $value;
                $specs['sizes'][$size][$target_fp]['grade_rule_stretch'] = $specs['sizes'][$size][$target_fp]['grade_rule'] + ($specs['sizes'][$size][$target_fp]['grade_rule'] * $specs['sizes'][$size][$target_fp]['stretch_percentage']/100);
            }else{
                if($target_pointer == true){
                    $specs['sizes'][$size][$target_fp]['garment_dimension'] = $specs['sizes'][$prev_size_title][$target_fp]['garment_dimension'] - $specs['sizes'][$prev_size_title][$target_fp]['grade_rule'];
                    $specs['sizes'][$size][$target_fp]['garment_stretch'] = $specs['sizes'][$size][$target_fp]['garment_dimension'] + ($specs['sizes'][$size][$target_fp]['garment_dimension'] * $specs['sizes'][$size][$target_fp]['stretch_percentage'] / 100);
                    #~~~~~~> require to do related calculations for ranges
                    $fit_model_measurement = $specs['sizes'][$size][$target_fp]['garment_stretch'] * $fit_model_ratio[$target_fp]['fit_model'];
                    $specs['sizes'][$size][$target_fp]['fit_model'] = $fit_model_measurement;
                    $specs['sizes'][$size][$target_fp]['min_calc'] = array_key_exists($target_fp, $fit_model_ratio) ? $fit_model_measurement * $fit_model_ratio[$target_fp]['min_calc'] : 0;
                    $specs['sizes'][$size][$target_fp]['ideal_low'] = array_key_exists($target_fp, $fit_model_ratio) ? $fit_model_measurement * $fit_model_ratio[$target_fp]['ideal_low'] : 0;
                    $specs['sizes'][$size][$target_fp]['ideal_high'] = array_key_exists($target_fp, $fit_model_ratio) ? $fit_model_measurement * $fit_model_ratio[$target_fp]['ideal_high'] : 0;
                    $specs['sizes'][$size][$target_fp]['max_calc'] = array_key_exists($target_fp, $fit_model_ratio) ? $fit_model_measurement * $fit_model_ratio[$target_fp]['max_calc'] : 0;                    
                }
            }
            $prev_size_title = $size;
        }
        return $specs;        
    }
    #------------------->4b Grade Rule Greater than fit model size
    private function generate_specs_for_grade_rule_plus($specs, $target, $value) {
        $str = explode('-', $target);        #calculate ratio sizes-6-bust-grade_rule
        $target_fp = $str[2];
        $target_size = $str[1];
        $target_attrib = $str[3];        
        
        $fit_model_ratio = $this->calculate_fit_model_ratio($specs);
        
        $size_keys = array_keys($specs['sizes']);               
        #-------------> if size is after the fit model size        
        $target_pointer = false;
        $prev_size_title = null;
                
        foreach ($size_keys as $size) {                        
            if ($size==$target_size){             
                $target_pointer = true;
                $specs['sizes'][$size][$target_fp]['grade_rule'] = $value;
                $specs['sizes'][$size][$target_fp]['grade_rule_stretch'] = $specs['sizes'][$size][$target_fp]['grade_rule'] + ($specs['sizes'][$size][$target_fp]['grade_rule'] * $specs['sizes'][$size][$target_fp]['stretch_percentage']/100);
            }else{
                if($target_pointer == true){
                    $specs['sizes'][$size][$target_fp]['garment_dimension'] = $specs['sizes'][$prev_size_title][$target_fp]['garment_dimension'] + $specs['sizes'][$prev_size_title][$target_fp]['grade_rule'];
                    $specs['sizes'][$size][$target_fp]['garment_stretch'] = $specs['sizes'][$size][$target_fp]['garment_dimension'] + ($specs['sizes'][$size][$target_fp]['garment_dimension'] * $specs['sizes'][$size][$target_fp]['stretch_percentage'] / 100);
                    #~~~~~~> require to do related calculations for ranges
                    $fit_model_measurement = $specs['sizes'][$size][$target_fp]['garment_stretch'] * $fit_model_ratio[$target_fp]['fit_model'];
                    $specs['sizes'][$size][$target_fp]['fit_model'] = $fit_model_measurement;
                    $specs['sizes'][$size][$target_fp]['min_calc'] = array_key_exists($target_fp, $fit_model_ratio) ? $fit_model_measurement * $fit_model_ratio[$target_fp]['min_calc'] : 0;
                    $specs['sizes'][$size][$target_fp]['ideal_low'] = array_key_exists($target_fp, $fit_model_ratio) ? $fit_model_measurement * $fit_model_ratio[$target_fp]['ideal_low'] : 0;
                    $specs['sizes'][$size][$target_fp]['ideal_high'] = array_key_exists($target_fp, $fit_model_ratio) ? $fit_model_measurement * $fit_model_ratio[$target_fp]['ideal_high'] : 0;
                    $specs['sizes'][$size][$target_fp]['max_calc'] = array_key_exists($target_fp, $fit_model_ratio) ? $fit_model_measurement * $fit_model_ratio[$target_fp]['max_calc'] : 0;                    
                }
            }
            $prev_size_title = $size;
        }
        return $specs;        
    }
    #--------------------------------------
    private function strip_to_fitpoint($specs, $fp){
        $striped=array();
        foreach ($specs['sizes'] as $size => $fit_points) {
            $striped[$size][$fp]=$fit_points[$fp]['garment_dimension'];
        }
        return $striped;
    }
    #------------------->5 Garment Dimension
    public function generate_specs_for_garment_dimension($specs, $target, $value) {
        $str = explode('-', $target);        #sizes-6-bust-garment_dimension
        $target_fp = $str[2];
        $target_size = $str[1];
        $target_attrib = $str[3]; 

        $fmtfp=$specs['sizes'][$target_size][$target_fp];
        $specs['sizes'][$target_size][$target_fp]['garment_dimension']=$value;
        $specs['sizes'][$target_size][$target_fp]['garment_stretch'] = $fmtfp['garment_dimension'] + ($fmtfp['garment_dimension'] * $fmtfp['stretch_percentage']/100);
        
        #--------- calculate grade rule
        $size_keys = array_keys($specs['sizes']);
        $fit_model_ratio = $this->calculate_fit_model_ratio($specs);
        #-------------> if size is after the fit model size        
        $target_pointer = false;
        $prev_size_title = null;
                
        foreach ($size_keys as $size) {                        
            if ($size==$target_size){             
                $target_pointer = true;
            }else{
                if($target_pointer == true){
                    $specs['sizes'][$size][$target_fp]['garment_dimension'] = $specs['sizes'][$prev_size_title][$target_fp]['garment_dimension'] + $specs['sizes'][$size][$target_fp]['grade_rule'];
                    $specs['sizes'][$size][$target_fp]['garment_stretch'] = $this->get_garment_stretch($specs['sizes'][$size][$target_fp]);
                    #~~~~~~> require to do related calculations for ranges
                    $specs['sizes'][$size][$target_fp] = $this->calculate_ranges($specs['sizes'][$size][$target_fp], $fit_model_ratio[$target_fp]);
                    }
            }
            $prev_size_title = $size;
        }
        

        #-------------> if size is before the fit model size
        $size_keys = array_reverse($size_keys);
        $target_pointer = false;

        foreach ($size_keys as $size) {                        
            if ($size==$target_size){             
                $target_pointer = true;
            }else{
                if($target_pointer == true){
                    $specs['sizes'][$size][$target_fp]['garment_dimension'] = $specs['sizes'][$prev_size_title][$target_fp]['garment_dimension'] - $specs['sizes'][$prev_size_title][$target_fp]['grade_rule'];
                    $specs['sizes'][$size][$target_fp]['garment_stretch'] = $this->get_garment_stretch($specs['sizes'][$size][$target_fp]);
                    #~~~~~~> require to do related calculations for ranges
                    $specs['sizes'][$size][$target_fp] = $this->calculate_ranges($specs['sizes'][$size][$target_fp], $fit_model_ratio[$target_fp]);
                    }
            }
            $prev_size_title = $size;
        }
        
       #$specs= $this->strip_to_fitpoint($specs,$target_fp);
        return $specs;
        
    }     
    private function get_garment_stretch($fp){
                    return $fp['garment_dimension'] + ($fp['garment_dimension'] * $fp['stretch_percentage'] / 100);
    }
    private function calculate_ranges($fp_specs, $ratio){
        $fit_model_measurement = $fp_specs['garment_stretch'] * $ratio['fit_model'];
        $fp_specs['fit_model'] = $fit_model_measurement;
        $fp_specs['min_calc'] =  $fit_model_measurement * $ratio['min_calc'];
        $fp_specs['ideal_low'] =  $fit_model_measurement * $ratio['ideal_low'];
        $fp_specs['ideal_high'] =  $fit_model_measurement * $ratio['ideal_high'];
        $fp_specs['max_calc'] =  $fit_model_measurement * $ratio['max_calc']; 
        return $fp_specs;
    }
    #------------------->6 Fit Model Size
    private function generate_specs_for_fit_model_size($specs, $fit_model_size_id) {
        return $specs;
        }
    
    ########################################################################
    ############################## Product Creation ##########################################
    ########################################################################
    
    public function create_product($id){
        $specs = $this->find($id);
        $data = json_decode($specs->getSpecsJson(),true);
        $clothing_type = $this->get('admin.helper.clothingtype')->findOneByGenderNameCSV(strtolower($data['gender']), strtolower($data['clothing_type']));
        $brand = $this->get('admin.helper.brand')->findOneByName($data['brand']);
        $product=new Product;
        $product->setBrand($brand);
        $product->setClothingType($clothing_type);        
        $product->setName(array_key_exists('name', $data)?$data['name']:'');
        $product->setName(array_key_exists('control_number', $data)?$data['control_number']:'');        
        $product->setDescription(array_key_exists('description', $data)?$data['description']:'');
        $product->setStretchType(array_key_exists('stretch_type', $data)?$data['stretch_type']:'');
        $product->setHorizontalStretch($data['horizontal_stretch']);
        $product->setVerticalStretch($data['vertical_stretch']);        
        $product->setCreatedAt(new \DateTime('now'));
        $product->setUpdatedAt(new \DateTime('now'));
        $product->setGender($data['gender']);
        $product->setStylingType($data['styling_type']);
        $product->setNeckline(array_key_exists('neck_line', $data)?$data['neck_line']:$data['neckline']);
        $product->setSleeveStyling($data['sleeve_styling']);
        $product->setRise($data['rise']);
        $product->setHemLength($data['hem_length']);
        $product->setFabricWeight($data['fabric_weight']);
        $product->setStructuralDetail($data['structural_detail']);
        $product->setFitType($data['fit_type']);
        $product->setLayering(array_key_exists('layring', $data)?$data['layring']:$data['layering']);
        $product->setFitPriority(json_encode($data['fit_priority']));
        $product->setFabricContent(json_encode(array_key_exists('fabric_content', $data)?$data['fabric_content']:''));
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
        $size_titles = $this->get('admin.helper.size')->getSizeArray($data['gender'],$data['size_title_type']);        
        $i=1;
        foreach ($size_titles['regular'] as $size_title => $value) {
            if(array_key_exists($size_title, $data['sizes'])){
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
            if($key!='key'){
            $psm = new ProductSizeMeasurement;
            $psm->setTitle($key);
            $psm->setProductSize($size);            
            array_key_exists('garment_dimension',$value)?$psm->setGarmentMeasurementFlat($value['garment_dimension']):null;
            array_key_exists('garment_stretch',$value)?$psm->setGarmentMeasurementStretchFit($value['garment_stretch']):null;
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
        $color_names=explode(",", $data['colors']);        
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
}
