<?php

namespace LoveThatFit\ProductIntakeBundle\Controller;

use LoveThatFit\AdminBundle\Entity\ProductCSVDataUploader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;

class ProductController extends Controller
{
    #----------------------- /product_intake/product/index
    public function imageUploadIndexAction(){
        
        return $this->render('LoveThatFitProductIntakeBundle:Product:image_upload_index.html.twig');
    }
    
     
}
