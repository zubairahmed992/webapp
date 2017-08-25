<?php

namespace LoveThatFit\ProductIntakeBundle\Controller;

use LoveThatFit\AdminBundle\Entity\ProductCSVDataUploader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;

class ProductSpecsController extends Controller
{
    #----------------------- /product_intake/product_specs/index
    public function indexAction(){
        $ps = $this->get('pi.product_specification')->findAll(); 
        return $this->render('LoveThatFitProductIntakeBundle:ProductSpecs:index.html.twig', array(
            'specs' => $ps,  
             'cs_file'      =>  $this->get('pi.product_specification')->csvDownloads($ps),        
        ));
    }
    
     #----------------------- /product_intake/product_specs/new
    public function newAction(){        
        $brands = $this->get('admin.helper.brand')->getBrnadArray();
        $mapping = $this->get('product_intake.product_specification_mapping')->getAllMappingArray();
        $size_specs = $this->get('admin.helper.size')->getDefaultArray();        
        return $this->render('LoveThatFitProductIntakeBundle:ProductSpecs:new.html.twig', array(
            'brands' => $brands,
            'mapping' => $mapping,            
            'mapping_json' => json_encode($mapping),            
            'size_specs_json' => json_encode($size_specs),
        ));
    }
    
     #----------------------- /product_intake/product_specs/edit
    public function editAction($id, $tab){   
        $ps = $this->get('pi.product_specification')->find($id);
        $product_specs = $this->get('admin.helper.product.specification')->getProductSpecification();      
        $parsed_data = json_decode($ps->getSpecsJson(),true);
        $gender = ($parsed_data['gender'] == 'f')?'women':'man';
        $fms=$this->get('productIntake.fit_model_measurement')->getTitleArray($parsed_data['brand']);  
        $parsed_data['horizontal_stretch']=  $parsed_data['horizontal_stretch'];
        $parsed_data['vertical_stretch']=$parsed_data['vertical_stretch'];
        $gen_specs = $this->get('admin.helper.product.specification')->getProductSpecification(); 
        $drop_down_values = $this->get('admin.helper.product.specification')->getIndividuals(); 
        $drop_down_values['styling_type'] = $this->get('admin.helper.product.specification')->getStylingType($parsed_data['clothing_type']);         
        $drop_down_values['fit_model_size'] = array_flip($fms);      
        //  $drop_down_values['fit_model_size'] = $fms;      
        $clothing_types = ($parsed_data['gender'] == 'f'? $product_specs['women']['clothing_types']:$product_specs['man']['clothing_type']);       
        if(isset($parsed_data['fit_model_size'])){ 
            $fit_model_selected_size= $parsed_data['fit_model_size']==null?null:$this->get('productIntake.fit_model_measurement')->find($parsed_data['fit_model_size']);
            $fit_model_selected = $fit_model_selected_size?$fit_model_selected_size->getSize():null; 
        } else { 
            $fit_model_selected = null;
            $parsed_data['fit_model_size'] = '';
        }    
        foreach ($parsed_data['sizes'] as $key => $size) {
            foreach ($size as $key => $value) {
                $size_attribute[$key] =  $key;
            }
            break;
        }      
        $product_id =0;
        if(array_key_exists('style_id_number', $parsed_data) && array_key_exists('brand', $parsed_data) ){
            $product_array = $this->get('service.repo')->getProductDetailOnly($parsed_data['brand'], $parsed_data['style_id_number']); 
            $product_id = is_array($product_array) && array_key_exists(0, $product_array) ? $product_array[0]['id'] : 0;
        }
        
        return $this->render('LoveThatFitProductIntakeBundle:ProductSpecs:edit.html.twig', array(
                    'product_specs'=>$ps,
                    'parsed_data' => $parsed_data,
                    'product_specs_json' => json_encode($gen_specs),  
                    'drop_down_values' =>$drop_down_values,
                    'fit_model_selected_size' => $fit_model_selected,
                    'fit_point_stretch' => $ps->getFitPointStretchArray(), 
                    'disabled_fields' => array('clothing_type', 'brand', 'gender', 'size_title_type', 'mapping_description', 'mapping_title', 'body_type'),                    
                    'clothing_types' => $clothing_types,
                    'size_attribute' => $size_attribute,
                    'tab' => $tab,
                    'searched_product_id'=>$product_id,
                    'cs_file'      =>  $this->get('pi.product_specification')->csvDownloads($ps),
                ));
    }

#-----------------------------------> product_intake_product_specs_fetch_json:  /product_intake/product_specs/fetch_json/{id}
    public function fetchJsonAction($id, $attrib = null) {
        
        if($attrib=='fit_model'){
            $ps = $this->get('pi.product_specification')->getFitModelMeasurements($id);
             return new response(json_encode($ps));            
        }else{
            $ps = $this->get('pi.product_specification')->find($id);
            if ($ps) {                            
                return new response($ps->getSpecsJson());
            } else {
                return new response('false');
            }    
            
        }
        
        return new response(json_encode($ps));
        
        
    }

    #----------------------- /product_intake/product_specs/show
    public function showAction($id, $json = null) {

        $gen_specs = $this->get('admin.helper.product.specification')->getProductSpecification();
        $ps = $this->get('pi.product_specification')->find($id);
        if ($json) {
            if ($ps) {
                return new response($ps->getSpecsJson());
            } else {
                return new response('false');
            }
        }
        $parsed_data = json_decode($ps->getSpecsJson(), true);
        if (isset($parsed_data['fit_model_size']) && strlen($parsed_data['fit_model_size']) > 0) {
            $fit_model_selected_size = $parsed_data['fit_model_size'] == null ? null : $this->get('productIntake.fit_model_measurement')->find($parsed_data['fit_model_size']);
            $fit_model_selected = $fit_model_selected_size->getSize();
        } else {
            $fit_model_selected = null;
        }

        return $this->render('LoveThatFitProductIntakeBundle:ProductSpecs:show.html.twig', array(
                    'parsed_data' => json_decode($ps->getSpecsJson(), true),
                    'product_specification_id' => $ps->getId(),
                    'product_specs_json' => json_encode($gen_specs),
                    'fit_model_selected_size' => $fit_model_selected,
        ));
    }

    #----------------------- /product_intake/product_specs/delete
    public function deleteAction($id){         
        $remove_csv_file = $this->get('pi.product_specification')->find($id);
        if($remove_csv_file->getAbsolutePath()){
        unlink($remove_csv_file->getAbsolutePath());
        }
        $msg_ar = $this->get('pi.product_specification')->delete($id);          
        $this->get('session')->setFlash($msg_ar['message_type'], $msg_ar['message']);   
        return $this->redirect($this->generateUrl('product_intake_product_specs_index'));
    }
    
    #----------------------- /product_intake/product_specs/create_product
    public function createProductAction($id){            
        $msg = $this->get('pi.product_specification')->create_product($id);        
        $this->get('session')->setFlash('success', $msg['message']);   
        return $this->redirect($this->generateUrl('product_intake_product_specs_index'));        
    }
    
    #----------------------- /product_intake/Prod_specs/update    
    public function updateAction($id){  
        $msg_ar = $this->get('pi.product_specification')->updateAndFill($id, $_POST);        
        $this->get('session')->setFlash($msg_ar['message_type'], $msg_ar['message']);           
        return $this->redirect($this->generateUrl('product_intake_product_specs_edit', array('id' => $id)));        
    }
     #------------------------------------- /product_intake/Prod_specs/update_foo 
    public function updateDynamicAction(){  
        $decoded = $this->getRequest()->request->all();        
        $sizes_json = $this->get('pi.product_specification')->dynamicCalculations($decoded);
        return new Response(json_encode($sizes_json));
        return new Response(json_encode($decoded));
    }
    
    #----------------------- /product_intake/Prod_specs/undo
    public function undoAction($id){  
        $entity = $this->get('pi.product_specification')->find($id);       
        if( $entity->getUndoSpecsJson() ) {
        $entity->setSpecsJson($entity->getUndoSpecsJson());
        $msg_ar = $this->get('pi.product_specification')->update($entity);        
        $this->get('session')->setFlash($msg_ar['message_type'], $msg_ar['message']);              
        } else {
             $this->get('session')->setFlash('success', "Please Firstly Save Data Form then Apply Undo Action!");   
        }
        return $this->redirect($this->generateUrl('product_intake_product_specs_edit', array('id' => $id)));       
    }
    
    ######################################################################################    
    #------------------------------------ /product_intake/product_specs/csv_upload
    public function csvUploadAction(Request $request) {
        #-------------- CSV to array
        $csv_array = $this->csv_to_array($request->files->get('csv_file'));
        #------------------------ get mapping        
        $product_specs_mapping = $this->get('product_intake.product_specification_mapping')->find($request->request->get('sel_mapping'));
        $map = json_decode($product_specs_mapping->getMappingJson(), true);
        #-------------->
        $parsed_data = $this->get('admin.helper.product.specification')->getStructure();
        $parsed_data['gender'] = $product_specs_mapping->getGender();
        $parsed_data['size_title_type'] = $product_specs_mapping->getSizeTitleType();
        foreach ($map['fabric_content'] as $fabric_content_k => $fabric_content_v) {  
            $f_c = $this->extracts_coordinates($fabric_content_v);
            $parsed_data['fabric_content'][$fabric_content_k] = count($f_c) > 1 ? $csv_array[$f_c['r']][$f_c['c']] : $fabric_content_v;
        }   
        unset($map['fabric_content']);
        #----------------- fill array with csv data
        foreach ($map as $specs_k => $specs_v) {           
            if ($specs_k != 'formula') {
                if (is_array($specs_v) || is_object($specs_v)) {
                    foreach ($specs_v as $size_key => $fit_points) {
                        foreach ($fit_points as $fit_pont_key => $fit_model_measurement) {
                            $coordins = $this->extracts_coordinates($fit_model_measurement);
                            $fmm_value = $this->fraction_to_number(floatval($csv_array[$coordins['r']][$coordins['c']]));
                                if($fmm_value != 0){
                                    $original_value = $fmm_value;
                                    #~~~~~~>convert to measuring unit
                                    if (array_key_exists('measuring_unit', $map) && $map['measuring_unit'] == 'centimeter') {
                                        $fmm_value = $fmm_value * 0.393700787;
                                    }
                                    $unit_converted_value = $fmm_value;

                                    #~~~~~~>calculate formula
                                    if (array_key_exists('formula', $map)) {
                                        $fmm_value = $this->upply_formula($map['formula'], $fit_pont_key, $fmm_value);
                                    }

                                    #----------------------* parsed data array calculate fit modle values for fit model size
                                    $parsed_data[$specs_k][$size_key][$fit_pont_key] = array('garment_dimension' => $fmm_value, 'stretch_percentage' => 0, 'garment_stretch' => 0, 'grade_rule' => 0, 'grade_rule_stretch' => 0, 'min_calc' => 0, 'max_calc' => 0, 'min_actual' => 0, 'max_actual' => 0, 'ideal_low' => 0, 'ideal_high' => 0, 'fit_model' => 0, 'prev_garment_dimension' => 0, 'grade_rule' => 0, 'no' => 0,
                                        'original_value' => $original_value,
                                        'unit_converted_value' => $unit_converted_value,
                                    );
                                }
                        }
                    }
                } else if($specs_k == 'max_horizontal_stretch'){
                    $cdns = $this->extracts_coordinates($specs_v);
                    $parsed_data[$specs_k] = count($cdns) > 1 ? $csv_array[$cdns['r']][$cdns['c']] : $specs_v;                                
                    $parsed_data['horizontal_stretch'] =  ($parsed_data[$specs_k]/3);
                } else if( $specs_k == 'max_vertical_stretch'){
                    $cdns = $this->extracts_coordinates($specs_v);
                    $parsed_data[$specs_k] = count($cdns) > 1 ? $csv_array[$cdns['r']][$cdns['c']] : $specs_v;                  
                    $parsed_data['vertical_stretch'] = ($parsed_data[$specs_k]/3);
                } else {#----------------------* if not related to measurements add as a field
                    $cdns = $this->extracts_coordinates($specs_v);
                    $parsed_data[$specs_k] = count($cdns) > 1 ? $csv_array[$cdns['r']][$cdns['c']] : $specs_v;
                }
            }
        }

        #--------------------- calculate fit model measrements & ratio
        if (!array_key_exists('sizes', $parsed_data)) {
            return new Response('Measurements & sizes are missing');
        }
        #------------------------ Grade Rule calculation + sorting of sizes
        $size_specs = $this->get('admin.helper.size')->getDefaultArray();
        $prev_size_key = null;
        $ordered_sizes = array();
        $size_no = 0;
        foreach ($size_specs['sizes'][$parsed_data['gender'] == 'm' ? 'man' : 'woman'][$parsed_data['size_title_type']] as $size_key => $size_title) {
            if (array_key_exists($size_key, $parsed_data['sizes'])) {
                $ordered_sizes['sizes'][$size_key] = $parsed_data['sizes'][$size_key];
            }
        }
        $parsed_data['sizes'] = $ordered_sizes['sizes'];
        #---------> Save to DB
        $specs = $this->get('pi.product_specification')->createNew($product_specs_mapping->getTitle(), $product_specs_mapping->getDescription(), $parsed_data);
        $specs->setSpecFileName('csv_spec_' . $specs->getId() . '.csv');
        $this->container->get('pi.product_specification')->save($specs);
        move_uploaded_file($_FILES["csv_file"]["tmp_name"], $specs->getAbsolutePath());

        $this->get('session')->setFlash('success', 'New Product specification added!');
        return $this->redirect($this->generateUrl('product_intake_product_specs_edit', array('id' => $specs->getId())));
    }
    
#----------------------------
    private function csv_to_array($file){
        $csv_array = array();        
        $i = 0;
        if (($handle = fopen($file->getRealPath(), "r")) !== FALSE) {
            while (($row = fgetcsv($handle)) !== FALSE) {
                for ($j = 0; $j < count($row); $j++) {
                    $csv_array[$i][$j] = $row[$j];
                }
                $i++;
            }
        }
        return $csv_array;
    }
#----------------------------
    private function upply_formula($formula, $fit_pont_key, $fmm_value) {
        if (array_key_exists($fit_pont_key, $formula)) {
            $st = str_replace("x",$fmm_value,$formula[$fit_pont_key]);
            $p = eval('return '.$st.';');
            return $p;
        } else {
            return $fmm_value;
        }
    }
    #----------------------------
    private function fraction_to_number($raw_value) {
        if (strpos($raw_value, '/')) {
            $raw_exploded = explode(' ', $raw_value);
            $frac = explode('/', $raw_exploded[1]);
            return (intval($raw_exploded[0]) + (intval($frac[0]) / intval($frac[1])));
        } else {
            return floatval($raw_value);
        }
    }

#----------------------------------------------------------------    
    private function extracts_coordinates($str) {
        $cdns = explode(',', $str);
        $c = array();
        if (count($cdns) > 1) {
            $c['r'] = intval($cdns[0]);
            $c['c'] = intval($cdns[1]);
        }
        return $c;
    }

    ######################################################################################    
    ######################################################################################    
    ######################################################################################    
    #----------------------- /product_intake/product_specs/compare_new
    public function compareNewAction(){
        
        $brands = $this->get('admin.helper.brand')->getBrnadArray();
        $mapping = $this->get('product_intake.product_specification_mapping')->getAllMappingArray();
        $size_specs = $this->get('admin.helper.size')->getDefaultArray();        
        return $this->render('LoveThatFitProductIntakeBundle:ProductSpecs:new.html.twig', array(
            'brands' => $brands,
            'mapping' => $mapping,            
            'mapping_json' => json_encode($mapping),            
            'size_specs_json' => json_encode($size_specs),
        ));
    }
    #--------------------- /product_intake/product_specs/compare_upload
    public function compareUploadAction(Request $request){  
        $decoded = $request->request->all();  
        $filename = $request->files->get('csv_file');     
        $ps = $this->get('pi.product_specification')->find($decoded['product_specification_id']);      
        $filename_data = ($filename == '')? $ps->getAbsolutePath():$filename;        
        $pcsv = new ProductCSVDataUploader($filename_data);
        $file_data = $pcsv->read();
        #return new response(json_encode($file_data));
        
        $gen_specs = $this->get('admin.helper.product.specification')->getProductSpecification();
        $ps = $this->get('pi.product_specification')->find($decoded['product_specification_id']);         
        $mix = json_decode($ps->getSpecsJson(),true);
        
        return $this->render('LoveThatFitProductIntakeBundle:ProductSpecs:compare.html.twig', array(
                'file_data' => $file_data,
                'parsed_data' => $mix,
                'product_specification_id' => $ps->getId(),
                'product_specs_json' => json_encode($gen_specs),                    
                ));      
    }
   //--------------------   Product Copy to Next Server 
    public function productCopyAction() {
        return $this->render('LoveThatFitProductIntakeBundle:ProductSpecs:product_copy.html.twig');
        
    }
    //------------------------------------- /product_intake/Prod_specs/get_clothing_type 
    public function getClothingTypeAction(){  
        $clothing_type = $this->getRequest()->request->all();   
        $styling_type = $this->get('admin.helper.product.specification')->getStyleType($clothing_type['clothing_type']); 
     return new Response(json_encode($styling_type));
    }
       
    //---------------------------- Create Product Specification from Existing Product
    public function CreateSpecificationAction() {      
         return $this->render('LoveThatFitProductIntakeBundle:ProductSpecs:create_product_specification.html.twig');
       
    }
    
    //-------------------------- Create Product Specification From Existing Product
    public function createProductSpecificationAction(Request $request) {
        $product_id =  $request->get('product_id');
        $data = $this->get('pi.product_specification')->getExistingProductDetails($product_id);
        $this->get('session')->setFlash('success', 'Successfully Create Product Specification From Existing Product');  
        return $this->indexAction();
        return new JsonResponse($data);
    }
    
    //------------------ Update Product Specification
    public function UpdateProductSpecificationAction() {
        return $this->render('LoveThatFitProductIntakeBundle:ProductSpecs:update_product_specification.html.twig');
    }
    
    // --------------------- ExisitingProductUpdateSpecification
    public function ExisitingProductUpdateSpecificationAction(Request $request) {
        $product_id =  $request->get('product_id');
        $specification_id =  $request->get('specification_id');
        $ps = $this->get('pi.product_specification')->find($specification_id);  
        $parsed_data = json_decode($ps->getSpecsJson(),true);       
        $product = $this->get('admin.helper.product')->find($product_id);        
        if (!$product) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }
        $brand = $this->container->get('admin.helper.brand')->findOneByName( $parsed_data['brand'] );
        $clothing_type = $this->container->get("admin.helper.clothingtype")->findOneByGenderName($parsed_data['gender'], $parsed_data['clothing_type'] );
        $product->setBrand($brand);
        $product->setClothingType($clothing_type);
        $product->setName(array_key_exists('style_name', $parsed_data) ? $parsed_data['style_name'] : '');
        $product->setControlNumber(array_key_exists('style_id_number', $parsed_data) ? $parsed_data['style_id_number'] : '');
        #$product->setDescription(array_key_exists('description', $parsed_data) ? $parsed_data['description'] : '');        
        $product->setStretchType(array_key_exists('stretch_type', $parsed_data) ? $parsed_data['stretch_type'] : '');
        $product->setHorizontalStretch($parsed_data['horizontal_stretch']);
        $product->setVerticalStretch($parsed_data['vertical_stretch']);
        $product->setCreatedAt(new \DateTime('now'));
        $product->setUpdatedAt(new \DateTime('now'));
        $product->setGender($parsed_data['gender']);
        $product->setStylingType($parsed_data['styling_type']);
        $product->setNeckline(array_key_exists('neck_line', $parsed_data) ? $parsed_data['neck_line'] : $parsed_data['neckline']);
        $product->setSleeveStyling($parsed_data['sleeve_styling']);
        $product->setRise($parsed_data['rise']);
        $product->setHemLength($parsed_data['hem_length']);
        $product->setFabricWeight($parsed_data['fabric_weight']);
        $product->setStructuralDetail(json_encode($parsed_data['structural_detail']));
        $product->setFitType($parsed_data['fit_type']);
        $product->setLayering(array_key_exists('layring', $parsed_data) ? $parsed_data['layring'] : $parsed_data['layering']);
        $product->setFitPriority(array_key_exists('fit_priority', $parsed_data) ? json_encode($parsed_data['fit_priority']) : 'NULL' );
        $product->setFabricContent(json_encode(array_key_exists('fabric_content', $parsed_data) ? $parsed_data['fabric_content'] : ''));
        $product->setDisabled(false);
        $product->setDeleted(false);
        $product->setSizeTitleType($parsed_data['size_title_type']);       
        $productArray = $this->get('admin.helper.product')->update($product);
        $this->get('pi.product_specification')->getProductSizeMeasurments($parsed_data, $product_id);
        $this->get('session')->setFlash('success', $productArray);
        #return  $this->showAction($specification_id);
         return $this->redirect($this->generateUrl('product_intake_product_specs_show', array('id' => $specification_id)));     
        return new Response(json_encode($productArray));
    }
    #---------------------------------------------------
    public function createSessionAction(Request $request) {
        $session = $request->getSession();
        $session->set('opt_specs_'.$request->get('id'), $request->get('value'));        
        return new Response(json_encode($session->get('opt_specs_'.$request->get('id'), $request->get('value'))));
    }
    
    //------------------ run checks 
    public function runChecksAction($id) { 
         $ps = $this->get('pi.product_specification')->find($id);  
        $parsed_data = json_decode($ps->getSpecsJson(),true);    
        //$size =   $this->get('pi.product_specification')->getProductSizeMeasurments($parsed_data['product_id], $id);
         $data = $this->container->get('service.repo')->getExistingProductDetails($parsed_data['product_id']); 
         
          foreach ($product_sizes_sorted as $key => $product_size_value) {                  
                 foreach ($product_size_value['product_size_measurements'] as  $value) {  
                     $fp = array_key_exists($value['title'], $new_fp) ? $new_fp[$value['title']] : $value['title'];#replacing old fit point with new
                    $stretch_percentage = ($value['garment_measurement_stretch_fit'] == 0)? 0 :(($value['garment_measurement_stretch_fit'] - $value['garment_measurement_flat'])/$value['garment_measurement_flat'])*100;
                    $data1['sizes'][$product_size_value['title']][$fp]['fit_model'] = $value['fit_model_measurement'];
                    $data1['sizes'][$product_size_value['title']][$fp]['garment_dimension'] = $value['garment_measurement_flat'];
                    $data1['sizes'][$product_size_value['title']][$fp]['garment_stretch'] = $value['garment_measurement_stretch_fit'];
                    $data1['sizes'][$product_size_value['title']][$fp]['grade_rule'] = $value['grade_rule'];
                    #$data1['sizes'][$product_size_value['title']][$fp]['grade_rule_stretch'] = $value['horizontal_stretch'];
                    #$data1['sizes'][$product_size_value['title']][$fp]['grade_rule_stretch'] = $value['vertical_stretch'];                
                    $data1['sizes'][$product_size_value['title']][$fp]['stretch_percentage'] = $stretch_percentage;//$value['stretch_type_percentage'];
                    $data1['sizes'][$product_size_value['title']][$fp]['ideal_high'] = $value['ideal_body_size_high'];
                    $data1['sizes'][$product_size_value['title']][$fp]['ideal_low'] = $value['ideal_body_size_low'];
                    $data1['sizes'][$product_size_value['title']][$fp]['max_actual'] = $value['max_body_measurement'];
                    $data1['sizes'][$product_size_value['title']][$fp]['max_calc'] = $value['max_calculated'];
                    $data1['sizes'][$product_size_value['title']][$fp]['min_actual'] = $value['min_body_measurement'];
                    $data1['sizes'][$product_size_value['title']][$fp]['min_calc'] = $value['min_calculated'];
                    $data1['body_type'] = $product_size_value['body_type'];
                    $data1['fit_point_stretch'][$fp] = $stretch_percentage;
                }
             }
         
         
        echo "<pre>";
        print_r($data[0][0]['product_sizes']);
         print_r($parsed_data['sizes']);
        echo $id;
        die;
        
    }
    //~~~~Validate product specification sizes---------product_intake/validate_product_specification 
    public function validateProductSpecificationAction($id) {
        $ps = $this->get('pi.product_specification')->find($id);
        $parsed_data = json_decode($ps->getSpecsJson(), true);
        $result = array();
          //2. Flag when fit priority for a fit point is assigned to an incorrect garment:
        //--Tops, dresses, and skirts should not have thigh assigned fit priority
        //--Skirts, pants, and shorts should not have bust assigned fit priority    
        $clothing_type = ["blouse","tunic","tee_knit","tank_knit","jackets","sweater","skirt","dress","coat","shirt"];
        if ( in_array($parsed_data['clothing_type'],$clothing_type) ) {           
            if( array_key_exists('thigh', $parsed_data['fit_priority']) ){                
               $result['fit_priority_assigned_to_an_incorrect_garment'] = " ~~ Clothing Type ".$parsed_data['clothing_type']. " of fit point tigh is assigned to an incorrect garment";
            }
        } else{
            if( array_key_exists('bust', $parsed_data['fit_priority']) ){
               $result['fit_priority_assigned_to_an_incorrect_garment'] = " ~~  Clothing Type ". $parsed_data['clothing_type'] . "  of fit point bust is assigned to an incorrect garment";
            }
        }       
        foreach ($parsed_data['sizes'] as $key => $product_size_value) {
            $size_title = array_keys($parsed_data['sizes']);
            $size_count = count($size_title);
            foreach ($product_size_value as $key1 => $value) {
                //1. Garment Dimension minus max actual for each size should not be 0 or a negative number.
                $rule_one = $value['garment_dimension'] - $value['max_actual'];
                if ($rule_one <= 0) {
                    $result['garment_dimension_minus_max_actual'][$key][$key1] = $rule_one . " ~~ 1.Garment Dimension minus max actual for each size should not be 0 or a negative number";
                }
                //3. Ranges are sequential (ex. Fit Model Dimension, Min Actual, Max Actual, Ideal High, Ideal Low, Garment Dimensions should all be smaller than the same value in the next size up. i.e. Fit Model Bust in size S should be smaller than in size M.)
                //return  new Response(json_encode($result));
                $find_next_elements = array_search($key, $size_title) + 1;
                $next_size_title = ($find_next_elements < $size_count) ? $size_title[$find_next_elements] : null;
                $next_array_elements = (in_array($next_size_title, $size_title)) ? $parsed_data['sizes'][$next_size_title] : null;
                if ($next_array_elements) {
                    if ($value['garment_dimension'] > $next_array_elements[$key1]['garment_dimension']) {
                        $result['sequential'][$key][$key1] = $value['garment_dimension'] . ' ~~ Garment Dimensions grather than next Size';
                    }
                    if ($value['fit_model'] > $next_array_elements[$key1]['fit_model']) {
                        $result['sequential'][$key][$key1] = $value['fit_model'] . ' ~~ Fit Model Dimension grather than next Size';
                    }
                    if ($value['min_actual'] > $next_array_elements[$key1]['min_actual']) {
                        $result['sequential'][$key][$key1] = $value['min_actual'] . ' ~~ Min Actual grather than next Size';
                    }
                    if ($value['max_actual'] > $next_array_elements[$key1]['max_actual']) {
                        $result['sequential'][$key][$key1] = $value['max_actual'] . ' ~~ Max Actual grather than next Size';
                    }
                    if ($value['ideal_high'] > $next_array_elements[$key1]['ideal_high']) {
                        $result['sequential'][$key][$key1] = $value['ideal_high'] . ' ~~ Ideal High grather than next Size';
                    }
                    if ($value['ideal_low'] > $next_array_elements[$key1]['ideal_low']) {
                        $result['sequential'][$key][$key1] = $value['ideal_low'] . ' ~~ Ideal Low grather than next Size';
                    }
                    // 4. Make sure that there is no gap between max actual of smaller size and min actual of next size up
                    if ($value['max_actual'] != $next_array_elements[$key1]['min_actual']) {
                        $result['next_size_up'][$key][$key1] = $value['max_actual'] . ' ~~ Not Equal to max actual of smaller size and min actual of next size up';
                    }
                    //5. Grade rules become generally larger as the sizes increase within a certain % tolerance (Ex. if there is a size run of S, M, L, XL and grade rules for S-M-L are all 2" but it changes to 1" for L-XL, this should be called out. It is possible, but we want to check it.)
                    if ($value['grade_rule'] > $next_array_elements[$key1]['grade_rule']) {
                        $result['grade_rules_become_generally_larger'][$key][$key1] = $value['grade_rule'] . ' ~~ Grade rules become generally decrease as the sizes increase within a certain ';
                    }
                    //6. Have general guide for Fit Model Body proportions: --Flag if bust to waist ratio is more than 11" --Flag if waist to hip is more than 12"
                    if ( isset($product_size_value["waist"]['fit_model']) && $key1 == 'bust' ) {
                        $bust_waist = $value["fit_model"] - $product_size_value["waist"]['fit_model'];
                            if( $bust_waist > 11 ){
                                $result['bust_to_waist_ratio'][$key][$key1] = $bust_waist . ' ~~ Flag if bust to waist ratio is more than 11';
                            }
                    }
                    if ( isset($product_size_value["waist"]['fit_model']) && $key1 == 'hip' ) {
                        $bust_hip = $product_size_value["waist"]['fit_model'] - $value["fit_model"];
                            if( $bust_hip > 12 ){
                                $result['bust_to_hip_ratio'][$key][$key1] = $bust_hip . ' ~~ Flag if waist to hip is more than 12';
                            }
                    }
                    //7. Minimum Actual should be above or equal to min calc but below ideal low, and max actual should be below or equal to max calc but above ideal high.
                    if ( $value['min_actual'] < $value['min_calc'] ) {
                        $result['minimum_actual_should_be_above'][$key][$key1] = $value['min_actual'] . ' ~~  Minimum Actual should be above or equal to min calc but below ideal low';
                    }
                     if ( $value['min_actual'] > $value['ideal_low'] ) {
                        $result['min_calc_but_below_ideal_low'][$key][$key1] = $value['ideal_low'] . ' ~~  Minimum Actual below ideal low';
                    }
                    if ( $value['max_actual'] > $value['max_calc'] ) {
                        $result['max_actual_should_be_below'][$key][$key1] = $value['max_actual'] . ' ~~  max actual should be below or equal to max calc but above ideal high.';
                    }
                    if ( $value['max_actual'] < $value['ideal_high'] ) {
                        $result['max_actual_should_be_above_ideal_high'][$key][$key1] = $value['ideal_high'] . ' ~~  max actual above ideal high.';
                    }
                    //-Need a tolerance of + or - 0.25" that if the garment dimension increased by 2" from one size to the next (i.e. has a 2" grade rule) then the fit model body dimension for that fit point should increase by 2" + or - 0.25".
                     $garment_dimension_difference = $next_array_elements[$key1]['garment_dimension'] - $value['garment_dimension'] ;
                    if ( !( $garment_dimension_difference <=  ($value['grade_rule']+0.25) && $garment_dimension_difference >=  ($value['grade_rule']-0.25) )) {
                        $result['tolerance'][$key][$key1] = $garment_dimension_difference . ' ~~ garment dimension defference incoorect';
                    }
                }
            }
        }
           
        return new Response(json_encode($result));
    }
    
     //~~~~ Update product specification sizes---------product_intake/update_product_specification 
    public function mappingUpdateProductSpecificationAction($mapping_title, $specs_id)
    {
        $product_specs_mapping = $this->get('productIntake.product_specification_mapping')->findOneByTitle($mapping_title);
       // $ps = $this->get('productIntake.product_specification_mapping')->find($product_specs_mapping->getId());
       // $csv_file = $this->get('pi.product_specification')->csvDownloads($ps);

      //  $csv_array = $this->csv_to_array($csv_file);

        $i=0;
        if( file_exists($product_specs_mapping->getAbsolutePath()) ){
            if (($handle = fopen($product_specs_mapping->getAbsolutePath(), "r")) !== FALSE) {
                while(($row = fgetcsv($handle)) !== FALSE) {
                    for ($j=0;$j<count($row);$j++){
                        $csv_array[$i][$j] = $row[$j];
                    }
                    $i++;
                }
            }
        }

        #------------------------ get mapping
      //  $product_specs_mapping = $this->get('product_intake.product_specification_mapping')->find($request->request->get('sel_mapping'));
        $map = json_decode($product_specs_mapping->getMappingJson(), true);
        #-------------->
        $parsed_data = $this->get('admin.helper.product.specification')->getStructure();
        $parsed_data['gender'] = $product_specs_mapping->getGender();
        $parsed_data['size_title_type'] = $product_specs_mapping->getSizeTitleType();
        foreach ($map['fabric_content'] as $fabric_content_k => $fabric_content_v) {
            $f_c = $this->extracts_coordinates($fabric_content_v);
            $parsed_data['fabric_content'][$fabric_content_k] = count($f_c) > 1 ? $csv_array[$f_c['r']][$f_c['c']] : $fabric_content_v;
        }
        unset($map['fabric_content']);
        #----------------- fill array with csv data
        foreach ($map as $specs_k => $specs_v) {
            if ($specs_k != 'formula') {
                if (is_array($specs_v) || is_object($specs_v)) {
                    foreach ($specs_v as $size_key => $fit_points) {
                        foreach ($fit_points as $fit_pont_key => $fit_model_measurement) {
                            $coordins = $this->extracts_coordinates($fit_model_measurement);
                            $fmm_value = $this->fraction_to_number(floatval($csv_array[$coordins['r']][$coordins['c']]));
                            if($fmm_value != 0){
                                $original_value = $fmm_value;
                                #~~~~~~>convert to measuring unit
                                if (array_key_exists('measuring_unit', $map) && $map['measuring_unit'] == 'centimeter') {
                                    $fmm_value = $fmm_value * 0.393700787;
                                }
                                $unit_converted_value = $fmm_value;

                                #~~~~~~>calculate formula
                                if (array_key_exists('formula', $map)) {
                                    $fmm_value = $this->upply_formula($map['formula'], $fit_pont_key, $fmm_value);
                                }

                                #----------------------* parsed data array calculate fit modle values for fit model size
                                $parsed_data[$specs_k][$size_key][$fit_pont_key] = array('garment_dimension' => $fmm_value, 'stretch_percentage' => 0, 'garment_stretch' => 0, 'grade_rule' => 0, 'grade_rule_stretch' => 0, 'min_calc' => 0, 'max_calc' => 0, 'min_actual' => 0, 'max_actual' => 0, 'ideal_low' => 0, 'ideal_high' => 0, 'fit_model' => 0, 'prev_garment_dimension' => 0, 'grade_rule' => 0, 'no' => 0,
                                    'original_value' => $original_value,
                                    'unit_converted_value' => $unit_converted_value,
                                );
                            }
                        }
                    }
                } else if($specs_k == 'max_horizontal_stretch'){
                    $cdns = $this->extracts_coordinates($specs_v);
                    $parsed_data[$specs_k] = count($cdns) > 1 ? $csv_array[$cdns['r']][$cdns['c']] : $specs_v;
                    $parsed_data['horizontal_stretch'] =  ($parsed_data[$specs_k]/3);
                } else if( $specs_k == 'max_vertical_stretch'){
                    $cdns = $this->extracts_coordinates($specs_v);
                    $parsed_data[$specs_k] = count($cdns) > 1 ? $csv_array[$cdns['r']][$cdns['c']] : $specs_v;
                    $parsed_data['vertical_stretch'] = ($parsed_data[$specs_k]/3);
                } else {#----------------------* if not related to measurements add as a field
                    $cdns = $this->extracts_coordinates($specs_v);
                    $parsed_data[$specs_k] = count($cdns) > 1 ? $csv_array[$cdns['r']][$cdns['c']] : $specs_v;
                }
            }
        }

        #--------------------- calculate fit model measrements & ratio
        if (!array_key_exists('sizes', $parsed_data)) {
            return new Response('Measurements & sizes are missing');
        }
        #------------------------ Grade Rule calculation + sorting of sizes
        $size_specs = $this->get('admin.helper.size')->getDefaultArray();
        $prev_size_key = null;
        $ordered_sizes = array();
        $size_no = 0;
        foreach ($size_specs['sizes'][$parsed_data['gender'] == 'm' ? 'man' : 'woman'][$parsed_data['size_title_type']] as $size_key => $size_title) {
            if (array_key_exists($size_key, $parsed_data['sizes'])) {
                $ordered_sizes['sizes'][$size_key] = $parsed_data['sizes'][$size_key];
            }
        }
        $parsed_data['sizes'] = $ordered_sizes['sizes'];
        #---------> Save to DB
       // $specs = $this->get('pi.product_specification')->createNew($product_specs_mapping->getTitle(), $product_specs_mapping->getDescription(), $parsed_data);
      //  $specs->setSpecFileName('csv_spec_' . $specs->getId() . '.csv');
      //  $this->container->get('pi.product_specification')->save($specs);
      //  move_uploaded_file($_FILES["csv_file"]["tmp_name"], $specs->getAbsolutePath());

        $msg_ar = $this->get('pi.product_specification')->updateAndFill($specs_id, $parsed_data);
        $this->get('session')->setFlash($msg_ar['message_type'], $msg_ar['message']);
        return $this->redirect($this->generateUrl('product_intake_product_specs_edit', array('id' => $specs_id)));

        $this->get('session')->setFlash('success', 'New Product specification added!');
        return $this->redirect($this->generateUrl('product_intake_product_specs_edit', array('id' => $specs->getId())));



        return new Response($mapping->getTitle());
    }

}
