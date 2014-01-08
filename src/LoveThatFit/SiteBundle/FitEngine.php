<?php

namespace LoveThatFit\SiteBundle;

class FitEngine {

    private $user;
    private $product_item;
    private $product_size;
    private $user_measurement;

    function __construct($user = null, $product_item = null) {
        if ($user) $this->setUser($user);
        if ($product_item) $this->setProductItem($product_item);
    }

#----------------------------------------------------------------------------------------------------
    function setProductItem($product_item) {
        $this->product_item = $product_item;
        $this->product_size = $product_item->getProductSize();
    }
#--------------------->
    function setUser($user) {
        $this->user = $user;
        $this->user_measurement = $user->getMeasurement();
    }
#--------------------->
    function getProductItem() {
        return $this->product_item;
    }
#--------------------->
    function getUser() {
        return $this->user;
    }
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>
    function getFeedBackJSON() {
        return json_encode($this->getBasicFeedback());
    }
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>
    function getFittingItem($product = null) {
         if ($product === NULL) {
            $product = $this->product_item->getProduct();
        }
        $fitting_sizes = $this->getFittingSize($product);
        $product_color = $product->getDisplayProductColor();        
        return $product_color->getItemBySizeId($fitting_sizes[0]['id']);
        }
#------------------------------------------    
    function getFittingSize($product = null) {
        
        if ($this->product_item === NULL) {
            $current_item = $this->product_item;
        } 
        if ($product === NULL) {
            $product = $current_item->getProduct();
        }
        
        $sizes = $product->getProductSizes();
        $priority = $product->getFitPriorityArray();
        $body_specs = $this->user->getMeasurement()->getArray();
       
        if ($priority===null){
            return null;
        }
        
        $fit_rec = array();
        $tight_fit_rec = array();
        $loose_fit_rec = array();
        $lowest_varience=null;
        foreach ($sizes as $size) {
            $item_specs = $size->getMeasurementArray();#~~~~~~~~>
            $feedback = $this->fits($priority, $body_specs, $item_specs);
            $feedback['id'] =$size->getId();
            if ($feedback['fit']) {
                array_push($fit_rec , $feedback);                    
            } elseif ($feedback['status']==0) {
                    array_push($tight_fit_rec , $feedback);
            }elseif ($feedback['status']==2) {
                if ($lowest_varience == null || $lowest_varience > $feedback['varience']){
                    $lowest_varience=$feedback['varience'];
                    $loose_fit_rec = $feedback;
                    }        
            }
        }
        
        if (count($fit_rec)>0){
        return $fit_rec;    
        }elseif (count($tight_fit_rec)>0){
        return $tight_fit_rec;    
        }elseif (count($loose_fit_rec)>0){
        return $loose_fit_rec;                
        }
        return null;
    }
    
#--------------------------------------------------------------------------------->
#----------------------------   Get Fitting Size  -----------------------------------------------------|
#--------------------------------------------------------------------------------->

    function getFittingSizeRecommendation($current_item = null) {
        if ($current_item === NULL) {
            $current_item = $this->product_item;
        }

        $product = $current_item->getProduct();
        $sizes = $product->getProductSizes();
        $priority = $product->getFitPriorityArray();
        $body_specs = $this->user->getMeasurement()->getArray();
        
        $logger="";
        $fit_rec = "";
        $tight_fit_rec = "";
        $loose_fit_rec = "";
        $lowest_varience=null;
        foreach ($sizes as $size) {
            $item_specs = $size->getMeasurementArray();
            $feedback = $this->fits($priority, $body_specs, $item_specs);
            $str_size=" ~> " . $size->getDescription() . " : ". $feedback['msg'];
            if ($feedback['fit']) {
                    $fit_rec.= $str_size;
            } elseif ($feedback['status']==0) {
                    $tight_fit_rec .= $str_size;
            }elseif ($feedback['status']==2) {
                if ($lowest_varience == null || $lowest_varience > $feedback['varience']){
                    $lowest_varience=$feedback['varience'];
                    $loose_fit_rec = $str_size . " varience index (". $feedback['varience'] .")";
                    }        
            }
            $logger.="   ".$size->getDescription() ."|||   status(".$feedback['status'].")   vaience:".$feedback['varience'];
        }
        
        //if(strlen($tip)==0) $tip= " you are in between sizes.";
        $str="";
        if (strlen($fit_rec)>0){
        $str=" Perfect fitting Size ".$fit_rec;    
        }elseif (strlen($tight_fit_rec)>0){
        $str="  Tight fitting Size ". $tight_fit_rec;    
        }elseif (strlen($loose_fit_rec)>0){
        $str="  Loose fitting Size " . $loose_fit_rec;    
        }
        return $str;
    }

#--------------------------------------------------------------------------------->

    private function fits($priority, $body_specs, $item_specs) {
        #  missing params=-2, tight=-1, max_fit=0, fit=1, loose=2
        $fit = true;
        $status = 1;
        $msg = "";
        $varience = 0;

        foreach ($priority as $key => $value) {
            $fb = $this->evaluate_fit_point($body_specs, $item_specs, strtolower($key), $value);
            if ($fb != NULL) {
                 
                $msg.='  ' .$key.':'. $fb['msg']; # concatinating messages
                # adding evaluation params (fits/max_fits/loose/tight)

                if ($fb['fit'] === false) {
                    $fit = false;
                    if ($fb['ideal_low'] === null || $fb['ideal_high'] === null || $fb['body'] === null) {  #~~~~~~~~~~~~~> params missing
                           $status = -2;
                    }elseif($fb['max_fit']) { #~~~~~~~~~~~~~> max fit
                        if($status != -2 && $status != -1) $status = 0;#if not tight    
                    }elseif ($fb['varience_index'] >0) {  #~~~~~~~~~~~~~> loose
                        $varience = $varience + $fb['varience_index'];
                        if($status != -2 && $status != -1) $status = 2;#if not tight 
                    }else {  #~~~~~~~~~~~~~> tight
                        $status = -1;
                    }
                }
            }
        }
        $varience = number_format($varience, 2, '.', '');        
        return array('fit' => $fit, 'msg' => $msg, 'varience' => $varience, 'status' => $status);
    }


#--------------------------------------------------------------------------------->
#---------------------------  Feedback Methods ------------------------------------------------------|
#--------------------------------------------------------------------------------->
private function getAllKeysTesting($ar){
    $str="";
    foreach ($ar as $key => $value) {
        $str.=$key. " ,";
    }
    return $str;
}#--------------------------------------------
    function getBasicFeedback($current_item = null) {

        $feed_back = array();
        $is_ltf = true;

        if ($current_item === NULL) {
            $current_item = $this->product_item;
        }

        if ($current_item) {
            $product = $current_item->getProduct();
            $measurement_array = $this->product_size->getMeasurementArray();
            $fp_array = $product->getFitPriorityArray();
            $body_measurement = $this->user->getMeasurement()->getArray();

            foreach ($fp_array as $key => $value) {
                $fb = $this->evaluate_fit_point($body_measurement, $measurement_array, strtolower($key), $value);
                if ($fb != NULL) {
                    $feed_back [strtolower($key)] = $fb;
                    if ($fb['fit'] === FALSE) {
                        $is_ltf = false;
                    }
                }
            }
        }
        if ($is_ltf === true) {
            $str = 'Love that Fit!';
            $feed_back = null;
            $feed_back['Overall'] = $this->getFeedbackArrayElement(null, null, null, 0, null, true, $str);
        } else {
            $str = $this->getFittingSizeRecommendation();
            $feed_back['Tip'] = $this->getFeedbackArrayElement(null, null, null, 0, null, true, $str);
        }

        return $feed_back;
    }
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    private function evaluate_fit_point($body_specs, $item_specs, $fit_point, $fit_priority = null) {

        if ($fit_point === NULL || $fit_priority === NULL || $fit_priority <= 0) {
            return null;
        }

        $ideal_low = null;
        $ideal_high = null;
        $max_body_measurement = null;
        $body = null;
        //------------------------------
        $diff = null;
        $max_body_diff = null;
        $varience_index = null;
        $diff_percent = null;
        //------------------------------
        $priority = $fit_priority;
        $str = "";
        //---------------------------
        $fit = false;
        $max_fit = false;
        $ideal_fit = false;


//~~~~~~~~~~~~~~~~~~~~~~~~~~~ check if nodes exists 1
        if (array_key_exists($fit_point, $item_specs) && array_key_exists($fit_point, $body_specs)) {
//~~~~~~~~~~~~~~~~~~~~~~~~~~~ Product specs high & low nodes exists 2
            if ($item_specs[$fit_point]['ideal_body_high'] === NULL || $item_specs[$fit_point]['ideal_body_high'] == 0 || $item_specs[$fit_point]['ideal_body_low'] === NULL || $item_specs[$fit_point]['ideal_body_low'] == 0) {
                if ($item_specs[$fit_point]['ideal_body_high'] === NULL || $item_specs[$fit_point]['ideal_body_high'] == 0) {
                    $str = 'Product maximum ' . $fit_point . ' measurement not available. ';
                } else {
                    $ideal_high = $item_specs[$fit_point]['ideal_body_high']; #~~~~~~~~~>
                }
                if ($item_specs[$fit_point]['ideal_body_low'] === NULL || $item_specs[$fit_point]['ideal_body_low'] == 0) {
                    $str .= 'Product minimam ' . $fit_point . ' measurement not available. ';
                } else {
                    $ideal_low = $item_specs[$fit_point]['ideal_body_low']; #~~~~~~~~~>
                }
//~~~~~~~~~~~~~~~~~~~~~~~~~~~ body measurement exists 3
            } elseif ($body_specs[$fit_point] === NULL || $body_specs[$fit_point] == 0) {
                $str = 'User body ' . $fit_point . ' measurement not provided. ';
                $ideal_high = $item_specs[$fit_point]['ideal_body_high']; #~~~~~~~~~>
                $ideal_low = $item_specs[$fit_point]['ideal_body_low']; #~~~~~~~~~>
            } else {
                $ideal_high = $item_specs[$fit_point]['ideal_body_high']; #~~~~~~~~~>
                $ideal_low = $item_specs[$fit_point]['ideal_body_low']; #~~~~~~~~~>
                $body = $body_specs[$fit_point]; #~~~~~~~~~>
                // if perfect fi 4-a
                if ($body_specs[$fit_point] <= $item_specs[$fit_point]['ideal_body_high'] && $body_specs[$fit_point] >= $item_specs[$fit_point]['ideal_body_low']) {
                    $str = ' (Perfect fit) ';
                    $diff = 0;
                    $fit = true;
                    $ideal_fit = true;
//------------- if tight 4-b
                } elseif ($body_specs[$fit_point] > $item_specs[$fit_point]['ideal_body_high']) {

                    $str = '(Tight)';
                    $diff = $item_specs[$fit_point]['ideal_body_high'] - $body_specs[$fit_point]; #~~~~~~~~~>                        
//~~~~~~~~~~~~~~~ Check if max measurement exists 4-c
                    if ($item_specs[$fit_point]['max_body_measurement'] != 0) {
                        $max_body_measurement = $item_specs[$fit_point]['max_body_measurement']; #~~~~~~~~~>
//~~~~~~~~~~~~~~~ Check if body measurement under max measurement 4-d
                        $max_body_diff = $max_body_measurement - $body_specs[$fit_point];
                        $mid_max = ($max_body_measurement - $item_specs[$fit_point]['ideal_body_high'])/2;
                        
                        if ($max_body_diff > 0) {
                            #Tight Fit: User Measurement is in first half of the value between Ideal Body Size High & Max Body Measurement
                            #Too Tight, restrictive : User Measurement is in second half of the value between Ideal Body Size High & Max Body Measurement
                            $half_point = $mid_max - $max_body_diff;
                          
                           if ($half_point>0){
                               $str .= ' tight fight, high-max:first half';
                           }else{
                               $str .= ' too tight, restrictive, high-max:second half';
                           }
                            $max_fit = true;
                        } else {
#Too Small: User Measurement value beyond Max Body Measurement
                            $str .= 'Too Small, (Exceeds Max Limit)';
                        }
                    }
//-------------if loose 4-e
#Feedback  comments for loose item 
#Difference between Ideal Body Size Low & User Measurement
#Loose Fit: if the difference of measurement is equal to One size
#Too Loose, Baggy fit : if the difference is equal to Two sizes
#Too Large: if the difference is equal to Three sizes
                    
                } elseif ($body_specs[$fit_point] < $item_specs[$fit_point]['ideal_body_low']) {
                    $str = ' (Loose)';
                    $diff = $item_specs[$fit_point]['ideal_body_low'] - $body_specs[$fit_point]; #~~~~~~~~~>
                    //~~~ Check & calculate possible recomendation based on fit priority & diffs

                    $diff_percent = ($diff / $item_specs[$fit_point]['ideal_body_low']) * 100;
                    $varience_index = ($fit_priority * $diff_percent) / 100;
                } else {
                    $str = 'No Comparision';
                }
            }
        } elseif (!array_key_exists($fit_point, $item_specs)) {
            $str = 'Product measurement not found! ' . $fit_point;
        } elseif (!array_key_exists($fit_point, $body_specs)) {
            $str = 'Please enter you measurement.';
        }

        return $this->getFeedbackArrayElement($ideal_low, $ideal_high, $body, $diff, $priority, $fit, $str, $ideal_fit, $max_fit, $varience_index, $diff_percent, $max_body_measurement, $max_body_diff);
    }


#----------------------------------------------------------------------------------------------------
# create array element for the feed back array
    private function getFeedbackArrayElement($ideal_low, $ideal_high, $body, $diff, $priority, $fit, $msg, $ideal_fit = null, $max_fit = null, $varience_index = null, $diff_percent = null, $max_body_measurement = null, $max_body_diff = null) {
        return array(
            'ideal_low' => $ideal_low,
            'ideal_high' => $ideal_high,
            'max_body_measurement' => $max_body_measurement,
            'body' => $body,
            //---------------            
            'diff' => $diff,
            'max_body_diff' => $max_body_diff,
            'diff_percent' => $diff_percent,
            'varience_index' => $varience_index,
            //---------------            
            'priority' => $priority,
            'msg' => $msg,
            //------------------------
            'fit' => $fit,
            'ideal_fit' => $ideal_fit,
            'max_fit' => $max_fit,
        );
    }

}
