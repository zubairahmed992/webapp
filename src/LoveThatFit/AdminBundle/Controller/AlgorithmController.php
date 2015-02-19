<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\AdminBundle\Form\Type\AlgoritumTestlType;
use LoveThatFit\AdminBundle\Form\Type\AlgoritumProductTestlType;
use LoveThatFit\SiteBundle\Comparison;
use LoveThatFit\SiteBundle\AvgAlgorithm;
use LoveThatFit\SiteBundle\DependencyInjection\FitAlgorithm2;

class AlgorithmController extends Controller {

    public function avgAction() {
        $userForm = $this->createForm(new AlgoritumTestlType());
        $productForm = $this->createForm(new AlgoritumProductTestlType());
        return $this->render('LoveThatFitAdminBundle:Algoritm:avg_index.html.twig', array(
                    'userForm' => $userForm->createView(),
                    'productForm' => $productForm->createView(),
                    'user' => '',
                ));
    }

    public function avgComparisonAction($user_id, $product_id, $json) {
        $product = $this->get('admin.helper.product')->find($product_id);
        $user = $this->get('user.helper.user')->find($user_id);
        $fe = new AvgAlgorithm($user, $product);
        #return new Response(json_encode($fe->getComparison()));  
        #return new Response(json_encode($fe->getRecommendation()));        
        $fb = $fe->getFeedback();
        if ($json) {
            return new Response(json_encode($fb));
        } else {
            return $this->render('LoveThatFitAdminBundle:Algoritm:_avg_comparison.html.twig', array(
                        'product' => $product, 'user' => $user, 'data' => $fb,
                    ));
        }
    }

    //------------------------------------------------------------------------------------------
################################################################
#   Fit Algorithm 2    
################################################################

    public function fitAlgorithmIndexAction() {
        $userForm = $this->createForm(new AlgoritumTestlType());
        $productForm = $this->createForm(new AlgoritumProductTestlType());
        return $this->render('LoveThatFitAdminBundle:Algoritm:algo2_index.html.twig', array(
                    'userForm' => $userForm->createView(),
                    'productForm' => $productForm->createView(),
                    'user' => '',
                ));
    }
//------------------------------------------------------------------------------------------
    
    public function fitAlgorithmCompareAction($user_id, $product_id, $json) {
        $product = $this->get('admin.helper.product')->find($product_id);
        $user = $this->get('user.helper.user')->find($user_id);
        $fe = new FitAlgorithm2($user, $product);
        $fb = $fe->getFeedback();
        if ($json) {
            return new Response(json_encode($fb));
        } else {
           return $this->render('LoveThatFitAdminBundle:Algoritm:_algo2_comparison.html.twig', array(
                        'product' => $product, 'user' => $user, 'data' => $fb,
                    ));
        }
    }
    //------------------------------------------------------------------------------------------
    #admin_algorithm_foo:
    #pattern:  /admin/algorithm/foo/{user_id}/{product_id}
    #defaults: { _controller: LoveThatFitAdminBundle:Algorithm:foo, user_id: 0, product_id: 0, size_id: 0}


    public function fooAction($user_id=0, $product_id=0, $size_id=0) {
        $user_id=18; $product_id=8;
        $product = $this->get('admin.helper.product')->find($product_id);
        $user = $this->get('user.helper.user')->find($user_id);
        $fa = new FitAlgorithm2($user, $product);                
        return new response($fa->getFeedBackJSON());
    }

}
