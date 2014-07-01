<?php

namespace LoveThatFit\ShopifyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class DataController extends Controller
{
    
    
    public function indexAction() {
        $form = $this->createFormBuilder()
                ->add('csvfile', 'file')
                ->getForm();
        return $this->render('LoveThatFitShopifyBundle:Data:import_csv.html.twig', array('form' => $form->createView())
        );
    }
    
    public function skuUploadAction() {
        $form = $this->createFormBuilder()
                ->add('csvfile', 'file')
                ->getForm();
        $form->bindRequest($request);        
        $file = $form->get('csvfile');
        $filename = $file->getData();
        $pcsv = new shopifyCSVHelper($filename);        
        $data = $pcsv->map();
        return new Response(json_encode($data));
        
     }
}
