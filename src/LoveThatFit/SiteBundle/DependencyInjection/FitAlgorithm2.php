<?php

namespace LoveThatFit\SiteBundle\DependencyInjection;
use LoveThatFit\AdminBundle\Entity\SizeHelper;
class FitAlgorithm2 {

    private $user;
    private $product;
    private $size_helper;
    private $scale=array(
        'below_min' => array('status'=>5, 'start'=>0, 'end'=>0,'low_point'=>null, 'high_point'=>'at_min',  'message'=>'below_min'),
        'at_min' => array('status'=>4, 'start'=>0, 'end'=>0,'low_point'=>'at_min', 'high_point'=>'at_min',  'message'=>'at_min'),
        'between_min_low' => array('status'=>-3, 'start'=>0, 'end'=>0.8,'low_point'=>'calc_min_body_measurement', 'high_point'=>'ideal_body_size_low',  'message'=>'between_min_low'),
        'at_low' => array('status'=>2, 'start'=>0.8, 'end'=>0.8,'low_point'=>'at_low', 'high_point'=>'at_low',  'message'=>'at_low'),
        'between_low_mid' => array('status'=> 1 , 'start'=>0.8, 'end'=>1,'low_point'=>'ideal_body_size_low', 'high_point'=>'mid_low_high',  'message'=>'between_low_mid'),
        'at_mid' => array('status'=>0, 'start'=>1, 'end'=>1,'low_point'=>'mid_low_high', 'high_point'=>'mid_low_high',  'message'=>'at_mid'),
        'between_mid_high' => array('status'=>-1, 'start'=>0.8, 'end'=>1,'low_point'=>'mid_low_high', 'high_point'=>'ideal_body_size_high',  'message'=>'between_mid_high'),
        'at_high' => array('status'=>-2, 'start'=>0.8, 'end'=>0.8,'low_point'=>'at_high', 'high_point'=>'at_high',  'message'=>'at_high'),
        'between_high_max' => array('status'=>-3, 'start'=>0, 'end'=>0.8,'low_point'=>'ideal_body_size_high', 'high_point'=>'calc_max_body_measurement',  'message'=>'between_high_max'),        
        'at_max' => array('status'=>-4, 'start'=>0, 'end'=>0,'low_point'=>'at_max', 'high_point'=>'at_max',  'message'=>'at_max'),        
        'beyond_max' => array('status'=>-5, 'start'=>0, 'end'=>0,'low_point'=>'at_max', 'high_point'=>null,  'message'=>'beyond_max'),        
    );
#-----------------------------------------------------

    function __construct($user = null, $product = null) {
        $this->user = $user;
        $this->product = $product;
        $this->size_helper = new SizeHelper();
    }

#-----------------------------------------------------

    function getFeedBackJSON() {
        return json_encode($this->getFeedBack());
    }

#-----------------------------------------------------

    function getFeedBack() {
        if ($this->product->fitPriorityAvailable()) {
            $cm = $this->array_mix();
            return $cm;
        }
    }

#-----------------------------------------------------
    private function array_mix($sizes = null) {
        if ($sizes == null) {
            $sizes = $this->product->getProductSizes();
        }
        $body_specs = $this->user->getMeasurement()->getArray();
        $fb = array();
        $fpwp = $this->product->getFitPointsWithPriority();
        foreach ($sizes as $size) {
            $size_specs = $size->getMeasurementArray(); #~~~~~~~~>
            $size_identifier = $size->getDescription();
            $fb[$size_identifier]['id'] = $size->getId();
            $fb[$size_identifier]['description'] = $size_identifier;
            $fb[$size_identifier]['title'] = $size->getTitle();
            $fb[$size_identifier]['body_type'] = $size->getBodyType();
            $fb[$size_identifier]['fit_index']=0;
            $fb[$size_identifier]['min_fx'] =0;
            $fb[$size_identifier]['max_fx'] =0;
            $fb[$size_identifier]['high_fx'] =0;
            $fb[$size_identifier]['low_fx'] =0;
            $fb[$size_identifier]['avg_fx'] =0;
            $fb[$size_identifier]['status'] =6;
            
            if (is_array($size_specs)) {
             foreach($fpwp as $pfp_key=>$pfp_value){
                    if (array_key_exists($pfp_key, $size_specs)) {
                        $fb[$size_identifier]['fit_points'][$pfp_key] =
                                $this->get_fit_point_array($size_specs[$pfp_key], $body_specs);                        
                        $fb[$size_identifier]['min_fx'] =$fb[$size_identifier]['min_fx']+$fb[$size_identifier]['fit_points'][$pfp_key]['min_fx'];
                        $fb[$size_identifier]['max_fx'] =$fb[$size_identifier]['max_fx']+$fb[$size_identifier]['fit_points'][$pfp_key]['max_fx'];
                        $fb[$size_identifier]['high_fx'] =$fb[$size_identifier]['high_fx']+$fb[$size_identifier]['fit_points'][$pfp_key]['high_fx'];
                        $fb[$size_identifier]['low_fx'] =$fb[$size_identifier]['low_fx']+$fb[$size_identifier]['fit_points'][$pfp_key]['low_fx'];
                        $fb[$size_identifier]['avg_fx'] =$fb[$size_identifier]['avg_fx']+$fb[$size_identifier]['fit_points'][$pfp_key]['avg_fx'];
                        
                        if ($fb[$size_identifier]['fit_points'][$pfp_key]['status']==$this->status['beyond_max']){
                            $fb[$size_identifier]['status'] =$this->status['beyond_max'];
                            $fb[$size_identifier]['fit_index'] = 0;
                        }elseif($fb[$size_identifier]['status'] != $this->status['beyond_max']){
                            $fb[$size_identifier]['fit_index'] = $fb[$size_identifier]['fit_index']+$fb[$size_identifier]['fit_points'][$pfp_key]['body_fx'];                        
                        }
                        
                    }else{
                        $fb[$size_identifier]['status'] =$this->status['product_measurement_not_available'];
                    }
             }
            }
            
        }
        $sorted_array=$this->array_sort($fb);
        $recommendation=$this->get_recommended_size($fb);
        return array('feedback' => $sorted_array, 'recommendation'=>  $recommendation);
        #return array('feedback' => $this->array_sort($fb));
    }
    ###################################################
    
    private function get_recommended_size($sizes){
        $rec_size=null;
        $fit_greatest_index=0;
        foreach ($sizes as $size) {
            if ($fit_greatest_index<$size['fit_index']){
                $fit_greatest_index=$size['fit_index'];
                $rec_size=$size;
            }
        }
        return $rec_size;
    }
    # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    private function get_relevant_body_measurement($fp_specs, $body_specs){
        $body = 0;
        if ($fp_specs['fit_point'] == 'waist' && $this->product->getGender() == 'm' && $this->product->getClothingType()->getTarget()=='bottom'){
            if (array_key_exists('belt', $body_specs) && $body_specs['belt']!=null && $body_specs['belt'] > 0){
                $body = $body_specs['belt'];
            }else{
                $body = array_key_exists($fp_specs['fit_point'], $body_specs) ? $body_specs[$fp_specs['fit_point']] : 0;    
            }
        }else{
            $body = array_key_exists($fp_specs['fit_point'], $body_specs) ? $body_specs[$fp_specs['fit_point']] : 0;
        }
        return $body;
    }
    
# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    private function get_fit_point_array($fp_specs, $body_specs) {
        
        $max_min=$this->calculate_maxmin($fp_specs);
        $body = $this->get_relevant_body_measurement($fp_specs, $body_specs);
        $fp=($fp_specs['fit_priority']/10);

        $fp_measurements = array('fit_point' => $fp_specs['fit_point'],
            'label' => $this->getFitPointLabel($fp_specs['fit_point']),
            'calc_min_body_measurement' => $max_min['calc_min_body_measurement'],
            'min_body_measurement' => $fp_specs['min_body_measurement'],
            'ideal_body_size_low' => $fp_specs['ideal_body_size_low'],
            'mid_low_high' => $max_min['mid_low_high'],
            'ideal_body_size_high' => $fp_specs['ideal_body_size_high'],
            'max_body_measurement' => $fp_specs['max_body_measurement'],
            'calc_max_body_measurement' => $max_min['calc_max_body_measurement'],
            'fit_priority' => $fp,
            'body_measurement' => $body,                 
            'min_fx' => $this->scale['between_min_low']['start'] * $fp,
            'max_fx' => $this->scale['between_high_max']['start'] * $fp,
            'high_fx' => $this->scale['between_mid_high']['start'] * $fp,
            'low_fx' => $this->scale['between_low_mid']['start'] * $fp,
            'avg_fx' => $fp,
        );
        $message_array=$this->calculate_fitindex($fp_measurements);
        $fp_measurements['status'] = $message_array['status'];
        $fp_measurements['message'] = $message_array['message'];                
        $fp_measurements['body_fx'] = $message_array['body_fx'];        
        return $fp_measurements;
    }
#---------------------------------------------------    
private function calculate_maxmin($fp_specs){
     
        $ar['mid_low_high'] = ($fp_specs['ideal_body_size_low'] + $fp_specs['ideal_body_size_high']) / 2;
        $grading_scale = ($fp_specs['ideal_body_size_high'] - $fp_specs['ideal_body_size_low']) * 2.5;        
        
        $ar['grading_scale'] = $grading_scale;
        $ar['calc_min_body_measurement'] = $ar['mid_low_high'] - $grading_scale;
        $ar['calc_max_body_measurement'] = $ar['mid_low_high'] + $grading_scale;
       
      #   $ar['calc_min_body_measurement'] =I4-((K4-G4)*2.5)
        return $ar;
        
}
#---------------------------------------------------
private function calculate_fitindex($fp_specs){
    $fp_fx=0;       
    $fp_scale=array();
    
    if ($fp_specs['body_measurement'] == $fp_specs['mid_low_high']) {
            $fp_scale = $this->scale['at_mid'];
        } elseif ($fp_specs['mid_low_high'] > $fp_specs['body_measurement']) {
            $fp_scale = $this->scale['below_min'];
            
            if ($fp_specs['body_measurement'] > $fp_specs['ideal_body_size_low']) {
                $fp_fx=  $this->grade_to_scale($fp_specs);
                $fp_scale = $this->scale['between_low_mid'];
                
            }elseif ($fp_specs['body_measurement'] > $fp_specs['calc_min_body_measurement']) {
                $fp_fx=  $this->grade_to_scale($fp_specs);
                $fp_scale= $this->scale['between_min_low'];  
            }
        } elseif ($fp_specs['mid_low_high'] < $fp_specs['body_measurement']) {
            $fp_scale = $this->scale['beyond_max'];
            
            if ($fp_specs['body_measurement'] < $fp_specs['ideal_body_size_high']) {
                $fp_fx=  $this->grade_to_scale($fp_specs);                                
                $fp_scale= $this->scale['between_mid_high'];
                
            } elseif ($fp_specs['body_measurement'] < $fp_specs['max_body_measurement']) {
                $fp_fx=  $this->grade_to_scale($fp_specs);                
                $fp_scale = $this->scale['between_high_max'];
            } 
        }        
        
        $fx = $this->limit_num($fp_fx);        
        return array('body_fx'=>$fx, 'message' => $fp_scale['message'], 'status'=>$fp_scale['status'],
            );
}
    # -----------------------------------------------------
    //avgFX-((body-avg)/(maxCALC-avg))*(avgFX-maxCALC FX)
#y = 1 + (x-A)*(10-1)/(B-A)
 private function _grade_to_scale($fp_specs, $position) {    
        $fs = 1 + (($fp_specs['body_measurement'] - $fp_specs[$position['low_point']]) * ($position['end'] - $position['start'])) / ($fp_specs[$position['high_point']] - $fp_specs[$position['low_point']]);                
        return $this->limit_num($fs);
    }
 # -----------------------------------------------------
    private function grade_to_scale($fp_specs) {        
        $findex   =0;   
        if($fp_specs['body_measurement']>=$fp_specs['mid_low_high']){
         $findex   =$fp_specs['avg_fx']-((($fp_specs['body_measurement']-$fp_specs['mid_low_high'])/($fp_specs['calc_max_body_measurement']-$fp_specs['mid_low_high']))*($fp_specs['avg_fx']-$fp_specs['max_fx']));
         }elseif ($fp_specs['body_measurement']<=$fp_specs['mid_low_high']) {
         $findex   =$fp_specs['avg_fx']-((($fp_specs['mid_low_high']-$fp_specs['body_measurement'])/($fp_specs['mid_low_high']-$fp_specs['calc_min_body_measurement']))*($fp_specs['avg_fx']-$fp_specs['min_fx']));   
        }else{
         $findex   =1;   
        }

        return $this->limit_num($findex);
    }
    
    #------------------------------------------------
       private function limit_num($n){        
        if ($n == round($n)) {
          return $n;
        }else{
        return number_format($n, 2, '.', '');
        }
    }
# -----------------------------------------------------
    private function array_sort($sizes) {
        if ($this->product){
            $size_titles = $this->getSizeTitleArray($this->product->getGender(), $this->product->getSizeTitleType());
            $size_types = $this->getSizeTypes($this->product->getGender());
            $fb = array();
            $size_identifier = '';
            if (is_array($size_titles) && count($size_titles) > 0) {
                if (is_array($size_titles) && count($size_titles) > 0) {
                    foreach ($size_types as $stype) {
                        foreach ($size_titles as $stitle) {
                            $size_identifier = ucfirst($stype) . ' ' . $stitle;
                            if (array_key_exists($size_identifier, $sizes))
                                $fb[$size_identifier] = $sizes[$size_identifier];
                        }
                    }
                }
            }
            return $fb;
        }
    }
    #----------------------------------------------------------       
    private function snakeToNormal($str) {
        return str_replace('_', ' ', ucfirst($str));
    }
 
    #----------------------------------------------------------       
    public function getSizeTitleArray($gender = 'f', $type = 'numbers') {
        $gender = strtolower($gender);
        $type = strtolower($type);
        
        if ($gender == 'f' && ($type == 'letters' || $type == 'letter')) {//letters
            return $this->size_helper->getWomanLetterSizes(false);
        } else if ($gender == 'f' && ($type == 'number' || $type == 'numbers')) {//$female_standard
            return $this->size_helper->getWomanNumberSizes(false);
        } else if ($gender == 'f' && $type == 'waist') {//$female_waist
            return $this->size_helper->getWomanWaistSizes(false);
        }
        else if ($gender == 'f' && $type == 'bra') {//$female_bra
            return $this->size_helper->getWomanBraSizes(false);
        } 
        else if ($gender == 'm' && ($type == 'letters' || $type == 'letter')) {//letters
            return $this->size_helper->getManLetterSizes(false);    
        }
        else if ($gender == 'm' && $type == 'chest') {//man Chest
            return $this->size_helper->getManChestSizes(false);
        } else if ($gender == 'm' && $type == 'waist') {//man bottom
            return $this->size_helper->getManWaistSizes(false);
        } else if ($gender == 'm' && $type == 'neck') {//man neck
            return $this->size_helper->getManNeckSizes(false);
        }else if ($gender == 'm' && $type == 'shirt') {//man shirt
            return $this->size_helper->getManShirtSizes(false);
        }
    }
        /*
         Man: letter, chest, shirt, neck, waist
         Woman: letter, number, waist, bra
         */

    #------------------------------------------------
    public function getSizeTypes($gender='f') {
        return $this->size_helper->getFitType($gender, false);        
    }

    #----------------------------------------------------------       
    private function getFitPointLabel($str) {
        $str = str_replace(' ', '_', strtolower($str));
        switch ($str) {
            case 'shoulder_across_back':
                return 'Shoulder';
                break;
            default:
                return $this->snakeToNormal($str);
                break;
        }
    }

    #----------------------------------------------------------       
    var $status = array(
        'fit_point_dose_not_match' => -8,
        'body_measurement_not_available' => -7,
        'product_measurement_not_available' => -6,
        'beyond_max' => -5,
        'at_max' => -4,
        'between_max_high' => -3,
        'at_high' => -2,
        'between_high_mid' => -1,
        'at_mid' => 0,
        'between_mid_low' => 1,
        'at_low' => 2,
        'between_low_min' => 3,
        'at_min' => 4,
        'below_min' => 5,
        'anywhere_below_max' => 6,
    );
    
  
}
