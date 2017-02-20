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
        return $this->render('LoveThatFitProductIntakeBundle:FitModel:create_new.html.twig', array(
                    'fit_points' => $fit_points,
                    'brands' => $brands,
                    'clothing_types' => $clothing_types,
                    'product_specs_json' => json_encode($product_specs),
                    'size_specs_json' => json_encode($size_specs),
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
        return new Response('saved!');
    }

    #------------------------/product_intake/fit_model_specs/edit/id

    public function editAction($id) {
        $fit_model_measurement = $this->get('productIntake.fit_model_measurement')->find($id);
        $fit_point_values = json_decode($fit_model_measurement->getMeasurementJson(), true);
        $brands = $this->get('admin.helper.brand')->getBrnadArray();
//       print_R($brands);
//        die;
        $clothing_types = $this->get('admin.helper.clothing_type')->getArray();
        $size_specs = $this->get('admin.helper.size')->getDefaultArray();
        $product_specs = $this->get('admin.helper.product.specification')->getProductSpecification();
        $fit_points = $this->get('admin.helper.product.specification')->getFitPoints();
        return $this->render('LoveThatFitProductIntakeBundle:FitModel:edit.html.twig', array(
                    'fit_model_measurement' => $fit_model_measurement,
                    'fit_point_values' => $fit_point_values,
                    'fit_points' => $fit_points,
                    'brands' => $brands,
                    'clothing_types' => $clothing_types,
                    'product_specs_json' => json_encode($product_specs),
                    'size_specs_json' => json_encode($size_specs),
                ));
    }

}
