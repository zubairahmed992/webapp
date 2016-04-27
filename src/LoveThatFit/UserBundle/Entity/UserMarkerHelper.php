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
    public function save($usermarker){
        $usermarker->setUpdatedAt(new \DateTime('now'));            
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
        if(array_key_exists('default_user', $specs) && $specs['default_user']){$user_marker->setDefaultUser($specs['default_user']);}else{$user_marker->setDefaultUser(false);}
        if(array_key_exists('image_actions', $specs) && $specs['image_actions']){$user_marker->setImageActions($specs['image_actions']);}
        
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
       $specs['image_actions']=$user_marker->getImageActions();       
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
   public function removeDefaultAccountStatus($user){
      $maskMarker=$this->findMarkerByUser($user);
      if(count($maskMarker)>0){
            $maskMarker->setDefaultUser(false);
            $this->save($maskMarker);
        }
        return $maskMarker;
   }
   
      #----------------------------------------------------------------------------
    public function findMarkerByUser($user)
    {
        return $this->repo->findMarkerByUser($user);
    }

       #----------------------------------------------------------------------------
       public function setDefaultUserAs($user, $default_user = false) {           
        $m = $this->repo->findMarkerByUser($user);
        if ($m) {
            $m->setDefaultUser($default_user);
            $this->save($m);
            return $default_user;
        } else {
            return false;
        }
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
    #----------------------------------------------------------------------------    
    public function arrayToObject($user, $ar){
       
       $class = $this->class;
       $user_marker = new $class();
       array_key_exists('svg_paths', $ar)? $user_marker->setSvgPaths($ar['svg_paths']):'';
       array_key_exists('marker_json', $ar)? $user_marker->setMarkerJson($ar['marker_json']):'';
       array_key_exists('default_svg_paths', $ar)? $user_marker->setDefaultMarkerSvg($ar['default_svg_paths']):'';       
       array_key_exists('mask_x', $ar)? $user_marker->setMaskX($ar['mask_x']):'';
       array_key_exists('mask_y', $ar)? $user_marker->setMaskY($ar['mask_y']):'';
       array_key_exists('rect_x', $ar)? $user_marker->setRectX($ar['rect_x']):'';
       array_key_exists('rect_y', $ar)? $user_marker->setRectY($ar['rect_y']):'';
       array_key_exists('rect_height', $ar)? $user_marker->setRectHeight($ar['rect_height']):'';
       array_key_exists('rect_width', $ar)? $user_marker->setRectWidth($ar['rect_width']):'';
       $user_marker->setDefaultMarkerJson('[278.9083138087104,52.82931905298821],[281.3183138087104,88.10550958712773],[280.0823138087104,103.62837490433701],[288.1063138087104,118.02535338526721],[295.2083138087104,148.5137301106626],[288.7743138087104,182.68000383456956],[222.56631380871042,207.91663749456956],[195.1829538087104,323.1315919058254],[187.4569538087104,386.44476016722075],[179.00695380871042,543.7814567774533],[188.4603138087104,627.7726147638721],[198.3323138087104,603.1109020415463],[202.21831380871038,543.7790612735463],[225.11295380871042,394.86735190415095],[232.48895380871042,331.6356307755928],[236.87031380871042,286.08592248573234],[235.84231380871043,307.5232869492673],[230.13431380871043,384.0572412732673],[228.59831380871043,420.56711631949986],[226.1063138087104,465.90202775903475],[226.52631380871043,545.1620655291744],[238.14799380871042,638.4565621889882],[254.17879380871037,763.5473807074069],[253.45479380871043,801.2741717383836],[252.47879380871044,871.7043821074069],[265.3656338087104,970.9596909890814],[269.3406438087104,1028.923699026198],[265.0483138087104,1056.9534902417329],[274.2883138087104,1100.9678632651746],[300.8663138087104,1056.9678632651746],[298.1709138087104,1028.9284900340117],[308.30231380871044,871.6732405566165],[306.5671938087104,795.7094161624768],[317.1994338087104,638.4948902515],[319.8643138087104,572.4756010765233],[322.54919380871036,638.3918835835001],[332.83543380871043,795.3668591037791],[331.1563138087104,871.4480631893605],[341.55571380871044,1028.9692136004305],[339.2943138087104,1056.9007891557792],[367.3543138087104,1100.9606767534535],[373.83431380871036,1056.9606767534535],[370.38798380871043,1028.892557475407],[376.08183380871037,970.998019051593],[387.58983380871035,872.0517301739187],[386.3518338087104,801.1424190235],[385.8338338087104,763.8300501684303],[401.77663380871036,638.4110476147558],[412.69631380871044,545.1884160721512],[413.9443138087104,465.95472884498844],[412.47031380871044,420.5814893429419],[410.5923138087104,384.18180747643027],[403.8343138087105,307.5855700508489],[402.4763138087104,286.22965272015125],[422.82871090127327,320.6474039481593],[440.8559446492608,381.6741516710141],[488.6180749462691,524.5952489144759],[503.13689525381,582.3285820642939],[516.7836596587007,605.1550519264335],[514.2955098818866,520.2486851430875],[478.7933664194267,366.7356720659809],[460.16710067202206,305.6003936643956],[415.5143138087104,207.8711229203372],[350.72831380871037,182.55064662359305],[344.4603138087104,148.7820265482442],[351.3803138087104,117.95827927587209],[359.21431380871036,103.76012761922095],[358.01231380871036,88.201329743407],[360.67631380871035,52.838901068616345],[319.8643138087104,23.427702600058197]');
       
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
    #---------------------------------------------------------------------------
    
  public function getDeviceTypeForModel($model) {      
        $iphone5_models = array('iPhone5', 'iphone5', 'iPhone5c', 'iphone5c', 'iPhone5s', 'iphone5s', 'iphonese', 'iPhoneSE');
        $iphone6_models = array('iPhone6', 'iphone6', 'iPhone6Plus', 'iphone6plus', 'iPhone6s', 'iphone6s', 'iPhone6sPlus', 'iphone6splus');

        if (in_array($model, $iphone5_models)) {
            return 'iphone5';
        } elseif (in_array($model, $iphone6_models)) {
            return 'iphone6';
        } else {
            return $model;
        }
    }
    #---------------------------------------------------------------------------
    
  public function getDefaultMask($gender, $body_type){
      $mask_type_array = $this->getMaskedMarkerSpecs();      
      if(array_key_exists($gender,$mask_type_array['mask_type'])){
          if(array_key_exists($body_type, $mask_type_array['mask_type'][$gender])){
              return $mask_type_array['mask_type'][$gender][$body_type];
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
            //return ($px_measure * 0.89478);
            return ($px_measure * 0.8278);
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