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
        $c->setUpdatedAt(new \DateTime('now'));        
        $this->save($c);
        return  $c;        
    }

//--------------------------Save ----------------------------------------------------------------

    public function save($entity) {       
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
        $entity->setSpecsJson($this->regenerate_json($entity));
        $entity->setUpdatedAt(new \DateTime('now'));
        $this->em->persist($entity);
        $this->em->flush();
        
            return array(
                'message' => 'The product specs for ' . $title . ' has been Updated!',
                'message_type' => 'success',
                'success' => true,
            );
    }
    private function regenerate_json($specs){
        $specs_data = json_decode($specs->getSpecsJson(),true);
        $fm = $this->container->get('productIntake.fit_model_measurement')->find($specs_data['fit_model_size']);
        $updated_measurements =  $this->calculateRanges($specs_data);  
        $sizes = $this->calculateWithFitModel($updated_measurements['sizes'], $fm);                  
        $specs_data['sizes']=$sizes;
        return json_encode($specs_data);
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
#----------------------    
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
    
    public function _calculateRanges($specs) {        
        $fpa = $this->getFitPointArray();
        $size_no = 1;
        $prev_size_key = null;
        $prev_garment_dimension = 0;
        $specs_updated=array();
        foreach ($specs['sizes'] as $size => $fit_points) {
            foreach ($fit_points as $fpk => $fpv) {
                $specs_updated['sizes'][$size][$fpk] = array('garment_dimension' => $fpv['garment_dimension'], 
                                                             'garment_stretch' => $fpv['garment_stretch'], 'min_calc' => 0, 'min_actual' => 0, 'ideal_low' => 0, 'fit_model' => 0, 'ideal_high' => 0, 'max_calc' => 0, 'max_actual' => 0, 'grade_rule' => 0);
                if ($prev_size_key) {
                    $grade_rule = $fpv['garment_dimension'] - $specs_updated['sizes'][$prev_size_key][$fpk]['garment_dimension'];                    
                    $stretch = $this->calculate_stretch($fpk, $fpa, $specs);
                    $specs_updated['sizes'][$size][$fpk]['garment_stretch'] = $stretch > 0 ? $stretch : $specs_updated['sizes'][$size][$fpk]['garment_stretch'];
                    $grade_rule = $grade_rule + (($grade_rule * $stretch) / 100);
                    
                    $specs_updated['sizes'][$size][$fpk]['grade_rule'] = $grade_rule;
                    if ($size_no == 2) {
                        $specs_updated['sizes'][$prev_size_key][$fpk]['grade_rule'] = $grade_rule;
                        $specs_updated['sizes'][$prev_size_key][$fpk]['garment_stretch'] = $stretch > 0 ? $stretch : $specs_updated['sizes'][$prev_size_key][$fpk]['garment_stretch'];
                    }                    
                }
                $prev_garment_dimension = $fpv['garment_dimension'];
            }
            $size_no = $size_no + 1;            
            $prev_size_key = $size;
        }
        return $specs_updated;
    }

    #------------------------
    private function calculate_stretch($fpk, $fpa, $specs) {
        if (array_key_exists($fpk, $fpa['x'])) {
            return $specs['horizontal_stretch'] > 0 ? $specs['horizontal_stretch'] : 0;
        } else {
            return $specs['vertical_stretch'] > 0 ? $specs['vertical_stretch'] : 0;
        }
    }
    #------------------------
    public function getFitPointArray(){
        $fp=array();
        foreach ($this->conf['fit_points'] as $fpk => $fpv) {
            #$fp[$fpv['axis']=='x'?'horizontal':'vertical'][$fpk]=$fpv['title'];
            $fp[$fpv['axis']][$fpk]=$fpv['title'];
        }
        return $fp;
    }
    #----------------------------------
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
    #-----------------------------------------------
    #-----------------------------------------------
    #-----------------------------------------------
    public function generate($specs, $stretch_enabled=false) {
        $specs_updated = $specs;
        $prev_size_key = null; 
        $size_no = 1;
        $fit_model_obj = $this->container->get('productIntake.fit_model_measurement')->find($specs['fit_model_size']);
        $fit_model_ratio = array();
        $fit_model_fit_points = json_decode($fit_model_obj->getMeasurementJson(), true);        
        $fpa = $this->getFitPointArray();
        
        foreach ($specs['sizes'][$fit_model_obj->getSize()] as $fit_point => $measure) {
            $fit_model_ratio[$fit_point] = ($measure['garment_dimension'] > 0 ) ? ($fit_model_fit_points[$fit_point] / $measure['garment_dimension']) : 0;            
        }
        
        foreach ($specs['sizes'] as $size => $fit_points) {
            foreach ($fit_points as $fpk => $fpv) {
                $us = array('garment_dimension' => $fpv['garment_dimension'], 'stretch_percentage' => 0, 'stretch_value' => 0, 'garment_stretch' => $fpv['garment_stretch'], 'grade_rule' => 0, 'grade_rule_stretch' => 0, 'min_calc' => 0, 'min_actual' => 0, 'ideal_low' => 0, 'fit_model' => 0, 'ideal_high' => 0, 'max_calc' => 0, 'max_actual' => 0);
                if ($prev_size_key) {#----------> for all the sizes after first                    
                    $us['fit_model'] =  array_key_exists($fpk, $fit_model_ratio)? number_format($fpv['garment_dimension'] * $fit_model_ratio[$fpk], 2, '.', ''):0;
                    $us['grade_rule'] = array_key_exists($fpk, $specs_updated['sizes'][$prev_size_key])? $fpv['garment_dimension'] - $specs_updated['sizes'][$prev_size_key][$fpk]['garment_dimension']:0;                                        
                    
                    #-------- stretch calculation                                    
                    $stretch_percentage = 0;
                    if (array_key_exists($fpk, $fpa['x'])) {                            
                            $stretch_percentage = $specs['horizontal_stretch'] > 0 ?$specs['horizontal_stretch']:0;                            
                    }else{
                        $stretch_percentage = $specs['vertical_stretch'] > 0 ?$specs['vertical_stretch']:0;
                    }
                    $us=$this->grade_rules($us, $stretch_percentage);
                }             
                #---------- Ranges calculations
                $specs_updated['sizes'][$size][$fpk] = $us;
            }
            $prev_size_key = $size;
            $size_no = $size_no + 1;
        }
        return $specs_updated;
    }
    private function grade_rules($us, $stretch_percentage=0){
                    $us['min_calc'] = $us['fit_model'] > 0 ? number_format($us['fit_model'] - (2.5 * $us['grade_rule']), 2, '.', '') : 0;
                    $us['min_actual'] = $us['min_calc'];
                    $us['ideal_low'] = $us['fit_model'] > 0 ? number_format($us['fit_model'] - (0.5 * $us['grade_rule']), 2, '.', ''):0;                    
                    $us['ideal_high'] = $us['fit_model'] > 0 ? number_format($us['fit_model'] + (0.5 * $us['grade_rule']), 2, '.', ''):0;
                    $us['max_calc']=$us['fit_model'] > 0 ? number_format($us['fit_model'] + (2.5 * $us['grade_rule']), 2, '.', ''):0;
                    $us['max_actual']=$us['max_calc'];                    
                    return $us;
    }
    
    
}
