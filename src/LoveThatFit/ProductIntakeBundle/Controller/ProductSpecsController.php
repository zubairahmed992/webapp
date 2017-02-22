<?php

namespace LoveThatFit\ProductIntakeBundle\Controller;
use LoveThatFit\AdminBundle\Entity\ProductCSVHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Entity\ProductSize;
use LoveThatFit\AdminBundle\Entity\ProductColor;
use LoveThatFit\AdminBundle\Entity\ProductSizeMeasurement;
use LoveThatFit\AdminBundle\Entity\Product;

class ProductSpecsController extends Controller
{
    #----------------------- /product_intake/product_specs/index
    public function indexAction(){
        $ps = $this->get('pi.product_specification')->findAll();        
        return $this->render('LoveThatFitProductIntakeBundle:ProductSpecs:index.html.twig', array(
            'specs' => $ps,            
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
    public function editAction($id, $fit_model_measurement_id=null){                
        #$conf = $this->get('pi.product_specification')->getFitPointArray();  
        #return new Response(json_encode($conf));
        $fms=$this->get('productIntake.fit_model_measurement')->getTitleArray();        
        $fm=$fit_model_measurement_id==null?null:$this->get('productIntake.fit_model_measurement')->find($fit_model_measurement_id);
        
        $gen_specs = $this->get('admin.helper.product.specification')->getProductSpecification();         
        $drop_down_values = $this->get('admin.helper.product.specification')->getIndividuals(); 
        $ps = $this->get('pi.product_specification')->find($id);  
        $parsed_data = json_decode($ps->getSpecsJson(),true);
        $parsed_data['fit_model_size']=  array_key_exists('fit_model_size',$parsed_data)?$parsed_data['fit_model_size']:'';
        $drop_down_values['fit_model_size'] = $this->get('productIntake.fit_model_measurement')->getTitleArray();
        
        if ($fm){                        
            return new Response(json_encode($this->apply_fit_model($parsed_data['sizes'], $fm)));            
         }
        
        return $this->render('LoveThatFitProductIntakeBundle:ProductSpecs:edit.html.twig', array(
                    'parsed_data' => $parsed_data,
                    'product_specs_json' => json_encode($gen_specs),  
                    'drop_down_values' =>$drop_down_values,
                    'fit_points' => $ps->getFitPointArray(),
                    'fit_model_list' => $fms,
                    'fit_model' => $fm,
                    'disabled_fields' => array('clothing_type', 'brand', 'gender', 'size_title_type', 'mapping_description', 'mapping_title', 'body_type'),
                ));
    }
    #----------------------- /product_intake/product_specs/measurements_with_fitmodel
    public function measurementsWithFitModelAction(Request $request){                        
        $decoded = $request->request->all();             
        $ps = $this->get('pi.product_specification')->find($decoded['product_specification_id']);  
        $fm = $this->get('productIntake.fit_model_measurement')->find($decoded['fit_model_measurement_id']);        
        $parsed_data = json_decode($ps->getSpecsJson(),true);        
        $ps = $this->get('pi.product_specification')->calculateGradeRule($parsed_data);  
        $updated_measurements =  $this->get('pi.product_specification')->calculateWithFitModel($ps['sizes'], $fm);  
        return new Response(json_encode($updated_measurements));        
    }
      #----------------------- /product_intake/product_specs/measurement_recalculate
    public function measurementRecalculateAction(Request $request){                        
        $decoded = $request->request->all();    
        $ps = $this->get('pi.product_specification')->find($decoded['product_specification_id']);          
        $specs_data = json_decode($ps->getSpecsJson(),true);        
        $specs_data['horizontal_stretch'] = array_key_exists('horizontal_stretch',$decoded)?$decoded['horizontal_stretch']:0;
        $specs_data['vertical_stretch'] = array_key_exists('vertical_stretch',$decoded)?$decoded['vertical_stretch']:0;        
        $specs_data['fit_model_measurement_id'] = array_key_exists('fit_model_measurement_id',$decoded)?$decoded['fit_model_measurement_id']:0;                
        $fm = $this->get('productIntake.fit_model_measurement')->find($decoded['fit_model_measurement_id']);
        $updated_measurements =  $this->get('pi.product_specification')->calculateRanges($specs_data);  
        $updated_measurements =  $this->get('pi.product_specification')->calculateWithFitModel($updated_measurements['sizes'], $fm);  
        return new Response(json_encode($updated_measurements));        
    }
      
    #----------------------- /product_intake/product_specs/show
    public function showAction($id){                
        $gen_specs = $this->get('admin.helper.product.specification')->getProductSpecification();
        $ps = $this->get('pi.product_specification')->find($id);         
        $data =  json_decode($ps->getSpecsJson(),true);        
        return $this->render('LoveThatFitProductIntakeBundle:ProductSpecs:show.html.twig', array(
                    'parsed_data' => json_decode($ps->getSpecsJson(),true),
                    'product_specs_json' => json_encode($gen_specs),                    
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
        $entity = $this->get('pi.product_specification')->find($id);
        $this->create_product(json_decode($entity->getSpecsJson(),true));
        $this->get('session')->setFlash('success', 'Product created.');   
        return $this->redirect($this->generateUrl('product_intake_product_specs_index'));
    }
    #----------------------- /product_intake/Prod_specs/update
    public function updateAction($id){  
           $output = array();          
        foreach ($_POST as $key => $value)
        {   
            $sizes = explode('-',$key);//[sizes-XS-neck-garment_dimension]
            $array_length =  count($sizes);      
            if($array_length == '4' ){  
                 $output['sizes'][$sizes[1]][$sizes[2]][$sizes[3]] = $value;
            } else {
                $output[$key] = $value;
            } 
        }      
        
        $entity = $this->get('pi.product_specification')->find($id);
        $entity->setUndoSpecsJson($entity->getSpecsJson());
        $entity->setSpecsJson(json_encode($output));
        $msg_ar = $this->get('pi.product_specification')->update($entity);        
        $this->get('session')->setFlash($msg_ar['message_type'], $msg_ar['message']);   
        $specs = $this->get('pi.product_specification')->find($id);
        #return new Response($specs->getSpecsJson());
        return $this->redirect($this->generateUrl('product_intake_product_specs_edit', array('id' => $id)));
        #return $this->redirect($this->generateUrl('product_intake_product_specs_index'));
    }
    
    #----------------------- /product_intake/Prod_specs/undo
    public function undoAction($id){  
        $entity = $this->get('pi.product_specification')->find($id);       
        $entity->setSpecsJson($entity->getUndoSpecsJson());
        $msg_ar = $this->get('pi.product_specification')->update($entity);        
        $this->get('session')->setFlash($msg_ar['message_type'], $msg_ar['message']);   
        $specs = $this->get('pi.product_specification')->find($id);    
        return $this->redirect($this->generateUrl('product_intake_product_specs_edit', array('id' => $id)));       
    }
    
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
                            $parsed_data[$specs_k][$size_key][$fit_pont_key] = array('garment_dimension' => $fmm_value, 'stretch_value' => 0, 'garment_stretch' => 0, 'grade_rule' => 0, 'grade_rule_stretch' => 0, 'min_calc' => 0, 'max_calc' => 0, 'min_actual' => 0, 'max_actual' => 0, 'ideal_low' => 0, 'ideal_high' => 0, 'fit_model' => 0, 'prev_garment_dimension' => 0, 'grade_rule' => 0, 'no' => 0,
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
                $size_no = $size_no + 1;
                foreach ($parsed_data['sizes'][$size_key] as $fit_point => $fit_point_value) {                    
                        $parsed_data['sizes'][$size_key][$fit_point]['no'] = $size_no;
                        if ($prev_size_key && array_key_exists('garment_dimension', $parsed_data['sizes'][$size_key][$fit_point])
                                && array_key_exists('garment_dimension', $parsed_data['sizes'][$prev_size_key][$fit_point])
                        ) {
                            if ($parsed_data['sizes'][$prev_size_key][$fit_point]['garment_dimension'] > 0) {
                                $grade_rule = $parsed_data['sizes'][$size_key][$fit_point]['garment_dimension'] - $parsed_data['sizes'][$prev_size_key][$fit_point]['garment_dimension'];                                
                                $parsed_data['sizes'][$size_key][$fit_point]['grade_rule'] = $grade_rule;                                
                                if ($size_no == 2) {                                    
                                    $ordered_sizes['sizes'][$prev_size_key][$fit_point]['grade_rule'] = $grade_rule;                                    
                                }                            
                        }
                    }
                }
                $ordered_sizes['sizes'][$size_key] = $parsed_data['sizes'][$size_key];
                $prev_size_key = $size_key;
            }
        }
        $parsed_data['sizes'] = $ordered_sizes['sizes'];
        
        #---------> Save to DB
        
        $specs=$this->get('pi.product_specification')->createNew(
                $product_specs_mapping->getTitle(),
                $product_specs_mapping->getDescription(),
                json_encode($parsed_data));
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

#----------------------------
    private function create_product($data){
        $clothing_type = $this->get('admin.helper.clothingtype')->findOneByGenderNameCSV(strtolower($data['gender']), strtolower($data['clothing_type']));
        $brand = $this->get('admin.helper.brand')->findOneByName($data['brand']);
        $product=new Product;
        $product->setBrand($brand);
        $product->setClothingType($clothing_type);        
        $product->setName(array_key_exists('name', $data)?$data['name']:'');
        $product->setName(array_key_exists('control_number', $data)?$data['control_number']:'');        
        $product->setDescription(array_key_exists('description', $data)?$data['description']:'');
        $product->setStretchType(array_key_exists('stretch_type', $data)?$data['stretch_type']:'');
        $product->setHorizontalStretch($data['horizontal_stretch']);
        $product->setVerticalStretch($data['vertical_stretch']);        
        $product->setCreatedAt(new \DateTime('now'));
        $product->setUpdatedAt(new \DateTime('now'));
        $product->setGender($data['gender']);
        $product->setStylingType($data['styling_type']);
        $product->setNeckline(array_key_exists('neck_line', $data)?$data['neck_line']:$data['neckline']);
        $product->setSleeveStyling($data['sleeve_styling']);
        $product->setRise($data['rise']);
        $product->setHemLength($data['hem_length']);
        $product->setFabricWeight($data['fabric_weight']);
        $product->setStructuralDetail($data['structural_detail']);
        $product->setFitType($data['fit_type']);
        $product->setLayering(array_key_exists('layring', $data)?$data['layring']:$data['layering']);
        $product->setFitPriority(json_encode($data['fit_priority']));
        $product->setFabricContent(json_encode(array_key_exists('fabric_content', $data)?$data['fabric_content']:''));
        $product->setDisabled(false);        
        $product->setSizeTitleType($data['size_title_type']);    
        #------------------------
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();
        $this->create_product_sizes($product, $data);
        $this->create_product_colors($data, $product);
        
    }
        #------------------------------------------------------------
    private function create_product_sizes($product, $data) {
        $em = $this->getDoctrine()->getManager();
        $size_titles = $this->get('admin.helper.size')->getSizeArray($data['gender'],$data['size_title_type']);        
        $i=1;
        foreach ($size_titles['regular'] as $size_title => $value) {
            if(array_key_exists($size_title, $data['sizes'])){
                $ps = new ProductSize();
                $ps->setTitle($size_title);
                $ps->setProduct($product);
                $ps->setBodyType($data['body_type']);                                
                $ps->setIndexValue($i);
                $em->persist($ps);
                $em->flush();
                $this->create_product_size_measurements($ps, $data['sizes'][$size_title]);            
            }
            $i++;
        }
        return $product;
    }
     #------------------------------------------------------
    private function create_product_size_measurements($size, $data) {
        $em = $this->getDoctrine()->getManager();
        foreach ($data as $key => $value) {
            if($key!='key'){
            $psm = new ProductSizeMeasurement;
            $psm->setTitle($key);
            $psm->setProductSize($size);            
            array_key_exists('garment_dimension',$value)?$psm->setGarmentMeasurementFlat($value['garment_dimension']):null;
            array_key_exists('garment_stretch',$value)?$psm->setGarmentMeasurementStretchFit($value['garment_stretch']):null;
            $psm->setMaxBodyMeasurement($value['max_actual']);
            $psm->setIdealBodySizeHigh($value['ideal_high']);
            $psm->setIdealBodySizeLow($value['ideal_low']);
            $psm->setMinBodyMeasurement($value['min_actual']);
            $psm->setFitModelMeasurement($value['fit_model']);
            $psm->setMaxCalculated($value['max_calc']);
            $psm->setMinCalculated($value['min_calc']);
            $psm->setGradeRule($value['grade_rule']);
            $em->persist($psm);
            $em->flush();
            }            
        }
        return;
    }
    #------------------------------------------------------------
    private function create_product_colors($data, $product) {        
        $color_names=explode(",", $data['colors']);        
            $em = $this->getDoctrine()->getManager();
            foreach ($color_names as $cn) {
                $pc = new ProductColor();
                $pc->setTitle(trim($cn));
                $pc->setProduct($product);
                $em->persist($pc);
                $em->flush();
            }
                
        return $product;
    }
    #------------------------------------------------------------
    private function validate_specs($data) {        
            foreach ($data['sizes'] as $s=>$fp) {
                
                # garment_dimension ~~~~~~~>
                
                
                # garment_stretch ~~~~~~~>	
                
                
                # min_calc	 ~~~~~~~>
                
                
                # min_actual	 ~~~~~~~>
                
                
                # ideal_low	 ~~~~~~~>
                
                
                # fit_model	 ~~~~~~~>
                
                
                # ideal_high	 ~~~~~~~>
                
                
                # max_calc	 ~~~~~~~>
                
                
                # max_actual	 ~~~~~~~>
                
                
                # grade rule    ~~~~~~~>                
                
                
            }
    }
}
