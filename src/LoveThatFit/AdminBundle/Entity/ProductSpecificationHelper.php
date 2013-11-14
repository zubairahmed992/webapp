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
public function getWomenClothingType(){
    return $this->conf["constants"]["product_specification"]["women"]["clothing_type"];
}

#--------------Reading Styling Type Of Blouse------------------------#

public function getWomenBlouseStylingType(){
    return $this->conf["constants"]["product_specification"]["women"]["style_type"]["blouse"];
}

#--------------Reading Styling Type Of tunic------------------------#

public function getWomenTunicStylingType(){
    return $this->conf["constants"]["product_specification"]["women"]["style_type"]["tunic"];
}

#--------------Reading Styling Type Of tee_knit------------------------#

public function getWomenTeeKnitStylingType(){
    return $this->conf["constants"]["product_specification"]["women"]["style_type"]["tee_knit"];
}

#--------------Reading Styling Type Of tank_knit------------------------#

public function getWomenTankKnitStylingType(){
    return $this->conf["constants"]["product_specification"]["women"]["style_type"]["tank_knit"];
}
#--------------Reading Styling Type Of jacket------------------------#

public function getWomenJacketStylingType(){
    return $this->conf["constants"]["product_specification"]["women"]["style_type"]["jacket"];
}
#--------------Reading Styling Type Of sweater------------------------#

public function getWomenSweaterStylingType(){
    return $this->conf["constants"]["product_specification"]["women"]["style_type"]["sweater"];
}
#--------------Reading Styling Type Of trouser------------------------#

public function getWomenTrouserStylingType(){
    return $this->conf["constants"]["product_specification"]["women"]["style_type"]["trouser"];
}
#--------------Reading Styling Type Of jean------------------------#

public function getWomenJeanStylingType(){
    return $this->conf["constants"]["product_specification"]["women"]["style_type"]["jean"];
}
#--------------Reading Styling Type Of skirt------------------------#

public function getWomenSkirtStylingType(){
    return $this->conf["constants"]["product_specification"]["women"]["style_type"]["skirt"];
}
#--------------Reading Styling Type Of dress------------------------#

public function getWomenDressStylingType(){
    return $this->conf["constants"]["product_specification"]["women"]["style_type"]["dress"];
}
#--------------Reading Styling Type Of coat------------------------#

public function getWomenCoatStylingType(){
    return $this->conf["constants"]["product_specification"]["women"]["style_type"]["coat"];
}
#--------------Reading Styling Type Of Hemlength of blouse----------------------#
public function getWomenHemlengthBlouse(){
    return $this->conf["constants"]["product_specification"]["women"]["hem_length"]["blouse"];
    
}
#--------------Reading Styling Type Of Hemlength of Tunic----------------------#
public function getWomenHemlengthTunic(){
    return $this->conf["constants"]["product_specification"]["women"]["hem_length"]["tunic"];
    
}
#--------------Reading Styling Type Of Hemlength of tee_knit----------------------#
public function getWomenHemlengthTeeKnit(){
    return $this->conf["constants"]["product_specification"]["women"]["hem_length"]["tee_knit"];
    
}
#--------------Reading Styling Type Of Hemlength of tank_knit----------------------#
public function getWomenHemlengthTankKnit(){
    return $this->conf["constants"]["product_specification"]["women"]["hem_length"]["tank_knit"];
    
}

#--------------Reading Styling Type Of Hemlength of jacket----------------------#
public function getWomenHemlengthJacket(){
    return $this->conf["constants"]["product_specification"]["women"]["hem_length"]["jacket"];
    
}#--------------Reading Styling Type Of Hemlength of Sweater----------------------#
public function getWomenHemlengthSweater(){
    return $this->conf["constants"]["product_specification"]["women"]["hem_length"]["sweater"];
    
}
#--------------Reading Styling Type Of Hemlength of trouser----------------------#
public function getWomenHemlengthtrouser(){
    return $this->conf["constants"]["product_specification"]["women"]["hem_length"]["trouser"];
    
}
#--------------Reading Styling Type Of Hemlength of jeans----------------------#
public function getWomenHemlengthJean(){
    return $this->conf["constants"]["product_specification"]["women"]["hem_length"]["jean"];
    
}
#--------------Reading Styling Type Of Hemlength of skirt----------------------#
public function getWomenHemlengthSkirt(){
    return $this->conf["constants"]["product_specification"]["women"]["hem_length"]["skirt"];
    
}
#--------------Reading Styling Type Of Hemlength of dress----------------------#
public function getWomenHemlengthDress(){
    return $this->conf["constants"]["product_specification"]["women"]["hem_length"]["dress"];
}
#--------------Reading Styling Type Of Hemlength of Coat----------------------#
public function getWomenHemlengthCoat(){
    return $this->conf["constants"]["product_specification"]["women"]["hem_length"]["coat"];
}
#---------------Neck Line ------------------------------------#
public function getWomenNeckLineBlouse(){
    return $this->conf["constants"]["product_specification"]["women"]["neck_line"]["blouse"];
}

public function getWomenNeckLineTunic(){
    return $this->conf["constants"]["product_specification"]["women"]["neck_line"]["tunic"];
}
public function getWomenNeckLineTeeKnit(){
    return $this->conf["constants"]["product_specification"]["women"]["neck_line"]["tee_knit"];
}
public function getWomenNeckLineTankKnit(){
    return $this->conf["constants"]["product_specification"]["women"]["neck_line"]["tank_knit"];
}
public function getWomenNeckLineJacket(){
    return $this->conf["constants"]["product_specification"]["women"]["neck_line"]["jacket"];
}
public function getWomenNeckLineSweater(){
    return $this->conf["constants"]["product_specification"]["women"]["neck_line"]["sweater"];
}
public function getWomenNeckLineTrouser(){
    return $this->conf["constants"]["product_specification"]["women"]["neck_line"]["trouser"];
}
public function getWomenNeckLineJean(){
    return $this->conf["constants"]["product_specification"]["women"]["neck_line"]["jean"];
}
public function getWomenNeckLineSkirt(){
    return $this->conf["constants"]["product_specification"]["women"]["neck_line"]["skirt"];
}
public function getWomenNeckLineDress(){
    return $this->conf["constants"]["product_specification"]["women"]["neck_line"]["dress"];
}
public function getWomenNeckLineCoat(){
    return $this->conf["constants"]["product_specification"]["women"]["neck_line"]["coat"];
}
#---Seleeving Styling -----------------------------------------------#
public function getWomenSleeveStylingBlouse(){
    return $this->conf["constants"]["product_specification"]["women"]["sleeve_styling"]["blouse"];
}
public function getWomenSleeveStylingTunic(){
    return $this->conf["constants"]["product_specification"]["women"]["sleeve_styling"]["tunic"];
}
public function getWomenSleeveStylingTeeKnit(){
    return $this->conf["constants"]["product_specification"]["women"]["sleeve_styling"]["tee_knit"];
}
public function getWomenSleeveStylingTankKnit(){
    return $this->conf["constants"]["product_specification"]["women"]["sleeve_styling"]["tank_knit"];
}
public function getWomenSleeveStylingJacket(){
    return $this->conf["constants"]["product_specification"]["women"]["sleeve_styling"]["jacket"];
}
public function getWomenSleeveStylingSweater(){
    return $this->conf["constants"]["product_specification"]["women"]["sleeve_styling"]["sweater"];
}
public function getWomenSleeveStylingJean(){
    return $this->conf["constants"]["product_specification"]["women"]["sleeve_styling"]["jean"];
}
public function getWomenSleeveStylingSkirt(){
    return $this->conf["constants"]["product_specification"]["women"]["sleeve_styling"]["skirt"];
}
public function getWomenSleeveStylingdress(){
    return $this->conf["constants"]["product_specification"]["women"]["sleeve_styling"]["dress"];
}
public function getWomenSleeveStylingCoat(){
    return $this->conf["constants"]["product_specification"]["women"]["sleeve_styling"]["coat"];
}

#---Getting The Rise-----------------------------------------------#
public function getWomenRiseBlouse(){
    return $this->conf["constants"]["product_specification"]["women"]["rise"]["blouse"];
}
public function getWomenRiseTunic(){
    return $this->conf["constants"]["product_specification"]["women"]["rise"]["tunic"];
}
public function getWomenRiseTeeKnit(){
    return $this->conf["constants"]["product_specification"]["women"]["rise"]["tee_knit"];
}
public function getWomenRiseTankKnit(){
    return $this->conf["constants"]["product_specification"]["women"]["rise"]["tank_knit"];
}
public function getWomenRiseJacket(){
    return $this->conf["constants"]["product_specification"]["women"]["rise"]["jacket"];
}
public function getWomenRiseSweater(){
    return $this->conf["constants"]["product_specification"]["women"]["rise"]["sweater"];
}
public function getWomenRiseJean(){
    return $this->conf["constants"]["product_specification"]["women"]["rise"]["jean"];
}
public function getWomenRiseSkirt(){
    return $this->conf["constants"]["product_specification"]["women"]["rise"]["skirt"];
}
public function getWomenRisedress(){
    return $this->conf["constants"]["product_specification"]["women"]["rise"]["dress"];
}
public function getWomenRiseCoat(){
    return $this->conf["constants"]["product_specification"]["women"]["rise"]["coat"];
}
#--------------Stretch Type -------------------------#
public function getWomenStretchType(){
    return $this->conf["constants"]["product_specification"]["women"]["stretch_type"];
}
#-----------Fabric Weight-----------------------------------------------#
public function getWomenFabricWeight(){
    return $this->conf["constants"]["product_specification"]["women"]["fabric_weight"];
    
}
#-------------Structural Details----------------------------#
public function getWomenStructuralDetails(){
    return $this->conf["constants"]["product_specification"]["women"]["structural_details"];
}
#-------------Fit Type----------------------------#
public function getWomenFitType(){
    return $this->conf["constants"]["product_specification"]["women"]["fit_type"];
}
#-------Layerring----------------------------------------------------#
public function getWomenLayering(){
    return $this->conf["constants"]["product_specification"]["women"]["layering"];
}
#----------Fabric Content-----------------------------------------------------#
public function getWomenFabricContent(){
     return $this->conf["constants"]["product_specification"]["women"]["fabric_content"];
}
#---------------------Garments Detail------------------------------------------#
public function getWomenGarmentDetail(){
     return $this->conf["constants"]["product_specification"]["women"]["garment_detail"];
}

#--------------------MALE PRODUCT SPECIFICATION------------------------------#
###############################################################################
#--------------Reading Garment Type------------------------#
public function getManClothingType(){
    return $this->conf["constants"]["product_specification"]["man"]["clothing_type"];
}
#-------------------------Styling Type-----------------------------------------#
#--------------Reading Styling Type Of Blouse------------------------#

public function getManShirtStylingType(){
    return $this->conf["constants"]["product_specification"]["man"]["style_type"]["shirt"];
}
public function getManTankKnitStylingType(){
    return $this->conf["constants"]["product_specification"]["man"]["style_type"]["tank_knit"];
}
public function getManCasualJacketStylingType(){
    return $this->conf["constants"]["product_specification"]["man"]["style_type"]["casual_jacket"];
}
public function getManSweaterStylingType(){
    return $this->conf["constants"]["product_specification"]["man"]["style_type"]["sweater"];
}
public function getManTrouserStylingType(){
    return $this->conf["constants"]["product_specification"]["man"]["style_type"]["trouser"];
}
public function getManJacketStylingType(){
    return $this->conf["constants"]["product_specification"]["man"]["style_type"]["jacket"];
}
public function getManJeanStylingType(){
    return $this->conf["constants"]["product_specification"]["man"]["style_type"]["jean"];
}

public function getManDressStylingType(){
    return $this->conf["constants"]["product_specification"]["man"]["style_type"]["dress"];
}
public function getManCoatStylingType(){
    return $this->conf["constants"]["product_specification"]["man"]["style_type"]["coat"];
}
#-------------------------HEM _LENGTH------------------------------------------#

public function getManHemlengthShirt(){
    return $this->conf["constants"]["product_specification"]["man"]["hem_length"]["shirt"];
}
public function getManHemlengthTankKnit(){
    return $this->conf["constants"]["product_specification"]["man"]["hem_length"]["tank_knit"];
}
public function getManHemlengthCasualJacket(){
    return $this->conf["constants"]["product_specification"]["man"]["hem_length"]["casual_jacket"];
}
public function getManHemlengthSweater(){
    return $this->conf["constants"]["product_specification"]["man"]["hem_length"]["sweater"];
}
public function getManHemlengthTrouser(){
    return $this->conf["constants"]["product_specification"]["man"]["hem_length"]["trouser"];
}
public function getManHemlengthJean(){
    return $this->conf["constants"]["product_specification"]["man"]["hem_length"]["jean"];
}
public function getManHemlengthSportJacket(){
    return $this->conf["constants"]["product_specification"]["man"]["hem_length"]["sport_jacket"];
}
public function getManHemlengthDressJacket(){
    return $this->conf["constants"]["product_specification"]["man"]["hem_length"]["dress_jacket"];
}
public function getManHemlengthCoat(){
    return $this->conf["constants"]["product_specification"]["man"]["hem_length"]["coat"];
}
#------------------------------------NECKLINE----------------------------------#
public function getManNeckLineShirt(){
    return $this->conf["constants"]["product_specification"]["man"]["neck_line"]["shirt"];
}
public function getManNeckLineTankKnit(){
    return $this->conf["constants"]["product_specification"]["man"]["neck_line"]["tank_knit"];
}
public function getManNeckLineCasualJacket(){
    return $this->conf["constants"]["product_specification"]["man"]["neck_line"]["casual_jacket"];
}
public function getManNeckLineSweater(){
    return $this->conf["constants"]["product_specification"]["man"]["neck_line"]["sweater"];
}
public function getManNeckLineSportJacket(){
    return $this->conf["constants"]["product_specification"]["man"]["neck_line"]["sport_jacket"];
}
public function getManNeckLineDressJacket(){
    return $this->conf["constants"]["product_specification"]["man"]["neck_line"]["dress_jacket"];
}
public function getManNeckLineCoat(){
    return $this->conf["constants"]["product_specification"]["man"]["neck_line"]["coat"];
}
#--------------Sleeving Style--------------------------------------------------#
public function getManSleeveStylingShirt(){
    return $this->conf["constants"]["product_specification"]["man"]["sleeve_styling"]["shirt"];
}
public function getManSleeveStylingTankKnit(){
    return $this->conf["constants"]["product_specification"]["man"]["sleeve_styling"]["tank_knit"];
}
public function getManSleeveStylingCasualJacket(){
    return $this->conf["constants"]["product_specification"]["man"]["sleeve_styling"]["casual_jacket"];
}
public function getManSleeveStylingSweater(){
    return $this->conf["constants"]["product_specification"]["man"]["sleeve_styling"]["sweater"];
}
public function getManSleeveStylingSportJacket(){
    return $this->conf["constants"]["product_specification"]["man"]["sleeve_styling"]["sport_jacket"];
}
public function getManSleeveStylingDress(){
    return $this->conf["constants"]["product_specification"]["man"]["sleeve_styling"]["dress"];
}
public function getManSleeveStylingCoat(){
    return $this->conf["constants"]["product_specification"]["man"]["sleeve_styling"]["coat"];
}
#------------------------------STRETCH TYPE------------------------------------#
public function getManStretchType(){
    return $this->conf["constants"]["product_specification"]["man"]["stretch_type"];
}
#----------------------FABRIC WEIGHT-------------------------------------------#
public function getManFabricWeight(){
    return $this->conf["constants"]["product_specification"]["man"]["fabric_weight"];
    
}
#-------------------------STRUCTURAL DETAILS-----------------------------------#
public function getManStructuralDetails(){
    return $this->conf["constants"]["product_specification"]["man"]["structural_details"];
}
#----------------------STYLING DETAILS-----------------------------------------#
public function getManStylingDetails(){
    return $this->conf["constants"]["product_specification"]["man"]["styling_details"];
}
#----------------------FIT TYPE-----------------------------------------------#
public function getManFitType(){
    return $this->conf["constants"]["product_specification"]["man"]["fit_Type"];
  
}
#----------------------LAYERRING------------------------------------------------#
public function getManLayering(){
 return $this->conf["constants"]["product_specification"]["man"]["layering"];   
}
#-----------------------FABRIC CONTENT-----------------------------------------#
public function getManFabricContent(){
    return $this->conf["constants"]["product_specification"]["man"]["fabric_content"];   
}
#-----------------------Garment Details----------------------------------------#
public function getManGarmentDetail(){
    return $this->conf["constants"]["product_specification"]["man"]["garment_detail"];   
}






#-----------------Product Attribute -------------------------------------------#
###########################################################################
#------Get All Product Attribute------------#
public function getAllAttribute(){
    return $this->conf["constants"]["clothing_type_attributes"];
}
#---Get Clothing type attribute----------------------#
public function getAttributesFor($clothing_type){
    return $this->conf["constants"]["clothing_type_attributes"][$clothing_type];
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

#---------Getting Fitting Priority----------------------------------------#
public function gettingAllFittingPriority(){
    return $this->conf["constants"]["product_specification"]["fit_priority"];   
}
#------------Fitting Priority for Man--------------------------------------#
public function gettingManFittingPriority(){
    return $this->conf["constants"]["product_specification"]["fit_priority"]["man"];   
}
#------------Fitting Priority for Women--------------------------------------#
public function gettingWomenFittingPriority(){
    return $this->conf["constants"]["product_specification"]["fit_priority"]["women"];   
}
#----Man Fitting Attribute----------------------------#
public function gettingTopManFittingPriority(){
    return $this->conf["constants"]["product_specification"]["fit_priority"]["man"]["top"];   
}
public function gettingBottomManFittingPriority(){
    return $this->conf["constants"]["product_specification"]["fit_priority"]["man"]["bottom"];   
}
#-----------Women Fittinf priority----------------------------------------#
public function gettingTopWomenFittingPriority(){
    return $this->conf["constants"]["product_specification"]["fit_priority"]["women"]["top"];   
}
public function gettingBottomWomenFittingPriority(){
    return $this->conf["constants"]["product_specification"]["fit_priority"]["women"]["bottom"];   
}
public function gettingDressWomenFittingPriority(){
    return $this->conf["constants"]["product_specification"]["fit_priority"]["women"]["dress"];   
}




 




}