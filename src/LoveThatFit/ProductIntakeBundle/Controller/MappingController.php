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
                    'csv_file'      =>  $this->get('productIntake.product_specification_mapping')->csvDownloads($product_specs_mappings),        
        ));
    }
    
    #----------------------- /product_intake/specs_mapping/new
    public function newAction() {
        $brands = $this->get('admin.helper.brand')->getBrnadArray();
        $clothing_types = $this->get('admin.helper.clothing_type')->getArray();
        $size_specs = $this->get('admin.helper.size')->getDefaultArray();
        $product_specs = $this->get('admin.helper.product.specification')->getProductSpecification();        
        //$fit_points =      array('neck', 'shoulder_across_front', 'shoulder_across_back', 'shoulder_length', 'arm_length','bicep', 'triceps', 'wrist', 'bust', 'chest', 'back_waist', 'waist', 'cf_waist', 'waist_to_hip', 'abdomen', 'high_hip', 'low_hip', 'outseam', 'inseam', 'thigh', 'knee', 'calf', 'ankle', 'hem_length');
         $fit_points = $this->get('admin.helper.product.specification')->getFitPoints();
        unset($fit_points['hip']);
        //unset($fit_points['hem_length']);
        unset($fit_points['thigh']);
        return $this->render('LoveThatFitProductIntakeBundle:Mapping:new.html.twig', array(
                    'fit_points' => array_keys($fit_points),
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
    public function editAction($id)
    {
        $pm = $this->get('productIntake.product_specification_mapping')->find($id); 
        //----- Get File data 
        $str=array("File not Exist on Server");
        $i=0;
        if( file_exists($pm->getAbsolutePath()) ){   
            if (($handle = fopen($pm->getAbsolutePath(), "r")) !== FALSE) {
                while(($row = fgetcsv($handle)) !== FALSE) {
                for ($j=0;$j<count($row);$j++){
                    $str[$i][$j] = $row[$j];                
                    }
                $i++;
                }
            }       
        }
        $parsed_data   = json_decode($pm->getMappingJson(),true);        
        $brands = $this->get('admin.helper.brand')->getBrnadArray();
        $size_specs = $this->get('admin.helper.size')->getDefaultArray();
        $product_specs = $this->get('admin.helper.product.specification')->getProductSpecification();        
        //$fit_points =      array('neck', 'shoulder_across_front', 'shoulder_across_back', 'shoulder_length', 'arm_length', 'bicep', 'triceps', 'wrist', 'bust', 'chest', 'back_waist', 'waist', 'cf_waist', 'waist_to_hip', 'abdomen', 'high_hip', 'low_hip', 'outseam', 'inseam', 'thigh', 'knee', 'calf', 'ankle', 'hem_length');
        $fit_points = $this->get('admin.helper.product.specification')->getFitPoints();
        unset($fit_points['hip']);
        //unset($fit_points['hem_length']);
        unset($fit_points['thigh']);
        $clothing_types = ($parsed_data['gender'] == 'f'? $product_specs['women']['clothing_types']:$product_specs['man']['clothing_type']);
        $body_types = ($parsed_data['gender'] == 'f'? $size_specs['fit_types']['woman']:$size_specs['fit_types']['man']);
        $size_title = ($parsed_data['gender'] == 'f'? $size_specs['size_title_type']['woman']:$size_specs['size_title_type']['man']);
         (array_key_exists('formula', $parsed_data))?true :$parsed_data['formula']=array();
        $parsed_data['mapping_title']= $pm->getTitle();
        return $this->render('LoveThatFitProductIntakeBundle:Mapping:edit.html.twig', array(
                    'fit_points' => array_keys($fit_points),
                    'brands' => $brands,
                    'clothing_types' => $clothing_types,
                    'product_specs' => $product_specs,
                    'size_specs' => $size_specs,
                    'product_specs_json' => json_encode($product_specs),
                    'size_specs_json' => json_encode($size_specs),
                    'parsed_data' => $parsed_data,
                    'body_types'  => $body_types,
                    'size_title' => $size_title,
                    'csv_file_data'  => json_encode($str),      
                ));
    }
    #----------------------- /product_intake/specs_mapping/update
     
    public function updateAction(Request $request, $id)
    {  
        $entity = $this->get('productIntake.product_specification_mapping')->find($id);
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
        
        $entity->setBrand($decoded['brand']);
        $entity->setSizeTitleType($decoded['size_title_type']);
        $entity->setClothingType($decoded['clothing_type']);
        $entity->setGender($decoded['gender']);
        $entity->setTitle($decoded['mapping_title']);
        $entity->setDescription($decoded['mapping_description']);
        $entity->setMappingJson(json_encode($apecs_arr));       
        $this->get('productIntake.product_specification_mapping')->update($entity);
        $entity->setMappingFileName('csv_mapping_' . $entity->getId() . '.csv');
        if (move_uploaded_file($_FILES["csv_file"]["tmp_name"], $entity->getAbsolutePath())) {
            $this->get('productIntake.product_specification_mapping')->update($entity);          
        } 

        $this->get('session')->setFlash('info', 'Updated Product specification Mapping created.');        
        return $this->redirect($this->generateUrl('product_intake_specs_mapping_index'));
     
    }
    #----------------------- /product_intake/specs_mapping/delete
    public function deleteAction($id){  
        clearstatcache();      
        $remove_csv_file = $this->get('productIntake.product_specification_mapping')->find($id);     
         if( file_exists($remove_csv_file->getAbsolutePath()) ){
            unlink($remove_csv_file->getAbsolutePath());
         }
        $msg_ar = $this->get('productIntake.product_specification_mapping')->delete($id);             
        $this->get('session')->setFlash($msg_ar['message_type'], $msg_ar['message']);   
        return $this->redirect($this->generateUrl('product_intake_specs_mapping_index'));
    }
    
    
    //------------------------- /product_intake/specs_mapping/duplicate
    public function duplicateAction($id)
    { 
        $entity = $this->get('productIntake.product_specification_mapping')->find($id);      
        $csv_file =   $imagepath =  str_replace('\\', '/', getcwd()). '/uploads/ltf/products/product_csv/';       
        $mapping = $this->container->get('productIntake.product_specification_mapping')->createNew();
        $mapping->setBrand($entity->getBrand());
        $mapping->setSizeTitleType($entity->getSizeTitleType());
        $mapping->setClothingType($entity->getClothingType());
        $mapping->setGender($entity->getGender());
        $mapping->setTitle("Duplicate Mapping of ".$entity->getId());
        $mapping->setDescription($entity->getDescription());
        $mapping->setMappingJson($entity->getMappingJson());           
        $this->container->get('productIntake.product_specification_mapping')->save($mapping);  
         clearstatcache();
        if( file_exists($entity->getAbsolutePath()) ) {              
              $mapping->setMappingFileName('csv_mapping_' . $mapping->getId() . '.csv');
              copy($entity->getAbsolutePath(),$csv_file.'csv_mapping_' . $mapping->getId() . '.csv');
        }
        $this->container->get('productIntake.product_specification_mapping')->save($mapping);       

        $this->get('session')->setFlash('info', 'Duplicate Product specification Mapping created.');        
        return $this->redirect($this->generateUrl('product_intake_specs_mapping_index'));
    
    }
    
    
}
