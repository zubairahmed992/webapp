<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\Yaml\Parser;

class ProductSpecificationHelper {

    protected $conf;
   
    public function __construct() {
        $conf_yml = new Parser();
        $this->conf = $conf_yml->parse(file_get_contents('../app/config/config_product_specification.yml'));
        
    }
#-GEtting All Product Specification-------------------------#
public function getProductSpecification(){
    return $this->conf["constants"]["product_specification"];
}
#--------------Reading Garment Type------------------------#
public function getGarmentType(){
    return $this->conf["constants"]["product_specification"]["garment_type"];
}

#--------------Reading Styling Type Of Blouse------------------------#

public function getBlouseStylingType(){
    return $this->conf["constants"]["product_specification"]["style_type"]["blouse"];
}

#--------------Reading Styling Type Of tunic------------------------#

public function getTunicStylingType(){
    return $this->conf["constants"]["product_specification"]["style_type"]["tunic"];
}

#--------------Reading Styling Type Of tee_knit------------------------#

public function getTeeKnitStylingType(){
    return $this->conf["constants"]["product_specification"]["style_type"]["tee_knit"];
}

#--------------Reading Styling Type Of tank_knit------------------------#

public function getTankKnitStylingType(){
    return $this->conf["constants"]["product_specification"]["style_type"]["tank_knit"];
}
#--------------Reading Styling Type Of jacket------------------------#

public function getJacketStylingType(){
    return $this->conf["constants"]["product_specification"]["style_type"]["jacket"];
}
#--------------Reading Styling Type Of sweater------------------------#

public function getSweaterStylingType(){
    return $this->conf["constants"]["product_specification"]["style_type"]["sweater"];
}
#--------------Reading Styling Type Of trouser------------------------#

public function getTrouserStylingType(){
    return $this->conf["constants"]["product_specification"]["style_type"]["trouser"];
}
#--------------Reading Styling Type Of jean------------------------#

public function getJeanStylingType(){
    return $this->conf["constants"]["product_specification"]["style_type"]["jean"];
}
#--------------Reading Styling Type Of skirt------------------------#

public function getSkirtStylingType(){
    return $this->conf["constants"]["product_specification"]["style_type"]["skirt"];
}
#--------------Reading Styling Type Of dress------------------------#

public function getDressStylingType(){
    return $this->conf["constants"]["product_specification"]["style_type"]["dress"];
}
#--------------Reading Styling Type Of coat------------------------#

public function getCoatStylingType(){
    return $this->conf["constants"]["product_specification"]["style_type"]["coat"];
}
#--------------Reading Styling Type Of Hemlength of blouse----------------------#
public function getHemlengthBlouse(){
    return $this->conf["constants"]["product_specification"]["hem_length"]["blouse"];
    
}
#--------------Reading Styling Type Of Hemlength of Tunic----------------------#
public function getHemlengthTunic(){
    return $this->conf["constants"]["product_specification"]["hem_length"]["tunic"];
    
}
#--------------Reading Styling Type Of Hemlength of tee_knit----------------------#
public function getHemlengthTeeKnit(){
    return $this->conf["constants"]["product_specification"]["hem_length"]["tee_knit"];
    
}
#--------------Reading Styling Type Of Hemlength of tank_knit----------------------#
public function getHemlengthTankKnit(){
    return $this->conf["constants"]["product_specification"]["hem_length"]["tank_knit"];
    
}

#--------------Reading Styling Type Of Hemlength of jacket----------------------#
public function getHemlengthJacket(){
    return $this->conf["constants"]["product_specification"]["hem_length"]["jacket"];
    
}#--------------Reading Styling Type Of Hemlength of Sweater----------------------#
public function getHemlengthSweater(){
    return $this->conf["constants"]["product_specification"]["hem_length"]["sweater"];
    
}
#--------------Reading Styling Type Of Hemlength of trouser----------------------#
public function getHemlengthtrouser(){
    return $this->conf["constants"]["product_specification"]["hem_length"]["trouser"];
    
}
#--------------Reading Styling Type Of Hemlength of jeans----------------------#
public function getHemlengthJean(){
    return $this->conf["constants"]["product_specification"]["hem_length"]["jean"];
    
}
#--------------Reading Styling Type Of Hemlength of skirt----------------------#
public function getHemlengthSkirt(){
    return $this->conf["constants"]["product_specification"]["hem_length"]["skirt"];
    
}
#--------------Reading Styling Type Of Hemlength of dress----------------------#
public function getHemlengthDress(){
    return $this->conf["constants"]["product_specification"]["hem_length"]["dress"];
}
#--------------Reading Styling Type Of Hemlength of Coat----------------------#
public function getHemlengthCoat(){
    return $this->conf["constants"]["product_specification"]["hem_length"]["coat"];
}
#---------------Neck Line ------------------------------------#
public function getNeckLineBlouse(){
    return $this->conf["constants"]["product_specification"]["neck_line"]["blouse"];
}

public function getNeckLineTunic(){
    return $this->conf["constants"]["product_specification"]["neck_line"]["tunic"];
}
public function getNeckLineTeeKnit(){
    return $this->conf["constants"]["product_specification"]["neck_line"]["tee_knit"];
}
public function getNeckLineTankKnit(){
    return $this->conf["constants"]["product_specification"]["neck_line"]["tank_knit"];
}
public function getNeckLineJacket(){
    return $this->conf["constants"]["product_specification"]["neck_line"]["jacket"];
}
public function getNeckLineSweater(){
    return $this->conf["constants"]["product_specification"]["neck_line"]["sweater"];
}
public function getNeckLineTrouser(){
    return $this->conf["constants"]["product_specification"]["neck_line"]["trouser"];
}
public function getNeckLineJean(){
    return $this->conf["constants"]["product_specification"]["neck_line"]["jean"];
}
public function getNeckLineSkirt(){
    return $this->conf["constants"]["product_specification"]["neck_line"]["skirt"];
}
public function getNeckLineDress(){
    return $this->conf["constants"]["product_specification"]["neck_line"]["dress"];
}
public function getNeckLineCoat(){
    return $this->conf["constants"]["product_specification"]["neck_line"]["coat"];
}
#---Seleeving Styling -----------------------------------------------#
public function getSleeveStylingBlouse(){
    return $this->conf["constants"]["product_specification"]["sleeve_styling"]["blouse"];
}
public function getSleeveStylingTunic(){
    return $this->conf["constants"]["product_specification"]["sleeve_styling"]["tunic"];
}
public function getSleeveStylingTeeKnit(){
    return $this->conf["constants"]["product_specification"]["sleeve_styling"]["tee_knit"];
}
public function getSleeveStylingTankKnit(){
    return $this->conf["constants"]["product_specification"]["sleeve_styling"]["tank_knit"];
}
public function getSleeveStylingJacket(){
    return $this->conf["constants"]["product_specification"]["sleeve_styling"]["jacket"];
}
public function getSleeveStylingSweater(){
    return $this->conf["constants"]["product_specification"]["sleeve_styling"]["sweater"];
}
public function getSleeveStylingJean(){
    return $this->conf["constants"]["product_specification"]["sleeve_styling"]["jean"];
}
public function getSleeveStylingSkirt(){
    return $this->conf["constants"]["product_specification"]["sleeve_styling"]["skirt"];
}
public function getSleeveStylingdress(){
    return $this->conf["constants"]["product_specification"]["sleeve_styling"]["dress"];
}
public function getSleeveStylingCoat(){
    return $this->conf["constants"]["product_specification"]["sleeve_styling"]["coat"];
}

#---Getting The Rise-----------------------------------------------#
public function getRiseBlouse(){
    return $this->conf["constants"]["product_specification"]["rise"]["blouse"];
}
public function getRiseTunic(){
    return $this->conf["constants"]["product_specification"]["rise"]["tunic"];
}
public function getRiseTeeKnit(){
    return $this->conf["constants"]["product_specification"]["rise"]["tee_knit"];
}
public function getRiseTankKnit(){
    return $this->conf["constants"]["product_specification"]["rise"]["tank_knit"];
}
public function getRiseJacket(){
    return $this->conf["constants"]["product_specification"]["rise"]["jacket"];
}
public function getRiseSweater(){
    return $this->conf["constants"]["product_specification"]["rise"]["sweater"];
}
public function getRiseJean(){
    return $this->conf["constants"]["product_specification"]["rise"]["jean"];
}
public function getRiseSkirt(){
    return $this->conf["constants"]["product_specification"]["rise"]["skirt"];
}
public function getRisedress(){
    return $this->conf["constants"]["product_specification"]["rise"]["dress"];
}
public function getRiseCoat(){
    return $this->conf["constants"]["product_specification"]["rise"]["coat"];
}
#--------------Stretch Type -------------------------#
public function getStretchType(){
    return $this->conf["constants"]["product_specification"]["stretch_type"];
}
#-----------Fabric Weight-----------------------------------------------#
public function getFabricWeight(){
    return $this->conf["constants"]["product_specification"]["fabric_weight"];
    
}
#-------------Structural Details----------------------------#
public function getStructuralDetails(){
    return $this->conf["constants"]["product_specification"]["structural_details"];
}
#-------------Fit Type----------------------------#
public function getFitType(){
    return $this->conf["constants"]["product_specification"]["fit_type"];
}
#-------Layerring----------------------------------------------------#
public function getLayering(){
    return $this->conf["constants"]["product_specification"]["layering"];
}
#----------Fabric Content-----------------------------------------------------#
public function getFabricContent(){
     return $this->conf["constants"]["product_specification"]["fabric_content"];
}
#---------------------Garments Detail------------------------------------------#
public function getGarmentDetail(){
     return $this->conf["constants"]["product_specification"]["garment_detail"];
}

#-----------------Product Attribute -------------------------------------------#
###########################################################################
#------Get All Product Attribute------------#
public function getAllAttribute(){
    return $this->conf["constants"]["clothing_type_attributes"];
}
#---Get Blouse Clothing type attribute----------------------#
public function getBlouseAttribute(){
    return $this->conf["constants"]["clothing_type_attributes"]["blouse"];
}
#------------Get Tunic ---------------------------#
public function getTunicAttribute(){
    return $this->conf["constants"]["clothing_type_attributes"]["tunic"];
}
#----------Getting Product Attribute of TeeKnite----------------------------#
public function getTeeNiteAttribute(){
    return $this->conf["constants"]["clothing_type_attributes"]["tee_knit"];
}
#----------Getting Product Attribute of TankKnite----------------------------#
public function getTankNiteAttribute(){
    return $this->conf["constants"]["clothing_type_attributes"]["tank_knit"];
}
#----------Getting Product Attribute of jacket----------------------------#
public function getJacketAttribute(){
    return $this->conf["constants"]["clothing_type_attributes"]["jacket"];
}
#----------Getting Product Attribute of sweater----------------------------#
public function getSweaterAttribute(){
    return $this->conf["constants"]["clothing_type_attributes"]["sweater"];
}
#----------Getting Product Attribute of Trouser----------------------------#
public function getTrouserAttribute(){
    return $this->conf["constants"]["clothing_type_attributes"]["trouser"];
}
#----------Getting Product Attribute of jean----------------------------#
public function getJeanAttribute(){
    return $this->conf["constants"]["clothing_type_attributes"]["jean"];
}
#----------Getting Product Attribute of Skirt--------------------------#
public function getSkirtAttribute(){
    return $this->conf["constants"]["clothing_type_attributes"]["skirt"];
}
#----------Getting Product Attribute of Dress--------------------------#
public function getDressAttribute(){
    return $this->conf["constants"]["clothing_type_attributes"]["dress"];
}
#----------Getting Product Attribute of Coat--------------------------#
public function getCoatAttribute(){
    return $this->conf["constants"]["clothing_type_attributes"]["coat"];
}
#----------Getting Clothing Type target----------------------------------#
###########################################################################
public function getClothingTypeTarget(){
    return $this->conf["constants"]["clothing_type_target"];
}



 




}