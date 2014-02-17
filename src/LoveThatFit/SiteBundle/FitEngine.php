<?php

namespace LoveThatFit\SiteBundle;
use LoveThatFit\AdminBundle\Entity\ProductItemHelper;

class FitEngine {

    private $user;
    private $product;
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
        $this->product = $product_item->getProduct();        
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

        if ($fitting_sizes && array_key_exists(0, $fitting_sizes)) {
            return $product_color->getItemBySizeId($fitting_sizes[0]['id']);
        } else {
            return;
        }
    }
#------------------------------------------    
    function getFittingSize($product = null) {
        # used to have the dragable image link for a fitting size
        if ($product === NULL) {
            $product = $this->product_item->getProduct();
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
                    #$loose_fit_rec = $feedback;
                    #array_push($loose_fit_rec , $feedback);
                   array_unshift($loose_fit_rec , $feedback);
                    
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
        $fit_size = array();
        $tight_size =  array();
        $loose_size =  array();
        

        $lowest_varience=null;
        
        foreach ($sizes as $size) {
            $item_specs = $size->getMeasurementArray();
            $feedback = $this->fits($priority, $body_specs, $item_specs);
            #$str_size=" ~> " . $size->getDescription() . " : ". $feedback['msg'];
            $str_size = $size->getDescription();
            $message = " Try size " . $size->getDescription();
            if ($feedback['fit']) {
                    $fit_rec.= $str_size;
                    array_push($fit_size, array('id'=>$size->getId(),'size_title'=>$size->getTitle(),'size_description'=>$size->getDescription(),'status'=>$feedback['status'], 'variance'=>$feedback['varience'], 'message'=>$message));
            } elseif ($feedback['status']==0) {
                    $tight_fit_rec .= $str_size;
                    array_push($tight_size, array('id'=>$size->getId(),'size_title'=>$size->getTitle(),'size_description'=>$size->getDescription(),'status'=>$feedback['status'], 'variance'=>$feedback['varience'], 'message'=>$message));
            }elseif ($feedback['status']==2) {
                if ($lowest_varience == null || $lowest_varience > $feedback['varience']){
                    $lowest_varience=$feedback['varience'];
                    $loose_fit_rec = $str_size;
                    $loose_size = array('id'=>$size->getId(),'size_title'=>$size->getTitle(),'size_description'=>$size->getDescription(),'status'=>$feedback['status'], 'variance'=>$feedback['varience'], 'message'=>$message);
                    }        
            }
            $logger.="   ".$size->getDescription() ."|||   status(".$feedback['status'].")   vaience:".$feedback['varience'];
        }
        
        $str=" Try size ";        
        if (strlen($fit_rec) > 0) {#$str=" Perfect fitting Size ".$fit_rec;    
            $str = $str . $fit_rec;
            return $fit_size[0];
        } elseif (strlen($tight_fit_rec) > 0) {# tight sizes
            $str = $str . $tight_fit_rec;
            return $tight_size[0];
        } elseif (strlen($loose_fit_rec) > 0) {#$str="  Try size " . $loose_fit_rec;    
            $str = $str . $loose_fit_rec;
            return $loose_size;
        } else {
            return ;
        }        
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
            $this->product = $current_item->getProduct();            
            $measurement_array = $this->product_size->getMeasurementArray();
            $fp_array = $this->product->getFitPriorityArray();
            if(!$fp_array){
                $fp_array=array();
            }
            $body_measurement = $this->user->getMeasurement()->getArray();

            foreach ($fp_array as $key => $value) {
                $fb = $this->evaluate_fit_point_get_feedback($body_measurement, $measurement_array, strtolower($key), $value);
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
            $recomended_size = $this->getFittingSizeRecommendation();
            //$recomended_size=  json_encode($recomended_size);
            if ($recomended_size && $recomended_size['id'] != $current_item->getProductSize()->getId()){
            $feed_back['Tip'] = $this->getFeedbackArrayElement(null, null, null, 0, null, true, $recomended_size['message']);
            }
        }
        $feed_back['fits']=$is_ltf ;
        /*
        if($product->getHemLength()=='Full Length'){            
            $feed_back ['inseam'] = $this->inseam_diff_message($body_measurement, $measurement_array);
        }
         *
         */
         
        return $feed_back;
    }
    #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    private function evaluate_fit_point($body_specs, $item_specs, $fit_point, $fit_priority = null) {

        if ($fit_point === NULL || $fit_priority === NULL || $fit_priority <= 0) {
            return null;
        }

        $ideal_low = null; $ideal_high = null; $body = null;
        //------------------------------
        $diff = null; $varience_index = null; $diff_percent = null;
        //------------------------------        
        $str = "";
        //---------------------------
        $fit = false; $max_fit = false; $ideal_fit = false;

        if (array_key_exists($fit_point, $item_specs) && array_key_exists($fit_point, $body_specs)) {
            $ideal_high = $item_specs[$fit_point]['ideal_body_high']; #~~~~~~~~~>
            $ideal_low = $item_specs[$fit_point]['ideal_body_low']; #~~~~~~~~~>
            $body = $body_specs[$fit_point]; #~~~~~~~~~>
            if ($body_specs[$fit_point] <= $item_specs[$fit_point]['ideal_body_high'] && $body_specs[$fit_point] >= $item_specs[$fit_point]['ideal_body_low']) {
                $diff = 0;
                $fit = true;
                $ideal_fit = true;
                $str = "good fit";
            } elseif ($body_specs[$fit_point] < $item_specs[$fit_point]['ideal_body_low']) {
                $diff = $item_specs[$fit_point]['ideal_body_low'] - $body_specs[$fit_point]; #~~~~~~~~~>
                $diff_percent = ($diff / $item_specs[$fit_point]['ideal_body_low']) * 100;
                $varience_index = ($fit_priority * $diff_percent) / 100;
                $str = "loose fit";
            }elseif ($body_specs[$fit_point] > $item_specs[$fit_point]['ideal_body_high']) {
                $str = "tight fit";
            }
        }
        return $this->getFeedbackArrayElement($ideal_low, $ideal_high, $body, $diff, 0, $fit, $str, $ideal_fit, $max_fit, $varience_index);
    }
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    private function evaluate_fit_point_get_feedback($body_specs, $item_specs, $fit_point, $fit_priority = null) {

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
            $max_body_measurement = $item_specs[$fit_point]['max_body_measurement'];
//~~~~~~~~~~~~~~~~~~~~~~~~~~~ Product specs high & low nodes exists 2
            if ($item_specs[$fit_point]['ideal_body_high'] === NULL || $item_specs[$fit_point]['ideal_body_high'] == 0 || $item_specs[$fit_point]['ideal_body_low'] === NULL || $item_specs[$fit_point]['ideal_body_low'] == 0) {
                if ($item_specs[$fit_point]['ideal_body_high'] === NULL || $item_specs[$fit_point]['ideal_body_high'] == 0) {
                    $str = 'Product max ' . $fit_point . ' not available. ';
                } else {
                    $ideal_high = $item_specs[$fit_point]['ideal_body_high']; #~~~~~~~~~>
                }
                if ($item_specs[$fit_point]['ideal_body_low'] === NULL || $item_specs[$fit_point]['ideal_body_low'] == 0) {
                    $str .= 'Product min ' . $fit_point . ' not available. ';
                } else {
                    $ideal_low = $item_specs[$fit_point]['ideal_body_low']; #~~~~~~~~~>
                }
//~~~~~~~~~~~~~~~~~~~~~~~~~~~ body measurement exists 3
            } elseif ($body_specs[$fit_point] === NULL || $body_specs[$fit_point] == 0) {
                $str = 'Please provide you ' . $fit_point . ' measurement ';
                $ideal_high = $item_specs[$fit_point]['ideal_body_high']; #~~~~~~~~~>
                $ideal_low = $item_specs[$fit_point]['ideal_body_low']; #~~~~~~~~~>
            } else {
                $ideal_high = $item_specs[$fit_point]['ideal_body_high']; #~~~~~~~~~>
                $ideal_low = $item_specs[$fit_point]['ideal_body_low']; #~~~~~~~~~>
                $body = $body_specs[$fit_point]; #~~~~~~~~~>
                // if perfect fi 4-a
                if ($body_specs[$fit_point] <= $item_specs[$fit_point]['ideal_body_high'] && $body_specs[$fit_point] >= $item_specs[$fit_point]['ideal_body_low']) {
                    $str = ' great fit ';
                    $diff = 0;
                    $fit = true;
                    $ideal_fit = true;
//------------- if tight 4-b
                } elseif ($body_specs[$fit_point] > $item_specs[$fit_point]['ideal_body_high']) {

                    $str = '';
                    $diff = $item_specs[$fit_point]['ideal_body_high'] - $body_specs[$fit_point]; #~~~~~~~~~>
//1~~~~~~~~~~~~~~~ Check if max measurement exists 4-c
                    if ($item_specs[$fit_point]['max_body_measurement'] != 0) {
                        $diff = $item_specs[$fit_point]['ideal_body_high'] - $body_specs[$fit_point]; #~~~~~~~~~>
                        $max_body_diff = $item_specs[$fit_point]['max_body_measurement'] - $body_specs[$fit_point]; #~~~~~~~~~>
//2~~~~~~~~~~~~~~~ Check if body measurement under max measurement 4-d
                        if ($item_specs[$fit_point]['max_body_measurement'] > $body_specs[$fit_point]) {
                            $max_fit=true;
                            $mid_of_high_max = ($item_specs[$fit_point]['max_body_measurement'] + $item_specs[$fit_point]['ideal_body_high']) / 2;
                            #3~~~~~~~~~~~Tight Fit: User Measurement is in first half of the value between Ideal Body Size High & Max Body Measurement
                            if ($mid_of_high_max > $body_specs[$fit_point]) {
                                $str .= ' tight fight';
                                #4~~~~~~~~~~~Too Tight, restrictive : User Measurement is in second half of the value between Ideal Body Size High & Max Body Measurement
                            } else {
                                $str .= ' too tight, restrictive';
                            }
                        } else {
#5~~~~~~~~~~~Too Small: User Measurement value beyond Max Body Measurement
                            $str .= ' too Small';
                            $max_fit=false;
                        }
                    }
                    

//-------------if loose 4-e
#Feedback  comments for loose item 
#Difference between Ideal Body Size Low & User Measurement
#Loose Fit: if the difference of measurement is equal to One size
#Too Loose, Baggy fit : if the difference is equal to Two sizes
#Too Large: if the difference is equal to Three sizes
                    
                } elseif ($body_specs[$fit_point] < $item_specs[$fit_point]['ideal_body_low']) {
                    $str = ' Loose';
                    #$loose_size_count=0;
                   // $str = $this->get_foo_bar($body_specs,$item_specs[$fit_point]['title'], $item_specs[$fit_point]['title']);
                    //$loose_size_count=$this->get_loose_message($item_specs, $body_specs,$item_specs[$fit_point]['title']);

                    $loose_size_count = $this->get_loose_message($item_specs, $body_specs,$item_specs[$fit_point]['title']);
                    if ($loose_size_count==1){// one size too big
                        $str = ' Loose Fit';
                    }elseif($loose_size_count==2){// two size too big
                        $str = ' Too Loose';
                    }elseif($loose_size_count>=3){// three or more size too big
                     $str = ' Too Large';
                    }

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
#------------------------

    private function get_loose_message($item_specs, $body_specs, $fit_point) {
        $sizes = $this->getSizeTitleArray($this->product->getGender(), $this->product->getSizeTitleType());
        $size_fit_points = $this->product->getProductSizeTitleFitPointArray($fit_point, $item_specs['body_type']);
        $j = 0;
        $in_range = false;
        for ($i = 0; $i < count($sizes) - 1; $i++) {
            if (array_key_exists($sizes[$i], $size_fit_points)) {
                if ($size_fit_points[$sizes[$i]]->getIdealBodySizeHigh() > $body_specs[$fit_point] &&
                        $size_fit_points[$sizes[$i]]->getIdealBodySizeLow() < $body_specs[$fit_point]) {
                    $in_range = true;
                } elseif ($in_range) {
                    if ($item_specs['size_title'] == $sizes[$i])
                        $in_range = false;
                    $j++;
                }
            }
        }
        return $j;
    }

    private function get_foo_bar($body_specs, $size_title, $fit_point){
        $sizes=  $this->getSizeTitleArray();
        $j=0;
        $str="";
        for($i=count($sizes)-1;$i>=0; $i--){
            if($sizes[$i]===$size_title) $j=1;
            $str.=$sizes[$i].' = '.$size_title. ', ';
            if($j!=0){
              $fp_specs=$this->get_size_measurement($sizes[$i], $fit_point);
              if ($fp_specs['ideal_body_high']>$body_specs[$fit_point]){
                $j++;    
              }
            }
        }
        return $str;
        return $j;
    }
    
    
    private function get_size_measurement($size_title, $fit_point) {
        $product = $this->product_item->getProduct();
        $sizes = $product->getProductSizes();

        foreach ($sizes as $size) {
            if ($size->getTitle() == $size_title) {
                $item_specs = $size->getMeasurementArray();
                return $item_specs[$fit_point];
            }
        }
        return;
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
#------------------------
 private function inseam_diff_message($body_specs, $item_specs) {
        $str = '';
        if (array_key_exists('inseam', $item_specs) && array_key_exists('inseam', $body_specs)) {
            
        $diff=$item_specs['inseam']['ideal_body_high'] - $body_specs['inseam'];
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

        return $this->getFeedbackArrayElement($item_specs['inseam']['ideal_body_low'], $item_specs['inseam']['ideal_body_high'],  $body_specs['inseam'], $diff, 0, true, $str);
        }else{
            return;
        }
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
