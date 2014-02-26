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
            $fb[$size->getTitle()]['fits']=true;
            $variance=0;
            $max_variance=0;
            foreach ($size_specs as $fp_specs) {
                if (is_array($fp_specs) && array_key_exists('id', $fp_specs)){
                    #get fit point specs merged & calculated
                    $fb[$size->getTitle()]['fit_points'][$fp_specs['fit_point']] =
                    $this->get_fit_point_array($fp_specs, $body_specs);                                    
                    $it_fits=$fb[$size->getTitle()]['fit_points'][$fp_specs['fit_point']]['fits'];                                            
                    #if true then keep the old value else assign false ~~~>
                    $fb[$size->getTitle()]['fits'] = $it_fits? $fb[$size->getTitle()]['fits']: $it_fits;                
                    $max_variance=  $this->get_accumulated_variance($max_variance, $fb[$size->getTitle()]['fit_points'][$fp_specs['fit_point']]['max_variance']);
                    $variance = $this->get_accumulated_variance($variance, $fb[$size->getTitle()]['fit_points'][$fp_specs['fit_point']]['variance']);                      
                }
            }
                $fb[$size->getTitle()]['variance'] = $variance;
                $fb[$size->getTitle()]['max_variance'] = $max_variance;
                $fb[$size->getTitle()]['message'] = $fb[$size->getTitle()]['fits']?' fits~~~~~* ': '';
        }
        return $fb;
    }
#-----------------------------------------------------    
    private function get_fit_point_array($fp_specs, $body_specs) {

        $body_measurement = array_key_exists($fp_specs['fit_point'], $body_specs) ? $body_specs[$fp_specs['fit_point']] : 0;

        $variance=$this->get_variance($body_measurement, $fp_specs['ideal_body_size_high'],
                    $fp_specs['ideal_body_size_low'], $fp_specs['max_body_measurement'], $fp_specs['fit_priority']);
        $message=  $this->get_fp_status_text($variance[2]);
        
        return array('fit_point' => $fp_specs['fit_point'],
            'ideal_body_size_low' => $fp_specs['ideal_body_size_low'],
            'ideal_body_size_high' => $fp_specs['ideal_body_size_high'],
            'max_body_measurement' => $fp_specs['max_body_measurement'],
            'fit_priority' => $fp_specs['fit_priority'],
            'body_measurement' => $body_measurement,
            'fits' => $variance[2]==1?true:false,
            'variance' => $variance[0],
            'max_variance' => $variance[1],
            'message'=>$message,
            'status'=>$variance[2],
        );
    }
    
#-----------------------------------------------------
    private function get_variance($body, $high, $low, $max, $priority) {

        if ($high==0 || $low==0 || $max==0){
            return array(null, null, $this->fit_point_comparison_status['product_measurement_not_available']);
        }elseif ($body==0 ){
            return array(null, null, $this->fit_point_comparison_status['body_measurement_not_available']);
        }
        
        $mid_of_high_max = ($max + $high) / 2;
        $varience = 0; $max_varience = 0; $status = 0; 

        if ($body >= $low && $body <= $high) { #perfect
            $status = 0; 
        } elseif ($body < $low) { #loose
            $status = $this->fit_point_comparison_status['below_low'];
            $varience = $this->calculate_variance($body, $low, $priority);
            $max_varience = $varience;
        } else {#tight
            if ($body > $high && $body < $mid_of_high_max) { #high max 1st half    
                $status = $this->fit_point_comparison_status['first_half_high_mid_max'];
            } elseif ($body > $mid_of_high_max && $body < $max) { #high max 2nd half
                $status = $this->fit_point_comparison_status['second_half_high_mid_max'];
            } elseif ($body >= $max) { #not fitting
                $status = $this->fit_point_comparison_status['beyond_max'];
            }
            $varience = $this->calculate_variance($body, $high, $priority);
            $max_varience = $this->calculate_variance($body, $mid_of_high_max, $priority);
        }

        return array($varience, $max_varience, $status);
    }
    
    #----------------------------------------------------------
    private function calculate_variance($body, $item, $priority) {
        if ($item > 0 && $body > 0) {
            $diff = $item - $body;
            $diff_percent = ($diff / $item) * 100;
            return number_format(($priority * $diff_percent) / 100, 2, '.', '');
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
    private function get_fp_status_text($id){                        
          $str=array_search($id,$this->fit_point_comparison_status);
          return str_replace('_', ' ', $str);
    }
    #----------------------------------------------------------
    var $fit_point_comparison_status=array(
        'fit_point_dose_not_match'=>-6,
        'body_measurement_not_available'=>-5,
        'product_measurement_not_available'=>-4,
        'between_low_high'=>0,
        'first_half_high_mid_max'=>-1,
        'second_half_high_mid_max'=>-2,
        'beyond_max'=>-3,
        'below_low'=>1,
        'one_size_below_low'=>2,
        'two_size_below_low'=>3,
        'more_size_below_low'=>4,
    );
    #----------------------------------------------------------
     var $size_comparison_status=array(
        'body_measurement_not_available'=>-5,
        'product_measurement_not_available'=>-4,
        'perfect_fit'=>0,
        'loose'=>1,
        'loose_and_between_high_mid_max'=>-11,
        'between_high_mid_max'=>-1,
        'beyond_max'=>-3,
    );
     
      
    
}