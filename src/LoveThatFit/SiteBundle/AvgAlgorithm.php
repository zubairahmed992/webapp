<?php

namespace LoveThatFit\SiteBundle;
use LoveThatFit\AdminBundle\Entity\SizeHelper;
class AvgAlgorithm {

    private $user;
    private $product;
    private $size_helper;
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
            return $this->generateFakeFeedback($cm['feedback']);
            $rc = $this->getRecommendation($cm['feedback']);
            if ($rc){
                return array(
                    'feedback' => $cm['feedback'],
                    'recommendation' => $rc,
                );
            }else{
                #return $this->generateFakeFeedback($cm['feedback']);
            return array('feedback' => $cm['feedback'], 'recommendation' => $rc);
                
            }
        } else {
            return array(
                'message' => 'Fit Priority is not set for this product.',
            );
        }
    }

#-----------------------------------------------------
    function getRecommendation($sizes = null) {        
        if(!$sizes){
            $cm = $this->array_mix();
            $sizes = $cm['feedback'];
        }
        return $this->getFittingSize($sizes);        
    }
    
    
#--------------------------------------------------------
    private function getFittingSize($sizes) {
        if ($sizes == null)
            return;
        $lowest_variance = null;        
        $best_fit = null;

        foreach ($sizes as $size) {
            #if ($size['status'] != $this->status['beyond_max'] && $size['status'] != $this->status['below_min']) {
            if (!($size['status'] <= $this->status['beyond_max']) && $size['status'] != $this->status['below_min']) {
                if ($lowest_variance == null || $lowest_variance > $size['variance']) {
                    $lowest_variance = $size['variance'];
                    $fitting_size = array($size['description'] => $size);
                    $best_fit = $size;
                } elseif($lowest_variance == $size['variance']){                    
                    $best_fit = $size;
                }
            }
        }
        return $best_fit;
    }    
    #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>*Fake 1
    private function generateFakeFeedback($sizes){
        $min_size=$this->getSizeWithLowestVarience($sizes);
        $floated = $this->floatMinimumMeasurement($min_size);
        $sizes[$floated['description']]=$floated;
         return array(
                'feedback' => $sizes,
                'recommendation' => $floated,
            );
    }
    #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>*Fake 2   
    private function getSizeWithLowestVarience($sizes) {
        if ($sizes == null)
            return;
        $lowest_variance = null;        
        $best_fit = null;

        foreach ($sizes as $size) {
            #if ($size['status'] != $this->status['beyond_max'] && $size['status'] != $this->status['below_min']) {
            if (!($size['status'] <= $this->status['beyond_max'])) {
                if ($lowest_variance == null || $lowest_variance > $size['variance']) {
                    $lowest_variance = $size['variance'];
                    $best_fit = $size;
                } elseif($lowest_variance == $size['variance']){                    
                    $best_fit = $size;
                }
            }
        }
        return $best_fit;
    }    
    #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>*Fake 3
    private function floatMinimumMeasurement($size){
        
        $size['variance']=0;
        $size['max_variance'] =0;
        $fit_index_sum=0;
        foreach ($size['fit_points'] as $k=>$v) {
            if ($v['body_measurement']<=$v['min_body_measurement']){
                #1) change min_body_measurement
                $min=$size['fit_points'][$k]['body_measurement']-0.1;
                $size['fit_points'][$k]['min_body_measurement'] = $min;
                #2) calculate fp min_variance
                $min_variance = $this->calculate_variance($min, $v['mid_low_high'], $v['fit_priority']);
                $min_variance = $min_variance[0];
                $size['fit_points'][$k]['min_variance'] = $min_variance ;
                #3) fit point fit index
                $size['fit_points'][$k]['fit_index'] =  $this->grade_to_scale($v['variance'], $min_variance);                
            }
            $fit_index_sum=$fit_index_sum + $size['fit_points'][$k]['fit_index'];
        #4) calculate accumulated variance
            $accumulated = $this->calculate_accumulated_variance($v,$size['variance']);
            $size['variance'] = $accumulated['variance'] ;
        #5) calculate max_variance
            $size['max_variance'] = $size['max_variance'] + $accumulated['max_variance'];            
        }
        
        # calculate fit_index
        #$size['fit_index'] = $this->grade_to_scale($size['variance'], $size['max_variance']);
        $size['fit_index'] = $fit_index_sum/count($size['fit_points']);
        return $size;
    }
    #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>*Fake 4
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
            $fb[$size_identifier]['fits'] = true;
            $fb[$size_identifier]['description'] = $size_identifier;
            $fb[$size_identifier]['title'] = $size->getTitle();
            $fb[$size_identifier]['body_type'] = $size->getBodyType();
            $fb[$size_identifier]['variance'] = 0;
            $fb[$size_identifier]['max_variance'] = 0;            
            $fb[$size_identifier]['status'] =0;
            if (is_array($size_specs)) {
             foreach($fpwp as $pfp_key=>$pfp_value){
                    if (array_key_exists($pfp_key, $size_specs)) {
                        $fb[$size_identifier]['fit_points'][$pfp_key] =
                                $this->get_fit_point_array($size_specs[$pfp_key], $body_specs);
                        
                        $accumulated = $this->calculate_accumulated_variance($fb[$size_identifier]['fit_points'][$pfp_key],
                                        $fb[$size_identifier]['variance']);
                        $fb[$size_identifier]['variance'] = $accumulated['variance'] ;
                        $fb[$size_identifier]['max_variance'] = $fb[$size_identifier]['max_variance'] + $accumulated['max_variance'];
                        $fb[$size_identifier]['status'] = $this->get_accumulated_status($fb[$size_identifier]['status'], $fb[$size_identifier]['fit_points'][$pfp_key]['status']);
                    }else{
                        $fb[$size_identifier]['status'] =$this->status['product_measurement_not_available'];
                    
                        #$fb[$size_identifier]['status'] =  json_encode($fpwp);
                    }
             }
                 
            }
            $fb[$size_identifier]['message']=  $this->get_fp_status_text($fb[$size_identifier]['status']);
            # calculate fit index only if measurement is not beyond_max or below_min
            if ($fb[$size_identifier]['status']==$this->status['beyond_max']
                    || $fb[$size_identifier]['status']==$this->status['below_min']){
                $fb[$size_identifier]['fit_index'] = 0;
            }else{
                $fb[$size_identifier]['fit_index'] = $this->grade_to_scale($fb[$size_identifier]['variance'], $fb[$size_identifier]['max_variance'], 0);
                #########################################3?????
                #if (array_key_exists('fit_points', $fb[$size_identifier]) )
                #$fb[$size_identifier]['fit_index'] = $this->get_average_fit_index($fb[$size_identifier]['fit_points']);
                
            }
            $hem_bits = $this->get_hem_advice($size_specs, $body_specs);
            if ($hem_bits) $fb[$size_identifier]['hem_advice'] = $hem_bits;
        }
        return array('feedback' => $this->array_sort($fb));
    }
    ###################################################
    //
    private function get_average_fit_index($fps){
        $sum=0;
        $count=0;
        
        foreach ($fps as $k=>$v) {
            $count++;
            if ($sum==-1 && $v['fit_index']==0)
                $sum=-1;
            else
                $sum+=$v['fit_index'];
        }
        $avg=$sum==-1?0:($sum/$count);
        return $avg;
        
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

        $low = $fp_specs['ideal_body_size_low'];
        $high = $fp_specs['ideal_body_size_high'];
        $max = $fp_specs['max_body_measurement'];
        $min = $fp_specs['min_body_measurement'];
        
        $body = $this->get_relevant_body_measurement($fp_specs, $body_specs);
        #$body = array_key_exists($fp_specs['fit_point'], $body_specs) ? $body_specs[$fp_specs['fit_point']] : 0;
        $mid_low_high = ($low + $high) / 2;
        $mid_high_max = ($high + $max) / 2;
        $variance = $this->calculate_variance($body, $mid_low_high, $fp_specs['fit_priority']);
        $min_variance = $this->calculate_variance($min, $mid_low_high, $fp_specs['fit_priority']);
        $max_variance = $this->calculate_variance($max, $mid_low_high, $fp_specs['fit_priority']);
        
        
        if($variance[0]<0){
            $fit_index = $this->grade_to_scale($variance[0], $max_variance[0], 0);
        }elseif ($variance[0]>0){            
            $fit_index = $this->grade_to_scale($variance[0], $min_variance[0], 0);
        }else{            
            $fit_index = 0;
        }
        
        $status=null;
        $fits = null;
        if ($body==0){
            $status = $this->status['body_measurement_not_available'];
            $fits = false;
        }else{
            if ($body >= $low && $body <= $high) { #perfect
                $status = $this->status['between_low_high'];
                $fits = true;
            } elseif ($body > $high) { #above high
                if ($body < $mid_high_max) { #high max 1st half    
                    $status = $this->status['first_half_high_max'];
                    $fits = true;
                } elseif ($body > $mid_high_max && $body <= $max) { #high max 2nd half
                    $status = $this->status['second_half_high_max'];
                    $fits = true;
                } elseif ($body > $max) { #not fitting
                    $status = $this->status['beyond_max'];
                    $fits = false;
                }
            } elseif ($body < $low && $body > $min) {#below low    
                $status = $this->status['below_low'];
                $fits = false;
            } elseif ($body < $min) {
                $status = $this->status['below_min'];
                $fits = false;
            }
        }

        return array('fit_point' => $fp_specs['fit_point'],
            'label' => $this->getFitPointLabel($fp_specs['fit_point']),
            'min_body_measurement' => $fp_specs['min_body_measurement'],
            'ideal_body_size_low' => $low,
            'mid_low_high' => $mid_low_high,
            'ideal_body_size_high' => $high,
            'mid_high_max' => $mid_high_max,
            'max_body_measurement' => $max,
            'fit_priority' => $fp_specs['fit_priority'],
            'body_measurement' => $body,
            'variance' => $variance[0],
            'min_variance' => $min_variance[0],
            'max_variance' => $max_variance[0],
            'fit' => $fits,
            'status'=> $status,
            'message'=>$this->get_fp_status_text($status),
            'fit_index'=> $fit_index,
        );
    }
    

    #----------------------------------------------------------

    private function calculate_variance($body, $item, $priority) {
        if ($item > 0 && $body > 0) {
            $diff = $item - $body;
            if ($diff == 0) {
                $v = 0;
                $dp = 0;
                $d = 0;
            } else {
                $diff_percent = ($diff / $item) * 100; # how much (in %age of item measurement) the difference is?
                $d = number_format($diff, 2, '.', '');
                $dp = number_format($diff_percent, 2, '.', '');
                $v = number_format(($priority * $diff_percent) / 100, 2, '.', '');
            }
            return array($v, $d, $dp);
        }else
            return;
    }
 #----------------------------------------------------------
    private function calculate_accumulated_variance($fp, $accumulated) {
        #return array('variance'=>$accumulated, 'max_variance'=>$fp['max_variance']);
        $max_variance=0;
        if($fp['variance']==0){
            $max_variance = $fp['min_variance'];            
        }elseif($fp['variance']<0){
            $max_variance = $fp['max_variance'] * (-1);
            $accumulated = $accumulated + ($fp['variance'] * (-1));
        }elseif($fp['variance']>0){
            $max_variance = $fp['min_variance'];
            $accumulated = $accumulated + $fp['variance'];
        }
        
        return array('variance'=>$accumulated, 'max_variance'=>$max_variance);
    }
 #~~~~~~~~~~~~~~~~~~~~~~~~~~>>
    
    private function grade_to_scale($current, $high, $low=0) {    
        
        if($current<0){
            $high = $high * (-1);
            $current = $current * (-1);
        }elseif($current==0){
            return 10;
        }
        if ($high==0) return 0;
        if ($high<=$current) return 0;
          
        $fs = 1 + (($current - $low) * (10 - 1)) / ($high - $low);
                
        return $this->limit_num(10 - $fs);#making it reverse
    }
   #----------------------------------------------------------
    private function get_accumulated_status($accumulated, $current) {

        if ($accumulated == $this->status['between_low_high']) #accumulated is LTF
            return $current;

        if ($current == $this->status['between_low_high']) #current is LTF
            return $accumulated;

        if ($accumulated == $this->status['body_measurement_not_available'] ||
                $accumulated == $this->status['product_measurement_not_available']) # body not available in either
            return $accumulated;

        if ($current == $this->status['body_measurement_not_available'] ||
                $current == $this->status['product_measurement_not_available']) # product not available in either
            return $current;

        if ($accumulated == $this->status['beyond_max']) # accumulated beyond Max
            return $accumulated;

        if ($current == $this->status['beyond_max']) # current beyond Max
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
    
    # -----------------------------------------------------
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
    private function get_fp_status_text($id) {
        switch ($id) {
            case $this->status['fit_point_dose_not_match'] :
                return 'Fitting point dose not exists';
                break;
            case $this->status['body_measurement_not_available'] :
                return 'User measurement not provided';
                break;
            case $this->status['product_measurement_not_available'] :
                return 'Product measurement missing';
                break;
            case $this->status['beyond_max'] :
                return 'Too Small';
                break;
            case $this->status['second_half_high_max'] :
                return 'tight fitting';
                break;
            case $this->status['first_half_high_max'] :
                return 'close fitting';
                break;
            case $this->status['between_low_high'] :
                return 'Love That Fit';
                break;
            case $this->status['below_low'] :
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
    
    private function get_fp_status_raw_text($id) {
        switch ($id) {
            case $this->status['fit_point_dose_not_match'] :
                return 'Fitting point dose not exists';
                break;
            case $this->status['body_measurement_not_available'] :
                return 'User measurement not provided';
                break;
            case $this->status['product_measurement_not_available'] :
                return 'Product measurement missing';
                break;
            case $this->status['beyond_max'] :
                return 'Too Small (beyond_max)';
                break;
            case $this->status['second_half_high_max'] :
                return 'tight fitting (2nd_half_high_max)';
                break;
            case $this->status['first_half_high_max'] :
                return 'close fitting (first_half_high_max)';
                break;
            case $this->status['between_low_high'] :
                return 'Love That Fit (between_low_high)';
                break;
            case $this->status['below_low'] :
                return 'Loose (below_low)';
                break;
            case $this->status['below_min'] :
                return 'Extra Loose (below_min)';
                break;
            case $this->status['anywhere_below_max'] :
                return 'Tight at some points & loose at others';
                break;
        }        
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
        'fit_point_dose_not_match' => -6,
        'body_measurement_not_available' => -5,
        'product_measurement_not_available' => -4,
        'beyond_max' => -3,
        'second_half_high_max' => -2,
        'first_half_high_max' => -1,
        'between_low_high' => 0,
        'below_low' => 1,
        'below_min' => 2,
        'anywhere_below_max' => 3,
    );
    
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
    #------------------------------------------------     
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
                        $str = 'very long, hem or wear with 4” – 5” heels';
                    } elseif (2.25 <= $diff && $diff <= 3.5) {
                        $str = 'long, hem or wear with 3” – 4" heels';
                    } elseif (1.25 <= $diff && $diff <= 2.5) {
                        $str = 'long, hem or wear with 2" - 3” heels';
                    } elseif (0 <= $diff && $diff <= 1.5) {
                        $str = 'long, hem or wear with 1” – 2” heels';
                    } elseif (-1 <= $diff && $diff <= -0.5) {
                        $str = 'perfect fit wear with flats or heels';
                    }
                }
            }
        }
        return $str;
    }
    #-----------------------------------------------------
    // ___________ Web Services ------------
    #-----------------------------------------------------
    function getStrippedFeedBackJSON() {
        return json_encode($this->getStrippedFeedBack());
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
    
    # -----------------------------------------------------

    private function strip_for_services($sizes, $recommendation) {
        foreach ($sizes as $key => $value) {
            unset($sizes[$key]['max_variance']);
            unset($sizes[$key]['variance']);
            unset($sizes[$key]['description']);
            if ($recommendation!=null && array_key_exists('id', $recommendation)){
                if ($recommendation['id']==$sizes[$key]['id']){
                    $sizes[$key]['recommended'] = true;
                }else{
                    $sizes[$key]['recommended'] = false;
                }
            }
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

}
