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
        return json_encode($this->getFeedBack());
    }
#-----------------------------------------------------
    function getFeedBack() {
        if ($this->product->fitPriorityAvailable()) {
            $cm = $this->array_mix();
            $rc = $this->getFittingSize($cm['feedback']);
            $bfs = $this->getBestFittingSize($rc);
            return array(
                'feedback' => $cm['feedback'],
                'recommendation' => $rc,
                'best_fit' => $bfs,
            );
        } else {
            return array(
                'message' => 'Fit Priority is not set, please update the product detail.',
            );
        }
    }
  
#-----------------------------------------------------
    function getStripedFeedBackJSON() {
        return json_encode($this->getStripedFeedBack());
    }
#-----------------------------------------------------
    function getStrippedFeedBack() {
        $cm = $this->array_mix();
        return array('feedback' => $this->strip_for_services($cm['feedback']),
        );
    }
#-----------------------------------------------------
    function getComparison() {
        $cm = $this->array_mix();
        return $cm['feedback'];
    }
#-----------------------------------------------------

    function getRecommendation($sizes = null) {
        if ($sizes) {
            return $this->getFittingSize($sizes);
        } else {
            $cm = $this->array_mix();
            return $this->getFittingSize($cm['feedback']);
        }
    }
 
#-----------------------------------------------------

    private function array_mix($sizes=null) {
        if ($sizes==null){ 
                $sizes = $this->product->getProductSizes();
        }
        $sizes = $this->product->getProductSizes();
        $body_specs = $this->user->getMeasurement()->getArray();
        $fb = array();
        $highest_variance=0;
        $highest_high_variance=0;
        $highest_max_variance=0;
        $highest_ideal_variance=0;
        $lowest_ideal_variance=0;
        
        foreach ($sizes as $size) {
            $size_specs = $size->getPriorityMeasurementArray(); #~~~~~~~~>
            $size_identifier = $size->getDescription();
            $fb[$size_identifier]['id'] = $size->getId();
            $fb[$size_identifier]['fits'] = true;
            $fb[$size_identifier]['description'] = $size_identifier;
            $fb[$size_identifier]['title'] = $size->getTitle();
            $fb[$size_identifier]['body_type'] = $size->getBodyType();
            $variance = 0;
            $ideal_variance = 0;
            $max_variance = 0;
            $status = 0;
            $fit_scale = 0;
            if (is_array($size_specs)) {
                foreach ($size_specs as $fp_specs) {
                    if (is_array($fp_specs) && array_key_exists('id', $fp_specs)) {
                        #get fit point specs merged & calculated
                        $fb[$size_identifier]['fit_points'][$fp_specs['fit_point']] =
                                $this->get_fit_point_array($fp_specs, $body_specs);
                        #if true then keep the old value else assign false ~~~>
                        $variance = $this->get_accumulated_variance($variance, $fb[$size_identifier]['fit_points'][$fp_specs['fit_point']]['variance']);
                        $ideal_variance = $this->get_accumulated_variance($ideal_variance, $fb[$size_identifier]['fit_points'][$fp_specs['fit_point']]['ideal_variance']);
                        $max_variance = $this->get_accumulated_variance($max_variance, $fb[$size_identifier]['fit_points'][$fp_specs['fit_point']]['max_variance']);
                        $status = $this->get_accumulated_status($status, $fb[$size_identifier]['fit_points'][$fp_specs['fit_point']]['status']);
                        if ($variance < 0 || $fit_scale < 0) {
                            $fit_scale = -1;
                        } elseif ($variance == 0) {
                            $fit_scale = $fit_scale + $fb[$size_identifier]['fit_points'][$fp_specs['fit_point']]['fit_priority'] / 10;
                        } elseif ($variance > 0) {
                            $fit_scale = $fit_scale + $fb[$size_identifier]['fit_points'][$fp_specs['fit_point']]['fit_priority'] / 20;
                        }
                    }
                }
                if (!array_key_exists('fit_points', $fb[$size_identifier])) {
                    $status = $this->status['product_measurement_not_available'];
                }
                $fb[$size_identifier]['variance'] = $variance;
                $fb[$size_identifier]['ideal_variance'] = $ideal_variance;
                $fb[$size_identifier]['max_variance'] = $max_variance;
                $fb[$size_identifier]['status'] = $status;
                $fb[$size_identifier]['message'] = $this->get_fp_status_text($status);
                #$fb[$size_identifier]['fit_scale'] = $fit_scale > 0 ? $fit_scale : 0;
                $fb[$size_identifier]['fits'] = $status == 0 || $status == -1 || $status == -2 ? true : false;
                $fb[$size_identifier]['recommended'] = $status == 0 || $status == -1 || $status == -2 ? true : false;
            
                $highest_variance=$highest_variance>$variance?$highest_variance:$variance;
                $highest_max_variance=$highest_max_variance>$max_variance?$highest_variance:$max_variance;
                $highest_ideal_variance=$highest_ideal_variance>$ideal_variance?$highest_ideal_variance:$ideal_variance;
                $lowest_ideal_variance=$lowest_ideal_variance<$ideal_variance?$lowest_ideal_variance:$ideal_variance;
                
            }
        }
        $fb = $this->addFitScale($fb, $highest_variance,$highest_max_variance, $highest_ideal_variance, $lowest_ideal_variance);
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

# -----------------------------------------------------

    private function strip_for_services($sizes) {
        foreach ($sizes as $key => $value) {
            unset($sizes[$key]['message']);
            unset($sizes[$key]['variance']);
            unset($sizes[$key]['description']);
            unset($sizes[$key]['ideal_variance']);
            if (array_key_exists('fit_points', $sizes[$key])) {
                $sizes[$key]['summary'] = $this->strip_fit_point_summary($sizes[$key]['fit_points']);
            }
            unset($sizes[$key]['fit_points']);
        }

        return $sizes;
    }

# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    private function strip_fit_point_summary($fit_points) {
        $str = '';
        foreach ($fit_points as $key => $value) {
            $str.=$this->snakeToNormal($key) . ':' . $value['message'] . ', ';
        }
        return trim($str, ", ");
    }

# --------------------------------------------------------------    

    private function get_fit_point_array($fp_specs, $body_specs) {

        $body_measurement = array_key_exists($fp_specs['fit_point'], $body_specs) ? $body_specs[$fp_specs['fit_point']] : 0;
        $calc_values = $this->get_calculated_values(
                $body_measurement, $fp_specs['ideal_body_size_high'], $fp_specs['ideal_body_size_low'], $fp_specs['max_body_measurement'], $fp_specs['fit_priority']);

        $message = $this->get_fp_status_text($calc_values['status']);
        return array('fit_point' => $fp_specs['fit_point'],
            'label' =>  $this->getFitPointLabel($fp_specs['fit_point']),
            'ideal_body_size_low' => $fp_specs['ideal_body_size_low'],
            'ideal_measurement' => $calc_values['avg_low_high'],
            'ideal_body_size_high' => $fp_specs['ideal_body_size_high'],
            'mid_high_max' => $calc_values['mid_high_max'],
            'max_body_measurement' => $fp_specs['max_body_measurement'],
            'fit_priority' => $fp_specs['fit_priority'],
            'body_measurement' => $body_measurement,
            'variance' => $calc_values['variance'],
            'ideal_variance' => $calc_values['ideal_variance'],
            'max_variance' => $calc_values['max_variance'],
            'message' => $message,
            'status' => $calc_values['status'],
            'diff' => $calc_values['diff'],            
            'ideal_diff' => $calc_values['ideal_diff'],
            'max_diff' => $calc_values['max_diff'],
            
        );
    }

#--------------------------------------------------------------

    private function get_calculated_values($body, $high, $low, $max, $priority) {

        if ($high == 0 || $low == 0 || $max == 0) {
            return $this->get_calculated_values_array_element($this->status['product_measurement_not_available']);
        } elseif ($body == 0) {
            return $this->get_calculated_values_array_element($this->status['body_measurement_not_available']);
        } else {
                        
            $avg_low_high = ($low+$high)/2;
            $mid_of_high_max = ($max + $high) / 2;
            $status = 0;
            $varience =  array(0,0,0);
            $max_variance =  array(0,0,0);
            $ideal_varience = array(0,0,0); 
                
            if ($body >= $low && $body <= $high) { #perfect
                $status = $this->status['between_low_high'];
                $ideal_varience = $this->calculate_variance($body, $avg_low_high, $priority);                
            } elseif ($body < $low) { #loose
                $status = $this->status['below_low'];               
                $varience = $this->calculate_variance($body, $low, $priority);
                $max_variance = $varience;
                $ideal_varience = $this->calculate_variance($body, $avg_low_high, $priority);
            } else {#tight
                if ($body > $high && $body < $mid_of_high_max) { #high max 1st half    
                    $status = $this->status['first_half_high_mid_max'];
                } elseif ($body > $mid_of_high_max && $body <= $max) { #high max 2nd half
                    $status = $this->status['second_half_high_mid_max'];
                } elseif ($body > $max) { #not fitting
                    $status = $this->status['beyond_max'];
                }
                $varience = $this->calculate_variance($body, $high, $priority);
                $ideal_varience = $this->calculate_variance($body, $avg_low_high, $priority);
                $max_variance = $this->calculate_variance($body, $max, $priority);
            }
            
            return $this->get_calculated_values_array_element($status, $varience[0], $ideal_varience[0], $max_variance[0], $varience[1], $ideal_varience[1], $max_variance[1], $avg_low_high, $mid_of_high_max);
        }                       
    }

    private function get_calculated_values_array_element($status, $varience = 0, $ideal_varience = 0, $max_variance = 0, $diff = 0, $ideal_diff = 0, $max_diff = 0, $avg_low_high = 0, $mid_high_max = 0) {
        return array(
            'status' => $status,            
            'variance' => $this->limit_num($varience),
            'ideal_variance' => $this->limit_num($ideal_varience),
            'max_variance' => $this->limit_num($max_variance),
            'diff' => $this->limit_num($diff),            
            'ideal_diff' => $this->limit_num($ideal_diff),            
            'max_diff' => $this->limit_num($max_diff),            
            'avg_low_high'=>$this->limit_num($avg_low_high),
            'mid_high_max'=>$this->limit_num($mid_high_max),
        );
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

    #-------------------------------------------------

    private function getFittingSize($sizes) {

        if ($sizes == null)
            return;

        $fits = array();
        $loose = array();
        $tights = array();
        $lowest_variance = null;
        foreach ($sizes as $size) {
            if ($size['status'] == $this->status['between_low_high']) {
                $fits[$size['description']] = $size;
            } elseif ($this->is_loose_tight_status($size['status'])) {
                $tights[$size['description']] = $size;
            } elseif ($this->is_loose_status($size['status'])) {
                if ($lowest_variance == null || $lowest_variance > $size['variance']) {
                    $lowest_variance = $size['variance'];
                    $loose = array($size['description'] => $size) + $loose; //array_shift
                }
            }
        }
        if (count($fits) > 0)
            return $fits;
        if (count($tights) > 0)
            return $tights;
        if (count($loose) > 0)
            return $loose;
    }
     #-------------------------------------------------

    private function getBestFittingSize($recomended_sizes) {
        $bfs=null;
        if ($recomended_sizes){
        $lowest_variance=null;
        foreach ($recomended_sizes as $key => $value) {
            if($lowest_variance==null || $value['ideal_variance']<=$lowest_variance){
                $lowest_variance=$value['ideal_variance'];
                $bfs = $value;
            }
        }                
        return $bfs;
       }
    }
     #----------------------------- Size Thing ~~~~~~~~~~~~~~~~~>
    function getSizeFeedBack($size) {
       $fb = $this->getFeedBack();

        if ($fb == null)
            return;
        if ($size == null || !isset($size))
            return 'no size';
        
        if ($fb['best_fit']['id']==$size->getId()){ # if it matches best fit            
            return array(
                'feedback' => $fb['best_fit'],
                );
        }
        
        foreach ($fb['feedback'] as $size_fb) {            
            if ($size_fb['id'] ==  $size->getId()){ 
                #return array($size_fb['description'] => $size_fb);
                return array(
                'feedback' => $size_fb,
                'recommendation' => $fb['best_fit'],
                );
            }        
        }
               
        return null;
    }   
#----------------------------------------------------------       
    private function get_loose_size_messages($sizes) {

        foreach ($sizes as $size) {
            foreach ($size['fit_points'] as $fp) {
                if ($fp['status'] == $this->status['below_low']) {
                    $count = $this->get_loose_size_rating($sizes, $size, $fp['fit_point']);
                    if ($count == 1) {
                        $fp['status'] == $this->status['one_size_below_low'];
                        $fp['message'] = $this->get_fp_status_text($fp['status']);
                    } elseif ($count == 2) {
                        $fp['status'] == $this->status['two_size_below_low'];
                        $fp['message'] = $this->get_fp_status_text($fp['status']);
                    } elseif ($count > 2) {
                        $fp['status'] == $this->status['more_size_below_low'];
                        $fp['message'] = $this->get_fp_status_text($fp['status']);
                    }
                }
            }
        }
        return $sizes;
    }
#----------------------------------------------------------       
    private function get_loose_size_rating($sizes, $current_size, $fit_point) {
        $count = 0;
        foreach ($sizes as $size) {
            if ($size['fit_points'][$fit_point]['status'] == $this->status['between_low_high'] || $count > 0) {
                $count++;
            }
            if ($size['id'] == $current_size['id']) {
                break;
            }
        }
        return $count;
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

    #------------------------------------------------
 
    private function inseam_diff_message($body_specs, $item_specs) {        
        $str = '';
        if (array_key_exists('inseam', $item_specs) && array_key_exists('inseam', $body_specs)) {
            $body_specs = $this->calculate_body_hem_bits($body_specs);
            
        $diff=$item_specs['inseam']['max'] - $body_specs['inseam'];
        if (4.5 < $diff){
            $str = 'too long, hem';
        }elseif (3.25 <=$diff && $diff <= 4.5) {
            $str = 'very long, hem or wear with 4” – 5” heels';
        } elseif(2.25 <=$diff && $diff <= 3.5) {
            $str = 'long, hem or wear with 3” – 4" heels';
        } elseif(1.25 <=$diff && $diff <= 2.5) {
            $str = 'long, hem or wear with 2" - 3” heels';
        } elseif(0 <=$diff && $diff <= 1.5) {
            $str = 'long, hem or wear with 1” – 2” heels';
        } elseif(-1 <=$diff && $diff <= -0.5) {
            $str = 'perfect fit wear with flats or heels';
        } elseif(-2.5 <=$diff && $diff <=-1) {
            $str = 'short';
        } elseif(-4 <=$diff && $diff<=-2.5) {
            $str = 'ankle length';
        } elseif(-6 <=$diff && $diff<=-4) {
            $str = 'cropped';
        } elseif(-6 > $diff) {
            $str = 'too short';
        }

        return $this->getFeedbackArrayElement('inseam', $item_specs['inseam']['ideal_body_low'], $item_specs['inseam']['ideal_body_high'],  $body_specs['inseam'], $diff, 0, true, $str);
        }else{
            return;
        }
    }
    
    private function calculate_body_hem_bits($body_specs){
        $head=0;        
        if (array_key_exists('height', $body_specs) && $body_specs['height']>0) {
            $head=$body_specs['height']/8;
            if (array_key_exists('inseam', $body_specs) && $body_specs['inseam']>0){
                $body_specs['knee_height'] = $body_specs['inseam']-($head * 2);
                $body_specs['ankle_height'] = $head * 0.25;
                $body_specs['mid_calf_height']=0;
            }else{
                $body_specs['knee_height']=0;
                $body_specs['ankle_height']=0;
                $body_specs['mid_calf_height']=0;        
            }
        }        
        return $body_specs;
    }
    
}
/*
 * A perfect body is 8 heads high, total. 

The neck space is 1/4 of one head length, under the chin of the top first head. 
The second head starts under this neck space. 
One quarter of one head down on this second head is the shoulder line and clavicle. This leaves space for the for the neck support muscles. 

The shoulder line is two head lengths (two widths on a female) wide and is the top line of the torso triangle that extends down to the top of the hip triangle. 
The chin to the shoulder line is 1/2 of one heads length. That is, 1/4 of a head for the neck space and 1/4 head down on the second head. 
The nipple line equals one head length, at the bottom of the second head, the younger the higher. Males stay higher. 
The nipples to the belly button equals one head length. 
From the belly button to the space between the legs is one head length, that's the bottom of the third trunk head. This between the leg space is actually 4 1/4 heads down from the top, including the 1/4 head neck space. It's 3 and 3/4 heads up from the bottom of the feet, for a total of 8 heads high. The two center heads overlap by 1/4 head, the top of the bend line triangle is 4 heads up from the base. The top three torso heads were lowered 1/4 head because of the empty neck space. 
The width of the waist at the belly button is one head length wide. 
From the top line of the hip/trunk triangle to the space between the legs is 3/4's of one head, and is two head widths wide. Not more. 

The center of the body is the bend line, which forms the top line of this small, third, bend line triangle. Its top line is 1/4 head above the space between the legs and two head widths wide. Not more. 
This bend line can also be measured as four heads up from the base, which has no added 1/4 head space for the neck as happened in the top 4 heads. 
In other words.. 
The big torso triangle is from the shoulder line to the top of the hip/trunk triangle. The second triangle is the trunk triangle, the hip bones down to the space between the legs. The third triangle, the bend line triangle is a quarter head high, within the trunk triangle, starting at the space between the legs. 

The rib cage can be represented by an oval two heads high, starting at the top of the (lowered) second head to the bottom of the third head. 
The bottom of the forth head from the top plus the neck space is the space between the legs. 
The upper arm, from the shoulder triangles outside edge, is one and one half heads long. 
The lower arm is one and one quarter heads long. 
The hand is 3/4 of a head long, equal to the average face up to the hair line. 
The chest side view is one head, width wide, at the nipples. 
The upper arm, which is one and one half head lengths long, connects from the center of the shoulder ball which is a quarter head circle. 
Just below the leg space, the legs and the body are the widest. 
Two egg shaped heads, side by side, upside down, will fit in the trunk area. 

From the outside point of the bend line triangle down to the bottom of the knee cap is two head lengths. 
The bend line is always the center of the body. 
The knee cap is a 1/4 head length circle. 
The calf muscle is higher on the outside. 
From the center of the knee cap to the ground is two head lengths. 
The ankle is 1/4 head high off the ground. 
The foot is one head length long. 
The ankle bone is higher on the inside.
 */