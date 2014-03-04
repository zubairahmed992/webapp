<?php

namespace LoveThatFit\SiteBundle;

use Symfony\Component\Yaml\Parser;

class Comparison {

    private $user;
    private $product;
#-----------------------------------------------------
    function __construct($user = null, $product = null) {
        $this->user = $user;
        $this->product = $product;
    }
#-----------------------------------------------------
    function getFeedBackJSON() {
        return json_encode($this->array_mix());
    }
#-----------------------------------------------------
    function getComparison() {
        return $this->array_mix();
    }
#-----------------------------------------------------
    private function array_mix() {
        $sizes = $this->product->getProductSizes();
        $body_specs = $this->user->getMeasurement()->getArray();
        $fb = array();
        $str="";
      
        foreach ($sizes as $size) {
            $size_specs = $size->getPriorityMeasurementArray(); #~~~~~~~~>
            $size_title=$size->getDescription();
            $fb[$size_title]['fits']=true;
            $variance=0;
            $max_variance=0;
            $status=0;
            $fit_scale=0;
            if(is_array($size_specs)){
            foreach ($size_specs as $fp_specs) {
                if (is_array($fp_specs) && array_key_exists('id', $fp_specs)){
                    #get fit point specs merged & calculated
                    $fb[$size_title]['fit_points'][$fp_specs['fit_point']] =
                    $this->get_fit_point_array($fp_specs, $body_specs);                                    
                    #if true then keep the old value else assign false ~~~>
                    $max_variance=  $this->get_accumulated_variance($max_variance, $fb[$size_title]['fit_points'][$fp_specs['fit_point']]['max_variance']);
                    $variance = $this->get_accumulated_variance($variance, $fb[$size_title]['fit_points'][$fp_specs['fit_point']]['variance']);                      
                    $status=$this->get_accumulated_status($status, $fb[$size_title]['fit_points'][$fp_specs['fit_point']]['status']);
                    if ($variance<0 || $fit_scale<0){
                        $fit_scale=-1;
                    }elseif ($variance==0){
                        $fit_scale=$fit_scale+ $fb[$size_title]['fit_points'][$fp_specs['fit_point']]['fit_priority']/10;
                    }elseif ($variance>0){
                        $fit_scale=$fit_scale+ $fb[$size_title]['fit_points'][$fp_specs['fit_point']]['fit_priority']/20;
                    }                    
                }
            }
            if(!array_key_exists('fit_points',$fb[$size_title])){
                $status=$this->status['product_measurement_not_available'];                
                }
                $fb[$size->getDescription()]['variance'] = $variance;
                $fb[$size->getDescription()]['max_variance'] = $max_variance;
                $fb[$size->getDescription()]['status'] = $status;
                $fb[$size->getDescription()]['message'] =  $this->get_fp_status_text($status);
                $fb[$size->getDescription()]['fit_scale'] =  $fit_scale;            
            }                
        }
        return $fb;
    }
    

    
#--------------------------------------------------------------    
    private function get_fit_point_array($fp_specs, $body_specs) {

        $body_measurement = array_key_exists($fp_specs['fit_point'], $body_specs) ? $body_specs[$fp_specs['fit_point']] : 0;

        $calc_values=$this->get_calculated_values($body_measurement, $fp_specs['ideal_body_size_high'],
                    $fp_specs['ideal_body_size_low'], $fp_specs['max_body_measurement'], $fp_specs['fit_priority']);
        $message=  $this->get_fp_status_text($calc_values['status']);
        
        return array('fit_point' => $fp_specs['fit_point'],
            'ideal_body_size_low' => $fp_specs['ideal_body_size_low'],
            'ideal_body_size_high' => $fp_specs['ideal_body_size_high'],
            'max_body_measurement' => $fp_specs['max_body_measurement'],
            'fit_priority' => $fp_specs['fit_priority'],
            'body_measurement' => $body_measurement,            
            'variance' => $calc_values['variance'],
            'max_variance' => $calc_values['max_variance'],
            'message'=>$message,
            'status'=>$calc_values['status'],
            'diff' => $calc_values['diff'],
            'diff_percent' => $calc_values['diff_percent'],
            'max_diff' => $calc_values['max_diff'],
            'max_diff_percent' => $calc_values['max_diff_percent'],
            
            
        );
    }
    
#--------------------------------------------------------------
    private function get_calculated_values($body, $high, $low, $max, $priority) {

        if ($high==0 || $low==0 || $max==0){
            return array(null, null, $this->status['product_measurement_not_available']);
        }elseif ($body==0 ){
            return array(null, null, $this->status['body_measurement_not_available']);
        }
        
        $mid_of_high_max = ($max + $high) / 2;
        $varience = number_format(0, 2, '.', '');
        $max_varience = number_format(0, 2, '.', ''); $status = 0; 

        if ($body >= $low && $body <= $high) { #perfect
            $status = 0; 
        } elseif ($body < $low) { #loose
            $status = $this->status['below_low'];
            $varience = $this->calculate_variance($body, $low, $priority);
            $max_varience = $varience;
        } else {#tight
            if ($body > $high && $body < $mid_of_high_max) { #high max 1st half    
                $status = $this->status['first_half_high_mid_max'];
            } elseif ($body > $mid_of_high_max && $body < $max) { #high max 2nd half
                $status = $this->status['second_half_high_mid_max'];
            } elseif ($body >= $max) { #not fitting
                $status = $this->status['beyond_max'];
            }
            $varience = $this->calculate_variance($body, $high, $priority);
            $max_varience = $this->calculate_variance($body, $mid_of_high_max, $priority);
        }

        return array('variance'=>$varience[0], 
            'max_variance'=>$max_varience[0], 
            'status'=>$status, 
            'diff'=>$varience[1], 
            'diff_percent'=>$varience[2], 
            'max_diff'=>$max_varience[1],
            'max_diff_percent'=>$max_varience[2]);
    }    
    #----------------------------------------------------------
    private function calculate_variance($body, $item, $priority) {
        if ($item > 0 && $body > 0) {
            $diff = $item - $body;
            $diff_percent = ($diff / $item) * 100;# how much (in %age of item measurement) the difference is?
            $d=number_format($diff, 2, '.', '');
            $dp=number_format($diff_percent, 2, '.', '');
            $v=number_format(($priority * $diff_percent) / 100, 2, '.', '');
            
            return array($v, $dp, $d);
        }else
            return;
    }    
    #----------------------------------------------------------
    private function get_accumulated_variance($accumulated, $current) {
        if (($accumulated >= 0 && $current >= 0) || ($accumulated <= 0 && $current <= 0)) {
            $accumulated = $accumulated + $current;
        } elseif ($accumulated < 0 && $current > 0) {
            $accumulated = $accumulated;
        } else {
            $accumulated = $current;
        }
        return $accumulated;
    }
    #----------------------------------------------------------
    private function get_accumulated_status($accumulated, $current) {
        
        if ($accumulated == $this->status['between_low_high']) return $current;        
        
        if ($current == $this->status['between_low_high']) return $accumulated;            

        if ($accumulated == $this->status['body_measurement_not_available'] ||
                $accumulated == $this->status['product_measurement_not_available']) return $accumulated;            
        
        if ($current == $this->status['body_measurement_not_available'] ||
                $current == $this->status['product_measurement_not_available']) return $current;
        
        if ($accumulated == $this->status['beyond_max'] ||
                $accumulated == $this->status['second_half_high_mid_max']) return $accumulated;
        
        if ($current == $this->status['second_half_high_mid_max'] ||
                $current == $this->status['beyond_max']) return $current;
        
        if ($accumulated == $this->status['below_low']) {
            if ($this->is_loose_status($current)) return $this->status['below_low'];
            if ($this->is_loose_tight_status($current)) return $this->status['below_low_between_high_mid_max'];            
        
            
        } elseif ($accumulated == $this->status['first_half_high_mid_max'] ||
                $accumulated == $this->status['below_low_between_high_mid_max']) {
            if ($this->is_loose_tight_status($current) || $this->is_loose_status($current)) {
                return $this->status['below_low_between_high_mid_max'];
            }
        }
    }    
    #----------------------------------------------------------
    private function get_fp_status_text($id){                        
          switch ($id){
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
              case $this->status['second_half_high_mid_max'] :
                  return 'too tight, restrictive (2nd_half_high_mid_max)';
              break;
              case $this->status['first_half_high_mid_max'] :
                  return 'tight fit (first_half_high_mid_max)';
              break;
              case $this->status['between_low_high'] :
                  return 'Love That Fit (between_low_high)';
              break;
              case $this->status['below_low'] :
                  return 'Loose (below_low)';
              break;
              case $this->status['one_size_below_low'] :
                  return 'Loose Fit (2_size_big)';
              break;
              case $this->status['two_size_below_low'] :
                  return 'Too Loose (3_size_big)';
              break;
              case $this->status['more_size_below_low'] :
                  return 'Too Large (more_size_big)';
              break;
              case $this->status['below_low_between_high_mid_max'] :
                  return 'Tight at some points & loose at others';
              break;
              
          }
          #$str=array_search($id,$this->status);
          #return str_replace('_', ' ', $str);
          
    }
    #----------------------------------------------------------
     var $status=array(
        'fit_point_dose_not_match'=>-6,
        'body_measurement_not_available'=>-5,
        'product_measurement_not_available'=>-4,
        'beyond_max'=>-3,
        'second_half_high_mid_max'=>-2,
        'first_half_high_mid_max'=>-1,
        'between_low_high'=>0,
        'below_low'=>1,
        'one_size_below_low'=>2,
        'two_size_below_low'=>3,
        'more_size_below_low'=>4,
        'below_low_between_high_mid_max'=>5,
    );
   
    private function is_loose_status($status) {
        if ($status == $this->status['below_low'] ||
                $status == $this->status['one_size_below_low'] ||
                $status == $this->status['two_size_below_low'] ||
                $status == $this->status['more_size_below_low']) {
            return true;
        } else {
            return false;
        }
    }
    
     private function is_loose_tight_status($status) {
        if ($status == $this->status['first_half_high_mid_max'] ||
                $status == $this->status['below_low_between_high_mid_max']) {
            return true;
        } else {
            return false;
        }
    }
      
    #-----------------------------------------------------
    private function round2($sizes) {
        $str='' ;
        foreach ($sizes as $title=>$size) {
           foreach ($size['fit_points'] as $fit_point=>$specs) {
             $str.=$fit_point;
         }
         }
         return $str;
    }

    private function calculate_size_diff_count($size,$fp) {
     $sizes = $this->getSizeTitleArray($this->product->getGender(), $this->product->getSizeTitleType());
     $str='' ;
        for ($i = 0; $i < count($sizes) - 1; $i++) {
            if (array_key_exists($sizes[$i], $fp)) {
                
            }
            
        }
         
         
         return $str;
    }
          
    private function getSizeTitleArray($gender = 'f', $type = 'numbers') {
        $gender =  strtolower($gender);
        $type =  strtolower($type);
        
        if ($type == 'letters') {//$female_letters
            return array('XS', 'S', 'M', 'L', 'XL', 'XXL');
        } else if ($gender == 'f' && $type == 'numbers') {//$female_standard
            return array('00', '0', '2', '4', '6', '8', '10', '12', '16', '18', '20');
        } else if ($gender == 'f' && $type == 'waist') {//$female_waist
            return array('23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35', '36');
        } else if ($gender == 'm' && $type == 'top') {//man Top
            return array('35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47', '48');
        } else if ($gender == 'm' && $type == 'bottom') {//man bottom
            return array('28', '29', '30', '31', '32', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42');
        }
    }
}