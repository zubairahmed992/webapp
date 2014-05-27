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
            $rc = $this->getFittingSize($cm['feedback']);
            return array(
                'feedback' => $cm['feedback'],
                'recommendation' => $rc,
            );
        } else {
            return array(
                'message' => 'Fit Priority is not set, please update the product detail.',
            );
        }
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
    
#--------------------------------------------------------
    private function getFittingSize($sizes) {
        if ($sizes == null)
            return;
        $lowest_variance = null;        
        $best_fit = null;

        foreach ($sizes as $size) {
            if ($size['status'] != $this->status['beyond_max']
                    && $size['status'] != $this->status['below_min']) {
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
#-----------------------------------------------------    
 function getSizeFeedBack($size) {
       
        if ($size == null || !isset($size))
            return 'no size';
        
       $this->product = $size->getProduct();
       $fb = $this->getFeedBack(); 
       
        if ($fb['recommendation']['id']==$size->getId()){ # if it matches best fit            
            return array(
                'feedback' => $fb['recommendation'],
                );
        }
        
        foreach ($fb['feedback'] as $size_fb) {            
            if ($size_fb['id'] ==  $size->getId()){ 
                #return array($size_fb['description'] => $size_fb);
                return array(
                'feedback' => $size_fb,
                'recommendation' => $fb['recommendation'],
                );
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
                foreach ($size_specs as $fp_specs) {
                    if (is_array($fp_specs) && array_key_exists('id', $fp_specs) && $fp_specs['fit_priority'] > 0) {
                        $fb[$size_identifier]['fit_points'][$fp_specs['fit_point']] =
                                $this->get_fit_point_array($fp_specs, $body_specs);
                                $accumulated = $this->calculate_accumulated_variance($fb[$size_identifier]['fit_points'][$fp_specs['fit_point']],
                                        $fb[$size_identifier]['variance']);
                        $fb[$size_identifier]['variance'] = $accumulated['variance'] ;
                        $fb[$size_identifier]['max_variance'] = $fb[$size_identifier]['max_variance'] + $accumulated['max_variance'];
                        $fb[$size_identifier]['status'] = $this->get_accumulated_status($fb[$size_identifier]['status'], $fb[$size_identifier]['fit_points'][$fp_specs['fit_point']]['status']);
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
            }
            $hem_bits = $this->get_hem_advice($size_specs, $body_specs);
            if ($hem_bits) $fb[$size_identifier]['hem_advice'] = $hem_bits;
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
        
        
        if($variance[0]<0){
            $fit_index = $this->grade_to_scale($variance[0], $max_variance[0], 0);
        }elseif ($variance[0]>0){            
            $fit_index = $this->grade_to_scale($variance[0], $min_variance[0], 0);
        }else{            
            $fit_index = 0;
        }
        
        $status=null;
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
        $size_titles = $this->getSizeTitleArray($this->product->getGender(), $this->product->getSizeTitleType());
        $size_types = $this->getSizeTypes($this->product->getGender());
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
    private function getSizeTitleArray($gender = 'f', $type = 'numbers') {
        $gender = strtolower($gender);
        $type = strtolower($type);

        if ($type == 'letters' || $type == 'letter') {//letters
            return array('XS', 'S', 'M', 'L', 'X', 'XL', '1XL', '1X', 'XXL', '2X', '2XL', 'XXXL', '3XL', '3X', 'XXXXL', '4XL', '4X');
        } else if ($gender == 'f' && ($type == 'number' || $type == 'numbers')) {//$female_standard
            return array('00', '0', '2', '4', '6', '8', '10', '12', '14', '16', '18', '20', '22', '24', '26', '28', '30');
        } else if ($gender == 'f' && $type == 'waist') {//$female_waist
            return array('23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35', '36');
        } else if ($gender == 'm' && $type == 'chest') {//man Chest
            return array('35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47', '48');
        } else if ($gender == 'm' && $type == 'waist') {//man bottom
            return array('28', '29', '30', '31', '32', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42');
        } else if ($gender == 'm' && $type == 'neck') {//man neck
            return array('14', '14.5', '15', '15.5', '16', '16.5', '17', '17.5', '18', '18.5', '19', '20', '22');
        }else if ($gender == 'm' && $type == 'shirt') {//man shirt
            return array('14-32', '14-33', '14-34', '14-35', '14-36',
'14.5-32', '14.5-33', '14.5-34', '14.5-35', '14.5-36',
'15-32', '15-33', '15-34', '15-35', '15-36',
'15.5-32', '15.5-33', '15.5-34', '15.5-35', '15.5-36',
'16-32', '16-33', '16-34', '16-35', '16-36',
'16.5-32', '16.5-33', '16.5-34', '16.5-35', '16.5-36',
'17-32', '17-33', '17-34', '17-35', '17-36',
'17.5-32', '17.5-33', '17.5-34', '17.5-35', '17.5-36',
'18-32', '18-33', '18-34', '18-35', '18-36',
'18.5-32', '18.5-33', '18.5-34', '18.5-35', '18.5-36',
'19-32', '19-33', '19-34', '19-35', '19-36',
'20-32', '20-33', '20-34', '20-35', '20-36',
'22-32', '22-33', '22-34', '22-35', '22-36');
        }
    }
        /*
         Man: letter, chest, shirt, neck, waist
         Woman: letter, number, waist, bra
         */

   #----------------------------------------------------------       

    private function getSizeTypes($gender='f') {
        if($gender=='m'){
            return array('Regular', 'Athletic', 'Tall', 'Portley');
        }else{
            return array('Regular', 'Petite', 'Tall', 'Plus');
        }
        
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
}
