<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\AdminBundle\Form\Type\DeleteType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use LoveThatFit\AdminBundle\Form\Type\AlgoritumTestlType;
use LoveThatFit\AdminBundle\Form\Type\AlgoritumProductTestlType;
use LoveThatFit\SiteBundle\FitEngine;
use LoveThatFit\SiteBundle\Comparison;
use LoveThatFit\UserBundle\Entity\Measurement;
use LoveThatFit\UserBundle\Entity\User;

class AlgorithmController extends Controller {

    //------------------------------------------------------------------------------------------
    public function indexAction() { 
        $userForm = $this->createForm(new AlgoritumTestlType());
        $productForm = $this->createForm(new AlgoritumProductTestlType());        
        return $this->render('LoveThatFitAdminBundle:Algoritm:index.html.twig',array(
                    'userForm' => $userForm->createView(),                  
                    'productForm' => $productForm->createView(),   
                    'user'=>'',
                ));
    }
       //------------------------------------------------------------------------------------------
    public function getUserForTestAlgorithmAction($id)
    {
        $user=$this->get('user.helper.user')->find($id);               
        return $this->render('LoveThatFitAdminBundle:Algoritm:_user_detail.html.twig',array('user'=>$user));
    }
       //------------------------------------------------------------------------------------------
    public function getProductForTestAlgorithmAction($id)
    {
        $entity = $this->get('admin.helper.product')->find($id);                
        return $this->render('LoveThatFitAdminBundle:Algoritm:_product_detail.html.twig',array(                   
                    'product'=>$entity,
                ));
    }
     //------------------------------------------------------------------------------------------  
    public function getProductSizeForTestAlgorithmAction($id)
    {        
        $productsize = $this->get('admin.helper.productsizes')->find($id);               
        
        return $this->render('LoveThatFitAdminBundle:Algoritm:_size_detail.html.twig',array(                   
                    'product_size_measurement'=>$productsize,
                ));
    }
   //------------------------------------------------------------------------------------------
      public function getFeedbackAction($size_id, $user_id)
    {   
        $product_size = $this->get('admin.helper.productsizes')->find($size_id);               
        $product = $product_size->getProduct();  
        $product_color=$product->getDisplayProductColor();
        $product_item = $this->get('admin.helper.productitem')->findByColorSize($product_color->getId(), $size_id);
        $user = $this->get('user.helper.user')->find($user_id);  
        $fe = new FitEngine($user, $product_item);        
         $fb=$fe->getBasicFeedback();
        //return new Response($fe->getFeedBackJSON());
        return $this->render('LoveThatFitAdminBundle:Algoritm:_feedback.html.twig',array(                   
                    'product'=>$product, 'product_size'=>$product_size, 'user'=>$user, 'data'=>$fb, 'fb_json'=>  $fb,
                ));
    }
       //------------------------------------------------------------------------------------------
     public function compareAction($user_id, $product_id, $json)
    {   
        $product = $this->get('admin.helper.product')->find($product_id);
        $user = $this->get('user.helper.user')->find($user_id);  
        $fe = new Comparison($user, $product);                
        #return new Response(json_encode($fe->getComparison()));  
        #return new Response(json_encode($fe->getRecommendation()));        
        $fb=$fe->getFeedback();
         if($json){
            return new Response(json_encode($fb));        
        }else{
            return $this->render('LoveThatFitAdminBundle:Algoritm:_summary.html.twig',array(                   
                    'product'=>$product, 'user'=>$user, 'data'=>$fb,
                ));
        }           
    }
       //------------------------------------------------------------------------------------------
    public function comparisonAction() { 
        $userForm = $this->createForm(new AlgoritumTestlType());
        $productForm = $this->createForm(new AlgoritumProductTestlType());        
        return $this->render('LoveThatFitAdminBundle:Algoritm:comparison.html.twig',array(
                    'userForm' => $userForm->createView(),                  
                    'productForm' => $productForm->createView(),   
                    'user'=>'',
                ));
    }

           //------------------------------------------------------------------------------------------
     public function fooAction($user_id, $product_id)
    {   
        $product = $this->get('admin.helper.product')->find($product_id);
        $user = $this->get('user.helper.user')->find($user_id);  
        $fe = new Comparison($user, $product);                
        #return new Response($fe->getFeedBackJSON());                                
        return new Response(json_encode($fe->back_track()));                                
    }

    
}
