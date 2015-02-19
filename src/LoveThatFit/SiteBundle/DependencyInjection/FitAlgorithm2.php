<?php

namespace LoveThatFit\SiteBundle\DependencyInjection;
use LoveThatFit\AdminBundle\Entity\SizeHelper;
class FitAlgorithm2 {

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
            if (is_array($size_specs)) {
             foreach($fpwp as $pfp_key=>$pfp_value){
                    if (array_key_exists($pfp_key, $size_specs)) {
                        $fb[$size_identifier]['fit_points'][$pfp_key] =
                                $this->get_fit_point_array($size_specs[$pfp_key], $body_specs);                        
                        $fb[$size_identifier]['status'] ='';
                    }else{
                        $fb[$size_identifier]['status'] =$this->status['product_measurement_not_available'];
                    }
             }
            }
            
        }
        return array('feedback' => $this->array_sort($fb));
    }
    ###################################################
    
    
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
        

        return array('fit_point' => $fp_specs['fit_point'],
            'label' => $this->getFitPointLabel($fp_specs['fit_point']),
            'calc_min_body_measurement' => $max_min['calc_min_body_measurement'],
            'min_body_measurement' => $fp_specs['min_body_measurement'],
            'ideal_body_size_low' => $fp_specs['ideal_body_size_low'],
            'mid_low_high' => $max_min['mid_low_high'],
            'ideal_body_size_high' => $fp_specs['ideal_body_size_high'],
            'max_body_measurement' => $fp_specs['max_body_measurement'],
            'calc_max_body_measurement' => $max_min['calc_max_body_measurement'],
            'fit_priority' => $fp_specs['fit_priority'],
            'body_measurement' => $body,            
        );
    }
    
private function calculate_maxmin($fp_specs){
      
        $ar=array();
        $ar['mid_low_high'] = ($fp_specs['ideal_body_size_low'] + $fp_specs['ideal_body_size_high']) / 2;
        
        $max_diff=$fp_specs['max_body_measurement']-$ar['mid_low_high'];
        $min_diff=$ar['mid_low_high']-$fp_specs['min_body_measurement'];
        
        if ($max_diff>$min_diff){
            $ar['calc_max_body_measurement'] = $fp_specs['max_body_measurement'];
            $ar['calc_min_body_measurement'] = $ar['mid_low_high'] - $max_diff;            
        }elseif ($max_diff<$min_diff){
            $ar['calc_max_body_measurement'] = $ar['mid_low_high'] + $min_diff;
            $ar['calc_min_body_measurement'] = $fp_specs['min_body_measurement'];            
        }else{
            $ar['calc_max_body_measurement'] = $fp_specs['max_body_measurement'];
            $ar['calc_min_body_measurement'] = $fp_specs['min_body_measurement'];            
        }
        return $ar;
        
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
    
  
}
