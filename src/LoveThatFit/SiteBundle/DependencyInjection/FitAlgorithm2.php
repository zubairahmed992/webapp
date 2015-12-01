<?php

namespace LoveThatFit\SiteBundle\DependencyInjection;
use LoveThatFit\AdminBundle\Entity\SizeHelper;
class FitAlgorithm2 {

    private $user;
    private $product;
    private $size_helper;
    private $scale=array(
        'below_min' => array('status'=>5, 'start'=>0, 'end'=>0,'low_point'=>null, 'high_point'=>'at_min',  'message'=>'Extra Loose', 'status_text'=>'below_min'),
        'at_min' => array('status'=>4, 'start'=>0, 'end'=>0,'low_point'=>'at_min', 'high_point'=>'at_min',  'message'=>'Extra Loose', 'status_text'=>'at_min'),
        'between_min_low' => array('status'=>3, 'start'=>0, 'end'=>0.8,'low_point'=>'calc_min_body_measurement', 'high_point'=>'ideal_body_size_low',  'message'=>'Loose', 'status_text'=>'between_min_low'),
        'at_low' => array('status'=>2, 'start'=>0.8, 'end'=>0.8,'low_point'=>'at_low', 'high_point'=>'at_low',  'message'=>'Loose', 'status_text'=>'at_low'),
        'between_low_mid' => array('status'=> 1 , 'start'=>0.8, 'end'=>1,'low_point'=>'ideal_body_size_low', 'high_point'=>'fit_model',  'message'=>'Perfect Fit', 'status_text'=>'between_low_mid'),
        'at_mid' => array('status'=>0, 'start'=>1, 'end'=>1,'low_point'=>'fit_model', 'high_point'=>'fit_model',  'message'=>'Perfect Fit', 'status_text'=>'at_mid'),
        'between_mid_high' => array('status'=>-1, 'start'=>0.8, 'end'=>1,'low_point'=>'fit_model', 'high_point'=>'ideal_body_size_high',  'message'=>'Perfect Fit', 'status_text'=>'between_mid_high'),
        'at_high' => array('status'=>-2, 'start'=>0.8, 'end'=>0.8,'low_point'=>'at_high', 'high_point'=>'at_high',  'message'=>'close fitting', 'status_text'=>'at_high'),
        'between_high_max' => array('status'=>-3, 'start'=>0, 'end'=>0.8,'low_point'=>'ideal_body_size_high', 'high_point'=>'calc_max_body_measurement',  'message'=>'close fitting', 'status_text'=>'between_high_max'),        
        'at_max' => array('status'=>-4, 'start'=>0, 'end'=>0,'low_point'=>'at_max', 'high_point'=>'at_max',  'message'=>'Too Small', 'status_text'=>'at_max'),        
        'beyond_max' => array('status'=>-5, 'start'=>0, 'end'=>0,'low_point'=>'at_max', 'high_point'=>null,  'message'=>'Too Small', 'status_text'=>'beyond_max'),        
        'user_measurement_missing' => array('status'=>-6, 'start'=>0, 'end'=>0,'low_point'=>null, 'high_point'=>null,  'message'=>'User measurement not provided', 'status_text'=>'user_measurement_missing'),        
        'product_measurement_missing' => array('status'=>-7, 'start'=>0, 'end'=>0,'low_point'=>null, 'high_point'=>null,  'message'=>'Product measurement missing', 'status_text'=>'product_measurement_missing'),        
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
    function getSizeFeedBack($size) {

        if ($size == null || !isset($size))
            return 'no size';

        $this->product = $size->getProduct();
        $fb = $this->getFeedBack();
        if (array_key_exists('recommendation', $fb)) {
            if ($fb['recommendation']['id'] == $size->getId()) { # if it matches best fit            
                return array(
                    'feedback' => $fb['recommendation'],
                );
            }
        }
        if (array_key_exists('feedback', $fb)) {
        foreach ($fb['feedback'] as $size_fb) {
            if ($size_fb['id'] == $size->getId()) {
                #return array($size_fb['description'] => $size_fb);
                  if (array_key_exists('recommendation', $fb)) {
                        return array(
                            'feedback' => $size_fb,
                            'recommendation' => $fb['recommendation'],
                        );      
                  }else{
                    return array(
                          'feedback' => $size_fb,
                      );
                  }
      
                
            }
        }
        }
        return null;
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
            $fb[$size_identifier]['variance']=0;
            $fb[$size_identifier]['fits']=true;
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
                        $fb[$size_identifier]['variance']=$this->calculate_accumulated_variance($fb[$size_identifier]['fit_points'][$pfp_key]['variance'], $fb[$size_identifier]['variance']);
                        
                        if ($fb[$size_identifier]['fit_points'][$pfp_key]['status']==$this->status['beyond_max']){
                            $fb[$size_identifier]['status'] =$this->status['beyond_max'];
                            $fb[$size_identifier]['fit_index'] = 0;
                            $fb[$size_identifier]['fits']=false;
                        }elseif($fb[$size_identifier]['status'] != $this->status['beyond_max']){
                            $fb[$size_identifier]['fit_index'] = $fb[$size_identifier]['fit_index']+$fb[$size_identifier]['fit_points'][$pfp_key]['body_fx'];                        
                        }
                        
                    }else{
                        $fb[$size_identifier]['status'] =$this->status['product_measurement_not_available'];
                    }
             }
             $fb[$size_identifier]['message'] =$this->get_fitting_alert_message($fb[$size_identifier]['status']);
             $hem_bits = $this->get_hem_advice($size_specs, $body_specs);
             if ($hem_bits) $fb[$size_identifier]['hem_advice'] = $hem_bits;
            }
            
        }
        $sorted_array=$this->array_sort($fb);
        $recommendation = $this->get_recommended_size($sorted_array);
        if($recommendation==null){
            $recommendation=$this->get_recommended_loose_size($sorted_array);
        }
        if($recommendation==null){
            $recommendation=end($sorted_array);
        }
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
     ###################################################
    
    private function get_recommended_loose_size($sizes){
        $rec_size=null;
        $lowest_variance=999;
        foreach ($sizes as $size) {            
           if($size['status']!=$this->status['beyond_max']){ 
            if ($lowest_variance>$size['variance']){
                $lowest_variance=$size['variance'];
                $rec_size=$size;
            }
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
        #$max_min=$this->calculate_maxmin($fp_specs);
        $body = $this->get_relevant_body_measurement($fp_specs, $body_specs);
        $fp=($fp_specs['fit_priority']/10);

        $fp_measurements = array('fit_point' => $fp_specs['fit_point'],
            'label' => $this->getFitPointLabel($fp_specs['fit_point']),
            'calc_min_body_measurement' => $fp_specs['min_calculated'],
            'min_body_measurement' => $fp_specs['min_body_measurement'],
            'ideal_body_size_low' => $fp_specs['ideal_body_size_low'],
            'fit_model' => $fp_specs['fit_model'],
            'ideal_body_size_high' => $fp_specs['ideal_body_size_high'],
            'max_body_measurement' => $fp_specs['max_body_measurement'],
            'calc_max_body_measurement' => $fp_specs['max_calculated'],
            'grade_rule' => $fp_specs['grade_rule'],
            'fit_priority' => $fp,
            'body_measurement' => $body,                 
            'min_fx' => $this->scale['between_min_low']['start'] * $fp,
            'max_fx' => $this->scale['between_high_max']['start'] * $fp,
            'high_fx' => $this->scale['between_mid_high']['start'] * $fp,
            'low_fx' => $this->scale['between_low_mid']['start'] * $fp,
            'avg_fx' => $fp,
            'garment_measurement_flat' => $fp_specs['garment_measurement_flat'],
            'garment_measurement_stretch_fit' => $fp_specs['garment_measurement_stretch_fit'],
        );
        $message_array=$this->calculate_fitindex($fp_measurements);
        $fp_measurements['fits'] = $message_array['fits'];
        $fp_measurements['status'] = $message_array['status'];
        $fp_measurements['message'] = $message_array['message'];                
        $fp_measurements['status_text'] = $message_array['status_text'];
        $fp_measurements['fitting_alert'] = $message_array['message'];                
        $fp_measurements['body_fx'] = $message_array['body_fx'];   
        $fp_measurements['variance'] = $this->calculate_variance($fp_measurements);
        return $fp_measurements;
    }
    
#---------------------------------------------------    
private function calculate_maxmin($fp_specs){     
        $ar['fit_model'] = ($fp_specs['ideal_body_size_low'] + $fp_specs['ideal_body_size_high']) / 2;
        $grading_scale = ($fp_specs['ideal_body_size_high'] - $fp_specs['ideal_body_size_low']) * 2.5;                
        $ar['grading_scale'] = $grading_scale;
        $ar['calc_min_body_measurement'] = $ar['fit_model'] - $grading_scale;
        $ar['calc_max_body_measurement'] = $ar['fit_model'] + $grading_scale;
        return $ar;       
}
#---------------------------------------------------
private function calculate_fitindex($fp_specs){
    $fp_fx=0;       
    $fp_scale=array();
    $fits=false;
    if ($fp_specs['body_measurement'] == $fp_specs['fit_model']) {
            $fp_scale = $this->scale['at_mid'];
            $fp_fx = $fp_specs['avg_fx'];
            $fits=true;
        } elseif ($fp_specs['fit_model'] > $fp_specs['body_measurement']) {
            $fp_scale = $this->scale['below_min'];
            if ($fp_specs['body_measurement'] > $fp_specs['ideal_body_size_low']) {
                $fp_fx = $this->grade_to_scale($fp_specs);
                $fp_scale = $this->scale['between_low_mid'];
                $fits=true;
            } elseif ($fp_specs['body_measurement'] > $fp_specs['calc_min_body_measurement']) {
                $fp_fx = $this->grade_to_scale($fp_specs);
                $fp_scale = $this->scale['between_min_low'];
                $fits=true;
            }
        } elseif ($fp_specs['fit_model'] < $fp_specs['body_measurement']) {
            $fp_scale = $this->scale['beyond_max'];

            if ($fp_specs['body_measurement'] < $fp_specs['ideal_body_size_high']) {
                $fp_fx = $this->grade_to_scale($fp_specs);
                $fp_scale = $this->scale['between_mid_high'];
                $fits=true;
            } elseif ($fp_specs['body_measurement'] < $fp_specs['max_body_measurement']) {
                $fp_fx = $this->grade_to_scale($fp_specs);
                $fp_scale = $this->scale['between_high_max'];
                $fits=true;
            }
        }

        $fx = $this->limit_num($fp_fx);        
        return array('body_fx'=>$fx, 'message' => $fp_scale['message'], 'status'=>$fp_scale['status'],
            'fits'=>$fits, 'status_text'=>$fp_scale['status_text'],
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
        if($fp_specs['body_measurement']>$fp_specs['fit_model']){
            if (($fp_specs['calc_max_body_measurement']-$fp_specs['fit_model'])==0){
                $findex=0;
            }else{
                $findex=$fp_specs['avg_fx']-((($fp_specs['body_measurement']-$fp_specs['fit_model'])/($fp_specs['calc_max_body_measurement']-$fp_specs['fit_model']))*($fp_specs['avg_fx']-$fp_specs['max_fx']));
            }
         }elseif ($fp_specs['body_measurement']<$fp_specs['fit_model']) {
             if (($fp_specs['fit_model']-$fp_specs['calc_min_body_measurement'])==0){
                $findex=0;
            }else{
                $findex   =$fp_specs['avg_fx']-((($fp_specs['fit_model']-$fp_specs['body_measurement'])/($fp_specs['fit_model']-$fp_specs['calc_min_body_measurement']))*($fp_specs['avg_fx']-$fp_specs['min_fx']));   
            }            
        }else{
            $findex   = $fp_specs['avg_fx'];   
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
    
  #-------------------------------------------------------------
  #----------------------------------------------------------

    private function calculate_variance($fp_mix) {
        $body = $fp_mix['body_measurement'];
        $item = $fp_mix['fit_model'];
        $priority =  $fp_mix['fit_priority'];
        if ($item > 0 && $body > 0) {
            $diff = $item - $body;
            if ($diff == 0) {
                $v = 0;                
            } else {
                $diff_percent = ($diff / $item) * 100; # how much (in %age of item measurement) the difference is?
                $v = number_format(($priority * $diff_percent) / 100, 2, '.', '');
            }
            return $v;
        }else
            return;
    }
 #----------------------------------------------------------
    private function calculate_accumulated_variance($variance, $accumulated) {        
        if($variance<0){
            $accumulated = $accumulated + ($variance * (-1));
        }elseif($variance>0){
            $accumulated = $accumulated + $variance;
        }        
        return $accumulated;
    }  
    
        #----------------------------------------------------------       
    private function get_fitting_alert_message($id) {
        
        switch ($id) {
            case $this->status['fit_point_dose_not_match'] :
                return 'Fitting point dose not exists';
                break;
            case $this->status['body_measurement_not_available'] :
                return 'Member measurement not provided';
                break;
            case $this->status['product_measurement_not_available'] :
                return 'Product measurement missing';
                break;
            case $this->status['beyond_max'] :
                return 'Too Small';
                break;
            case $this->status['at_max'] :
                return 'tight fitting';
                break;
            case $this->status['between_max_high'] :
                return 'close fitting';
                break;
            case $this->status['at_high'] :
                return 'close fitting';
                break;
            case $this->status['between_high_mid'] :
                return 'Perfect Fit';
                break;
            case $this->status['at_mid'] :
                return 'Perfect Fit';
                break;
            case $this->status['between_mid_low'] :
                return 'Perfect Fit';
                break;
            case $this->status['at_low'] :
                return 'Loose';
                break;
            case $this->status['between_low_min'] :
                return 'Loose';
                break;
            case $this->status['at_min'] :
                return 'Loose';
                break;
            case $this->status['below_min'] :
                return 'Extra Loose';
                break;
            case $this->status['anywhere_below_max'] :
                return 'Tight at some points & loose at others';
                break;
        }        
    }
     #----------------------------------------------------------
    private function get_accumulated_status($accumulated, $current) {
        #accumulated is perfect fit -----------------------
        if ($accumulated == $this->status['between_high_mid'] ||
                $accumulated == $this->status['at_mid'] ||
                    $accumulated == $this->status['between_mid_low'])
            return $current;
        
        #current is perfect fit -----------------------
        if ($current == $this->status['between_high_mid'] ||
                $current == $this->status['at_mid'] ||
                    $current == $this->status['between_mid_low'])
            return $accumulated;
        # body not available in either ---------------------------------
        if ($accumulated == $this->status['body_measurement_not_available'] ||
                $accumulated == $this->status['product_measurement_not_available']) 
            return $accumulated;
        # product not available in either ---------------------------------
        if ($current == $this->status['body_measurement_not_available'] ||
                $current == $this->status['product_measurement_not_available']) 
            return $current;
        # accumulated beyond Max ---------------------------------
        if ($accumulated == $this->status['beyond_max'])
            return $accumulated;
        # current beyond Max ---------------------------------
        if ($current == $this->status['beyond_max'])
            return $current;

        if ($this->is_loose_status($accumulated)) { # accumulated loose
            if ($this->is_loose_status($current)) {
                return $accumulated >= $current ? $accumulated : $current; # greater will be returned                 
            } else {# Remaining b/w 1st & 2nd half of High-Max
                return $this->status['anywhere_below_max'];
            }
        }
        if ($this->is_loose_tight_status($accumulated)) { #accumulated tight or loose
            if ($accumulated == $this->status['first_half_high_max'] ||
                    $accumulated == $this->status['second_half_high_max']) {
                if ($this->is_loose_status($current)) {
                    return $this->status['anywhere_below_max'];
                } else { # current Remaining b/w 1st & 2nd half of High-Max
                    return $accumulated <= $current ? $accumulated : $current; # greater will be returned                 
                }
            } else { #accumulated=anywhere_below_max
                return $this->status['anywhere_below_max'];
            }
        }
    } 
    
    #----------------------------------------------------------
    private function is_loose_status($status) {
        if ($status == $this->status['below_low'] ||
                $status == $this->status['below_min']) {
            return true;
        } else {
            return false;
        }
    }

    #----------------------------------------------------------

    private function is_loose_tight_status($status) {
        if ($status == $this->status['first_half_high_max'] ||
                $status == $this->status['second_half_high_max'] ||
                $status == $this->status['anywhere_below_max']) {
            return true;
        } else {
            return false;
        }
    }
    
    #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~    
    #~~~~~~~~~~~~~~~~~~~~~~~~~~~> Hem Bits
    #------------------------------------------------     
    #------------------------------------------------
    //                  Hem Advice 
    #------------------------------------------------
 
    private function cut_to_natural_waste($hem_length) {
        if ($hem_length == null || $hem_length == 0) {
            return $hem_length;
        }

        if ($this->product->getClothingType() == 'skirt') {
          $rise = $this->product->getRise();
            switch ($rise) {
                case 'high_rise':
                    $hem_length = $hem_length + 2.25;
                    break;
                case 'mid_rise':
                    $hem_length = $hem_length - 3.5;
                    break;
                case 'low_rise':
                    $hem_length = $hem_length - 6.5;
                    break;
                case 'ultra_low_rise':
                    $hem_length = $hem_length;
                    break;
                default:
                    break;
            }
        }
        return $hem_length;
    }
    #-------------------------------------------------------------
    private function get_hem_advice($item_specs, $body_specs) {
        $clothing_type = $this->product->getClothingType();

        if ($clothing_type->getName() == 'trouser' ||
                $clothing_type->getName() == 'jean') {
            return $this->get_inseam_advice($item_specs, $body_specs);
        } elseif ($clothing_type->getName() == 'skirt' || $clothing_type->getName() == 'dress' || $clothing_type->getName() == 'coat') {
            return $this->get_hem_length_advice($item_specs, $body_specs);
        }
        
           
    }
    #-----------------------------------------------------
    private function get_hem_length_advice($item_specs, $body_specs) {

        if ($body_specs['outseam']==0 && $body_specs['height']==0){
            return null;
        }
        if (!array_key_exists('hem_length', $item_specs) || $item_specs['hem_length']['garment_measurement_flat']==0){
            return null;
        }
        $knee_height = (0.2695 * $body_specs['height']);
        $mid_calf_height = (0.1888 * $body_specs['height']);
        $ankle_height = (0.0374 * $body_specs['height']);        
        
        if ($body_specs['outseam']==0) {
            $body_specs['outseam'] = 0.6 * $body_specs['height'];
        }
        $body_specs['outseam_knee'] = $body_specs['outseam'] - $knee_height;
        $body_specs['outseam_mid_calf'] = $body_specs['outseam'] - $mid_calf_height;
        $body_specs['outseam_ankle'] = $body_specs['outseam'] - $ankle_height;
        
        $hem_length = $item_specs['hem_length']['garment_measurement_flat'];
        $actual_hem_length = $hem_length;
        $clothing_type=$this->product->getClothingType();
        
          if($clothing_type->getName()=='skirt'){
          $hem_length = $this->cut_to_natural_waste($hem_length);
          }
                
        $str = $this->get_hem_message($hem_length, $body_specs, 'outseam');
        
            return array('fit_point' => 'hem_advice',
            'label' =>  'Hem Advice',            
            'body_outseam' => $body_specs['outseam'],
            'item_hem_length' => $hem_length,
            'item_actual_hem_length' => $actual_hem_length,                
            'knee' => $body_specs['outseam_knee'],
            'mid_calf' => $body_specs['outseam_mid_calf'],
            'ankle' => $body_specs['outseam_ankle'],
            'message' => $str,            
        );
    }
    #-----------------------------------------------------
    private function get_inseam_advice($item_specs, $body_specs) {
           
        if ($body_specs['inseam']==0 && $body_specs['height']==0){
            return null;
        }
        if (!array_key_exists('inseam', $item_specs) || $item_specs['inseam']['garment_measurement_flat']==0){
            return null;
        }
        
        if ($body_specs['inseam']==0) {
        $body_specs['inseam'] = 0.269 * $body_specs['height'];
        }

        $knee_height = 0.574 * $body_specs['inseam'];
        $mid_calf_height = 0.4022 * $body_specs['inseam'];
        $ankle_height  = 0.0797 * $body_specs['inseam'];

        $body_specs['inseam_knee'] = $body_specs['inseam'] - $knee_height;
        $body_specs['inseam_mid_calf'] = $body_specs['inseam'] - $mid_calf_height;
        $body_specs['inseam_ankle'] = $body_specs['inseam'] - $ankle_height;

        $inseam=$item_specs['inseam']['garment_measurement_flat'];
          $str = $this->get_hem_message($inseam, $body_specs, 'inseam');
        
            return array('fit_point' => 'hem_advice',
            'label' =>  'Hem Advice',
            'body_inseam' => $body_specs['inseam'],                                    
            'item_inseam' => $inseam,                        
            'knee' => $body_specs['inseam_knee'],
            'mid_calf' => $body_specs['inseam_mid_calf'],
            'ankle' => $body_specs['inseam_ankle'],
            'message' => $str,            
        );
        
    }
    #-----------------------------------------------------
    function get_hem_message($item_measure, $body_specs, $fit_point){
        $str = '';
        if ($item_measure < $body_specs[$fit_point.'_knee']) {
            $str = 'less than knee';$level=1;
        } elseif ($item_measure == $body_specs[$fit_point.'_knee']) {
            $str = 'about knee high';$level=1;
        } else {
            if ($item_measure < $body_specs[$fit_point.'_mid_calf']) {
                $str = 'between knee & mid calf';$level=2;
            } elseif ($item_measure == $body_specs[$fit_point.'_mid_calf']) {
                $str = 'mid calf';$level=2;
            } else {
                if ($item_measure < $body_specs[$fit_point.'_ankle']) {
                    $str = 'between calf & ankle';$level=3;
                } elseif ($item_measure == $body_specs[$fit_point.'_ankle']) {
                    $str = 'ankle length';$level=3;
                } else {
                    $diff = $item_measure - $body_specs[$fit_point];
                    $level=4;
                    if (4.5 < $diff) {
                        $str = 'too long, hem';
                    } elseif (3.25 <= $diff && $diff <= 4.5) {
                        $str = 'very long, hem or wear with 4 – 5 inches heels';
                    } elseif (2.25 <= $diff && $diff <= 3.5) {
                        $str = 'long, hem or wear with 3 – 4 inches heels';
                    } elseif (1.25 <= $diff && $diff <= 2.5) {
                        $str = 'long, hem or wear with 2 - 3 inches heels';
                    } elseif (0 <= $diff && $diff <= 1.5) {
                        $str = 'long, hem or wear with 1 – 2 inches heels';
                    } elseif (-1 <= $diff && $diff <= -0.5) {
                        $str = 'perfect fit wear with flats or heels';
                    }
                }
            }
        }
        return $str;
    }
    
    #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~    
    #~~~~~~~~~~~~~~~~~~~~~~~~~~~> Devices Bits
    #------------------------------------------------    
    #-----------------------------------------------------
    function getStrippedFeedBackJSON() {
        return json_encode($this->getStrippedFeedBack());
    }
    
    #---------------------------------------------------
    function stripFeedBack($feed_back_array){
        $recom = array_key_exists('recommendation', $feed_back_array)?$feed_back_array['recommendation']:null;
        return array('feedback' => $this->strip_for_services($feed_back_array['feedback'], $recom),
            );
    }
    #-----------------------------------------------------    
    
    function getStrippedFeedBack() {
        if ($this->product->fitPriorityAvailable()) {
            $cm = $this->getFeedBack();
            $recom=array_key_exists('recommendation', $cm)?$cm['recommendation']:null;
            return array('feedback' => $this->strip_for_services($cm['feedback'], $recom),
            );
        }
        return;
    }
       #-----------------------------------------------------    
    
    public function getRecommendedFromStrippedFeedBack($striped_fb) {
        foreach ($striped_fb['feedback'] as $size => $specs) {     
            if (array_key_exists('recommended', $specs) && $specs['recommended']==true){
                return $specs;
            }
        }        
        return null;
    }
    # -----------------------------------------------------

    private function strip_for_services($sizes, $recommendation) {
        $product_id=$this->product->getId();
        $brand_name = $this->product->getBrand()->getName();
        $style = $this->product->getName()?$this->product->getName():'';
        foreach ($sizes as $key => $value) {
            $sizes[$key]['size_id']=$sizes[$key]['id'];
            $sizes[$key]['product_id']=$product_id;
            $sizes[$key]['brand']=$brand_name;
            $sizes[$key]['style']=$style;            
            unset($sizes[$key]['min_fx']);
            unset($sizes[$key]['max_fx']);
            unset($sizes[$key]['high_fx']);
            unset($sizes[$key]['low_fx']);
            unset($sizes[$key]['avg_fx']);
            
            unset($sizes[$key]['variance']);
            unset($sizes[$key]['description']);
            if ($recommendation!=null && array_key_exists('id', $recommendation)){
                if ($recommendation['id']==$sizes[$key]['id']){
                    $sizes[$key]['recommended'] = true;
                }else{
                    $sizes[$key]['recommended'] = false;
                }
            }else{
                    $sizes[$key]['recommended'] = false;
            }
            $sizes[$key]['price'] = 0;
            if (array_key_exists('fit_points', $sizes[$key])) {
                $sizes[$key]['fitting_alerts'] = $this->strip_fit_point_alerts($sizes[$key]);
                $sizes[$key]['summary'] = $this->strip_fit_point_summary($sizes[$key]);
            }else{
                $sizes[$key]['fitting_alerts'] = null;
                $sizes[$key]['summary'] = null;
            }
            if (array_key_exists('hem_advice', $sizes[$key])) {
                unset($sizes[$key]['hem_advice']);                
            }
            
            unset($sizes[$key]['fit_points']);
        }

        return $sizes;
    }
    
    # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    private function strip_fit_point_alerts($size) {
        $arr = array();        
        foreach ($size['fit_points'] as $key => $value) {     
            $arr[$key]=$value['message'];            
        }
        
        $hem_advice=$this->strip_size_hem_advice($size);
        if ($hem_advice!=null){            
               $arr["hem"]=$hem_advice;            
        }
        return $arr;
    }
    # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    private function strip_fit_point_summary($size) {
        $str = '';
        foreach ($size['fit_points'] as $key => $value) {
            $str.=$this->snakeToNormal($key) . ':' . $value['message'] . ', ';
        }
        
        $hem_advice=$this->strip_size_hem_advice($size);
        if ($hem_advice!=null){
            $str.="Hem:".$hem_advice;
        }else{
            $str=trim($str, ", ");
        }
        return trim($str, ", ");
    }
    
    #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    
   private function strip_size_hem_advice($size) {
        if (array_key_exists('hem_advice', $size) && array_key_exists('message', $size['hem_advice'])){
            return $size['hem_advice']['message'];
        }
        return null;
    }

    #-----------------------------------------------------    
    #-----------------------------------------------------    
    static function getDefaultSizeFeedback($fb) {
        if (isset($fb['recommendation']) && $fb['recommendation']) {
            return $fb['recommendation'];
        } elseif (isset($fb['feedback']) && $fb['feedback']) {            
            $default_size = null;
            foreach ($fb['feedback'] as $size) {
                if ($default_size == null || $default_size['id'] < $size['id'])
                    $default_size = $size;
            }
            return $default_size;
        }
        return;
    }
    
    
}
