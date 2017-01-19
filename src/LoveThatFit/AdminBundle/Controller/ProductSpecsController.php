<?php

namespace LoveThatFit\AdminBundle\Controller;

use LoveThatFit\AdminBundle\Entity\BrandFormatImport;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ProductColor;
use LoveThatFit\AdminBundle\Entity\ProductSize;
use LoveThatFit\AdminBundle\Entity\ProductSizeMeasurement;
use LoveThatFit\AdminBundle\Entity\ProductItem;
use LoveThatFit\AdminBundle\Entity\ProductCSVHelper;
use LoveThatFit\AdminBundle\Entity\ProductCSVDataUploader;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;

class ProductSpecsController extends Controller {
#----------------------- /admin/product_specs/list
    public function listAction() {
        $product_specs_mappings = $this->get('admin.helper.product_specification_mapping')->findAll();        
        #return new response(json_encode($clothing_types['woman']));
        return $this->render('LoveThatFitAdminBundle:ProductSpecs:index.html.twig', array(
                    'specs_mappings' => $product_specs_mappings,
                    ));
    }
#----------------------- /admin/product_specs/edit
    public function editAction($id) {
        $product_specs_mappings = $this->get('admin.helper.product_specification_mapping')->find($id);        
        $mapping_json_decoded = json_decode($product_specs_mappings->getMappingJson());
        $brands = $this->get('admin.helper.brand')->getBrnadArray();
        $clothing_types = $this->get('admin.helper.clothing_type')->getMixArray();
        $size_specs = $this->get('admin.helper.size')->getDefaultArray();
        $product_specs = $this->get('admin.helper.product.specification')->getProductSpecification();
        $fit_points = array('neck', 'shoulder_across_front', 'shoulder_across_back', 'shoulder_length', 'arm_length', 'bicep', 'triceps', 'wrist', 'bust', 'chest', 'back_waist', 'waist', 'cf_waist', 'waist_to_hip', 'hip', 'outseam', 'inseam', 'thigh', 'knee', 'calf', 'ankle', 'hem_length');
        
       return $this->render('LoveThatFitAdminBundle:ProductSpecs:map_edit.html.twig', array(
            'fit_model_measurement' => $this->get('admin.fit_model_measurement')->findAll(), 
            'fit_points' => $fit_points,
            'brands' => $brands,
            'clothing_types' => $clothing_types,
            'product_specs' => $product_specs,
            'size_specs' => $size_specs,
            'product_specs_json' => json_encode($product_specs),
            'size_specs_json' => json_encode($size_specs),
            'specs_mappings' => $product_specs_mappings,
            'specs_decoded' => $mapping_json_decoded,
        ));
    }

#----------------------- /admin/product_specs/foo
    public function fooAction() {
        return $this->render('LoveThatFitAdminBundle:ProductSpecs:foo.html.twig');
    }
 #----------------------------------------------- /admin/product_specs/bar 
      public function barAction($id){        
       $product_specs_mappings = $this->get('admin.helper.product_specification_mapping')->find($id);        
        $mapping_json_decoded = json_decode($product_specs_mappings->getMappingJson());
        return new Response($product_specs_mappings->getMappingJson()); 
    }
 #----------------------------------------------------- /admin/product_specs/mapping_input
     public function mappingInputAction(){        
        $brands = $this->get('admin.helper.brand')->getBrnadArray();
        $clothing_types = $this->get('admin.helper.clothing_type')->getArray();
        $size_specs = $this->get('admin.helper.size')->getDefaultArray();
        $product_specs = $this->get('admin.helper.product.specification')->getProductSpecification();
        $fit_points = array('neck', 'shoulder_across_front', 'shoulder_across_back', 'shoulder_length', 'arm_length',
            'bicep', 'triceps', 'wrist', 'bust', 'chest', 'back_waist', 'waist', 'cf_waist', 'waist_to_hip', 'hip', 'outseam', 'inseam', 'thigh', 'knee', 'calf', 'ankle', 'hem_length');
       return $this->render('LoveThatFitAdminBundle:ProductSpecs:map_input.html.twig', array(
           'fit_model_measurement' => $this->get('admin.fit_model_measurement')->findAll(), 
           'fit_points' => $fit_points,
            'brands' => $brands,
            'clothing_types' => $clothing_types,
            'product_specs' => $product_specs,
             'size_specs' => $size_specs,
             'product_specs_json' => json_encode($product_specs),
             'size_specs_json' => json_encode($size_specs),
        ));
        
    }
    #--------------------------------- /admin/product_specs/csv_upload
     public function csvUploadAction(Request $request){        
        $str=array();
         $file=$request->files->get('csv_file');
         $i=0;
        if (($handle = fopen($file->getRealPath(), "r")) !== FALSE) {
            while(($row = fgetcsv($handle)) !== FALSE) {
            for ($j=0;$j<count($row);$j++){
                $str[$i][$j] = $row[$j];                
                }
            $i++;
            }
        }
         return new Response(json_encode($str));
    }
    
    #------------------------------ /admin/product_specs/mapping_save
     public function mappingSaveAction(Request $request) {
        $decoded = $request->request->all();
        $apecs_arr=array();
        #return new Response(json_encode($decoded['fit_model_measurement']));
        foreach ($decoded as $k => $v) {
            if(!in_array($k, array('select_size', 'fit_point'))){
            if (strlen($v) > 0) {
                $ar = explode('-', $k);
                if (is_array($ar) && count($ar) > 1) {
                    switch (count($ar)) {
                        case 2:
                            $apecs_arr[$ar[0]][$ar[1]] = $v;
                            break;
                        case 3:
                            $apecs_arr[$ar[0]][$ar[1]][$ar[2]] = $v;
                            break;
                        case 4:
                            $apecs_arr[$ar[0]][$ar[1]][$ar[2]][$ar[3]] = $v;
                            break;
                        case 5:
                            $apecs_arr[$ar[0]][$ar[1]][$ar[2]][$ar[3]][$ar[4]] = $v;
                            break;
                        case 6:
                            $apecs_arr[$ar[0]][$ar[1]][$ar[2]][$ar[3]][$ar[4]][$ar[5]] = $v;
                    }
                } else {
                    $apecs_arr[$k] = $v;
                }
            }
            
            }
        }
          $fit_model_measurement = $this->get('admin.fit_model_measurement')->find($decoded['fit_model_measurement']);
          $mapping = $this->container->get('admin.helper.product_specification_mapping')->createNew();
          $mapping->setBrand($decoded['brand_name']);
          $mapping->setFitModelMeasurement($fit_model_measurement);
          $mapping->setTitle($decoded['mapping_title']);
          $mapping->setDescription($decoded['mapping_description']);
          $mapping->setMappingJson(json_encode($apecs_arr));
          $this->container->get('admin.helper.product_specification_mapping')->save($mapping);
          $mapping->setMappingFileName('csv_mapping_'. $mapping->getId() .'.csv');          
           if (move_uploaded_file($_FILES["csv_file"]["tmp_name"], $mapping->getAbsolutePath())){
               $this->container->get('admin.helper.product_specification_mapping')->save($mapping);
               return new Response($mapping->getId().'Mapping created. CSV file is saved.');
           }else{
               return new Response('Mapping created. CSV file is not saved.');
           }
          
        return new Response(json_encode($apecs_arr));
    }
    #------------------------------ /admin/product_specs/csv_data_input
     public function csvDataInputAction(){        
        $brands = $this->get('admin.helper.brand')->getBrnadArray();
        $mapping = $this->get('admin.helper.product_specification_mapping')->getAllMappingArray();
        #return new Response(json_encode($mapping));
        return $this->render('LoveThatFitAdminBundle:ProductSpecs:csv_data_input.html.twig', array(
            'brands' => $brands,
            'mapping' => $mapping,            
            'mapping_json' => json_encode($mapping),            
        ));
        
    }
    #------------------------------------ /admin/product_specs/csv_data_upload

    public function csvDataUploadAction(Request $request) {

        #-------------- CSV to array
        $csv_array = array();
        $file = $request->files->get('csv_file');
        $i = 0;
        if (($handle = fopen($file->getRealPath(), "r")) !== FALSE) {
            while (($row = fgetcsv($handle)) !== FALSE) {
                for ($j = 0; $j < count($row); $j++) {
                    $csv_array[$i][$j] = $row[$j];
                }
                $i++;
            }
        }
        #------------------------ get mapping
        $mapping_id = $request->request->get('sel_mapping');
        $product_specs_mapping = $this->get('admin.helper.product_specification_mapping')->find($mapping_id);
        $map = json_decode($product_specs_mapping->getMappingJson(), true);
        #------->
        $fit_model = $product_specs_mapping->getFitModelMeasurement();
        $fit_model_ratio = array();
        $fit_model_fit_points = json_decode($fit_model->getMeasurementJson(), true);
        #------->
        $parsed_data = array();
        $parsed_data['fit_model_size'] = $fit_model->getSize();
        #----------------- fill array with csv data
        #return new Response($product_specs_mapping->getMappingJson());
        foreach ($map as $specs_k => $specs_v) {
            if ($specs_k != 'formula') {
                if (is_array($specs_v) || is_object($specs_v)) {
                    foreach ($specs_v as $size_key => $fit_points) {
                        foreach ($fit_points as $fit_pont_key => $fit_model_measurement) {
                            $coordins = $this->extracts_coordinates($fit_model_measurement);
                            $fmm_value = $this->fraction_to_number(intval($csv_array[$coordins['r']][$coordins['c']]));
                            $original_value = $fmm_value;
                            #~~~~~~>convert to measuring unit
                            $fmm_value =  array_key_exists('measuring_unit', $map) && $map['measuring_unit'] == 'centimeter' ? $fmm_value * 0.393700787 : $fmm_value;                            
                            $unit_converted_value = $fmm_value;
                            #~~~~~~>calculate formula
                            if(array_key_exists('formula', $map)){
                                $fmm_value =  $this->upply_formula($map['formula'], $fit_pont_key, $fmm_value);                            
                            }
                            #----------------------* parsed data array calculate fit modle values for fit model size
                            $parsed_data[$specs_k][$size_key][$fit_pont_key] = array('garment_dimension' => $fmm_value, 'garment_stretch' => 0, 'min_calc' => 0, 'max_calc' => 0, 'min_actual' => 0, 'max_actual' => 0, 'ideal_low' => 0, 'ideal_high' => 0, 'fit_model' => 0, 'prev_garment_dimension' => 0, 'grade_rule' => 0, 'no' => 0,
                                'fit_model_size' => $size_key == $fit_model->getSize() ? true : false,
                                'original_value'=>$original_value,
                                'unit_converted_value'=>$unit_converted_value,
                                );
                            #------> fit model ratio to garment dimensions
                            if ($size_key == $fit_model->getSize() && $fmm_value > 0) {
                                $fit_model_ratio[$fit_pont_key] = ($fit_model_fit_points[$fit_pont_key] / $fmm_value);
                            }
                        }
                    }
                } else {#----------------------* if not related to measurements add as a field
                    $cdns = $this->extracts_coordinates($specs_v);
                    if (count($cdns) > 1) {
                        $parsed_data[$specs_k] = $csv_array[$cdns['r']][$cdns['c']];
                    }
                }
            }
        }
        $prev_size = null;
        #--------------------- calculate fit model measrements & ratio
        if (!array_key_exists('sizes', $parsed_data)) {
            return new Response('Measurements & sizes are missing');
        }
        foreach ($parsed_data['sizes'] as $size => $fps) {
            foreach ($fps as $fpk => $fpv) {
                if (array_key_exists($fpk, $fit_model_ratio)) {
                    $parsed_data['sizes'][$size][$fpk]['fit_model'] = $fpv['garment_dimension'] * $fit_model_ratio[$fpk];
                    $parsed_data['sizes'][$size][$fpk]['ratio'] = $fit_model_ratio[$fpk];
                    if ($prev_size) {
                        $parsed_data['sizes'][$size][$fpk]['grade_rule'] = $parsed_data['sizes'][$prev_size][$fpk]['garment_dimension'] > 0 ? $parsed_data['sizes'][$size][$fpk]['garment_dimension'] - $parsed_data['sizes'][$prev_size][$fpk]['garment_dimension'] : 0;
                        $parsed_data['sizes'][$size][$fpk]['prev_garment_dimension'] = $parsed_data['sizes'][$prev_size][$fpk]['garment_dimension'];
                    }
                }
            }
            $prev_size = $size;
        }
        #return new Response(json_encode($parsed_data['sizes']));
        #------------------------ Grade Rule calculation + sorting of sizes
        $size_specs = $this->get('admin.helper.size')->getDefaultArray();
        $prev_size_key = null;
        $ordered_sizes = array();
        $size_no = 0;
        foreach ($size_specs['sizes'][$fit_model->getGender() == 'm' ? 'man' : 'woman'][$fit_model->getSizeTitleType()] as $size_key => $size_title) {
            if (array_key_exists($size_key, $parsed_data['sizes'])) {
                $size_no = $size_no + 1;
                foreach ($parsed_data['sizes'][$size_key] as $fit_point => $fit_point_value) {
                    if (array_key_exists($fit_point, $fit_model_fit_points)) {
                        $parsed_data['sizes'][$size_key][$fit_point]['no'] = $size_no;
                        if ($prev_size_key && array_key_exists('garment_dimension', $parsed_data['sizes'][$size_key][$fit_point])
                                && array_key_exists('garment_dimension', $parsed_data['sizes'][$prev_size_key][$fit_point])
                        ) {
                            if ($parsed_data['sizes'][$prev_size_key][$fit_point]['garment_dimension'] > 0) {
                                $grade_rule = $parsed_data['sizes'][$size_key][$fit_point]['garment_dimension'] - $parsed_data['sizes'][$prev_size_key][$fit_point]['garment_dimension'];
                                $fit_model = $parsed_data['sizes'][$size_key][$fit_point]['fit_model'];
                                $parsed_data['sizes'][$size_key][$fit_point]['grade_rule'] = $grade_rule;
                                $parsed_data['sizes'][$size_key][$fit_point]['max_calc'] = $fit_model + (2.5 * $grade_rule);
                                $parsed_data['sizes'][$size_key][$fit_point]['min_calc'] = $fit_model - (2.5 * $grade_rule);
                                $parsed_data['sizes'][$size_key][$fit_point]['ideal_high'] = $fit_model + $grade_rule;
                                $parsed_data['sizes'][$size_key][$fit_point]['ideal_low'] = $fit_model - $grade_rule;
                                $parsed_data['sizes'][$size_key][$fit_point]['max_actual'] = $parsed_data['sizes'][$size_key][$fit_point]['max_calc'];
                                $parsed_data['sizes'][$size_key][$fit_point]['min_actual'] = $parsed_data['sizes'][$size_key][$fit_point]['min_calc'];

                                if ($size_no == 2) {

                                    $fit_model = $ordered_sizes['sizes'][$prev_size_key][$fit_point]['fit_model'];
                                    $ordered_sizes['sizes'][$prev_size_key][$fit_point]['grade_rule'] = $grade_rule;
                                    $ordered_sizes['sizes'][$prev_size_key][$fit_point]['max_calc'] = $fit_model + (2.5 * $grade_rule);
                                    $ordered_sizes['sizes'][$prev_size_key][$fit_point]['min_calc'] = $fit_model - (2.5 * $grade_rule);
                                    $ordered_sizes['sizes'][$prev_size_key][$fit_point]['ideal_high'] = $fit_model + $grade_rule;
                                    $ordered_sizes['sizes'][$prev_size_key][$fit_point]['ideal_low'] = $fit_model - $grade_rule;
                                    $ordered_sizes['sizes'][$prev_size_key][$fit_point]['max_actual'] = $ordered_sizes['sizes'][$prev_size_key][$fit_point]['max_calc'];
                                    $ordered_sizes['sizes'][$prev_size_key][$fit_point]['min_actual'] = $ordered_sizes['sizes'][$prev_size_key][$fit_point]['min_calc'];
                                }
                            }
                        }
                    }
                }
                $ordered_sizes['sizes'][$size_key] = $parsed_data['sizes'][$size_key];
                $prev_size_key = $size_key;
            }
        }
        $parsed_data['sizes'] = $ordered_sizes['sizes'];

        $product_specs = $this->get('admin.helper.product.specification')->getProductSpecification();
        return $this->render('LoveThatFitAdminBundle:ProductSpecs:csv_preview.html.twig', array(
                    'parsed_data' => $parsed_data,
                    'product_specs_json' => json_encode($product_specs),
                    
                ));

        return new Response(json_encode($parsed_data['sizes']));
        return new Response(json_encode($size_specs['sizes'][$fit_model->getGender() == 'm' ? 'man' : 'woman'][$fit_model->getSizeTitleType()]));
        return new Response(json_encode($parsed_data));
        return new Response(json_encode($fit_model_ratio));
        return new Response(json_encode($parsed_data['sizes'][$fit_model->getSize()]));
        return $this->render('LoveThatFitAdminBundle:ProductSpecs:csv_preview.html.twig', array(
                    'parsed_data' => $parsed_data,
                ));
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
            return intval($raw_value);
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

}
