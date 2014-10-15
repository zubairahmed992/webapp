<?php

namespace LoveThatFit\UserBundle\Entity;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\UserBundle\Entity\MaskMarker;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\UserBundle\Event\UserEvent;
use Symfony\Component\HttpFoundation\Request;

class UserMarkerHelper {

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
   #----------------------------------------------------------------------------
    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, Container $container) {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }
       #----------------------------------------------------------------------------
     public function createNew()
     {
       $class = $this->class;
       $maskMarker = new $class();
       return $maskMarker;
     }
     
        #----------------------------------------------------------------------------
     public function saveUserMarker($user,$usermarker) {
        //$class = $this->class;
       // $usermarker = new $class();
        $usermarker->setCreatedAt(new \DateTime('now'));    
        $usermarker->setUpdatedAt(new \DateTime('now'));            
        $usermarker->setUser($user);       
        $this->em->persist($usermarker);
        $this->em->flush();      
    }  
       #----------------------------------------------------------------------------
    public function updateUserMarker($user,$usermarker)
    {
        $usermarker->setCreatedAt(new \DateTime('now'));    
        $usermarker->setUpdatedAt(new \DateTime('now'));            
        $usermarker->setUser($user);               
        $this->em->persist($usermarker);
        $this->em->flush();       
    }
       #----------------------------------------------------------------------------
    public function setArray($specs,$user_marker){
        if(array_key_exists('svg_path', $specs) && $specs['svg_path']){$user_marker->setSvgPaths($specs['svg_path']);}
        if(array_key_exists('rect_x', $specs) && $specs['rect_x']){$user_marker->setRectX($specs['rect_x']);}
        if(array_key_exists('rect_y', $specs) && $specs['rect_y']){$user_marker->setRectY($specs['rect_y']);}
        if(array_key_exists('rect_height', $specs) && $specs['rect_height']){$user_marker->setRectHeight($specs['rect_height']);}
        if(array_key_exists('rect_width', $specs) && $specs['rect_width']){$user_marker->setRectWidth($specs['rect_width']);}
        if(array_key_exists('mask_x', $specs) && $specs['mask_x']){$user_marker->setMaskX($specs['mask_x']);}
        if(array_key_exists('mask_y', $specs) && $specs['mask_y']){$user_marker->setMaskY($specs['mask_y']);}
        if(array_key_exists('marker_json', $specs) && $specs['marker_json']){$user_marker->setMarkerJson($specs['marker_json']);}
    }
       #----------------------------------------------------------------------------
   public function getArray($user_marker) {
       $specs['svg_path']=$user_marker->getSvgPaths();
       $specs['rect_x']=$user_marker->getRectX();
       $specs['rect_y']=$user_marker->getRectY();
       $specs['rect_height']=$user_marker->getRectHeight();
       $specs['rect_width']=$user_marker->getRectWidth();
       $specs['mask_x']=$user_marker->getMaskX();
       $specs['mask_y']=$user_marker->getMaskY();
       $specs['marker_json']=$user_marker->getMarkerJson();
        return ($specs);
   }
      #----------------------------------------------------------------------------
   public function fillMarker($user,$usermaker){
        $maskMarker=$this->findMarkerByUser($user);
      
        if(count($maskMarker)>0){
            $this->setArray($usermaker,$maskMarker);
            $this->updateUserMarker($user,$maskMarker);
            return 'updated';
        }else{  
           $maskMarker=$this->createNew();
           $this->setArray($usermaker,$maskMarker);
           $this->saveUserMarker($user,$maskMarker);
            return 'added';
        }
   }
   
      #----------------------------------------------------------------------------
    public function findMarkerByUser($user)
    {
        return $this->repo->findMarkerByUser($user);
    }

   #----------------------------------------------------------------------------
    
    public function findByUser($user)
    {
     return $this->repo->findByUser($user);
    }
    
       #----------------------------------------------------------------------------
    public function find($id) {
        return $this->repo->find($id);
    } 
    
       #----------------------------------------------------------------------------
    private function getJsonForFields($fields){
        $f=array();
        foreach ($fields as $key => $value) {
        $f[$key]=$value;
        }
        return json_encode($f);
        
    }
   #----------------------------------------------------------------------------
    public function getByUser($user) {
        
        $m = $this->repo->findOneByUser($user);
        if ($m) {
            return $m;
        } else {
            return $this->getDefaultObject($user);
        }
    }
   #----------------------------------------------------------------------------    
    public function getDefaultObject($user){
       $class = $this->class;
       $user_marker = new $class();
       $mask_type=$this->getDefaultValuesBaseOnBodyType($user);
       $user_marker->setSvgPaths($mask_type['SvgPaths']);
       $user_marker->setMaskX($mask_type['MaskX']);
       $user_marker->setMaskY($mask_type['MaskY']);
       $user_marker->setRectX($mask_type['RectX']);
       $user_marker->setRectY($mask_type['RectY']);
       $user_marker->setRectHeight($mask_type['RectHeight']);
       $user_marker->setRectWidth($mask_type['RectWidth']);
       
       /*$user_marker->setSvgPaths('M151.614,270.262c-6.62,4.646-8.099,4.489-4.747-10.494c-0.07-4.79-5.163-12.409-1.999-24.485c0.053-1.571-5.985-16.437-12.714-50.684c-2.128-10.832-6.178-62.905-8.273-61.248c-2.726,2.157-2.236,7.373-2.248,8.745c-0.001,4.201-1.526,17.356-2.249,20.237c-1.276,5.093-1.967,12.611-1.998,15.241c-0.067,5.701,0.677,11.159,2.998,16.99c1.413,3.551,4.748,12.075,5.496,19.238c2.862,27.388,1.294,43.984,1.249,44.723c-0.766,7.277-8.104,48.245-10.493,60.464c-0.167,3.664-0.771,12.576-0.999,17.739c-0.101,7.295-0.117,9.695,0.499,15.74c3.127,30.661-2.252,47.058-5.246,70.957c-0.837,6.674-0.327,16.709-0.5,23.985c-0.118,4.974,1.984,8.982,2.498,11.493c13.695,19.154,3.199,18.183-2.748,18.988c-15.116,2.045-9.75-7.721-14.241-18.988c-1.546-3.879,1.579-9.416,1.499-11.493c-1.072-27.787-4.997-55.634-5.497-65.96c-0.91-12.801,2.903-25.788,1.749-30.981c-4.307-19.38-3.932-42.688-5.496-83.449c-0.066-1.712,0.872-8.745-1.999-8.745c-2.71-0.063-1.956,7.194-1.999,8.745c-1.117,40.668-1.433,62.483-5.746,83.449c-0.78,3.238,2.805,16.614,1.999,30.981c-1.673,29.808-5.862,49.721-5.497,65.96c0.071,3.174,2.115,5.71,1.749,11.493c-0.763,12.052,2.375,21.236-14.741,18.738c-12.492-3.439-7.897-9.562-3.117-18.841c1.692-3.285,3.032-6.67,2.867-11.391c0.334-9.011,0.106-20.041-0.121-23.914c-9.755-54.432-5.811-61.274-5.375-71.278c0.898-6.056,0.668-9.235,0.75-15.241c-0.5-7.161-0.75-10.827-1.249-17.988c-4.736-19.508-9.953-57.974-10.494-60.714c-1.479-7.418,0.483-35.172,1.499-44.722c1.191-11.204,3.291-13.009,4.997-18.989c0.684-2.397,2.549-3.177,3.248-16.99c0.204-4.032-1.087-11.003-1.999-15.241c-1.207-5.608-2.332-15.49-2.249-20.237c0.083-2.152,0.233-6.398-2.499-8.745c-1.91-0.397-6.483,53.751-8.042,61.36c-8.189,39.967-12.969,50.416-12.508,50.628c2.855,11.759-1.935,19.697-2.005,24.489c3.362,14.985,1.878,15.142-4.762,10.495c-17.464-12.221-2.39-22.442-2.004-35.484c0.366-12.408,0.802-42.319,4.761-67.221c0.833-5.236,5.724-38.381,6.245-44.461c2.396-27.968,10.081-25.894,16.567-29.039c0.546-0.097,22.023-5.233,25.235-9.994c2.171-2.912,2.273-8.37,2.499-13.242c-0.457-1.17-2.579-5.746-3.498-12.493c-2.62,1.118-3.647-5.756-3.998-5.996c-2.605-8.126-0.082-6.169,0.25-6.746c-3.309-10.197-3.853-26.484,19.238-26.484c27.639,0,20.271,24.2,19.238,26.484c2.799-0.126,0.349,5.03,0.25,6.746c-2.335,8.929-3.287,4.813-3.998,5.996c-0.942,5.645-1.337,8.107-3.497,12.493c0.102,2.303-0.061,10.189,2.248,13.242c3.509,4.639,22.114,9.061,25.39,9.968c7.817,2.822,13.714,2.715,16.835,29.258c1.473,12.527,5.361,39.249,6.246,44.223c4.414,24.818,4.382,54.803,4.747,67.209C153.997,247.822,169.026,258.043,151.614,270.262z');
       $user_marker->setMaskX(181);
       $user_marker->setMaskY(0);
       $user_marker->setRectX(32);
       $user_marker->setRectY(52.5);
       $user_marker->setRectHeight(400);
       $user_marker->setRectWidth(300);*/
       $user_marker->setUser($user);
       return $user_marker;
    }
       #----------------------------------------------------------------------------
    public function getDefaultValues(){
        
       $specs['svg_path']='M151.614,270.262c-6.62,4.646-8.099,4.489-4.747-10.494c-0.07-4.79-5.163-12.409-1.999-24.485c0.053-1.571-5.985-16.437-12.714-50.684c-2.128-10.832-6.178-62.905-8.273-61.248c-2.726,2.157-2.236,7.373-2.248,8.745c-0.001,4.201-1.526,17.356-2.249,20.237c-1.276,5.093-1.967,12.611-1.998,15.241c-0.067,5.701,0.677,11.159,2.998,16.99c1.413,3.551,4.748,12.075,5.496,19.238c2.862,27.388,1.294,43.984,1.249,44.723c-0.766,7.277-8.104,48.245-10.493,60.464c-0.167,3.664-0.771,12.576-0.999,17.739c-0.101,7.295-0.117,9.695,0.499,15.74c3.127,30.661-2.252,47.058-5.246,70.957c-0.837,6.674-0.327,16.709-0.5,23.985c-0.118,4.974,1.984,8.982,2.498,11.493c13.695,19.154,3.199,18.183-2.748,18.988c-15.116,2.045-9.75-7.721-14.241-18.988c-1.546-3.879,1.579-9.416,1.499-11.493c-1.072-27.787-4.997-55.634-5.497-65.96c-0.91-12.801,2.903-25.788,1.749-30.981c-4.307-19.38-3.932-42.688-5.496-83.449c-0.066-1.712,0.872-8.745-1.999-8.745c-2.71-0.063-1.956,7.194-1.999,8.745c-1.117,40.668-1.433,62.483-5.746,83.449c-0.78,3.238,2.805,16.614,1.999,30.981c-1.673,29.808-5.862,49.721-5.497,65.96c0.071,3.174,2.115,5.71,1.749,11.493c-0.763,12.052,2.375,21.236-14.741,18.738c-12.492-3.439-7.897-9.562-3.117-18.841c1.692-3.285,3.032-6.67,2.867-11.391c0.334-9.011,0.106-20.041-0.121-23.914c-9.755-54.432-5.811-61.274-5.375-71.278c0.898-6.056,0.668-9.235,0.75-15.241c-0.5-7.161-0.75-10.827-1.249-17.988c-4.736-19.508-9.953-57.974-10.494-60.714c-1.479-7.418,0.483-35.172,1.499-44.722c1.191-11.204,3.291-13.009,4.997-18.989c0.684-2.397,2.549-3.177,3.248-16.99c0.204-4.032-1.087-11.003-1.999-15.241c-1.207-5.608-2.332-15.49-2.249-20.237c0.083-2.152,0.233-6.398-2.499-8.745c-1.91-0.397-6.483,53.751-8.042,61.36c-8.189,39.967-12.969,50.416-12.508,50.628c2.855,11.759-1.935,19.697-2.005,24.489c3.362,14.985,1.878,15.142-4.762,10.495c-17.464-12.221-2.39-22.442-2.004-35.484c0.366-12.408,0.802-42.319,4.761-67.221c0.833-5.236,5.724-38.381,6.245-44.461c2.396-27.968,10.081-25.894,16.567-29.039c0.546-0.097,22.023-5.233,25.235-9.994c2.171-2.912,2.273-8.37,2.499-13.242c-0.457-1.17-2.579-5.746-3.498-12.493c-2.62,1.118-3.647-5.756-3.998-5.996c-2.605-8.126-0.082-6.169,0.25-6.746c-3.309-10.197-3.853-26.484,19.238-26.484c27.639,0,20.271,24.2,19.238,26.484c2.799-0.126,0.349,5.03,0.25,6.746c-2.335,8.929-3.287,4.813-3.998,5.996c-0.942,5.645-1.337,8.107-3.497,12.493c0.102,2.303-0.061,10.189,2.248,13.242c3.509,4.639,22.114,9.061,25.39,9.968c7.817,2.822,13.714,2.715,16.835,29.258c1.473,12.527,5.361,39.249,6.246,44.223c4.414,24.818,4.382,54.803,4.747,67.209C153.997,247.822,169.026,258.043,151.614,270.262z';
       $specs['rect_x']=32;
       $specs['rect_y']=52.5;
       $specs['rect_height']=400;
       $specs['rect_width']=300;
       $specs['mask_x']=181;
        return $specs;
    }
    #---------------------------------------------------------------------------
    // #-----------------------------------------------------------------------------#
  public function getDefaultValuesBaseOnBodyType($user){
      $mask_type_array=$this->getMaskedMarkerSpecs();
      if ($user->getGender() == "f") {
           $mask_type = $mask_type_array['mask_type']['woman'];
        } else {
            $mask_type =$mask_type_array['mask_type']['man'];
            if(!$mask_type){
                $mask_type = $mask_type_array['mask_type']['woman'];
            }
        }
         $bodyShapes = $this->getBodyShapeTitle($mask_type);

        if (in_array($user->getMeasurement()->getBodyShape(),$bodyShapes)) {
            return $mask_type[$user->getMeasurement()->getBodyShape()];
        } else {
            return $mask_type['regular'];
        }
    }
 #------------------------------------------------------------------------------# 
  public function getBodyShapeTitle($mask_type) {
        foreach ($mask_type as $key => $value) {
            $mask[] = $key;
        }
        return $mask;
    }
    #---------------------------------------------------------------------------#
     private function getMaskedMarkerSpecs() {
        $yaml = new Parser();
        return $yaml->parse(file_get_contents('../src/LoveThatFit/UserBundle/Resources/config/mask_marker.yml'));
    }
     
    #----------------------------------------------------------------------------
    
    public function getComparisionArray($user, $mm_specs){
        
        $mm=$this->getByUser($user);
        $mm_array = json_decode($mm->getMarkerJson());
        
        $ubm=$user->getMeasurement()->getArray();
        $comp=array();
        #$m1=0;
        foreach ($mm_specs['masked_marker'] as $mms_k=>$mms_v) {
            $user_fitpoint_measurement =  array_key_exists($mms_k, $ubm)?$ubm[$mms_k]:'';
            $m1 = $this->calculate_distance($mms_v, $mm_array);
            
            $comp[$mms_k] = array(  'axis'=>$mms_v['axis'], 
                                    'type'=>$mms_v['type'], 
                                    'segments'=>$mms_v['segments'], 
                                    'body'=>$user_fitpoint_measurement, 
                                    'pixels'=>$m1);                
        }
        return $comp;
        
    }
    #----------------------------------------------------------------------------
    private function calculate_distance($mms_v, $mm_array){
        $p1 = $mms_v['segments']['s1']['a'];
        $p2 = $mms_v['segments']['s1']['b'];
        $x1=round($mm_array[$p1][0],2);
        $y1=round($mm_array[$p1][1],2);
        $x2=round($mm_array[$p2][0],2);
        $y2=round($mm_array[$p2][1],2);        
        #single measurement
        $dst_px1 =   sqrt(pow(($x2-$x1),2) + pow(($y2-$y1),2));        
        
        #pair measurements
        $dst_px2 =   0;
        
        if($mms_v['type']=='pair'){
            $p3 = $mms_v['segments']['s2']['a'];
            $p4 = $mms_v['segments']['s2']['b'];
            $x3=round($mm_array[$p3][0],2);
            $y3=round($mm_array[$p3][1],2);
            $x4=round($mm_array[$p4][0],2);
            $y4=round($mm_array[$p4][1],2);        
            $dst_px2 =   sqrt(pow(($x4-$x3),2) + pow(($y4-$y3),2));                                        
        }
        
        return array('s1'=>$dst_px1,
                        's2'=>$dst_px2,
            );
    }
    #----------------------------------------------------------------------------
    public function _getComparisionArray($user, $mm_specs){
        $mm=$this->getByUser($user);
        $mm_array = json_decode($mm->getMarkerJson());
        
        $ubm=$user->getMeasurement()->getArray();
        $comp=array();
        #$m1=0;
        foreach ($mm_specs['masked_marker'] as $mms_k=>$mms_v) {
            $user_fitpoint_measurement =  array_key_exists($mms_k, $ubm)?$ubm[$mms_k]:'';
            $m1 = $this->calculate_distance($mms_v, $mm_array);
            $comp[$mms_k] = array($mms_v[0], $mms_v[1], $mms_v[2], $user_fitpoint_measurement, $m1);                
        }
        return $comp;
        
    }
   
    
}
    
?>