<?php

namespace LoveThatFit\ProductIntakeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class MappingController extends Controller
{
    #----------------------- /product_intake/specs_mapping/index
    public function indexAction(){
        $product_specs_mappings = $this->get('productIntake.product_specification_mapping')->findAll();                
        return $this->render('LoveThatFitProductIntakeBundle:Mapping:index.html.twig', array(
                    'specs_mappings' => $product_specs_mappings,
                    ));
    }
    
    #----------------------- /product_intake/specs_mapping/new
    public function newAction() {
        $brands = $this->get('admin.helper.brand')->getBrnadArray();
        $clothing_types = $this->get('admin.helper.clothing_type')->getArray();
        $size_specs = $this->get('admin.helper.size')->getDefaultArray();
        $product_specs = $this->get('admin.helper.product.specification')->getProductSpecification();
        $fit_points = array('neck', 'shoulder_across_front', 'shoulder_across_back', 'shoulder_length', 'arm_length',
            'bicep', 'triceps', 'wrist', 'bust', 'chest', 'back_waist', 'waist', 'cf_waist', 'waist_to_hip', 'hip', 'outseam', 'inseam', 'thigh', 'knee', 'calf', 'ankle', 'hem_length');
        return $this->render('LoveThatFitProductIntakeBundle:Mapping:new.html.twig', array(
                    'fit_model_measurement' => $this->get('productIntake.fit_model_measurement')->findAll(),
                    'fit_points' => $fit_points,
                    'brands' => $brands,
                    'clothing_types' => $clothing_types,
                    'product_specs' => $product_specs,
                    'size_specs' => $size_specs,
                    'product_specs_json' => json_encode($product_specs),
                    'size_specs_json' => json_encode($size_specs),
                ));
    }
    
    #--------------------------------- /product_intake/specs_mapping/csv_upload
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
    
    #----------------------- /product_intake/specs_mapping/create
     
     public function createAction(Request $request) {
        $decoded = $request->request->all();
        $apecs_arr = array();
        foreach ($decoded as $k => $v) {
            if (!in_array($k, array('select_size', 'fit_point'))) {
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

        $mapping = $this->container->get('productIntake.product_specification_mapping')->createNew();
        $mapping->setBrand($decoded['brand']);
        $mapping->setSizeTitleType($decoded['size_title_type']);
        $mapping->setClothingType($decoded['clothing_type']);
        $mapping->setGender($decoded['gender']);
        $mapping->setTitle($decoded['mapping_title']);
        $mapping->setDescription($decoded['mapping_description']);
        $mapping->setMappingJson(json_encode($apecs_arr));
        $this->container->get('productIntake.product_specification_mapping')->save($mapping);
        $mapping->setMappingFileName('csv_mapping_' . $mapping->getId() . '.csv');
        if (move_uploaded_file($_FILES["csv_file"]["tmp_name"], $mapping->getAbsolutePath())) {
            $this->container->get('productIntake.product_specification_mapping')->save($mapping);          
        } 

        $this->get('session')->setFlash('info', 'New Product specification Mapping created.');        
        return $this->redirect($this->generateUrl('product_intake_specs_mapping_index'));
    }
    #----------------------- /product_intake/specs_mapping/edit
    
    #----------------------- /product_intake/specs_mapping/update
    
    
}
