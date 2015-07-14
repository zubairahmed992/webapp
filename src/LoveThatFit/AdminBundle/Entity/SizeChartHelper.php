<?php
namespace LoveThatFit\AdminBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;


class SizeChartHelper{

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

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class,Container $container)
    {  
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }
 //------------------------------------------------------    
 public function createNew() {
        $class = $this->class;
        $size_chart = new $class();
        return $size_chart;
    }    
//-------------------------------------------------------
 public function save($entity) {
        $this->em->persist($entity);
        $this->em->flush();
        return array('message' => 'Size Chart for size  ' . $entity->getTitle() . ' has been saved.',
            'field' => 'all',
            'message_type' => 'success',
            'success' => true,
        );
    }

//-------------------------------------------------------
public function delete($id) {
        $entity = $this->repo->find($id);
        if ($entity) {
            $entity_name = $entity->getTitle();
            $this->em->remove($entity);
            $this->em->flush();            
            return array('sizechart' => $entity,
                'message' => 'The Size Chart ' . $entity_name . ' has been Deleted!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {
            return array('sizechart' => $entity,
                'message' => 'Sizechart not found!',
                'message_type' => 'warning',
                'success' => false,
            );
        }
    }    
#----------------------------------------------------------
public function fillInRequest($data,$new_size_chart=null) {
        
       if($new_size_chart==Null){
        $new_size_chart = $this->createNew();
        }
        $brand = $this->container->get('admin.helper.brand')->find($data['Brand']);        
        $new_size_chart->setBrand($brand);
        $new_size_chart->setGender($data['gender']);
        $new_size_chart->setTarget($data['target']);
        $new_size_chart->setBodytype($data['bodytype']);
        $new_size_chart->setSizeTitleType($data['size_title_type']);
        $new_size_chart->setTitle($data['title']);
        $new_size_chart->setNeck($data['neck']);
        $new_size_chart->setShoulderAcrossBack($data['shoulder_across_back']);
        $new_size_chart->setBust($data['bust']);
        $new_size_chart->setSleeve($data['sleeve']);
        $new_size_chart->setWaist($data['waist']);
        $new_size_chart->setHip($data['hip']);
        $new_size_chart->setThigh($data['thigh']);
        $new_size_chart->setOutseam($data['outseam']);
        $new_size_chart->setInseam($data['inseam']);
        $new_size_chart->setChest($data['chest']);
        $new_size_chart->setDisabled(array_key_exists('disabled', $data));
        return $new_size_chart;
    }

#----------------------------------------------------------   
public function find($id){
    return $this->repo->find($id);
}
#----------------------------------------------------------
public function findWithSpecs($id) {
        $entity = $this->repo->find($id);
        if (!$entity) {
            $entity = $this->createNew();
            return array(
                'entity' => $entity,
                'message' => 'Size Chart not found.',
                'message_type' => 'warning',
                'success' => false,
            );
        } else {
            return array(
                'entity' => $entity,
                'message' => 'Chart found!',
                'message_type' => 'success',
                'success' => true,
            );
        }
    }  
#----------------------------------------------------------
public function findOneById($id){
    return $this->repo->findOneById($id);    
}
#-------------------------------------------------------------------------
public function findOneByName($title) {
        return $this->repo->findOneByName($title);
    }


#-------------------------Evaluate Size Chart ------------------------------------------------------------------------#

    public function calculateMeasurements($entity, $request_array) {
        $measurement = $this->setMeasurementSizes($entity, $request_array);
        return $this->evaluateWithSizeChart($measurement);
    }
#-------------------------------------------------------------------------

    public function setMeasurementSizes($entity, $request_array) {
        
        $measurement = $entity->getMeasurement();
        
        if (array_key_exists('top_size', $request_array)) {
            $measurement->top_size = $request_array['top_size'];
        }
        
        if (array_key_exists('bottom_size', $request_array)) {
            $measurement->bottom_size = $request_array['bottom_size'];
        }        
        
        if ($entity->getGender() == 'f' && array_key_exists('dress_size', $request_array)) {
            $measurement->dress_size = $request_array['dress_size'];
        }

        return $measurement;
    }
   
//------------------------------------------------------------------------------------    
    public function measurementFromSizeCharts($measurement) {

        if (is_null($measurement)) {
            return;
        }
        $sc_measurements = array();
        #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>
        # priority is given to the measurement extracted from bra size
        $bra_size_spec = $this->container->get('admin.helper.size')->getWomanBraSpecs($measurement->getBrasize());
        if ($bra_size_spec != null) {
            $sc_measurements['shoulder_across_back'] = $bra_size_spec['shoulder_across_back'];
            $sc_measurements['bust'] = $bra_size_spec['average'];
        }
        #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>
        $top_size = $measurement->getTopFittingSizeChart();
        if ($top_size) {
            if ($top_size) {
                $sc_measurements['neck'] = ($top_size->getNeck() && $top_size->getNeck() > 0) ? $top_size->getNeck() : 0;
                $sc_measurements['chest'] = ($top_size->getChest() && $top_size->getChest() > 0) ? $top_size->getChest() : 0;
                $sc_measurements['sleeve'] = $this->eval_null($top_size->getSleeve());
                if (!array_key_exists('bust', $sc_measurements)) {
                    $sc_measurements['bust'] = ($top_size->getBust() && $top_size->getBust() > 0) ? $top_size->getBust() : 0;
                }
                if (!array_key_exists('shoulder_across_back', $sc_measurements)) {
                    $sc_measurements['shoulder_across_back'] = ($top_size->getShoulderAcrossBack() && $top_size->getShoulderAcrossBack() > 0) ? $top_size->getShoulderAcrossBack() : 0;
                }
            }
        }
        #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>
        $bottom_size = $measurement->getBottomFittingSizeChart();

        if ($bottom_size) {
            if ($bottom_size) {
                $sc_measurements['waist'] = ($bottom_size->getWaist() && $bottom_size->getWaist() > 0) ? $bottom_size->getWaist() : 0;
                $sc_measurements['hip'] = ($bottom_size->getHip() && $bottom_size->getHip() > 0) ? $bottom_size->getHip() : 0;
                $sc_measurements['inseam'] = ($bottom_size->getInseam() && $bottom_size->getInseam() > 0) ? $bottom_size->getInseam() : 0;
            }
        }
        #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>
        $dress_size = $measurement->getDressFittingSizeChart();

        if ($dress_size) {
            if ($dress_size) {
                if ($dress_size->getBust() && $dress_size->getBust() > 0) {
                    if (!array_key_exists('bust', $sc_measurements)) {#if measurement not calculated before
                        $sc_measurements['bust'] = $dress_size->getBust();
                    } else {
                        if ($bra_size_spec == null) { #if measurement did not extracted from bra size
                            $sc_measurements['bust'] = ($sc_measurements['bust'] + $dress_size->getBust()) / 2;
                        }
                    }
                }
                if ($dress_size->getShoulderAcrossBack() && $dress_size->getShoulderAcrossBack() > 0) {
                    if (!array_key_exists('shoulder_across_back', $sc_measurements)) {#if measurement not calculated before
                        $sc_measurements['shoulder_across_back'] = $dress_size->getShoulderAcrossBack();
                    } else {
                        if ($bra_size_spec == null) {#if measurement did not extracted from bra size
                            $sc_measurements['shoulder_across_back'] = ($sc_measurements['shoulder_across_back'] + $dress_size->getShoulderAcrossBack()) / 2;
                        }
                    }
                }
                if (array_key_exists('hip', $sc_measurements)) {
                    if ($dress_size->getHip() && $dress_size->getHip() > 0) {
                        $sc_measurements['hip'] = ($sc_measurements['hip'] + $dress_size->getHip()) / 2;
                    }
                } else {
                    $sc_measurements['hip'] = ($dress_size->getHip() && $dress_size->getHip() > 0) ? $dress_size->getHip() : 0;
                }
            }
        }
        #Neck & sleeve measurements for Men required to be evaluated
        return $sc_measurements;
    }

    private function eval_null($val){
        return ($val && $val > 0) ? $val : 0;
    }
//-------------------------------------------------------------------------------------
    public function evaluateWithSizeChart($measurement) {
        
        if (is_null($measurement)) {
            return;
        }

        $bust_size = 0;
        $hip_size = 0;
        
        if ($measurement->top_size) {

            $top_size = $this->repo->findOneById($measurement->top_size);

            $measurement->setTopFittingSizeChart($top_size); // set the selected size chart to the measurement table to have association

            if ($top_size) {

                if ($measurement->getNeck() == null || $measurement->getNeck() == 0) {
                    $measurement->setNeck($top_size->getNeck());
                }
                if ($measurement->getBust() == null || $measurement->getBust() == 0) {
                    $measurement->setBust($top_size->getBust());
                    $bust_size = $top_size->getBust();
                }
                if ($measurement->getChest() == null || $measurement->getChest() == 0) {
                    $measurement->setChest($top_size->getChest());
                }
                if ($measurement->getSleeve() == null || $measurement->getSleeve() == 0) {
                    $measurement->setSleeve($top_size->getSleeve());
                }
                if ($measurement->getShoulderAcrossBack() == null || $measurement->getShoulderAcrossBack() == 0) {
                    $measurement->setShoulderAcrossBack($top_size->getShoulderAcrossBack());
                }
            }
        }

        if ($measurement->bottom_size) {

            $bottom_size = $this->repo->findOneById($measurement->bottom_size);

            $measurement->setBottomFittingSizeChart($bottom_size); // set the selected size chart to the measurement table to have association

            if ($bottom_size) {
                if ($measurement->getWaist() == null || $measurement->getWaist() == 0) {
                    $measurement->setWaist($bottom_size->getWaist());
                    $measurement->setBelt($bottom_size->getWaist());
                }
                if ($measurement->getHip() == null || $measurement->getHip() == 0) {
                    $measurement->setHip($bottom_size->getHip());
                    $hip_size = $bottom_size->getHip();
                }
                if ($measurement->getInseam() == null || $measurement->getInseam() == 0) {
                    $measurement->setInseam($bottom_size->getInseam());
                }
                
            }
        }

        if ($measurement->dress_size) {

            $dress_size = $this->repo->findOneById($measurement->dress_size);

            $measurement->setDressFittingSizeChart($dress_size); // set the selected size chart to the measurement table to have association

            if ($dress_size) {

                if ($measurement->getBust() == null || $measurement->getBust() == 0) {
                    $measurement->setBust($dress_size->getBust());
                } else {
                    // If user already selected a brand & size for Top take average value
                    if ($bust_size > 0 && $dress_size->getBust() > 0) {
                        $measurement->setBust(($bust_size + $dress_size->getBust()) / 2);
                    } else {//this condition will not be called as per current condition/ refactor                        
                        $measurement->setBust($dress_size->getBust());
                    }
                }

                if ($measurement->getHip() == null || $measurement->getHip() == 0) {
                    $measurement->setHip($dress_size->getHip());
                } else {
                    // If user already selected a brand & size for bottom/pant
                    if ($hip_size > 0 && $dress_size->getHip() > 0) {
                        $dress_size->getHip(($hip_size + $dress_size->getHip()) / 2);
                    } else {//this condition will not be called as per current condition/ refactor                                                
                        $measurement->setHip($dress_size->getHip());
                    }
                }

                if ($measurement->getShoulderAcrossBack() == null || $measurement->getShoulderAcrossBack() == 0) {
                    $measurement->setShoulderAcrossBack($dress_size->getShoulderAcrossBack());
                }
            }
        }
        
        
        // Temporary hack for the back just to have the slider in step 4 in proper place if back not provided
        // As currently we are not comparing back measurement in fitting algorithm
        if ($measurement->getShoulderAcrossBack() == null || $measurement->getShoulderAcrossBack() == 0) {
            
            
            #---Get from Size Helper---------
          $genrealMeasurements=$this->container->get('admin.helper.general_measurements')->getMeasurementByNeck($measurement->getNeck());
            if($genrealMeasurements){
                 $measurement->setShoulderAcrossBack($genrealMeasurements['shoulder_across_back']);
            }else{
                $measurement->setShoulderAcrossBack(14.5); 
            }
          
           }
                $braSize=$measurement->getBraSize();
                $findAverage=$this->container->get('admin.helper.size')->getBustAverage($braSize);
                
                if($findAverage){
                    $measurement->setBust($findAverage);
                }
                    
        return $measurement;
    }
    
    #---------------------------------------------No Usage in search, refactor please
    /* 
    public function fooSizeChartMeasure($entity, $request_array) {
        
        $measurement = $entity->getMeasurement();
        
        if (array_key_exists('top_size', $request_array)) {
            $measurement->top_size = $request_array['top_size'];
            $top_size = $this->repo->findOneById($measurement->top_size);            
             if ($top_size) {
                $measurement->setTopFittingSizeChart($top_size);
                $measurement->setNeck($top_size->getNeck());
                $measurement->setBust($top_size->getBust());
                $measurement->setChest($top_size->getChest());
                $measurement->setSleeve($top_size->getSleeve());
                $measurement->setShoulderAcrossBack($top_size->getShoulderAcrossBack());
             }             
        }        
        if (array_key_exists('bottom_size', $request_array)) {
            $measurement->bottom_size = $request_array['bottom_size'];
            $bottom_size = $this->repo->findOneById($measurement->bottom_size);
            if ($bottom_size) {
                $measurement->setBottomFittingSizeChart($bottom_size); 
                $measurement->setWaist($bottom_size->getWaist());
                $measurement->setBelt($bottom_size->getWaist());
                $measurement->setHip($bottom_size->getHip());
                $measurement->setInseam($bottom_size->getInseam());                
            }
        }        
        if ($entity->getGender() == 'f' && array_key_exists('dress_size', $request_array)) {
            $measurement->dress_size = $request_array['dress_size'];
            $dress_size = $this->repo->findOneById($measurement->dress_size);
            if ($dress_size) {
                $measurement->setDressFittingSizeChart($dress_size); 
                $measurement->setBust($dress_size->getBust());
                $measurement->setHip($dress_size->getHip());
                $measurement->setShoulderAcrossBack($dress_size->getShoulderAcrossBack());
            }
        }
        return $measurement;
    }
    

*/
    //------------------------------------------------------------------------

    public function getBrandArray($target) {

        $brands = $this->repo->getBrandsByTarget($target);
        $brands_array = array();
        foreach ($brands as $i) {
            $brands_array[$i['id']] = $i['name'];
        }
        return $brands_array;
    }
#---------------------------------------------------------------------------------------------------------------------#
#-------------------------Web Service for size chart for registration step two-------------------------------------------------#
public function sizeChartList($request_array)
{
     
        $gender=$request_array['gender'];
        $bodytype=$request_array['body_type'];
        $target_top=$request_array['target_top'];
        $top_size=$request_array['top_size'];
        $target_bottom=$request_array['target_bottom']; 
        $bottom_size=$request_array['bottom_size'];    
        $target_dress=$request_array['target_dress'];    
        $dress_size=$request_array['dress_size'];     
       
         $neck=0;
         $bust=0;
         $chest=0;
         $waist=0;
         $sleeve=0;
         $inseam=0;
         $hip=0;
         $top_id=0;
         $bottom_id=0;
         $dress_id=0;
         $measurement= array();   
         if($target_top)
         {   
          $sizechart_top = $this->repo->getSizeChartByBrandGenderBodyTypeTopSize($gender,$bodytype,$target_top,$top_size);
          if($sizechart_top){
          $bust=$sizechart_top[0]['top_bust'];
          $neck=$sizechart_top[0]['top_neck'];
          $chest=$sizechart_top[0]['top_chest'];
          $waist=$sizechart_top[0]['top_waist'];
          $top_id=$sizechart_top[0]['size_chart_id'];
         }
          
         }
         if($target_bottom)
         {     
         $sizechart_bottom = $this->repo->getSizeChartByBrandGenderBodyTypeBottomSize($gender,$bodytype,$target_bottom,$bottom_size);
                if($sizechart_bottom)
                { 
                $waist=$sizechart_bottom[0]['bottom_waist'];
                $hip=$sizechart_bottom[0]['bottom_hip'];
                $inseam=$sizechart_bottom[0]['bottom_inseam'];
                $bottom_id=$sizechart_bottom[0]['size_chart_id'];
                }
         }
         if($target_dress)
         {     
         $size_chart_dress = $this->repo->getSizeChartByBrandGenderBodyTypeDressSize($gender, $bodytype, $target_dress, $dress_size);
            
            $bust_dress=0;
            $waist_dress=0;
            $hip_dress=0;
            $sleeve_dress=0;
            if($size_chart_dress){
                
            $bust_dress = $size_chart_dress[0]['dress_bust'];
            $waist_dress = $size_chart_dress[0]['dress_waist'];
            $hip_dress = $size_chart_dress[0]['dress_hip'];
            $sleeve_dress = $size_chart_dress[0]['dress_sleeve'];
            $dress_id=$size_chart_dress[0]['size_chart_id'];
            
            }
            
            #-----------------BUST AVERAGE-----------------------------------------------#
            if ($bust_dress == 0 && $bust > 0) {
                $bust = $bust;
            }
            if ($bust == 0 && $bust_dress) {
                $bust = $bust_dress;
            }
            if ($bust_dress > 0 && $bust > 0) {
                $bust = ($bust_dress + $bust) / 2;
            }

            #----------------- WAIST AVERAGE-----------------------------------------------#
            if ($waist_dress == 0 && $waist > 0) {
                $waist = $waist;
            }
            if ($waist_dress > 0 && $waist == 0) {
                $waist = $waist_dress;
            }
            if ($waist_dress > 0 && $waist > 0) {
                $waist = ($waist_dress + $waist) / 2;
            }
            #-------------------------------SLEEVE________________________________________________# 
            if ($sleeve > 0 && $sleeve_dress == 0) {
                $sleeve = $sleeve;
            }
            if ($sleeve == 0 && $sleeve_dress > 0) {
                $sleeve = $sleeve_dress;
            }
            if ($sleeve > 0 && $sleeve_dress > 0) {
                $sleeve = ($sleeve + $sleeve_dress) / 2;
            }


            #--------------------------HIP-----------------#
            if ($hip > 0 && $hip_dress == 0) {
                $hip = $hip;
            }
            if ($hip == 0 && $hip_dress > 0) {
                $hip = $hip_dress;
            }
            if ($hip > 0 && $hip_dress > 0) {
                $hip = ($hip + $hip_dress) / 2;
            }
        }  
    
     
        $measurement['neck']=$neck;
        $measurement['bust']=$bust;
        $measurement['chest']=$chest;
        $measurement['waist']=$waist;
        $measurement['hip']=$hip;
        $measurement['sleeve']=$sleeve;
        $measurement['inseam']=$inseam;
        
        
        if($top_id)
        {
            $measurement['sc_top_id']=$top_id;  
        }    
        else
        {
            $measurement['sc_top_id']=0;
        }
        if($bottom_id)
        {
            $measurement['sc_bottom_id']=$bottom_id; 
        } 
        else{
            $measurement['sc_bottom_id']=0;
        }
        if($dress_id)
        {
          $measurement['sc_dress_id']=$dress_id;  
        }
        else {
            $measurement['sc_dress_id']=0;
        }
        
        if($measurement)   
        {
            return   $measurement;
        }        
}
#-------------------------------Web Service---------------------------------------#
public function getBrandArraySizeChart() {

        $brands = $this->repo->getBrandList();

        $brands_array = array();

        foreach ($brands as $i) {
            array_push($brands_array, array('id' => $i['id'], 'brand_name' => $i['name']));
        }
        return $brands_array;
    }


  
//---------------------------get Size Chart By Brand----------------------------
  
 public function getSizeChartByBrand($brand)
 {
     return $this->repo->findSizeChartByBrand($brand);     
 }
  
  
  #--- For Web services to sending them brand with its sizes-----#
public function getBrandSizeTitleArray($gender = null) {
    return $this->repo->findSizeTitleTarget($gender);
  }
  
}