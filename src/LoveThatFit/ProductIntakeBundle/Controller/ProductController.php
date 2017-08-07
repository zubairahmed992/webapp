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
    #----------------------- /product_intake/product/image_exists_check
    public function imageExistsCheckAction(){    
        $info = $this->get('admin.helper.product')->allProductItemImage();        
        #return new Response(json_encode($products));        
        return $this->render('LoveThatFitProductIntakeBundle:Product:product_item_image.html.twig', array(
                'products' => $info['items'],             
                'total_items'=>$info['total_items'], 
                'missing_images'=>$info['missing_images'],
            ));
        
    }
    #----------------------- /pi/product/{id}/fit_points
    public function productFitPointsAction($product_id) {
        $product = $this->get('admin.helper.product')->find($product_id);
        $fps = array();
        $s = $product->getProductSizes()->first();
        foreach ($s->getProductSizeMeasurements() as $m) {
            $fps[$m->getTitle()] = 'default';
        }
        return new Response(json_encode($fps));
    }

}
