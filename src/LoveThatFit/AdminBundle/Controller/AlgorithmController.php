<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\AdminBundle\Form\Type\AlgoritumTestlType;
use LoveThatFit\AdminBundle\Form\Type\AlgoritumProductTestlType;
use LoveThatFit\SiteBundle\Comparison;
use LoveThatFit\SiteBundle\AvgAlgorithm;

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
            return $this->render('LoveThatFitAdminBundle:Algoritm:_summary.html.twig', array(
                        'product' => $product, 'user' => $user, 'data' => $fb,
                    ));
        }
    }

    //------------------------------------------------------------------------------------------
    ################################################################
    ################################################################

    public function compareAction($user_id, $product_id, $json) {
        $product = $this->get('admin.helper.product')->find($product_id);
        $user = $this->get('user.helper.user')->find($user_id);
        $fe = new Comparison($user, $product);
        #return new Response(json_encode($fe->getComparison()));  
        #return new Response(json_encode($fe->getRecommendation()));        
        $fb = $fe->getFeedback();
        if ($json) {
            return new Response(json_encode($fb));
        } else {
            return $this->render('LoveThatFitAdminBundle:Algoritm:_summary.html.twig', array(
                        'product' => $product, 'user' => $user, 'data' => $fb,
                    ));
        }
    }

    //------------------------------------------------------------------------------------------
    public function comparisonAction() {
        $userForm = $this->createForm(new AlgoritumTestlType());
        $productForm = $this->createForm(new AlgoritumProductTestlType());
        return $this->render('LoveThatFitAdminBundle:Algoritm:comparison.html.twig', array(
                    'userForm' => $userForm->createView(),
                    'productForm' => $productForm->createView(),
                    'user' => '',
                ));
    }

    //------------------------------------------------------------------------------------------
    #admin_algorithm_foo:
    #pattern:  /admin/algorithm/foo/{user_id}/{product_id}
    #defaults: { _controller: LoveThatFitAdminBundle:Algorithm:foo, user_id: 0, product_id: 0, size_id: 0}


    public function fooAction($user_id, $product_id, $size_id) {
        $product = $this->get('admin.helper.product')->find($product_id);
        $user = $this->get('user.helper.user')->find($user_id);
        $fe = new Comparison($user, $product);
        $product_size = $this->get('admin.helper.productsizes')->find($size_id);
        #return new Response($fe->getFeedBackJSON());                                
        return new Response(json_encode($fe->getSizeFeedBack($product_size)));
    }

}
