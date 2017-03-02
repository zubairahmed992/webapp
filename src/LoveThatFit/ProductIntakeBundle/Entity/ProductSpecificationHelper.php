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
    /*#----------------------    

 private function regenerate_json($specs){
        $specs_data = json_decode($specs->getSpecsJson(),true);
        $fm = $this->container->get('productIntake.fit_model_measurement')->find($specs_data['fit_model_size']);
        $updated_measurements =  $this->calculateRanges($specs_data);  
        $sizes = $this->calculateWithFitModel($updated_measurements['sizes'], $fm);                  
        $specs_data['sizes']=$sizes;
        return json_encode($specs_data);
    }
     #------------------------------------------
    public function calculateWithFitModel($sizes, $fit_model){
        $fit_model_ratio = array();
        $fit_model_fit_points = json_decode($fit_model->getMeasurementJson(), true);
        $updated_sizes=array();

        foreach ($sizes[$fit_model->getSize()] as $fit_point => $measure) {
            $fit_model_ratio[$fit_point] = ($measure['garment_dimension'] > 0 ) ? ($fit_model_fit_points[$fit_point] / $measure['garment_dimension']) : 0;            
        }
        
        foreach ($sizes as $size => $fit_points) {
            foreach ($fit_points as $fpk => $fpv) {
                $us = array('garment_dimension' => $fpv['garment_dimension'], 'stretch_value' => 0, 
                                                             'garment_stretch' => $fpv['garment_stretch'], 'grade_rule' => 0, 'grade_rule_stretch' => 0, 'min_calc' => 0, 'min_actual' => 0, 'ideal_low' => 0, 'fit_model' => 0, 'ideal_high' => 0, 'max_calc' => 0, 'max_actual' => 0);
                $fit_model =  array_key_exists($fpk, $fit_model_ratio)? $fpv['garment_dimension'] * $fit_model_ratio[$fpk]:0;

                $grade_rule = array_key_exists('grade_rule_stretch', $sizes[$size][$fpk])?$sizes[$size][$fpk]['grade_rule_stretch']:$sizes[$size][$fpk]['grade_rule'];
                #------------------------------------------------
                $us['garment_dimension']=$fpv['garment_dimension'];
                #$us['stretch']=$fpv['stretch'];
                $us['stretch_value']=$fpv['stretch_value'];
                $us['garment_stretch']=$fpv['garment_stretch'];
                $us['grade_rule']=$sizes[$size][$fpk]['grade_rule'];                
                $us['grade_rule_stretch']=$sizes[$size][$fpk]['grade_rule_stretch'];                
                
                $us['min_calc'] = $fit_model > 0 ? number_format($fit_model - (2.5 * $grade_rule), 2, '.', '') : 0;
                $us['min_actual'] = $us['min_calc'];
                $us['ideal_low'] = $fit_model > 0 ? number_format($fit_model - ($grade_rule/2), 2, '.', ''):0;
                $us['fit_model']=number_format($fit_model, 2, '.', '');
                $us['ideal_high'] = $fit_model > 0 ? number_format($fit_model + ($grade_rule/2), 2, '.', ''):0;
                $us['max_calc']=$fit_model > 0 ? number_format($fit_model + (2.5 * $grade_rule), 2, '.', ''):0;
                $us['max_actual']=$us['max_calc'];
                
                #------------------------------------------------                
                if(array_key_exists('grade_rule_stretched', $sizes[$size][$fpk])){
                    $grade_rule_stretched = $sizes[$size][$fpk]['grade_rule_stretched'];
                    $us['max_calc'] = $fit_model > 0 ? number_format($fit_model + (2.5 * $grade_rule_stretched), 2, '.', '') : 0;
                    $us['ideal_high'] = $fit_model > 0 ? number_format($fit_model + ($grade_rule_stretched/2), 2, '.', '') : 0;
                    $us['max_actual'] =  $us['max_calc'];                    
                    $us['min_calc'] = $fit_model > 0 ? number_format($fit_model - (2.5 * $grade_rule_stretched), 2, '.', '') : 0;
                    $us['ideal_low'] = $fit_model > 0 ? number_format($fit_model - ($grade_rule_stretched/2), 2, '.', '') : 0;
                    $us['min_actual'] =  $us['min_calc'];
                }
                $updated_sizes[$size][$fpk]=$us;
            }
        }
        return $updated_sizes;     
    }
    #----------------------    
    public function calculateRanges($specs) {        
        $fpa = $this->getFitPointArray();
        $size_no = 1;
        $prev_size_key = null;        
        $specs_updated=array();
        
        
        foreach ($specs['sizes'] as $size => $fit_points) {
            foreach ($fit_points as $fpk => $fpv) {                
                $us = array('garment_dimension' => $fpv['garment_dimension'], 'stretch_value' => 0, 
                                                             'garment_stretch' => $fpv['garment_stretch'], 'grade_rule' => 0, 'grade_rule_stretch' => 0, 'min_calc' => 0, 'min_actual' => 0, 'ideal_low' => 0, 'fit_model' => 0, 'ideal_high' => 0, 'max_calc' => 0, 'max_actual' => 0);
                if ($prev_size_key) {                    
                    
                    $grade_rule = array_key_exists($fpk, $specs_updated['sizes'][$prev_size_key])? $fpv['garment_dimension'] - $specs_updated['sizes'][$prev_size_key][$fpk]['garment_dimension']:0;                    
                    $us['grade_rule'] = $grade_rule;

                    #----------------                    
                    if (array_key_exists($fpk, $fpa['x'])) {
                        if($specs['horizontal_stretch'] > 0 ){                            
                            $us['stretch_value'] = ($specs['horizontal_stretch'] / 100) * $fpv['garment_dimension'];
                            $us['grade_rule_stretch'] = $grade_rule + (($grade_rule * $specs['horizontal_stretch']) / 100);
                            #$us['stretch'] = $specs['horizontal_stretch'];                            
                        }
                    } else {
                        if($specs['vertical_stretch'] > 0 ){                            
                            $us['stretch_value'] = ($specs['vertical_stretch'] / 100) * $fpv['garment_dimension'];
                            $us['grade_rule_stretch'] = $grade_rule + (($grade_rule * $specs['vertical_stretch']) / 100);
                           # $us['stretch'] = $specs['vertical_stretch'];
                        }                        
                    }
                    $us['garment_stretch'] = $fpv['garment_dimension'] + $us['stretch_value'];
                    if ($size_no == 2) {
                        $specs_updated['sizes'][$prev_size_key][$fpk]['grade_rule'] = $grade_rule;
                        $specs_updated['sizes'][$prev_size_key][$fpk]['grade_rule_stretch'] = $us['grade_rule_stretch'];
                        $specs_updated['sizes'][$prev_size_key][$fpk]['garment_stretch'] = $us['garment_stretch'];
                        $specs_updated['sizes'][$prev_size_key][$fpk]['stretch_value'] = $us['stretch_value'];
                        #$specs_updated['sizes'][$prev_size_key][$fpk]['stretch'] = $us['stretch'];                        
                    }                    
                }                
                $specs_updated['sizes'][$size][$fpk] = $us;
            }
            $size_no = $size_no + 1;            
            $prev_size_key = $size;
        }
        return $specs_updated;
    }
     * 
     * 
     */
    
    #------------------------
    public function getFitPointArray(){
        $fp=array();
        foreach ($this->conf['fit_points'] as $fpk => $fpv) {            
            $fp[$fpv['axis']][$fpk]=$fpv['title'];
        }
        return $fp;
    }
    /*#----------------------------------
    public function calculateGradeRule($specs) {
        $fpa = $this->getFitPointArray();
        $prev_size_key = null;
        $size_no = 0;
        foreach ($specs['sizes'] as $size_key => $fit_points) {
            $size_no = $size_no + 1;
            foreach ($fit_points as $fit_point => $ranges) {
                if ($prev_size_key) {
                    $grade_rule = $specs['sizes'][$size_key][$fit_point]['garment_dimension'] - $specs['sizes'][$prev_size_key][$fit_point]['garment_dimension'];
                    if (array_key_exists($fit_point, $fpa['x'])) {
                        if ($specs['horizontal_stretch'] > 0) {
                            $grade_rule = $grade_rule + (($grade_rule * $specs['horizontal_stretch']) / 100);                            
                            $specs['sizes'][$size_key][$fit_point]['grade_rule_stretched'] = $grade_rule;
                            if ($size_no == 2) {                                    
                                $specs['sizes'][$prev_size_key][$fit_point]['grade_rule_stretched'] = $grade_rule;                                    
                            }                            
                        }
                    } else {
                        if ($specs['vertical_stretch'] > 0) {
                            $grade_rule = $grade_rule + (($grade_rule * $specs['vertical_stretch']) / 100);
                            $specs['sizes'][$size_key][$fit_point]['grade_rule_stretched'] = $grade_rule;
                            if ($size_no == 2) {                                    
                                $specs['sizes'][$prev_size_key][$fit_point]['grade_rule_stretched'] = $grade_rule;                                    
                            }                            
                        }
                    }
                }
            }
            $prev_size_key = $size_key;
        }
        return $specs;
    }    
     * 
     */
    #-----------------------------------------------
    #-----------------------------------------------
    #-----------------------------------------------
    
      public function _generate($specs) {
        $specs_updated = $specs;
        $prev_size_key = null; 
        $size_no = 1;
        $fit_model_obj = $this->container->get('productIntake.fit_model_measurement')->find($specs['fit_model_size']);
        $fit_model_ratio = array();
        $fit_model_fit_points = json_decode($fit_model_obj->getMeasurementJson(), true);        
        $fpa = $this->getFitPointArray();
        $bust_m = array();;
        #--------- calculate grade rule
        foreach ($specs['sizes'] as $size => $fit_points) {
            foreach ($fit_points as $fpk => $fpv) 
                if($fpk=='bust')
                {
                $us = array('garment_dimension' => $fpv['garment_dimension'], 'stretch_percentage' => 0, 'garment_stretch' => 0, 'grade_rule' => 0, 'grade_rule_stretch' => 0, 'min_calc' => 0, 'min_actual' => 0, 'ideal_low' => 0, 'fit_model' => 0, 'ideal_high' => 0, 'max_calc' => 0, 'max_actual' => 0);

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
                if ($prev_size_key) {#----------> for all the sizes after first                    
                    $us['grade_rule'] = array_key_exists($fpk, $specs_updated['sizes'][$prev_size_key])? $fpv['garment_dimension'] - $specs_updated['sizes'][$prev_size_key][$fpk]['garment_dimension']:0;                                         
                    $us['grade_rule_stretch'] = $us['grade_rule'] + ($us['grade_rule'] * $us['stretch_percentage']/100);                        
                }   
                if ($size_no==2){ #----------> for first size only
                    $specs_updated['sizes'][$prev_size_key][$fpk]['grade_rule'] = $us['grade_rule'];    
                    $specs_updated['sizes'][$prev_size_key][$fpk]['grade_rule_stretch'] =$us['grade_rule_stretch'];                    
                    $bust_m[$prev_size_key][$fpk]['grade_rule'] = $us['grade_rule'];   
                    $bust_m[$prev_size_key][$fpk]['grade_rule_stretch'] =$us['grade_rule_stretch'];   
                }
                #--------------------------
                $bust_m[$size][$fpk]=$us;
            }
            $prev_size_key = $size;
            $size_no = $size_no + 1;
        }
        $specs_updated['sizes']=$bust_m;
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
       #return $specs_updated['sizes'][$fit_model_obj->getSize()]['bust'];
        #---------------------------------> calculate ranges

        foreach ($specs_updated['sizes'] as $size => $fit_points) {
            foreach ($fit_points as $fpk => $fpv) {
                                    
                    $specs_updated['sizes'][$size][$fpk]['ratio'] = $fit_model_ratio[$fpk]['fit_model'];
                    $specs_updated['sizes'][$size][$fpk]['fit_model'] =$fpv['garment_stretch'] *  $fit_model_ratio[$fpk]['fit_model'];                    

                
                
                    unset($specs_updated['sizes'][$size][$fpk]['min_actual']);
                    unset($specs_updated['sizes'][$size][$fpk]['min_calc']);
                    unset($specs_updated['sizes'][$size][$fpk]['ideal_low']);
                    unset($specs_updated['sizes'][$size][$fpk]['ideal_high']);
                    unset($specs_updated['sizes'][$size][$fpk]['max_calc']);
                    unset($specs_updated['sizes'][$size][$fpk]['max_actual']);
                    unset($specs_updated['sizes'][$size][$fpk]['stretch_percentage']);
            }
        }
        return $specs_updated['sizes'];
    
        
        
    }
    #?????????????????????????????
    public function generate($specs) {
        $specs_updated = $specs;
        $prev_size_key = null; 
        $size_no = 1;
        $fit_model_obj = $this->container->get('productIntake.fit_model_measurement')->find($specs['fit_model_size']);
        $fit_model_ratio = array();
        $fit_model_fit_points = json_decode($fit_model_obj->getMeasurementJson(), true);        
        $fpa = $this->getFitPointArray();
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
                if ($prev_size_key) {#----------> for all the sizes after first                    
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
        #return $specs_updated;
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
   
}
