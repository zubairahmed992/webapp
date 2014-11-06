<?php
namespace LoveThatFit\AdminBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\AdminBundle\Event\SizeChartEvent;



class SizeChartHelper{

     /**
     * Holds the Symfony2 event dispatcher service
     */
    protected $dispatcher;
     /**
     * Holds the Doctrine entity manager for database interaction
     * @var EntityManager 
     */
    protected $em;

    /**
     * Entity-specific repo, useful for finding entities, for example
     * @var EntityRepository
     */
    protected $repo;

    /**
     * The Fully-Qualified Class Name for our entity
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
         $conf_yml = new Parser();
        $this->conf = $conf_yml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
        
    }
 //---------------------------------------------------------------------   
    public function createNewSizeChart()
{
    $class = $this->class;
    $sizechart = new $class();
    return $sizechart;
}
    
    public function createNew()
{
    $class = $this->class;
    $size_chart = new $class();
    return $size_chart;
}
//-------------------------------------------------------

public function save($entity) {
        $title = $entity->getTitle();
       $brand = $entity->getBrand()->getId();       
       $gender = $entity->getGender();       
       $target = $entity->getTarget();
       $bodytype=$entity->getBodytype();       
        $msg_array = $this->validateForCreate($brand,$title,$gender,$target,$bodytype);
        if ($msg_array == null) {          
            $this->em->persist($entity);
            $this->em->flush();
            return array('message' => 'Size Chart succesfully created.',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );
        } else {
            return $msg_array;
        }
    }

public function saveSizeChart(SizeChart $size_chart)
{
    $this->em->persist($size_chart);
    $this->em->flush();  
}
//-------------------------------------------------------

public function update($entity) {
        $msg_array = '';
        //$msg_array = $this->validateForUpdate($entity);
        if ($msg_array == null) {
            $this->em->persist($entity);
            $this->em->flush();
            return array('message' => 'sizechart ' . $entity->getTitle() . ' succesfully updated!',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );
        } else {
            return $msg_array;
        }
    }

public function delete($id) {

        $entity = $this->repo->find($id);
        $entity_name = $entity->getTitle();

        if ($entity) {
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

public function find($id)
{
    return $this->repo->find($id);
}

public function findWithSpecs($id) {
        $entity = $this->repo->find($id);
        if (!$entity) {
            $entity = $this->createNewSizeChart();
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


public function findOneById($id)
{
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
                $findAverage=$this->getBustAverage($braSize);
                if($findAverage){
                    $measurement->setBust($findAverage);
                }
                    
        return $measurement;
    }
#------------------------------------------------------------------------------#
private function getBustAverage($bra_num){
    
     $bustRange = $this->conf["Bust_Measurement"];
     foreach($bustRange as $bust){
            $size_cup=$bust['size'].$bust['cup'];
            if($size_cup==$bra_num){
                return $bust['average'];
            }
            }
}
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
                $wasit = $waist;
            }
            if ($waist_dress > 0 && $waist == 0) {
                $wasit = $waist_dress;
            }
            if ($waist_dress > 0 && $waist > 0) {
                $wasit = ($waist_dress + $wasit) / 2;
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
#-----------------------------------------------------------------------------------#
//------------------------Pagination Function------------------------------------------------------
    public function getListWithPagination($page_number, $sort) {
        $yaml = new Parser();
        $pagination_constants = $yaml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
        $limit = $pagination_constants["constants"]["pagination"]["limit"];

        $entity = $this->repo->findAllSizeChart($page_number, $limit, $sort);
        $rec_count = count($this->repo->countAllSizeChartRecord());
        $cur_page = $page_number;

        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }
        return array('sizechart'=>$entity,
			   'rec_count' => $rec_count, 
                           'no_of_pagination' => $no_of_paginations, 
                           'limit' => $cur_page, 
                           'per_page_limit' => $limit,
                           'maleSizeChart'=>$this->getSizeChartByGender('m'),
                           'femaleSizeChart'=>$this->getSizeChartByGender('f'),
                           'topSizeChart'=>$this->getSizeChartByTarget('Top'),
                           'bottomSizeChart'=>  $this->getSizeChartByTarget('Bottom'),
                           'dressSizeChart'=>  $this->getSizeChartByTarget('Dress'),
                           'sort'=>$sort,
        );
    }
    
    public function getRecordsCountWithCurrentSizeChartLimit($sizechart_id){
    
    return $this->repo->getRecordsCountWithCurrentSizeChartLimit($sizechart_id);
}
     

    
    //-----------------------Get Size Chart By Gender----------------------------------------------------------------------------
    
    private function getSizeChartByGender($gender)
    {
        $rec_count =count($this->repo->findSizeChartByGender($gender));
        return $rec_count;
    }
    //-----------------------------Get Size Chart By Target--------------------------------------------------------
    private function getSizeChartByTarget($target)
    {
        $rec_count= count($this->repo->findSizeChartByTarget($target));
        return $rec_count;
    }
    
    
    //----------------------------------------------------------
    private function validateForCreate($brand,$title,$gender,$target,$bodytype) {
        if($title==="00")
       {
           $title="00";
       }
      else if($title=="0"){
         $title="0";
        
       }   
       if($gender!=null and $target!=null and $bodytype!=null)
       {
        $sizechart=  $this->getBrandSize($brand,$title,$gender,$target,$bodytype);
       if($sizechart>0)
       {        
            return array('message' => 'Size Chart already exists!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
       }else
       {
            return;
       }
       }else
       {
           return array('message' => 'Please Enter Values Correctly',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
       }
       
    }

//----------------------------------------------------------
    private function validateForUpdate($entity) {
       $title = $entity->getTitle();
       $brand = $entity->getBrand()->getId();       
       $gender = $entity->getGender();       
       $target = $entity->getTarget();
       $bodytype=$entity->getBodytype();
       $sizechart = $this->getBrandSize($brand,$title,$gender,$target,$bodytype);
        if ($sizechart>0) {
            return array('message' => 'Size Chart already exists!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }
        return;
    }

    private function getBrandSize($brand,$title,$gender,$target,$bodytype)
    {
        $rec_count= count($this->repo->findBrandSizeBy($brand,$title,$gender,$target,$bodytype));
        return $rec_count;        
    }  
#----------------------------Searching -----------------------------------#
public function searchSizeChartPagination($brand_id,$male,$female,$bodyType,$target,$page){
    
$cur_page = $page;
$page -= 1;
$per_page = 10; // Per page records
$previous_btn = true;
$next_btn = true;
$first_btn = true;
$last_btn = true;
$start = $page * $per_page;
$searchResult=$this->repo->searchSizeChart($brand_id,$male,$female,$bodyType,$target,$start,$per_page);
 //return new response(json_encode($searchResult));
$countRecord=count($searchResult);
$countSearchSizeChart=count($this->repo->countSearchSizeChart($brand_id,$male,$female,$bodyType,$target,$start,$per_page));

$no_of_paginations = ceil($countSearchSizeChart /$per_page);
  if ($cur_page >= 7) {
    $start_loop = $cur_page - 3;
    if ($no_of_paginations > $cur_page + 3)
        $end_loop = $cur_page + 3;
    else if ($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6) {
        $start_loop = $no_of_paginations - 6;
        $end_loop = $no_of_paginations;
    } else {
        $end_loop = $no_of_paginations;
    }
} else {
    $start_loop = 1;
    if ($no_of_paginations > 7)
        $end_loop = 7;
    else
        $end_loop = $no_of_paginations;
}
return array('searchResult'=>$searchResult,'countRecord'=>$countRecord,'first_btn'=>$first_btn,'cur_page'=>$cur_page,'previous_btn'=>$previous_btn,'last_btn'=>$last_btn,'start_loop'=>$start_loop,'end_loop'=>$end_loop,'next_btn'=>$next_btn,'no_of_paginations'=>$no_of_paginations);    
    
}
#--- Getting All mix size title-----------------------------------------------#
public function getMixSizeTitle(){
     return $this->conf["constants"]["size_titles"]["mix"];
}
public function getMixSizeTitleForMenTop(){
     return $this->conf["constants"]["size_titles"]["mix"]["man_top"];
}
public function getMixSizeTitleForMenBottom(){
     return $this->conf["constants"]["size_titles"]["mix"]["man_bottom"];
}
public function getMixSizeTitleForWoman(){
     return $this->conf["constants"]["size_titles"]["mix"]["woman"];
}
#--- get size chart id base on brand, gender, target for web services
public function  getIdBaseOnTargetGender($brand_id,$gender,$target,$size_title,$body_type){
    
   return  $this->repo->getIdBaseOnTargetGender($brand_id,$gender,$target,$size_title,$body_type);
    
}

#--- For Web services to sending them brand with its sizes-----#
public function getBrandSizeTitleArray() {
    return $this->repo->findSizeTitleTarget();
  }
  
//---------------------------get Size Chart By Brand----------------------------
  
 public function getSizeChartByBrand($brand)
 {
     return $this->repo->findSizeChartByBrand($brand);     
 }
  
  
  
  
}