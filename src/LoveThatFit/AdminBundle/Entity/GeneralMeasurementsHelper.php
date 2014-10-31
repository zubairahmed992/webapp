<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\Yaml\Parser;

class GeneralMeasurementsHelper {

    protected $conf;

    public function __construct() {
        $conf_yml = new Parser();
        $this->conf = $conf_yml->parse(file_get_contents('../app/config/general_measurements.yml'));
    }
#---------------- ------------------------------------------#

    public function getManGeneralMeasurement() {
        return $this->conf["general_measurements"]["man"];
    }

#---------------------------------------------------------------------------
 public function getMeasurementByNeck($neck_size) {
        $manMeasurements = $this->getManGeneralMeasurement();
        $averageArray = array();
        foreach ($manMeasurements as $key => $value) {
            if ($value['neck'] == $neck_size) {
                $averageArray['key'] = $key;
            }
        }
        if(isset($averageArray['key'])){
        return $this->getmeasurementByKey($averageArray['key']);}
        else{
        return false;    
        }
    }

#----------------------------------------------------------------------------
    public function getmeasurementByKey($key) {
        return $this->conf["general_measurements"]["man"][$key];
    }

}