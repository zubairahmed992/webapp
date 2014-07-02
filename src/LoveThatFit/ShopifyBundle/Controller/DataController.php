<?php

namespace LoveThatFit\ShopifyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\ShopifyBundle\DependencyInjection\ShopifyCSVHelper;


class DataController extends Controller
{
    
    
    public function indexAction() {
        $variants=array('product_name'=>'Teired swirl dress');
        $data=  $this->get('admin.helper.productitem')->findDetailsByVariants($variants);
        return new Response(json_encode($data));
        $form = $this->createFormBuilder()
                ->add('csvfile', 'file')
                ->getForm();
        return $this->render('LoveThatFitShopifyBundle:Data:import_csv.html.twig', array('form' => $form->createView())
        );
    }
    
    public function skuUploadAction(Request $request) {
        
        $form = $this->createFormBuilder()
                ->add('csvfile', 'file')
                ->getForm();
        $form->bindRequest($request);        
        $file = $form->get('csvfile');
        $filename = $file->getData();
        $pcsv = new ShopifyCSVHelper($filename);        
        $data = $pcsv->convertToArray();
        return new Response(json_encode($data));
        
     }
}
