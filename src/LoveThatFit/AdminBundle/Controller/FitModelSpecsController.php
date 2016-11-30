<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FitModelSpecsController extends Controller {

    public function createNewAction(){        
        
        #return new Response(json_encode($this->get('admin.helper.product.specification')->getFitPoints()));
        $brands = $this->get('admin.helper.brand')->getBrnadArray();
        $fit_points = $this->get('admin.helper.product.specification')->getFitPoints();
        return $this->render('LoveThatFitAdminBundle:FitModelSpecs:create_new.html.twig', array(
             'fit_points'=>$fit_points,
             'brands'=>$brands,
         ));        
    }
 
}
