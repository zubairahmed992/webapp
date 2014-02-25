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
                    $fb[$size->getTitle()][$fp_specs['fit_point']] =
                        $this->get_fit_point_array($fp_specs, $body_specs);                
                        $it_fits=$fb[$size->getTitle()][$fp_specs['fit_point']]['fits'];                        
                        #if true then keep the old value else assign false ~~~>
                        $fb[$size->getTitle()]['fits'] = $it_fits? $fb[$size->getTitle()]['fits']: $it_fits;                
                     
                        $max_variance=  $this->get_accumulated_variance($max_variance, $fb[$size->getTitle()][$fp_specs['fit_point']]['max_variance']);
                        $variance = $this->get_accumulated_variance($variance, $fb[$size->getTitle()][$fp_specs['fit_point']]['variance']);                      
                }
            }
                $fb[$size->getTitle()]['message'] = $fb[$size->getTitle()]['fits']?' fits~~~~~* ': ' v:'. $variance.' mv:'. $max_variance;
        }
        return $fb;
    }
#-----------------------------------------------------    
    private function get_fit_point_array($fp_specs, $body_specs) {

        $body_measurement = array_key_exists($fp_specs['fit_point'], $body_specs) ? $body_specs[$fp_specs['fit_point']] : 0;

        $variance=$this->get_variance($body_measurement, $fp_specs['ideal_body_size_high'],
                    $fp_specs['ideal_body_size_low'], $fp_specs['max_body_measurement'], $fp_specs['fit_priority']);
        
        return array('fit_point' => $fp_specs['fit_point'],
            'ideal_body_size_low' => $fp_specs['ideal_body_size_low'],
            'ideal_body_size_high' => $fp_specs['ideal_body_size_high'],
            'max_body_measurement' => $fp_specs['max_body_measurement'],
            'fit_priority' => $fp_specs['fit_priority'],
            'body_measurement' => $body_measurement,
            'fits' => $variance[3]==1?true:false,
            'variance' => $variance[0],
            'max_variance' => $variance[1],
            'message'=>$variance[2],
            'status'=>$variance[3],
        );
    }
    
#-----------------------------------------------------
    private function get_variance($body, $high, $low, $max, $priority) {

        if ($body==0 || $high==0 || $low==0 || $max==0 || $priority==0 || $body==null || $high==null || $low==null || $max==null || $priority==null){
            return array(null, null, 'missing values', 0);
        }
        $mid_of_high_max = ($max + $high) / 2;
        $varience = 0;
        $max_varience = 0;
        $status = 0;
        $message = '';

        if ($body >= $low && $body <= $high) { #perfect
            $varience = 0;
            $max_varience = 0;
            $status = 1;
            $message = 'ideal';
        } elseif ($body < $low) { #loose
            $message = 'loose';
            $status = 2;
            $varience = $this->calculate_variance($body, $low, $priority);
            $max_varience = $this->calculate_variance($body, $low, $priority);
        } else {
            if ($body > $high && $body < $mid_of_high_max) { #high max 1st half    
                $message = '1st half';
                $status = -1;
            } elseif ($body > $mid_of_high_max && $body < $max) { #high max 2nd half
                $message = '2nd half';
                $status = -2;
            } elseif ($body >= $max) { #not fitting
                $message = 'beyond max';
                $status = -3;
            }
            $varience = $this->calculate_variance($body, $high, $priority);
            $max_varience = $this->calculate_variance($body, $mid_of_high_max, $priority);
        }

        return array($varience, $max_varience, $status, $message);
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
    
}