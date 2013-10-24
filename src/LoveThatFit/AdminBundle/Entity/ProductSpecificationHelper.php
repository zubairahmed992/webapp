<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\Yaml\Parser;

class ProductSpecificationHelper {

    protected $conf;
   
    public function __construct() {
        $conf_yml = new Parser();
        $this->conf = $conf_yml->parse(file_get_contents('../app/config/config_product_specification.yml'));
        
    }

#--------------Reading Garment Type------------------------#
public function getGarmentType(){
    return $this->conf["constants"]["garment_type"];
}

#--------------Reading Styling Type Of Blouse------------------------#

public function getBlouseStylingType(){
    return $this->conf["constants"]["style_type"]["blouse"];
}

#--------------Reading Styling Type Of tunic------------------------#

public function getTunicStylingType(){
    return $this->conf["constants"]["style_type"]["tunic"];
}

#--------------Reading Styling Type Of tee_knit------------------------#

public function getTeeKnitStylingType(){
    return $this->conf["constants"]["style_type"]["tee_knit"];
}

#--------------Reading Styling Type Of tank_knit------------------------#

public function getTankKnitStylingType(){
    return $this->conf["constants"]["style_type"]["tank_knit"];
}
#--------------Reading Styling Type Of jacket------------------------#

public function getJacketStylingType(){
    return $this->conf["constants"]["style_type"]["jacket"];
}
#--------------Reading Styling Type Of sweater------------------------#

public function getSweaterStylingType(){
    return $this->conf["constants"]["style_type"]["sweater"];
}
#--------------Reading Styling Type Of trouser------------------------#

public function getTrouserStylingType(){
    return $this->conf["constants"]["style_type"]["trouser"];
}
#--------------Reading Styling Type Of jean------------------------#

public function getJeanStylingType(){
    return $this->conf["constants"]["style_type"]["jean"];
}
#--------------Reading Styling Type Of skirt------------------------#

public function getSkirtStylingType(){
    return $this->conf["constants"]["style_type"]["skirt"];
}
#--------------Reading Styling Type Of dress------------------------#

public function getDressStylingType(){
    return $this->conf["constants"]["style_type"]["dress"];
}
#--------------Reading Styling Type Of coat------------------------#

public function getCoatStylingType(){
    return $this->conf["constants"]["style_type"]["coat"];
}
#--------------Reading Styling Type Of Hemlength of blouse----------------------#
public function getHemlengthBlouse(){
    return $this->conf["constants"]["hem_length"]["blouse"];
    
}
#--------------Reading Styling Type Of Hemlength of Tunic----------------------#
public function getHemlengthTunic(){
    return $this->conf["constants"]["hem_length"]["tunic"];
    
}
#--------------Reading Styling Type Of Hemlength of tee_knit----------------------#
public function getHemlengthTeeKnit(){
    return $this->conf["constants"]["hem_length"]["tee_knit"];
    
}
#--------------Reading Styling Type Of Hemlength of tank_knit----------------------#
public function getHemlengthTankKnit(){
    return $this->conf["constants"]["hem_length"]["tank_knit"];
    
}

#--------------Reading Styling Type Of Hemlength of jacket----------------------#
public function getHemlengthJacket(){
    return $this->conf["constants"]["hem_length"]["jacket"];
    
}#--------------Reading Styling Type Of Hemlength of Sweater----------------------#
public function getHemlengthSweater(){
    return $this->conf["constants"]["hem_length"]["sweater"];
    
}
#--------------Reading Styling Type Of Hemlength of trouser----------------------#
public function getHemlengthtrouser(){
    return $this->conf["constants"]["hem_length"]["trouser"];
    
}
#--------------Reading Styling Type Of Hemlength of jeans----------------------#
public function getHemlengthJean(){
    return $this->conf["constants"]["hem_length"]["jean"];
    
}
#--------------Reading Styling Type Of Hemlength of skirt----------------------#
public function getHemlengthSkirt(){
    return $this->conf["constants"]["hem_length"]["skirt"];
    
}
#--------------Reading Styling Type Of Hemlength of dress----------------------#
public function getHemlengthDress(){
    return $this->conf["constants"]["hem_length"]["dress"];
}
#--------------Reading Styling Type Of Hemlength of Coat----------------------#
public function getHemlengthCoat(){
    return $this->conf["constants"]["hem_length"]["coat"];
}
#---------------Neck Line ------------------------------------#
public function getNeckLineBlouse(){
    return $this->conf["constants"]["neck_line"]["blouse"];
}

public function getNeckLineTunic(){
    return $this->conf["constants"]["neck_line"]["tunic"];
}
public function getNeckLineTeeKnit(){
    return $this->conf["constants"]["neck_line"]["tee_knit"];
}
public function getNeckLineTankKnit(){
    return $this->conf["constants"]["neck_line"]["tank_knit"];
}
public function getNeckLineJacket(){
    return $this->conf["constants"]["neck_line"]["jacket"];
}
public function getNeckLineSweater(){
    return $this->conf["constants"]["neck_line"]["sweater"];
}
public function getNeckLineTrouser(){
    return $this->conf["constants"]["neck_line"]["trouser"];
}
public function getNeckLineJean(){
    return $this->conf["constants"]["neck_line"]["jean"];
}
public function getNeckLineSkirt(){
    return $this->conf["constants"]["neck_line"]["skirt"];
}
public function getNeckLineDress(){
    return $this->conf["constants"]["neck_line"]["dress"];
}
public function getNeckLineCoat(){
    return $this->conf["constants"]["neck_line"]["coat"];
}
#---Seleeving Styling -----------------------------------------------#
public function getSleeveStylingBlouse(){
    return $this->conf["constants"]["sleeve_styling"]["blouse"];
}
public function getSleeveStylingTunic(){
    return $this->conf["constants"]["sleeve_styling"]["tunic"];
}
public function getSleeveStylingTeeKnit(){
    return $this->conf["constants"]["sleeve_styling"]["tee_knit"];
}
public function getSleeveStylingTankKnit(){
    return $this->conf["constants"]["sleeve_styling"]["tank_knit"];
}
public function getSleeveStylingJacket(){
    return $this->conf["constants"]["sleeve_styling"]["jacket"];
}
public function getSleeveStylingSweater(){
    return $this->conf["constants"]["sleeve_styling"]["sweater"];
}
public function getSleeveStylingJean(){
    return $this->conf["constants"]["sleeve_styling"]["jean"];
}
public function getSleeveStylingSkirt(){
    return $this->conf["constants"]["sleeve_styling"]["skirt"];
}
public function getSleeveStylingdress(){
    return $this->conf["constants"]["sleeve_styling"]["dress"];
}
public function getSleeveStylingCoat(){
    return $this->conf["constants"]["sleeve_styling"]["coat"];
}

#---Getting The Rise-----------------------------------------------#
public function getRiseBlouse(){
    return $this->conf["constants"]["rise"]["blouse"];
}
public function getRiseTunic(){
    return $this->conf["constants"]["rise"]["tunic"];
}
public function getRiseTeeKnit(){
    return $this->conf["constants"]["rise"]["tee_knit"];
}
public function getRiseTankKnit(){
    return $this->conf["constants"]["rise"]["tank_knit"];
}
public function getRiseJacket(){
    return $this->conf["constants"]["rise"]["jacket"];
}
public function getRiseSweater(){
    return $this->conf["constants"]["rise"]["sweater"];
}
public function getRiseJean(){
    return $this->conf["constants"]["rise"]["jean"];
}
public function getRiseSkirt(){
    return $this->conf["constants"]["rise"]["skirt"];
}
public function getRisedress(){
    return $this->conf["constants"]["rise"]["dress"];
}
public function getRiseCoat(){
    return $this->conf["constants"]["rise"]["coat"];
}
#--------------Stretch Type -------------------------#
public function getStretchType(){
    return $this->conf["constants"]["stretch_type"];
}
#-----------Fabric Weight-----------------------------------------------#
public function getFabricWeight(){
    return $this->conf["constants"]["fabric_weight"];
    
}
#-------------Structural Details----------------------------#
public function getStructuralDetails(){
    return $this->conf["constants"]["structural_details"];
}
#-------------Fit Type----------------------------#
public function getFitType(){
    return $this->conf["constants"]["fit_type"];
}
#-------Layerring----------------------------------------------------#
public function getLayering(){
    return $this->conf["constants"]["layering"];
}
#----------Fabric Content-----------------------------------------------------#
public function getFabricContent(){
     return $this->conf["constants"]["fabric_content"];
}
#---------------------Garments Detail------------------------------------------#
public function getGarmentDetail(){
     return $this->conf["constants"]["garment_detail"];
}



 




}