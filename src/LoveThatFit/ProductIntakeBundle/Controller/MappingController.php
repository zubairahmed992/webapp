<?php

namespace LoveThatFit\ProductIntakeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class MappingController extends Controller
{
    #----------------------- /product_intake/specs_mapping/index
    public function indexAction(){
        $product_specs_mappings = $this->get('productIntake.product_specification_mapping')->findAll();                
        return $this->render('LoveThatFitProductIntakeBundle:Mapping:index.html.twig', array(
                    'specs_mappings' => $product_specs_mappings,
                    ));
    }
    
    #----------------------- /product_intake/product_specs/new
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
    
    #----------------------- /product_intake/specs_mapping/create
    
    #----------------------- /product_intake/specs_mapping/edit
    
    #----------------------- /product_intake/specs_mapping/update
    
    
}
