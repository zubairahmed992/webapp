<?php

namespace LoveThatFit\UserBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\UserBundle\Event\UserEvent;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\AdminBundle\Entity\SizeChart;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;

class UserArchivesHelper {

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
    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class,  Container $container) {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;        
        $this->repo = $em->getRepository($class);
    }

    #-------------------------------------------------------------------------

    public function saveArchives($user_archives, $data) {

        if (array_key_exists('measurement', $data)) {
            if (strlen($user_archives->getMeasurementJson()) > 0) {
                $arc = json_decode($data['measurement']);
                $meas = json_decode($user_archives->getMeasurementJson());
                if (is_array($meas)&& is_array($arc)){
                    $user_archives->setMeasurementJson(json_encode(array_merge_recursive($meas, $arc)));
                }                
            } else {
                $user_archives->setMeasurementJson($data['measurement']);
            }
        } else {
            $user_archives->setMeasurementJson($this->extractMeasurements($data, $user_archives->getMeasurementJson()));
        }

        
        #---------------------------
        if (array_key_exists('image_actions', $data)) {
            if (strlen($user_archives->getImageActions()) > 0) {
                $param = json_decode($data['image_actions'],true);
                $arch = json_decode($user_archives->getImageActions(),true);
                if (is_array($param)&& is_array($arch)){
                    $user_archives->setImageActions(json_encode(array_merge($arch, $param)));
                }
            } else {
                $user_archives->setImageActions($data['image_actions']);
            }
        }
        #---------------------------
        #---------------------------
         if (array_key_exists('marker_params', $data)) {
            $user_archives->setMarkerParams($data['marker_params']);
        } else {
            $user_archives->setMarkerParams($this->extractMarkerParams($data));
        }
        if (array_key_exists('svg_path', $data)) {
            $user_archives->setSvgPaths($data['svg_path']);
        }
        if (array_key_exists('marker_json', $data)) {
            $user_archives->setMarkerJson($data['marker_json']);
            #--------------------------
            $image_actions_archive_array = json_decode($user_archives->getImageActions(),true);
            $predicted_measurement = $this->container->get('user.marker.helper')->getPredictedMeasurement($data['marker_json'], $image_actions_archive_array['device_type']);
            $measurement_archive_array  = json_decode($user_archives->getMeasurementJson(),true);            
            $measurement_archive_array['mask']=$predicted_measurement;
            $user_archives->setMeasurementJson(json_encode($measurement_archive_array));
        }
        if (array_key_exists('default_marker_svg', $data)) {
            $user_archives->setDefaultMarkerSvg($data['default_marker_svg']);
        }
        return $this->save($user_archives);
    }
    #----------------------------------------
    #----------------------------------------
    public function createFromExistingData($user) {
        $archive =  $this->createNew($user);
        $marker = $user->getUserMarker();
        #measurement
        $actual_measurement = $user->getMeasurement()->getJSONMeasurement('actual_user');
        $archive->setMeasurementJSON(is_array($actual_measurement) ? json_encode($actual_measurement) : null);
        #image specs        
        $device_specs = $user->getDeviceSpecs($user->getImageDeviceType());        
        $archive->setImageActions($this->extractImageActions($user, $marker, $device_specs));
        #-------------------------------------------
        $mp = array(
          'rect_x' => $marker->getRectX(),
          'rect_y' =>   $marker->getRectY(),
          'rect_width' =>   $marker->getRectWidth(),
          'rect_height' =>   $marker->getRectHeight(),
          'mask_x' =>   $marker->getMaskX(),
          'mask_y' =>   $marker->getMaskY(),            
        );                    
        $archive->setMarkerParams(json_encode($mp));
        #-------------------------------------------
        $archive->setSVGPaths($marker->getSVGPaths());
        $archive->setDefaultMarkerSVG($marker->getDefaultMarkerSvg());
        $archive->setMarkerJson($marker->getMarkerJson());
        $archive->setStatus(1);
        $archive->setImage(uniqid().'.png');
       #---------------------- copy images
        
        if (file_exists($user->getOriginalImageAbsolutePath())) {
            @copy($user->getOriginalImageAbsolutePath(),$archive->getAbsolutePath('original'));
        } else {
            @copy($user->getAbsolutePath(),$user->getOriginalImageAbsolutePath());
            @copy($user->getAbsolutePath(),$archive->getAbsolutePath('original'));
        }
        @copy($user->getAbsolutePath(),$archive->getAbsolutePath('cropped'));
        
        #----------------------
        $this->save($archive);
        return $archive;
        
        #device type
        
        #image
        
    }

#---------------------------------------------------------------------
    private function extractMarkerParams($ar) {
        $mp = array();            
        array_key_exists('rect_x', $ar)? $mp['rect_x'] = $ar['rect_x'] : '';
        array_key_exists('rect_y', $ar)? $mp['rect_y'] = $ar['rect_y'] : '';
        array_key_exists('rect_height', $ar)? $mp['rect_height'] = $ar['rect_height'] : '';
        array_key_exists('rect_width', $ar)? $mp['rect_width'] = $ar['rect_width'] : '';
        array_key_exists('mask_x', $ar)? $mp['mask_x'] = $ar['mask_x'] : '';
        array_key_exists('mask_y', $ar)? $mp['mask_y'] = $ar['mask_y'] : '';
        return  json_encode($mp);
    }
   
    #---------------------------------------------------------------------    
    private function extractImageActions($user, $marker, $device_specs) {
        $image_actions=  json_decode($marker->getImageActions(),true);
        $image_actions['device_type'] = $user->getImageDeviceType();                
        #--------------------------------------
        if ($user->getImageDeviceModel()==null){
            if (strtolower($user->getImageDeviceType())=='iphone5'){
                $image_actions['device_model'] = 'iphone5c';
            }else{
                $image_actions['device_model'] = $user->getImageDeviceType();
            }
        }else{
            $image_actions['device_model'] = $user->getImageDeviceModel();
        }
        
        $image_actions['height_per_inch'] = $device_specs ? $device_specs->getDeviceUserPerInchPixelHeight() : 7;
        $ia=array('move_up_down' => 0, "move_left_right" => 0, "img_rotate" => 0, "height_per_inch" => "7.12");
        foreach ($ia as $k => $v) {
            if(!array_key_exists($k, $image_actions)){
                $image_actions[$k]=$v;
            }
        }        
        return json_encode($image_actions);        
    }
#---------------------------------------------------------------------
    private function extractMeasurements($ar, $m_json) {
        $amja = json_decode($m_json, true);
        if (is_array($ar) && is_array($amja)) { #if both are array then proceed
            array_key_exists('hip_height', $ar) ? $amja['hip_height'] = $ar['hip_height'] : '';
            array_key_exists('shoulder_height', $ar) ? $amja['shoulder_height'] = $ar['shoulder_height'] : '';
            return json_encode($amja);
        }
        return $m_json;
    }
    #---------------------------------------------------------------------
	public function getListWithPagination($page_number, $sort) {
	  $yaml = new Parser();
	  $pagination_constants = $yaml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
	  $limit = $pagination_constants["constants"]["pagination"]["limit"];

	  $entity = $this->repo->listAllPendingUsers($page_number, $limit, $sort);
	  $rec_count = count($this->repo->countAllRecord());
	  $cur_page = $page_number;

	  if ($page_number == 0 || $limit == 0) {
		$no_of_paginations = 0;
	  } else {
		$no_of_paginations = ceil($rec_count / $limit);
	  }
	  return array('user' => $entity,
		'rec_count' => $rec_count,
		'no_of_pagination' => $no_of_paginations,
		'limit' => $cur_page,
		'per_page_limit' => $limit,
		'sort'=>$sort,
	  );
	}

    public function find($id) {
        return $this->repo->find($id);
    }
#-------------------------------------------------------------------------
   public function createNew($user) {
	 $user_archives = new $this->class();
	 $user_archives->setUser($user);
	 $user_archives->setStatus('-1');
	 $user_archives->setCreatedAt(new \DateTime('now'));
	 $user_archives->setUpdatedAt(new \DateTime('now'));
     return $user_archives;
    }

   public function save(UserArchives $user_archives) {
	 $user_archives->setUpdatedAt(new \DateTime('now'));
	 $this->em->persist($user_archives);
	 $this->em->flush();
     //echo $user_archives->getUser();
     $result = $this->getAllArchiveCount($user_archives->getUser());
     if($result["counter"] == 6){
        $this->removeFirstArchive($user_archives);
     }
	 return $user_archives;
 }
  #------------------ Delete First Archive Record with Images ------#
    public function removeFirstArchive($user_archives){
    $all_archive = $this->getAllArchive($user_archives->getUser());
    $counter=0;
    foreach($all_archive as $val){
        if($counter == 0){
            $archive = $this->repo->find($val["id"]);
            if(file_exists($archive->getAbsolutePath('original'))){
                unlink($archive->getAbsolutePath('original'));
            }
            if(file_exists($archive->getAbsolutePath('cropped'))){
                unlink($archive->getAbsolutePath('cropped'));
            }
            $this->delete($val["id"]);
        }
        $counter++;
    }
    }
  #------------------ End of Delete First Archive Record with Images ------#

  #-------------------- Get User Archive Measurement ----------------#
  public function getPendingArchive($user_id) {
	return $this->repo->getPendingArchive($user_id);
  }

  #-------------------- Get User Archive Measurement ----------------#
  public function getAllArchive($user_id) {
	return $this->repo->getAllArchive($user_id);
  }
  #-------------------- Get User Active Archive Count ----------------#
  public function getAllArchiveCount($user_id) {
	return $this->repo->getAllArchiveCount($user_id);
  }
  #-------------------- Update User Status ----------------#
  public function updateStatus($user_id) {
	$user = $this->container->get('user.helper.user')->find($user_id);
	$result = $this->getPendingArchive($user);
	$id = $result->getId();
	if($result->getStatus() == '-1'){
	  $user_cropped_image = "cropped_".$result->getImage();
	  $user_original_image = "original_".$result->getImage();
	  //echo $result->getImage();die;
	  #$result_user = $this->container->get('user.helper.user')->find($user_id);
	  $user->setStatus(0);
	  $user->setUpdatedAt(new \DateTime('now'));
	  $this->em->persist($user);
	  $this->em->flush();
	  $this->delete($id);
	  if (file_exists("../web/uploads/ltf/users/".$user_id."/".$user_cropped_image))
	  {
		unlink ("../web/uploads/ltf/users/".$user_id."/".$user_cropped_image);
	  }
	  if (file_exists("../web/uploads/ltf/users/".$user_id."/".$user_original_image))
	  {
		unlink ("../web/uploads/ltf/users/".$user_id."/".$user_original_image);
	  }
	}
 }
#--------------------
#-------------------- Discard User Status ----------------#
    public function discardStatus($user_id) {
        $user = $this->container->get('user.helper.user')->find($user_id);
        $result = $this->getPendingArchive($user);
        $id = $result->getId();
        if($result->getStatus() == '-1'){

            $user_cropped_image = "cropped_".$result->getImage();
            $user_original_image = "original_".$result->getImage();

            //echo $result->getImage();die;
            #$result_user = $this->container->get('user.helper.user')->find($user_id);
            $user->setStatus(0);
            $user->setUpdatedAt(new \DateTime('now'));
            $result->setStatus('-2');
            $this->em->persist($result);
            $this->em->flush();

             if (file_exists("../web/uploads/ltf/users/".$user_id."/".$user_cropped_image))
             {
                 unlink ("../web/uploads/ltf/users/".$user_id."/".$user_cropped_image);
             }
             if (file_exists("../web/uploads/ltf/users/".$user_id."/".$user_original_image))
             {
                 unlink ("../web/uploads/ltf/users/".$user_id."/".$user_original_image);
             }

        }
    }

  public function delete($id) {
	$entity = $this->repo->find($id);
	if ($entity) {
	  $this->em->remove($entity);
	  $this->em->flush();
	}
  }
  
  #--------------------
  public function makeArchiveToCurrent($archive_id) {
	$archive = $this->repo->find($archive_id);
        $user=$archive->getUser();
        #measurement------------>
        $measurement_archive = json_decode($archive->getMeasurementJson(), 1);     
        $measurement = $this->container->get('webservice.helper')->setUserMeasurementWithParams($measurement_archive, $user);
        
        if(array_key_exists('mask', $measurement_archive)){
            $measurement = $this->container->get('webservice.helper')->setUserMeasurementWithParams($measurement_archive['mask'], $user);
        }
        
        $this->container->get('user.helper.measurement')->saveMeasurement($measurement);
    
        #mask marker------------>
        $marker = $user->getUserMarker();
        $this->container->get('user.marker.helper')->setArray($archive->getMarkerArray(),$marker);
        $this->container->get('user.marker.helper')->save($marker);
    
        #image specs------------>
        $image_actions_archive = json_decode($archive->getImageActions(), 1);
        $this->container->get('user.helper.userimagespec')->updateWithParam($image_actions_archive,$user);                
   
        #user status------------>          
        $user->setStatus(0);
        array_key_exists('device_type', $image_actions_archive)? $user->setImageDeviceType($image_actions_archive['device_type']):'';
        array_key_exists('device_model', $image_actions_archive)? $user->setImageDeviceModel($image_actions_archive['device_model']):'';
        $this->container->get('user.helper.user')->saveUser($user);                
        #archive statuses------------>        
        $this->pendingToCurrent($user);
        $archive->setStatus(1);
        $this->save($archive);
        #image copy------------>
        $archive->copyImagesToUser();
        return $archive;
  }

    #-------------------- update status
  public function pendingToCurrent($user) {
        foreach ($user->getUserArchives() as $a) {
            if ($a->getStatus() != -1) {
                $a->setStatus(0);
                $this->save($a);
            }
        }
    }
    #status{"pending":-1, "active":1, "inactive":0}
  #-----------------------------------------------------------  
    public function deleteArchiveWithImages($archive_id) {
        $archive = $this->repo->find($archive_id);
        unlink($archive->getAbsolutePath('original'));
        unlink($archive->getAbsolutePath('cropped'));
        $this->em->remove($archive);
        $this->em->flush();
    }
}