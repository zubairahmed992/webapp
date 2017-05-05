<?php

namespace LoveThatFit\ProductIntakeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FitModelController extends Controller {
    
     #------------------------/product_intake/fit_model_specs/index

    public function indexAction() {
        return $this->render('LoveThatFitProductIntakeBundle:FitModel:index.html.twig', array(
                    'fit_model_measurements' => $this->get('productIntake.fit_model_measurement')->findAll(),
                ));
    }
  
#------------------------
    public function fooAction() {
        $size_specs = $this->get('admin.helper.size')->getDefaultArray();
        $product_specs = $this->get('admin.helper.product.specification')->getProductSpecification();
        $ind = $this->get('admin.helper.product.specification')->getIndividuals();
        return new Response(json_encode($ind));
    }
    #------------------------/product_intake/fit_model_specs/show/id

    public function showAction($id) {
        $fit_model_measurement = $this->get('productIntake.fit_model_measurement')->find($id);
        $fit_points = json_decode($fit_model_measurement->getMeasurementJson(), true);
        return $this->render('LoveThatFitProductIntakeBundle:FitModel:show.html.twig', array(
                    'fit_model_measurement' => $fit_model_measurement,
                    'fit_points' => $fit_points,
                ));
    }
    
    #------------------------/product_intake/fit_model_specs/create_new

    public function createNewAction() {
        #return new Response(json_encode($this->get('admin.helper.product.specification')->getFitPoints()));
        $brands = $this->get('admin.helper.brand')->getBrnadArray();
        $clothing_types = $this->get('admin.helper.clothing_type')->getArray();
        $size_specs = $this->get('admin.helper.size')->getDefaultArray();
        $product_specs = $this->get('admin.helper.product.specification')->getProductSpecification();
        #return new Response(json_encode($size_specs));
        $fit_points = $this->get('admin.helper.product.specification')->getFitPoints();
        unset($fit_points['hip']);
        unset($fit_points['hem_length']);
        $required_fields = array('txt_title');
        return $this->render('LoveThatFitProductIntakeBundle:FitModel:create_new.html.twig', array(
                    'fit_points' => $fit_points,
                    'brands' => $brands,
                    'clothing_types' => $clothing_types,
                    'product_specs_json' => json_encode($product_specs),
                    'size_specs_json' => json_encode($size_specs),
                    'required_fields' => $required_fields,
                ));
    }

    #------------------------/product_intake/fit_model_specs/save

    public function saveAction(Request $request) {
        $decoded = $request->request->all();
        $fmm = $this->get('productIntake.fit_model_measurement')->createNew();
        $brand = $this->get('admin.helper.brand')->findOneByName($decoded['sel_brand']);
        $fmm->setBrand($brand);
        $fmm->setTitle($decoded['txt_title']);
        $fmm->setDescription($decoded['txt_title']);
        $fmm->setSize($decoded['sel_size']);
        $fmm->setClothingType($decoded['sel_clothing_type']);
        $fmm->setGender($decoded['sel_gender']);
        $fmm->setSizeTitleType($decoded['sel_size_type']);
        $measurements = array();
        $fit_points = $this->get('admin.helper.product.specification')->getFitPoints();
        foreach ($fit_points as $fp => $fp_title) {
            if (array_key_exists($fp, $decoded)) {
                $measurements[$fp] = $decoded[$fp];
            }
        }
        $fmm->setMeasurementJson(json_encode($decoded));
        $this->get('productIntake.fit_model_measurement')->save($fmm);
        return $this->redirect($this->generateUrl('product_intake_fit_model_index'));
    }

    #------------------------/product_intake/fit_model_specs/edit/id

    public function editAction($id) {
        $fit_model_measurement = $this->get('productIntake.fit_model_measurement')->find($id);       
        $fit_point_values = json_decode($fit_model_measurement->getMeasurementJson(), true);
        $brands = $this->get('admin.helper.brand')->getBrnadArray();
        $clothing_types = $this->get('admin.helper.clothing_type')->getArray();
        $colthing_types_man_woman = ($fit_model_measurement->getGender()=='m' ? $clothing_types['man'] : $clothing_types['woman']);
        $size_specs = $this->get('admin.helper.size')->getDefaultArray();
       // print_r(json_encode($size_specs['sizes']['woman']['bra']));
       // die;
        $all_size_title = $this->get('admin.helper.size')->getAllSizeTitleType();
        $all_size_title_man_woman =($fit_model_measurement->getGender()=='m' ? $all_size_title['man'] : $all_size_title['woman']); 
        $gender =($fit_model_measurement->getGender()=='m' ? 'man' : 'woman'); 
        $size = $size_specs['sizes'][$gender][$fit_point_values['sel_size_type']];
        $product_specs = $this->get('admin.helper.product.specification')->getProductSpecification();
        $fit_points = $this->get('admin.helper.product.specification')->getFitPoints();
        unset($fit_point_values['hip']);        
        unset($fit_point_values['hem_length']);
        !array_key_exists('abdomen', $fit_point_values)?$fit_point_values['abdomen']=0:'';
        $required_fields = array('txt_title');
        return $this->render('LoveThatFitProductIntakeBundle:FitModel:edit.html.twig', array(
                    'fit_model_measurement' => $fit_model_measurement,
                    'fit_point_values' => $fit_point_values,
                    'fit_points' => $fit_points,
                    'brands' => $brands,
                    'clothing_types' =>  json_encode($clothing_types),
                    'product_specs_json' => json_encode($product_specs),
                    'size_specs_json' => json_encode($size_specs),
                    'all_size_title_man_woman' => $all_size_title_man_woman,
                    'colthing_types_man_woman' => $colthing_types_man_woman,
                    'required_fields' => array('bust', 'waist', 'hip'),   
                    'size' => $size,
                    'required_fields' => $required_fields,

                ));
    }
    
    #----------------------- /product_intake/fit_model/update
    public function updateAction(Request $request,$id){  
        $decoded = $request->request->all();
        $entity = $this->get('productIntake.fit_model_measurement')->find($id);
        $brand = $this->get('admin.helper.brand')->findOneByName($decoded['sel_brand']);
        $entity->setBrand($brand);
        $entity->setTitle($decoded['txt_title']);
        $entity->setDescription($decoded['txt_title']);
        $entity->setSize($decoded['sel_size']);
        $entity->setClothingType($decoded['sel_clothing_type']);
        $entity->setGender($decoded['sel_gender']);
        $entity->setSizeTitleType($decoded['sel_size_type']);          
        $entity->setMeasurementJson(json_encode($decoded));      
        $msg_ar = $this->get('productIntake.fit_model_measurement')->update($entity);
        $this->get('session')->setFlash($msg_ar['message_type'], $msg_ar['message']);   
        return $this->redirect($this->generateUrl('product_intake_fit_model_index'));
    }
    
    #----------------------- /product_intake/fit_model/delete
    public function deleteAction($id){                
        $msg_ar = $this->get('productIntake.fit_model_measurement')->delete($id);             
        $this->get('session')->setFlash($msg_ar['message_type'], $msg_ar['message']);   
        return $this->redirect($this->generateUrl('product_intake_fit_model_index'));
    }
    

}
