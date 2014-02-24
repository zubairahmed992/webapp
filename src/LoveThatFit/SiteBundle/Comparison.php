<?php

namespace LoveThatFit\SiteBundle;

use Symfony\Component\Yaml\Parser;

class Comparison {

    private $user;
    private $product;

    function __construct($user = null, $product = null) {
        $this->user = $user;
        $this->product = $product;
    }

    function getFeedBackJSON() {
        return json_encode($this->array_mix());
    }

    function getComparison() {
        return $this->array_mix();
    }

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
                     
                        $v = $fb[$size->getTitle()][$fp_specs['fit_point']]['variance'];
                        $m_v = $fb[$size->getTitle()][$fp_specs['fit_point']]['max_variance'];
                        
                        if (($variance>=0 && $v>=0)|| ($variance<=0 && $v<=0)){
                              $variance=$variance+$v;    
                        }elseif ($variance<0 && $v>0){
                             $variance=$variance;    
                        }else{
                             $variance=$v;    
                        }
                        
                        if (($max_variance>=0 && $m_v>=0) ||($max_variance<=0 && $m_v<=0)){
                            $max_variance=$max_variance+$m_v;
                        }elseif ($max_variance<0 && $m_v>0){
                            $max_variance=$max_variance;
                        }else{
                            $max_variance=$m_v;
                        }
                }
            }
                $fb[$size->getTitle()]['message'] = $fb[$size->getTitle()]['fits']?' fits~~~~~* ': ' v:'. $variance.' mv:'. $max_variance;
        }
        return $fb;
    }
    
    private function get_fit_point_array($fp_specs, $body_specs) {

        $body_measurement = array_key_exists($fp_specs['fit_point'], $body_specs) ? $body_specs[$fp_specs['fit_point']] : 0;

        $fits=false;
        if ($body_measurement >= $fp_specs['ideal_body_size_low'] && 
                    $body_measurement<=$fp_specs['ideal_body_size_high']) $fits=true;
        $variance=$this->calculate_variance($body_measurement, $fp_specs['ideal_body_size_high'],
                    $fp_specs['ideal_body_size_low'], $fp_specs['max_body_measurement'], $fp_specs['fit_priority']);
        
        return array('fit_point' => $fp_specs['fit_point'],
            'ideal_body_size_low' => $fp_specs['ideal_body_size_low'],
            'ideal_body_size_high' => $fp_specs['ideal_body_size_high'],
            'max_body_measurement' => $fp_specs['max_body_measurement'],
            'fit_priority' => $fp_specs['fit_priority'],
            'body_measurement' => $body_measurement,
            'fits' => $fits,
            'variance' => $variance[0],
            'max_variance' => $variance[1],
            'status'=>$variance[2],
        );
    }
    private function calculate_variance($body, $high, $low, $max, $priority){
        
        $mid_of_high_max = ($max + $high) / 2;
        $diff=0;
        $diff_percent = 0;
        $m_diff=0;
        $m_diff_percent = 0;
        $status='';
        
        if($body>=$low && $body<=$high){ #perfect
            $diff=0;
            $status='ideal';
        }elseif($body<$low){ #loose
            $status='loose';
            $diff=$low-$body;
            $diff_percent = ($diff / $low) * 100;
            $m_diff=$diff;
            $m_diff_percent = $diff_percent;
        }elseif($body>$high && $body<$mid_of_high_max){ #high max 1st half    
            $status='1st half';
            $diff=$high-$body;
            $diff_percent = ($diff / $high) * 100;
            $m_diff=$mid_of_high_max-$body;
            $m_diff_percent = ($m_diff / $mid_of_high_max) * 100;
        }elseif($body>$mid_of_high_max && $body<$max){ #high max 2nd half
            $status='2st half';
            $diff=$high-$body;
            $diff_percent = ($diff / $high) * 100;
            $m_diff=$mid_of_high_max-$body;
            $m_diff_percent = ($m_diff / $mid_of_high_max) * 100;
        }elseif($body>=$max){ #not fitting
            $status='beyond max';
            $diff=$high-$body;
            $diff_percent = ($diff / $high) * 100;
            $m_diff=$mid_of_high_max-$body;
            $m_diff_percent = ($m_diff / $mid_of_high_max) * 100;
        }
        $varience = number_format(($priority * $diff_percent) / 100, 2, '.', '');
        $max_varience = number_format(($priority * $m_diff_percent) / 100, 2, '.', '');
                
        return array($varience, $max_varience, $status);
    }
 
}