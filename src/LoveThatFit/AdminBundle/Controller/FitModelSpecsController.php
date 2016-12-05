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
    public function saveAction(Request $request){        
        $decoded = $request->request->all();
        return new Response(json_encode($decoded));
        $fmm = $this->get('admin.fit_model_measurement')->createNew();
        $brand = $this->get('admin.helper.brand')->find(11);        
        $fmm->setBrand($brand);
        $fmm->setTitle('rodney');
        $fmm->setDescription('rodney');
        $fmm->setSize('M');
        #$fmm->setClothingType('Trouser');
        #$fmm->setGender('m');
        $fmm->setSizeTitleType('Letter');
        $this->get('admin.fit_model_measurement')->save($fmm);
        return new Response('saved!');
    }
 
}
