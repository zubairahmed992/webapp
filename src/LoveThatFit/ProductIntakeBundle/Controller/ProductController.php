<?php

namespace LoveThatFit\ProductIntakeBundle\Controller;

use LoveThatFit\ProductIntakeBundle\DependencyInjection\MannequenHelper;
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

    #----------------------- realProductImageUploadIndexAction
    public function realProductImageUploadIndexAction()
    {
        return $this->render('LoveThatFitProductIntakeBundle:Product:real_product_image_upload_index.html.twig');
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
        if(!$product) { return new Response('Product not found');}
        $p = json_decode($product->getFitPriority(), true);
        #return new Response($p['']);
        $fps = array();
        $s = $product->getProductSizes()->first();
        foreach ($s->getProductSizeMeasurements() as $m) {            
            $fps[$m->getTitle()] = array_key_exists($m->getTitle(), $p) ? $p[$m->getTitle()] : 0;
            //            if (array_key_exists($m->getTitle(), $p) && $p[$m->getTitle()] > 0) {
            //                $fps[$m->getTitle()] = $p[$m->getTitle()];
            //                #$fps[$m->getTitle()] = "default";
            //            }
        }
        return new Response(json_encode($fps));
    }
    #----------------------- /pi/product/item_image_data/{id}
    public function productItemImageDataAction($id) {        
        $mh = new MannequenHelper();
        $m_co = $mh->getCoordinates();
        $ar=$mh->getRawCoordinates();
        for ($i=0;$i<count($ar);$i++) {
            $ar[$i][0] = floor(($ar[$i][0] * 3.71142908458136) + 6);
            $ar[$i][1] = floor(($ar[$i][1] * 3.71142908458136) + 6);
            #$ar[$i][0] = $ar[$i][0];
            #$ar[$i][1] = $ar[$i][1];
        }        
        return new Response(json_encode($ar));
        
        $fp_coor = array();        
        $item = $this->get('admin.helper.product_item')->find($id);                
        $img = imagecreatefrompng($item->getAbsolutePath());   
        $fabric=false;
        
        foreach ($m_co as $fp => $co) {
            $y = $co[1];
            $fp_coor[$fp]=array();
            $fabric = false;                    
            for ($x = 0; $x < imagesx($img); $x++) {
                $rgb = imagecolorat($img, $x, $y);
                $pixel_color = imagecolorsforindex($img, $rgb);                     
                if ($pixel_color['alpha'] == 0 && !$fabric) {       #fill & no fab
                        array_push($fp_coor[$fp], array($x, $y));
                        $fabric = true;                    
                }elseif ($pixel_color['alpha'] > 126 && $fabric) {   #empty & fab                 
                        array_push($fp_coor[$fp], array($x, $y));
                        $fabric = false;                    
                }
            }
        }

        return new Response(json_encode($fp_coor));
        
//        for ($y = 0; $y < imagesy($img); $y++) {
//            for ($x = 0; $x < imagesx($img); $x++) {
//                $rgb = imagecolorat($img, $x, $y);
//                $pixel_color = imagecolorsforindex($img, $rgb);
//                if ($pixel_color['alpha']==0){                    
//                    return new Response(json_encode(array($x, $y)));
//                }
////                //This is the algorithm from the link. But reordered to get the old color before the transparent color went over it.
////                $oldR = ($pixel_color['red'] - $alpha * $red) / (1 - $alpha);
////                $oldG = ($pixel_color['green'] - $alpha * $green ) / (1 - $alpha);
////                $oldB = ($pixel_color['blue'] - $alpha * $blue) / (1 - $alpha);
////
////                $color = imagecolorallocate($img, $oldR, $oldG, $oldB);
////                imagesetpixel($img, $x, $y, $color);
//            }
//        }
//
//        $rgb = imagecolorat($img, 1, 1);
//        $colors = imagecolorsforindex($img, $rgb);
//        return new Response(json_encode(array($x, $y)));
//        #return new Response($item->getAbsolutePath());
//        #return new Response(json_encode($fps));
    }

}
