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
        if(array_key_exists('default_marker_json', $specs) && $specs['default_marker_json']){$user_marker->setDefaultMarkerJson($specs['default_marker_json']);}
        if(array_key_exists('default_marker_svg', $specs) && $specs['default_marker_svg']){$user_marker->setDefaultMarkerSvg($specs['default_marker_svg']);}
        if(array_key_exists('default_user', $specs) && $specs['default_user']){$user_marker->setDefaultUser($specs['default_user']);}
        
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
       $specs['default_marker_json']=$user_marker->getDefaultMarkerJson();
       $specs['default_marker_svg']=$user_marker->getDefaultMarkerSvg();
       $specs['default_user']=$user_marker->getDefaultUser();       
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
       $user_marker->setUser($user);
       return $user_marker;
    }
    #---------------------------------------------------------------------------
    
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

        if (in_array(strtolower($user->getMeasurement()->getBodyShape()),$bodyShapes)) {
            return $mask_type[strtolower($user->getMeasurement()->getBodyShape())];
        } else {
            if ($user->getGender() == "f") {
                return $mask_type['apple'];
            }else{
                return $mask_type['regular'];
            }
            
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
    #---------------------------------------------------------------------------#
    public function getPridictedMeasurementArray($user){
        $ca=$this->getComparisionArray($user);
        $pa=array();
        foreach ($ca as $mms_k=>$mms_v) {
            if($mms_v['predicted']>0){
                $device_adjusted_px = $this->device_screen_adjustment($mms_v['predicted'], $user->getImageDeviceType());
                $pa[$mms_k]=number_format($device_adjusted_px, 2, '.', '');
            }
        }
        return $pa;
    }
    #---------------------------------------------------------------
    public function getComparisionArray($user){
        $mm_specs=  $this->getMaskedMarkerSpecs();
        $mm=$this->getByUser($user);
        $mm_array = json_decode($mm->getMarkerJson());
        
        $ubm=$user->getMeasurement()->getArray();
        $comp=array();
        #$m1=0;
        foreach ($mm_specs['masked_marker'] as $mms_k=>$mms_v) {
            $user_fitpoint_measurement =  array_key_exists($mms_k, $ubm)?$ubm[$mms_k]:'';
            $m1 = $this->calculate_distance($mms_v, $mm_array);    
            $device_adjusted_px = $this->device_screen_adjustment($m1['avg'], $user->getImageDeviceType());
            $comp[$mms_k] = array(  'axis'=>$mms_v['axis'], 
                                    'type'=>$mms_v['type'], 
                                    'segments'=>$mms_v['segments'], 
                                    'body'=>$user_fitpoint_measurement, 
                                    'pixels'=>$m1,
                                    'predicted'=>  $this->getPixelToInch($mm_specs, $mms_k, $device_adjusted_px),
                                );                
        }
        return $comp;
        
    }
    #---------------------------------------------------------------
    public function getPredictedMeasurement($mask_json, $device_type=null){                       
        $mm_specs =  $this->getMaskedMarkerSpecs();        
        $mm_array = json_decode($mask_json);
        $pred_measurements=array();        
        foreach ($mm_specs['masked_marker'] as $mms_k=>$mms_v) {
            $m1 = $this->calculate_distance($mms_v, $mm_array);                        
            $device_adjusted_px = $this->device_screen_adjustment($m1['avg'], $device_type);
            $predicted =  $this->getPixelToInch($mm_specs, $mms_k, $device_adjusted_px);
            if ($predicted>0){
                $pred_measurements[$mms_k]=$predicted;                
            }
        }
        return $pred_measurements;
    }    
    #----------------------------------------------------------------------------
    private function calculate_distance($mms_v, $mm_array) {
        if (is_array($mm_array)) {
            $dst_px1 = 0;
            $p1 = $mms_v['segments']['s1']['a'];
            $p2 = $mms_v['segments']['s1']['b'];
            if (array_key_exists($p1, $mm_array) && array_key_exists($p2, $mm_array)) {
                $x1 = round($mm_array[$p1][0], 2);
                $y1 = round($mm_array[$p1][1], 2);            
                $x2 = round($mm_array[$p2][0], 2);
                $y2 = round($mm_array[$p2][1], 2);
                #single measurement
                $dst_px1 = sqrt(pow(($x2 - $x1), 2) + pow(($y2 - $y1), 2));
            }
            
            #pair measurements
            $dst_px2 = 0;

            if ($mms_v['type'] == 'pair') {
                $p3 = $mms_v['segments']['s2']['a'];
                $p4 = $mms_v['segments']['s2']['b'];
                if (array_key_exists($p3, $mm_array) && array_key_exists($p4, $mm_array)) {                    
                    $x3 = round($mm_array[$p3][0], 2);
                    $y3 = round($mm_array[$p3][1], 2);
                    $x4 = round($mm_array[$p4][0], 2);
                    $y4 = round($mm_array[$p4][1], 2);
                    $dst_px2 = sqrt(pow(($x4 - $x3), 2) + pow(($y4 - $y3), 2));
                }
            }
            #--------------
            $dst_avg=0;
            if ($dst_px2 == 0){
                $dst_avg=$dst_px1;
            }else{
                $dst_avg=($dst_px1+$dst_px2)/2;
            }
                
            #--------------------    
            return array('s1' => $dst_px1, 's2' => $dst_px2, 'avg' => $dst_avg ); # 'avg' => $dst_avg * 2
        } else {
            return array('s1' => 0, 's2' => 0, 'avg' => 0);
        }
    }
    #----------------------------------------------------------------------------
    #---------------------------------------------------------------
    #this method converts the px value of a device equivalent to iPhone5 in order to convert to inches
    private function device_screen_adjustment($px_measure, $device_type=null){
        if( strtolower($device_type) == 'iphone6'){
            return ($px_measure * 0.89478);
        }else{
            return $px_measure;
        }
    }
    #---------------------------------------------------------------
    private function getPixelToInch($mm_specs, $fit_point, $pixels) {
        $prev_px_measure = 0;
        $prev_inch_measure = 0;        
        if(array_key_exists($fit_point, $mm_specs['pixel_conversion'])){
        foreach ($mm_specs['pixel_conversion'][$fit_point] as $px_measure => $inch_measure) {
            if ($px_measure == $pixels) {#exact match of pixel
                return $inch_measure;
            } elseif ($px_measure > $pixels) {
                if ($prev_px_measure < $pixels) {#in between values of pixel
                    if ($prev_px_measure == 0) { #compare with previous measurement                        
                        return ($px_measure - $pixels) < 5 ? $inch_measure : 0;
                    } else {
                        #grate to scale ~~~~~~~~>
                        $px_diff=$px_measure-$prev_px_measure;# diff in px
                        $inch_diff=$inch_measure-$prev_inch_measure;# diff in inches
                        $grade_scale=$inch_diff/$px_diff; # ratio of the diff
                        $current_inch_diff=$pixels-$prev_px_measure; # diff of the actual body px & prev item px
                        $current_inch_measure = $prev_inch_measure + ($grade_scale * $current_inch_diff);
                        return $current_inch_measure;
                    }
                }
            }
            $prev_px_measure = $px_measure;
            $prev_inch_measure = $inch_measure;
        }
        }else{
            return ($pixels/5)*(-1);
        }
    }
    #----------------------------------------------------------------------------
    public function getAxisArray($user, $mm_specs){
        $mm=$this->getByUser($user);
        $mm_array = json_decode($mm->getMarkerJson());
        #return $mm->getMarkerJson();
        foreach ($mm_specs['masked_marker'] as $mms_k=>$mms_v) {            
            $p1 = $mms_v['segments']['s1']['a'];
            $p2 = $mms_v['segments']['s1']['b'];
            $x1=round($mm_array[$p1][0],2);
            $y1=round($mm_array[$p1][1],2);
            $x2=round($mm_array[$p2][0],2);
            $y2=round($mm_array[$p2][1],2);    
            $dst_px1 =   sqrt(pow(($x2-$x1),2) + pow(($y2-$y1),2));        
            $comp[$mms_k]['segment_a'] = array('a'=> array('index'=>$p1, 'x'=>$x1, 'y'=>$y1),
                                   'b'=> array('index'=>$p2, 'x'=>$x2, 'y'=>$y2),
                                    'pixel_distance'=>round($dst_px1,2),
                                    );        
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
            $comp[$mms_k]['segment_b'] = array('a'=> array('index'=>$p3, 'x'=>$x3, 'y'=>$y3),
                                   'b'=> array('index'=>$p4, 'x'=>$x4, 'y'=>$y4),
                                    'pixel_distance'=>round($dst_px2,2),
                                    );        
        }
        
            #$comp[$mms_k] = $p1.'{'.$x1.','.$y1.'},'.$p2.'{'.$x2.','.$y2.'}';    #'a{x,y},b{x,y}'
            
            #$comp[$mms_k] = array($mms_v[0], $mms_v[1], $mms_v[2], $user_fitpoint_measurement, $m1);                
        }
        return $comp;
        
    }
    
   #------------------------------------------------------------------ 
   public function getMixMeasurementArray($user, $mm_specs) {
        $mm = $user->getUserMarker();
        #$comp = array('bust_px'=>0, 'chest_px'=>0, 'shoulder_across_front_px'=>0, 'waist_px'=>0, 'hip_px'=>0, 'inseam_px'=>0, 'thigh_px'=>0, 'shoulder_length_px'=>0, 'bicep_px'=>0, 'wrist_px'=>0, 'knee_px'=>0, 'calf_px'=>0, 'ankle_px'=>0, 'elbow_px'=>0, 'torso_px'=>0, 'neck_px'=>0);
        $comp = array('bust_px'=>0, 'bust_inch'=>0, 'chest_px'=>0, 'chest_inch'=>0, 'shoulder_across_front_px'=>0,  'shoulder_across_front_inch'=>0, 'waist_px'=>0, 'waist_inch'=>0, 'hip_px'=>0, 'hip_inch'=>0, 'inseam_px'=>0, 'inseam_inch'=>0, 'thigh_px'=>0, 'thigh_inch'=>0, 'shoulder_height_px'=>0, 'shoulder_height_inch'=>0, 'bicep_px'=>0, 'bicep_inch'=>0, 'wrist_px'=>0, 'wrist_inch'=>0, 'knee_px'=>0, 'knee_inch'=>0, 'calf_px'=>0, 'calf_inch'=>0, 'ankle_px'=>0, 'ankle_inch'=>0, 'elbow_px'=>0, 'elbow_inch'=>0, 'torso_px'=>0, 'torso_inch'=>0, 'neck_px'=>0,  'neck_inch'=>0);

        if ($mm) {
            $mm_array = json_decode($mm->getMarkerJson());
            foreach ($mm_specs['masked_marker'] as $mms_k => $mms_v) {
                $m1 = $this->calculate_distance($mms_v, $mm_array);
                #$comp[$mms_k . '_px'] = $m1['s2'] == 0 ? $m1['s1'] : ($m1['s1'] + $m1['s2']) / 2;
                #$comp[$mms_k . '_px'] = number_format($comp[$mms_k . '_px'], 2, '.', '') + 0;
                
                $comp[$mms_k . '_px'] = number_format($m1['avg'], 2, '.', '') + 0;
                $inch = $this->getPixelToInch($mm_specs, $mms_k, $m1['avg']);
                #incase of -ve number
                $comp[$mms_k . '_inch'] = number_format(($inch<0? $inch * (-1) : $inch), 2, '.', '') + 0;
            }
        }
        return array_merge($user->toDataArray(), $comp);        
    }
    
}
    
?>