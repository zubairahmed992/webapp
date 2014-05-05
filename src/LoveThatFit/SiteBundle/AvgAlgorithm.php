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

    private function array_mix($sizes = null) {
        if ($sizes == null) {
            $sizes = $this->product->getProductSizes();
        }
        $body_specs = $this->user->getMeasurement()->getArray();
        $fb = array();

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
            $fb[$size_identifier]['min_variance'] = 0;
            if (is_array($size_specs)) {
                foreach ($size_specs as $fp_specs) {
                    if (is_array($fp_specs) && array_key_exists('id', $fp_specs) && $fp_specs['fit_priority'] > 0) {
                        $fb[$size_identifier]['fit_points'][$fp_specs['fit_point']] =
                                $this->get_fit_point_array($fp_specs, $body_specs);
                                $accumulated = $this->calculate_accumulated_variance($fb[$size_identifier]['fit_points'][$fp_specs['fit_point']],
                                        $fb[$size_identifier]['variance']);
                        $fb[$size_identifier]['variance'] = $accumulated['variance'] ;
                        $fb[$size_identifier]['max_variance'] = $fb[$size_identifier]['max_variance'] + $accumulated['max_variance'];
                    }
                }
                 
            }
        }
        return array('feedback' => $this->array_sort($fb));
    }

# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    private function get_fit_point_array($fp_specs, $body_specs) {

        $low = $fp_specs['ideal_body_size_low'];
        $high = $fp_specs['ideal_body_size_high'];
        $max = $fp_specs['max_body_measurement'];
        $min = $fp_specs['min_body_measurement'];

        $body = array_key_exists($fp_specs['fit_point'], $body_specs) ? $body_specs[$fp_specs['fit_point']] : 0;
        $mid_low_high = ($low + $high) / 2;
        $mid_high_max = ($high + $max) / 2;
        $variance = $this->calculate_variance($body, $mid_low_high, $fp_specs['fit_priority']);
        $min_variance = $this->calculate_variance($min, $mid_low_high, $fp_specs['fit_priority']);
        $max_variance = $this->calculate_variance($max, $mid_low_high, $fp_specs['fit_priority']);
        
        $fits = null;
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


        return array('fit_point' => $fp_specs['fit_point'],
            'label' => $this->getFitPointLabel($fp_specs['fit_point']),
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
            'status'=> $status,
            'message'=>$this->get_fp_status_text($status),
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
        }
        #$str=array_search($id,$this->status);
        #return str_replace('_', ' ', $str);
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
    );
}
