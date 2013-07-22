<?php

namespace LoveThatFit\SiteBundle;

use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ProductItem;
use LoveThatFit\AdminBundle\Entity\ClothingType;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class Algorithm {

    var $user;
    var $product;
    var $user_measurement;
    var $product_measurement;
    var $adjustment;
    var $msg_array;
    var $feedback_array;

    function __construct($user, $product_item) {
        $this->setUser($user);
        $this->setProduct($product_item);
    }

//------------------------------------------------------------------------

    function setProduct($product_item) {
        if ($product_item) {
            $this->product = $product_item->getProduct();
            $this->product_measurement = $product_item->getProductSize();
        }
    }

    //------------------------------------------------------------------------
    function setUser($user) {
        if ($user) {
            $this->user = $user;
            $this->user_measurement = $this->user->getMeasurement();
        }
    }

//------------------------------------------------------------------------

    function setProductMeasurement($product_size) {
        if ($product_size) {
            $this->product_measurement = $product_size;
            $this->product = $product_size->getProduct();
        }
    }

//------------------------------------------------------------------------

    function getFeedBackJson() {
        return json_encode($this->getFeedBackArray());
    }

    //------------------------------------------------------------------------
    function getFeedBackArray() {
        if (!$this->user_measurement) {
            return "Please update your profile in order to get suggetions.";
        }

        if (!$this->product_measurement) {
            return "Product not found.";
        }

        $this->feedback_array = $this->getBasicFeedbackArray();

        return $this->getRecomendations();
    }

    //------------------------------------------------------------------------

    public function fit($sug_array = null) {

        if (!$this->user_measurement) {
            return false;
        }

        if (!$this->product_measurement) {
            return false;
        }
        if ($sug_array == null) {
            $sug_array = $this->getBasicFeedbackArray();
        }
        if ($sug_array != null) {

            foreach ($sug_array as $key => $value) {
                if ($value["fit"] == false) {
                    return false;
                }
            }
            return true;
        } else {
            return false;
        }
    }

    // ------------------------------------------------------

    function getBasicFeedbackArray() {

        if ($this->user->getGender() == 'm') {
            if ($this->product->getClothingType()->getTarget() == 'Top') {
                //chest neck & sleeve* / back, waist                
                return array(
                    "neck" => $this->getNeckFeedback(),
                    "chest" => $this->getChestFeedback(),
                );
            } elseif ($this->product->getClothingType()->getTarget() == 'Bottom') {
                //waist & inseam / outseam
                return array(
                    "waist" => $this->getWaistFeedback(),
                );
            } else {
                return null;
            }
        } elseif ($this->user->getGender() == 'f') {

            if ($this->product->getClothingType()->getTarget() == 'Top') {
                //bust, waist, back & sleeve*                
                return array(
                    "bust" => $this->getBustFeedback(),
                    "waist" => $this->getWaistFeedback(),
                );
            } elseif ($this->product->getClothingType()->getTarget() == 'Bottom') {
                //waist, hip, inseam / outseam
                return array(
                    "waist" => $this->getWaistFeedback(),
                    "hip" => $this->getHipFeedback(),
                );
            } elseif ($this->product->getClothingType()->getTarget() == 'Dress') {
                //bust, waist, back, hip & sleeve*
                return array(
                    "bust" => $this->getBustFeedback(),
                    "waist" => $this->getWaistFeedback(),
                    "hip" => $this->getHipFeedback(),
                );
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    //------------------------------------------------------------------------

    function getAdditionalFeedbackArray() {

        if ($this->user->getGender() == 'm') {
            if ($this->product->getClothingType()->getTarget() == 'Top') {
                return array(
                    "back" => $this->getBackFeedback(),
                );
            } elseif ($this->product->getClothingType()->getTarget() == 'Bottom') {
                return array(
                    "inseam" => $this->getInseamFeedback(),
                    "outseam" => $this->getOutseamFeedback(),
                );
            } else {
                return null;
            }
        } elseif ($this->user->getGender() == 'f') {
            if ($this->product->getClothingType()->getTarget() == 'Top') {
                return array(
                    "back" => $this->getBackFeedback(),
                );
            } elseif ($this->product->getClothingType()->getTarget() == 'Bottom') {
                return array(
                    "inseam" => $this->getInseamFeedback(),
                    "outseam" => $this->getOutseamFeedback(),
                );
            } elseif ($this->product->getClothingType()->getTarget() == 'Dress') {
                return array(
                    "back" => $this->getBackFeedback(),
                );
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

//------------------- comparison methods
    //neck back chest bust sleeve waist outseam inseam hip length 

    private function getNeckFeedback() {
        return $this->getMessageFill('neck', $this->compare($this->user_measurement->getNeck(), $this->product_measurement->getNeckMin(), $this->product_measurement->getNeckMax()));
    }

    private function getBackFeedback() {
        return $this->getMessageFill('back', $this->compare($this->user_measurement->getBack(), $this->product_measurement->getBackMin(), $this->product_measurement->getBackMax()));
    }

    private function getChestFeedback() {
        return $this->getMessageFill('chest', $this->compare($this->user_measurement->getChest(), $this->product_measurement->getChestMin(), $this->product_measurement->getChestMax()));
    }

    private function getBustFeedback() {
        return $this->getMessageFill('bust', $this->compare($this->user_measurement->getBust(), $this->product_measurement->getBustMin(), $this->product_measurement->getBustMax()));
    }

    private function getSleeveFeedback() {
        return $this->getMessageFill('sleeve', $this->compare($this->user_measurement->getSleeve(), $this->product_measurement->getSleeveMin(), $this->product_measurement->getSleeveMax()));
    }

    private function getWaistFeedback() {
        return $this->getMessageFill('waist', $this->compare($this->user_measurement->getWaist(), $this->product_measurement->getWaistMin(), $this->product_measurement->getWaistMax()));
    }

    private function getOutseamFeedback() {
        return $this->getMessageFill('outseam', $this->compare($this->user_measurement->getOutseam(), $this->product_measurement->getOutseamMin(), $this->product_measurement->getOutseamMax()));
    }

    private function getInseamFeedback() {
        return $this->getMessageFill('inseam', $this->compare($this->user_measurement->getInseam(), $this->product_measurement->getInseamMin(), $this->product_measurement->getInseamMax()));
    }

    private function getHipFeedback() {
        return $this->getMessageFill('hip', $this->compare($this->user_measurement->getHip(), $this->product_measurement->getHipMin(), $this->product_measurement->getHipMax()));
    }

//----------------------------------------------------------------------    
    public function getMessageFill($measuring_point, $comparison_result) {

        if (is_null($measuring_point) || strlen($measuring_point) == 0) {
            return null;
        }

        $this->setMessageArray();

        $diff = $comparison_result["diff"];

        if ($comparison_result["user_measurement"] == false && $comparison_result["item_measurement"] == false) {
            return array("diff" => $diff, "msg" => 'Measurement not provided.', 'fit' => false);
        } elseif ($comparison_result["user_measurement"] == true && $comparison_result["item_measurement"] == false) {
            return array("diff" => $diff, "msg" => $this->msg_array["{$measuring_point}"]['item_na'], 'fit' => false);
        } elseif ($comparison_result["user_measurement"] == false && $comparison_result["item_measurement"] == true) {
            return array("diff" => $diff, "msg" => $this->msg_array["{$measuring_point}"]['user_na'], 'fit' => false);
        }


        if ($diff > 0) {
            //add loose message //add diff //fits boolean false
            return array("diff" => $diff, "msg" => $this->msg_array["{$measuring_point}"]['loose'], 'fit' => false);
        } elseif ($diff < 0) {
            //add tight message //add diff //fits boolean false
            return array("diff" => $diff, "msg" => $this->msg_array["{$measuring_point}"]['tight'], 'fit' => false);
        } else {
            //get love message //add 0 or inclination //fits boolean true
            return array("diff" => $diff, "msg" => $this->msg_array["{$measuring_point}"]['fit'], 'fit' => true);
        }
    }

//----------------------------------------------------------------------

    protected function compare($u, $p_min, $p_max) {
        // incase if any measurement not provided
        if ((is_null($u) || $u == 0) && (is_null($p_min) || is_null($p_max))) {
            return array("user_measurement" => false, "item_measurement" => false, 'diff' => null);
        } elseif (is_null($u) || $u == 0) {
            return array("user_measurement" => false, "item_measurement" => true, 'diff' => null);
        } elseif (is_null($p_min) || is_null($p_max)) {
            return array("user_measurement" => true, "item_measurement" => false, 'diff' => null);
        }

        if ($u <= $p_max && $u >= $p_min) {
            $diff = 0; //love
        } elseif ($u > $p_max) {
            $diff = $p_max - $u; //tight: returns a negative value, difference of measurement in inches
        } elseif ($u < $p_min) {
            $diff = $p_min - $u; //loose: returns a positive value, difference of measurement in inches
        } else {
            $diff = null;
        }
        return array("user_measurement" => true, "item_measurement" => true, 'diff' => $diff);
    }

    //------------------------------------------------------------------------    
    function setMessageArray() {
        if (is_null($this->msg_array)) {
            $yaml = new Parser();
            $this->msg_array = $yaml->parse(file_get_contents('../app/config/fitting_feedback.yml'));
        }
    }

    //------------------------------------------------------------------------    

    public function getFittingSizeFeedBack() {
        $size_fits = $this->getFittingSize();
        if ($size_fits) {
            return array("diff" => 0, "msg" => 'Size ' . $size_fits->getTitle() . '', 'fit' => true);
        }
        return array("diff" => 0, "msg" => 'Your Size not available', 'fit' => false);
    }

    //------------------------------------------------------------------------    

    public function getFittingSize() {

        $productSizes = $this->product->getProductSizes();
        $current_size = $this->product_measurement;
        $size_that_fits = null;
        foreach ($productSizes as $ps) {
            $this->product_measurement = $ps;
            $fits = $this->fit();

            if ($fits) {
                $size_that_fits = $ps;
            }
        }
        $this->product_measurement = $current_size;
        return $size_that_fits;
    }

    //------------------------------------------------------------------------

    public function getRecomendations() {
        $fits = $this->fit($this->feedback_array);
        $recomendations = $this->feedback_array;
        if ($fits) {
            $recomendations = array("basic_fit" => array("diff" => 0, "msg" => 'Love that fit', 'fit' => true));
        } else {

            $size_that_fits = $this->getFittingSize();

            if ($size_that_fits) {
                $recomendations ['tip'] = array("diff" => 0, "msg" => 'Try Size ' . $size_that_fits->getTitle() . '', 'fit' => true);
            } else {
                $tip = $this->getGeneralSuggestion($this->feedback_array);
                $recomendations ['tip'] = array("diff" => 0, "msg" => $tip, 'fit' => $fits);
            }
        }
        return $recomendations;
    }

//------------------------------------------------------------------------


    private function getGeneralSuggestion($sug_array) {

        if ($sug_array != null) {
            $diff = 0;
            $tight = 0;
            $loose = 0;
            foreach ($sug_array as $key => $value) {
                if ($value["fit"] == false) {
                    if ($value["diff"]) {
                        $diff = $diff + $value["diff"];
                        if ($value["diff"] < 0) {
                            $tight = $tight + 1;
                        }
                    }
                }
            }

            if ($tight > 0) {
                return 'Please try bigger sizes.';
            } else {
                if ($diff > 0) {
                    return 'Please try smaller sizes.';
                } else {
                    return 'Please try anoth size.';
                }
            }
        } else {
            return;
        }
    }

    private function _getGeneralSuggestion($sug_array) {

        if ($sug_array != null) {
            $diff = 0;
            foreach ($sug_array as $key => $value) {
                if ($value["fit"] == false) {
                    if ($value["diff"]) {
                        $diff = $diff + $value["diff"];
                    }
                }
            }

            if ($diff > 0) {
                return 'Please try smaller sizes.';
            } elseif ($diff < 0) {
                return 'Please try bigger sizes.';
            } else {
                return 'Please try anoth size.';
            }
        } else {
            return;
        }
    }

}
