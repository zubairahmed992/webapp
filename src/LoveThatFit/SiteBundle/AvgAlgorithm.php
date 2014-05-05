<?php

namespace LoveThatFit\SiteBundle;

class AvgAlgorithm {

    private $user;
    private $product;
   
#-----------------------------------------------------

    function __construct($user = null, $product = null) {
        $this->user = $user;
        $this->product = $product;
    }

#-----------------------------------------------------
    function getFeedBackJSON() {
        return json_encode($this->getFeedBack());
    }
#-----------------------------------------------------
    function getFeedBack() {
        if ($this->product->fitPriorityAvailable()) {
            $cm = $this->array_mix();
            return array(
                'feedback' => $cm['feedback'],
                'recommendation' => null,
                'best_fit' => null,
            );
        } else {
            return array(
                'message' => 'Fit Priority is not set, please update the product detail.',
            );
        }
    }  
#-----------------------------------------------------
    private function array_mix($sizes=null) {
        if ($sizes==null){ 
                $sizes = $this->product->getProductSizes();
        }        
        $body_specs = $this->user->getMeasurement()->getArray();
        $fb = array();
        $highest_variance=0;
        $highest_high_variance=0;
        $highest_max_variance=0;
        $highest_ideal_variance=0;
        $lowest_ideal_variance=0;
        
        foreach ($sizes as $size) {
            #$size_specs = $size->getPriorityMeasurementArray(); #~~~~~~~~>
            $size_specs = $size->getMeasurementArray(); #~~~~~~~~>
            $size_identifier = $size->getDescription();
            $fb[$size_identifier]['id'] = $size->getId();
            $fb[$size_identifier]['fits'] = true;
            $fb[$size_identifier]['description'] = $size_identifier;
            $fb[$size_identifier]['title'] = $size->getTitle();
            $fb[$size_identifier]['body_type'] = $size->getBodyType();
            $variance = 0;
            $ideal_variance = 0;
            $max_variance = 0;
            $min_variance = 0;
            $status = 0;
            $fit_scale = 0;
            if (is_array($size_specs)) {
                foreach ($size_specs as $fp_specs) {
                    if (is_array($fp_specs) && array_key_exists('id', $fp_specs) && $fp_specs['fit_priority']>0) {
                        #get fit point specs merged & calculated
                        #~~~~~~~~~>~~~~~~~~~~~~~~>*
                        $fb[$size_identifier]['fit_points'][$fp_specs['fit_point']] =
                                $this->get_fit_point_array($fp_specs, $body_specs);
                        #~~~~~~~~~>~~~~~~~~~~~~~~>*
                        #$variance = 0;#$this->get_accumulated_variance($ideal_variance, $fb[$size_identifier]['fit_points'][$fp_specs['fit_point']]['ideal_variance']);
                        #$status =0;# $this->get_accumulated_status($status, $fb[$size_identifier]['fit_points'][$fp_specs['fit_point']]['status']);
                    }
                }
                if (!array_key_exists('fit_points', $fb[$size_identifier])) {
                    $status = $this->status['product_measurement_not_available'];
                }
                #$fb[$size_identifier]['ideal_variance'] = $ideal_variance;
                #$fb[$size_identifier]['status'] = $status;
                #$fb[$size_identifier]['message'] = $this->get_fp_status_text($status);
                #$fb[$size_identifier]['fit_scale'] = $fit_scale > 0 ? $fit_scale : 0;
                #$fb[$size_identifier]['fits'] = $status == 0 || $status == -1 || $status == -2 ? true : false;
                #   $fb[$size_identifier]['recommended'] = $status == 0 || $status == -1 || $status == -2 ? true : false;
            
                $highest_ideal_variance=$highest_ideal_variance>$ideal_variance?$highest_ideal_variance:$ideal_variance;
                $lowest_ideal_variance=$lowest_ideal_variance<$ideal_variance?$lowest_ideal_variance:$ideal_variance;
                
                
            }   
        }
        return array('feedback'=>$this->array_sort($fb),'highest_variance'=>$highest_variance);
    }

# -----------------------------------------------------
    private function array_sort($sizes) {
        $size_titles = $this->getSizeTitleArray($this->product->getGender(), $this->product->getSizeTitleType());
        $size_types = $this->getSizeTypes();
        $fb = array();
        $size_identifier = '';
        foreach ($size_types as $stype) {
            foreach ($size_titles as $stitle) {
                $size_identifier = $stype . ' ' . $stitle;
                if (array_key_exists($size_identifier, $sizes))
                    $fb[$size_identifier] = $sizes[$size_identifier];
            }
        }
        return $fb;
    }
# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    private function get_fit_point_array($fp_specs, $body_specs) {

        $low=$fp_specs['ideal_body_size_low'];
        $high=$fp_specs['ideal_body_size_high'];
        $max=$fp_specs['max_body_measurement'];
        
        $body = array_key_exists($fp_specs['fit_point'], $body_specs) ? $body_specs[$fp_specs['fit_point']] : 0;
        $mid_low_high = ($low+ $high)/2;
        $mid_high_max =($high+ $max)/2;
        $variance=$this->calculate_variance($body, $mid_low_high, $fp_specs['fit_priority']);
        $min_variance=$this->calculate_variance($fp_specs['min_body_measurement'], $mid_low_high, $fp_specs['fit_priority']);
        $max_variance=$this->calculate_variance($fp_specs['max_body_measurement'], $mid_low_high, $fp_specs['fit_priority']);
        $fits=true;
        
        if ($body>=$fp_specs['ideal_body_size_low'] && $body<=$fp_specs['max_body_measurement']){
            $fits=true;
        }
        
        if ($body >= $low && $body <= $high) { #perfect
                $status = $this->status['between_low_high'];                
            } elseif ($body < $low) { #loose
                $status = $this->status['below_low'];               
                $varience = $this->calculate_variance($body, $low, $fp_specs['fit_priority']);
                $max_variance = $varience;                
            } else {#tight
                if ($body > $high && $body < $mid_high_max) { #high max 1st half    
                    $status = $this->status['first_half_high_mid_max'];
                } elseif ($body > $mid_high_max && $body <= $max) { #high max 2nd half
                    $status = $this->status['second_half_high_mid_max'];
                } elseif ($body > $max) { #not fitting
                    $status = $this->status['beyond_max'];
                }
            }            
        
        
        $message = $this->get_fp_status_text(0);
        return array('fit_point' => $fp_specs['fit_point'],
            'label' =>  $this->getFitPointLabel($fp_specs['fit_point']),
            'min_body_measurement' => $fp_specs['min_body_measurement'],
            'ideal_body_size_low' => $low,
            '$mid_low_high' => $mid_low_high,
            'ideal_body_size_high' => $high,
            'mid_high_max' => $mid_high_max,
            'max_body_measurement' => $max,
            'fit_priority' => $fp_specs['fit_priority'],
            'body_measurement' => $body,
            'variance' => $variance[0],
            'min_variance' => $min_variance[0],
            'max_variance' => $max_variance[0],
            'fit' => $fits,
        );
    }

private function grade_fit_index($max, $min, $actual){
    if ($actual==0) return 10;
    if ($actual==$max || $actual==$min){
        return 0;
    }else{
    if ($actual<0 && $max>0){
        $actual=$actual * (-1);
        $fi = 1 + (($actual - 0) * (10 - 1)) / ($max - 0);
        return 10 - $fi;
    }elseif ($actual>0 && $min>0){
        $fi = 1 + (($actual - 0) * (10 - 1)) / ($min - 0);
        return 10 - $fi;
    }
     }
    return null;
}

private function limit_num($n){        
        if ($n == round($n)) {
          return $n;
        }else{
        return number_format($n, 2, '.', '');
        }
    }
    
    #----------------------------------------------------------

    private function calculate_variance($body, $item, $priority) {
        if ($item > 0 && $body > 0) {
            $diff = $item - $body;
            if ($diff==0){
                $v=0; $dp=0; $d=0;
            }else{
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
            if ($accumulated == $this->status['first_half_high_mid_max'] ||
                    $accumulated == $this->status['second_half_high_mid_max']) {
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

   #-------------------------------------------------
/*
 * 
 * Find the min and the max
then for each number scale x to 2 * (x - min)/( max - min) - 1
If it is a long list precomputing c = 2/(max - min) and scaling with 'c * x - 1` is a good idea.
 * applied formula:
 *  y = 1 + (x-A)*(10-1)/(B-A)
 */
    private function addFitScale($sizes, $high, $max_high=0, $ideal_high=0, $ideal_low=0, $low = 0) {
        if ($sizes == null) {
            return;
        }
        foreach ($sizes as $desc => $size) {
            $sizes[$desc]['fit_scale'] = $this->calculate_fit_scale($size['variance'], $high, $low);
            $sizes[$desc]['fit_max_scale'] = $this->calculate_fit_scale($size['max_variance'], $max_high, $low);
            $sizes[$desc]['fit_ideal_scale'] = $this->calculate_fit_scale($size['ideal_variance'], $ideal_high, $ideal_low);
        }
        return $sizes;
    }
    #~~~~~~~~~~~~~~~~~~~~~~~~~~>>
    private function calculate_fit_scale($variance, $high, $low){
            if ($variance < 0 || $high <= $low) {
                $fs = 0;
            } else {
                $fs = 1 + (($variance - $low) * (10 - 1)) / ($high - $low);
                $fs = 10 - $fs; #making it reverse
            }
            return $this->limit_num($fs);
    }

#----------------------------------------------------------       
    private function snakeToNormal($str) {
        return str_replace('_', ' ', ucfirst($str));
    }
 
#----------------------------------------------------------
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

    #----------------------------------------------------------

    private function is_loose_tight_status($status) {
        if ($status == $this->status['first_half_high_mid_max'] ||
                $status == $this->status['second_half_high_mid_max'] ||
                $status == $this->status['anywhere_below_max']) {
            return true;
        } else {
            return false;
        }
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
                return 'Too Small (beyond_max)';
                break;
            case $this->status['second_half_high_mid_max'] :
                return 'tight fitting (2nd_half_high_mid_max)';
                break;
            case $this->status['first_half_high_mid_max'] :
                return 'close fitting (first_half_high_mid_max)';
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
            case $this->status['anywhere_below_max'] :
                return 'Tight at some points & loose at others';
                break;
        }
        #$str=array_search($id,$this->status);
        #return str_replace('_', ' ', $str);
    }

#----------------------------------------------------------       
    
 var $status = array(
        'fit_point_dose_not_match' => -6,
        'body_measurement_not_available' => -5,
        'product_measurement_not_available' => -4,
        'beyond_max' => -3,
        'second_half_high_mid_max' => -2,
        'first_half_high_mid_max' => -1,
        'between_low_high' => 0,
        'below_low' => 1,
        'one_size_below_low' => 2,
        'two_size_below_low' => 3,
        'more_size_below_low' => 4,
        'anywhere_below_max' => 5,
    );

    #----------------------------------------------------------

    public static function getStatusArray() {
        return array(
            'Fitting point dose not exists' => -6,
            'User measurement not provided' => -5,
            'Product measurement missing' => -4,
            'Too Small' => -3,
            'Tight Fitting' => -2,
            'Close Fitting' => -1,
            'Love That Fit' => 0,
            'Loose' => 1,
            'Loose Fit' => 2,
            'Too Loose' => 3,
            'Too Large' => 4,
            'Tight at some points & loose at others' => 5,
        );
    }
    
#----------------------------------------------------------       

    private function getSizeTitleArray($gender = 'f', $type = 'numbers') {
        $gender = strtolower($gender);
        $type = strtolower($type);

        if ($type == 'letters') {//$female_letters
            return array('XS', 'S', 'M', 'L', 'X', 'XL', '1XL', '1X', 'XXL', '2X', '2XL', 'XXXL', '3XL', '3X', 'XXXXL', '4XL', '4X');
        } else if ($gender == 'f' && $type == 'numbers') {//$female_standard
            return array('00', '0', '2', '4', '6', '8', '10', '12', '14', '16', '18', '20', '22', '24', '26', '28', '30');
        } else if ($gender == 'f' && $type == 'waist') {//$female_waist
            return array('23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35', '36');
        } else if ($gender == 'm' && $type == 'numbers') {//man Top
            return array('35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47', '48');
        } else if ($gender == 'm' && $type == 'waist') {//man bottom
            return array('28', '29', '30', '31', '32', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42');
        }
    }

    #----------------------------------------------------------       

    private function getSizeTypes() {
        return array('Regular', 'Petite', 'Tall');
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


}
