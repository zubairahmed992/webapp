<?php

namespace LoveThatFit\ProductIntakeBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


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
    public function editAction($id){                
        $fms=$this->get('productIntake.fit_model_measurement')->getTitleArray();   
        $ps = $this->get('pi.product_specification')->find($id);  
        $parsed_data = json_decode($ps->getSpecsJson(),true);
        $gen_specs = $this->get('admin.helper.product.specification')->getProductSpecification(); 
        $drop_down_values = $this->get('admin.helper.product.specification')->getIndividuals(); 
        $drop_down_values['fit_model_size'] = array_flip($fms);      
//        $drop_down_values['fit_model_size'] = $fms;      
        if(isset($parsed_data['fit_model_size'])){ 
            $fit_model_selected_size= $parsed_data['fit_model_size']==null?null:$this->get('productIntake.fit_model_measurement')->find($parsed_data['fit_model_size']);
            $fit_model_selected = $fit_model_selected_size->getSize(); 
        } else { 
            $fit_model_selected = null;
            $parsed_data['fit_model_size'] = '';
        }               
        return $this->render('LoveThatFitProductIntakeBundle:ProductSpecs:edit.html.twig', array(
                    'product_specs'=>$ps,
                    'parsed_data' => $parsed_data,
                    'product_specs_json' => json_encode($gen_specs),  
                    'drop_down_values' =>$drop_down_values,
                    'fit_model_selected_size' => $fit_model_selected,
                    'fit_point_stretch' => $ps->getFitPointStretchArray(), 
                    'disabled_fields' => array('clothing_type', 'brand', 'gender', 'size_title_type', 'mapping_description', 'mapping_title', 'body_type'),                    
                ));
    }
      
    #----------------------- /product_intake/product_specs/show
    public function showAction($id){                
        $gen_specs = $this->get('admin.helper.product.specification')->getProductSpecification();
        $ps = $this->get('pi.product_specification')->find($id);         
        $parsed_data = json_decode($ps->getSpecsJson(),true);
         if(isset($parsed_data['fit_model_size'])){ 
            $fit_model_selected_size= $parsed_data['fit_model_size']==null?null:$this->get('productIntake.fit_model_measurement')->find($parsed_data['fit_model_size']);
            $fit_model_selected = $fit_model_selected_size->getSize(); 
         } else {
             $fit_model_selected =null;
         }        
         
        return $this->render('LoveThatFitProductIntakeBundle:ProductSpecs:show.html.twig', array(
                    'parsed_data' => json_decode($ps->getSpecsJson(),true),
                    'product_specification_id' => $ps->getId(),
                    'product_specs_json' => json_encode($gen_specs),
                    'fit_model_selected_size' => $fit_model_selected,
                ));
    }
    #----------------------- /product_intake/product_specs/delete
    public function deleteAction($id){                
        $msg_ar = $this->get('pi.product_specification')->delete($id);             
        $this->get('session')->setFlash($msg_ar['message_type'], $msg_ar['message']);   
        return $this->redirect($this->generateUrl('product_intake_product_specs_index'));
    }
    
    #----------------------- /product_intake/product_specs/create_product
    public function createProductAction($id){            
        $this->get('pi.product_specification')->create_product($id);
        $this->get('session')->setFlash('success', 'Product created.');   
        return $this->redirect($this->generateUrl('product_intake_product_specs_index'));
    }
    
    #----------------------- /product_intake/Prod_specs/update    
    public function updateAction($id){  
        $msg_ar = $this->get('pi.product_specification')->updateAndFill($id, $_POST);        
        $this->get('session')->setFlash($msg_ar['message_type'], $msg_ar['message']);           
        return $this->redirect($this->generateUrl('product_intake_product_specs_edit', array('id' => $id)));
        
        /*   $output = array();                     
        foreach ($_POST as $key => $value){   
            $sizes = explode('-',$key);//[sizes-XS-neck-garment_dimension]
            $array_length =  count($sizes);      
            if($array_length == '2' ){  
                 $output[$sizes[0]][$sizes[1]] = $value;
            } elseif($array_length == '4' ){  
                 $output['sizes'][$sizes[1]][$sizes[2]][$sizes[3]] = $value;
            } else {
                    $output[$key] = $value;                
            } 
        } 
        #$specs = $this->get('pi.product_specification')->generate($output);
        #return new Response(json_encode($specs)); 
        $entity = $this->get('pi.product_specification')->find($id);
        $specs =  json_decode($entity->getSpecsJson());
        array_key_exists('style_id_number', $specs) ? $entity->setStyleIdNumber($specs['style_id_number']):'';
        array_key_exists('style_name', $specs) ?$entity->setStyleName($specs['style_name']):'';
        array_key_exists('title', $specs) ?$entity->setTitle($specs['title']):'';
        array_key_exists('description', $specs) ?$entity->setDescription($specs['description']):'';
        array_key_exists('clothing_type', $specs) ?$entity->setClothingType($specs['clothing_type']):'';
        array_key_exists('brand', $specs) ?$entity->setBrandName($specs['brand']):'';
        $entity->setUndoSpecsJson($entity->getSpecsJson());
        $entity->setSpecsJson(json_encode($specs));
        $msg_ar = $this->get('pi.product_specification')->update($entity);        
        $this->get('session')->setFlash($msg_ar['message_type'], $msg_ar['message']);           
        return $this->redirect($this->generateUrl('product_intake_product_specs_edit', array('id' => $id)));
         * 
         */
        
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
        
        #----------------- fill array with csv data
        #return new Response($product_specs_mapping->getMappingJson());
        foreach ($map as $specs_k => $specs_v) {
            if ($specs_k != 'formula') {
                if (is_array($specs_v) || is_object($specs_v)) {
                    foreach ($specs_v as $size_key => $fit_points) {
                        foreach ($fit_points as $fit_pont_key => $fit_model_measurement) {
                            $coordins = $this->extracts_coordinates($fit_model_measurement);
                            $fmm_value = $this->fraction_to_number(floatval($csv_array[$coordins['r']][$coordins['c']]));
                            $original_value = $fmm_value;
                            #~~~~~~>convert to measuring unit
                            if(array_key_exists('measuring_unit', $map) && $map['measuring_unit'] == 'centimeter'){
                                $fmm_value = $fmm_value * 0.393700787;                            
                            }
                            $unit_converted_value = $fmm_value;
                            #~~~~~~>calculate formula
                            if(array_key_exists('formula', $map)){
                                $fmm_value =  $this->upply_formula($map['formula'], $fit_pont_key, $fmm_value);                            
                            }
                            #----------------------* parsed data array calculate fit modle values for fit model size
                            $parsed_data[$specs_k][$size_key][$fit_pont_key] = array('garment_dimension' => $fmm_value, 'stretch_percentage' => 0, 'garment_stretch' => 0, 'grade_rule' => 0, 'grade_rule_stretch' => 0, 'min_calc' => 0, 'max_calc' => 0, 'min_actual' => 0, 'max_actual' => 0, 'ideal_low' => 0, 'ideal_high' => 0, 'fit_model' => 0, 'prev_garment_dimension' => 0, 'grade_rule' => 0, 'no' => 0,
                                'original_value'=>$original_value,
                                'unit_converted_value'=>$unit_converted_value,
                                );
                        }
                    }
                } else {#----------------------* if not related to measurements add as a field
                    $cdns = $this->extracts_coordinates($specs_v);
                    $parsed_data[$specs_k] = count($cdns) > 1 ? $csv_array[$cdns['r']][$cdns['c']]:$specs_v;                    
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
//                $size_no = $size_no + 1;
//                foreach ($parsed_data['sizes'][$size_key] as $fit_point => $fit_point_value) {                    
//                        $parsed_data['sizes'][$size_key][$fit_point]['no'] = $size_no;
//                        if ($prev_size_key && array_key_exists('garment_dimension', $parsed_data['sizes'][$size_key][$fit_point])
//                                && array_key_exists('garment_dimension', $parsed_data['sizes'][$prev_size_key][$fit_point])
//                        ) {
//                            if ($parsed_data['sizes'][$prev_size_key][$fit_point]['garment_dimension'] > 0) {
//                                $grade_rule = $parsed_data['sizes'][$size_key][$fit_point]['garment_dimension'] - $parsed_data['sizes'][$prev_size_key][$fit_point]['garment_dimension'];                                
//                                $parsed_data['sizes'][$size_key][$fit_point]['grade_rule'] = $grade_rule;                                
//                                if ($size_no == 2) {                                    
//                                    $ordered_sizes['sizes'][$prev_size_key][$fit_point]['grade_rule'] = $grade_rule;                                    
//                                }                            
//                        }
//                    }
//                }
                $ordered_sizes['sizes'][$size_key] = $parsed_data['sizes'][$size_key];
                #$prev_size_key = $size_key;
            }
        }
        $parsed_data['sizes'] = $ordered_sizes['sizes'];   
        #---------> Save to DB
        $specs=$this->get('pi.product_specification')->createNew(
                $product_specs_mapping->getTitle(),
                $product_specs_mapping->getDescription(),
                json_encode($parsed_data));
         $specs->setSpecFileName('csv_spec_' . $specs->getId() . '.csv');
         $this->container->get('pi.product_specification')->save($specs);
         move_uploaded_file($_FILES["csv_file"]["tmp_name"], $specs->getAbsolutePath());
            
        $this->get('session')->setFlash('success', 'New Product specification added!');
        return $this->redirect($this->generateUrl('product_intake_product_specs_show', array('id' => $specs->getId())));
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
        $filename = $request->files->get('csv_file');              
        $pcsv = new ProductCSVDataUploader($filename);
        $file_data = $pcsv->read();
        #return new response(json_encode($file_data));
        $decoded = $request->request->all();  
        $gen_specs = $this->get('admin.helper.product.specification')->getProductSpecification();
        $ps = $this->get('pi.product_specification')->find($decoded['product_specification_id']);         
        $mix = $this->get('pi.product_specification')->dataMix(json_decode($ps->getSpecsJson(),true), $file_data);
        
        return $this->render('LoveThatFitProductIntakeBundle:ProductSpecs:compare.html.twig', array(
                'file_data' => $file_data,
                'parsed_data' => $mix,
                'product_specification_id' => $ps->getId(),
                'product_specs_json' => json_encode($gen_specs),                    
                ));
        return new response(json_encode($decoded));
    }
    
}
