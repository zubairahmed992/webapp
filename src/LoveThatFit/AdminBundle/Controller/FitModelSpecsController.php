<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FitModelSpecsController extends Controller {

    #------------------------/admin/fit_model_specs/create_new
    public function createNewAction(){        
        #return new Response(json_encode($this->get('admin.helper.product.specification')->getFitPoints()));
        $brands = $this->get('admin.helper.brand')->getBrnadArray();
        $clothing_types = $this->get('admin.helper.clothing_type')->getArray();
        $size_specs = $this->get('admin.helper.size')->getDefaultArray();
        $product_specs = $this->get('admin.helper.product.specification')->getProductSpecification();
        #return new Response(json_encode($size_specs));
        $fit_points = $this->get('admin.helper.product.specification')->getFitPoints();
        return $this->render('LoveThatFitAdminBundle:FitModelSpecs:create_new.html.twig', array(
             'fit_points'=>$fit_points,
             'brands'=>$brands,
            'clothing_types' => $clothing_types,
               'product_specs_json' => json_encode($product_specs),
             'size_specs_json' => json_encode($size_specs),
            
         ));        
    }
    #------------------------/admin/fit_model_specs/save
    public function saveAction(Request $request){        
        $decoded = $request->request->all();
        
        $fmm = $this->get('admin.fit_model_measurement')->createNew();
        $brand = $this->get('admin.helper.brand')->find($decoded['sel_brand']);        
        $fmm->setBrand($brand);
        $fmm->setTitle($decoded['txt_title']);
        $fmm->setDescription($decoded['txt_title']);
        $fmm->setSize($decoded['sel_size']);
        $fmm->setClothingType($decoded['sel_clothing_type']);
        $fmm->setGender($decoded['sel_gender']);
        $fmm->setSizeTitleType($decoded['sel_size_type']);
        $measurements=array();        
        $fit_points = $this->get('admin.helper.product.specification')->getFitPoints();
        foreach ($fit_points as $fp => $fp_title) {
            if(array_key_exists($fp, $decoded)){
            $measurements[$fp]=$decoded[$fp];
            }
        }
        $fmm->setMeasurementJson(json_encode($measurements));
        $this->get('admin.fit_model_measurement')->save($fmm);
        return new Response('saved!');
    }
    #------------------------/admin/fit_model_specs/index
  public function indexAction(){ 
        return $this->render('LoveThatFitAdminBundle:FitModelSpecs:index.html.twig', array(
             'fit_model_measurements'=>$this->get('admin.fit_model_measurement')->findAll(),
         ));        
    }
    #------------------------/admin/fit_model_specs/show/id
    public function showAction($id){ 
        $fit_model_measurement=$this->get('admin.fit_model_measurement')->find($id);
       $fit_points = json_decode($fit_model_measurement->getMeasurementJson(),true);
       return $this->render('LoveThatFitAdminBundle:FitModelSpecs:show.html.twig', array(
            'fit_model_measurement' => $fit_model_measurement,
            'fit_points' => $fit_points,             
         ));        
    }
    
}
