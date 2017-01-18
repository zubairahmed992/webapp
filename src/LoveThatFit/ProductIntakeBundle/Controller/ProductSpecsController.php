<?php

namespace LoveThatFit\ProductIntakeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class ProductSpecsController extends Controller
{
    #----------------------- /product_intake/product_specs/index
    public function indexAction(){
        $brands = $this->get('admin.helper.brand')->getBrnadArray();
        $mapping = $this->get('product_intake.product_specification_mapping')->getAllMappingArray();
        $size_specs = $this->get('admin.helper.size')->getDefaultArray();        
        return $this->render('LoveThatFitProductIntakeBundle:ProductSpecs:index.html.twig', array(
            'brands' => $brands,
            'mapping' => $mapping,            
            'mapping_json' => json_encode($mapping),            
            'size_specs_json' => json_encode($size_specs),
        ));
    }
    
     #------------------------------------ /product_intake/product_specs/csv_upload

    public function csvUploadAction(Request $request) {

        #-------------- CSV to array
        $csv_array = $this->csv_to_array($request->files->get('csv_file'));
        
        #------------------------ get mapping        
        $product_specs_mapping = $this->get('product_intake.product_specification_mapping')->find($request->request->get('sel_mapping'));
        $map = json_decode($product_specs_mapping->getMappingJson(), true);        
        #-------------->
        $parsed_data = array();
        $parsed_data['gender'] = $request->request->get('sel_gender');
        $parsed_data['size_title_type'] = $request->request->get('sel_size_type');
        
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
                            if(array_key_exists('measuring_unit', $map) && $map['measuring_unit'] == 'centimeter'){
                                $fmm_value = $fmm_value * 0.393700787;                            
                            }
                            $unit_converted_value = $fmm_value;
                            #~~~~~~>calculate formula
                            if(array_key_exists('formula', $map)){
                                $fmm_value =  $this->upply_formula($map['formula'], $fit_pont_key, $fmm_value);                            
                            }
                            #----------------------* parsed data array calculate fit modle values for fit model size
                            $parsed_data[$specs_k][$size_key][$fit_pont_key] = array('garment_dimension' => $fmm_value, 'garment_stretch' => 0, 'min_calc' => 0, 'max_calc' => 0, 'min_actual' => 0, 'max_actual' => 0, 'ideal_low' => 0, 'ideal_high' => 0, 'fit_model' => 0, 'prev_garment_dimension' => 0, 'grade_rule' => 0, 'no' => 0,
                                'original_value'=>$original_value,
                                'unit_converted_value'=>$unit_converted_value,
                                );
                            
                        }
                    }
                } else {#----------------------* if not related to measurements add as a field
                    $cdns = $this->extracts_coordinates($specs_v);
                    if (count($cdns) > 1) {
                        $parsed_data[$specs_k] = $csv_array[$cdns['r']][$cdns['c']];
                    }else{
                        $parsed_data[$specs_k] = $specs_v;
                    }
                }
            }
        }
        
        $prev_size = null;
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
                                $parsed_data['sizes'][$size_key][$fit_point]['max_calc'] = 0;
                                $parsed_data['sizes'][$size_key][$fit_point]['min_calc'] = 0;
                                $parsed_data['sizes'][$size_key][$fit_point]['ideal_high'] = 0;
                                $parsed_data['sizes'][$size_key][$fit_point]['ideal_low'] = 0;
                                $parsed_data['sizes'][$size_key][$fit_point]['max_actual'] = 0;
                                $parsed_data['sizes'][$size_key][$fit_point]['min_actual'] = 0;
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
        return new Response(json_encode($parsed_data));
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

#----------------------------
    
}
