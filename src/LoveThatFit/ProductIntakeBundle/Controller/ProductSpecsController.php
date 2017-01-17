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
        #return new Response(json_encode($mapping));
        return $this->render('LoveThatFitProductIntakeBundle:ProductSpecs:index.html.twig', array(
            'brands' => $brands,
            'mapping' => $mapping,            
            'mapping_json' => json_encode($mapping),            
        ));
    }
    
    
    
    
}
