<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\AdminBundle\Form\Type\AlgoritumTestlType;
use LoveThatFit\AdminBundle\Form\Type\AlgoritumProductTestlType;
use LoveThatFit\SiteBundle\Comparison;
use LoveThatFit\SiteBundle\DependencyInjection\FitAlgorithm2;

class AlgorithmController extends Controller {

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

    public function fitAlgorithmCompareAction($user_id, $product_id, $json = 0) {
        $product = $this->get('admin.helper.product')->find($product_id);
        $user = $this->get('user.helper.user')->find($user_id);
        $fe = new FitAlgorithm2($user, $product);

        if ($json == 0) {
            return $this->render('LoveThatFitAdminBundle:Algoritm:_algo2_comparison.html.twig', array(
                        'product' => $product, 'user' => $user, 'data' => $fe->getFeedback(),
                    ));
        } elseif ($json == 1) {
            return new Response(json_encode($fe->getFeedback()));
        } elseif ($json == 2) {
            return new Response($fe->getStrippedFeedBackJSON());
        }
    }

    //------------------------------------------------------------------------------------------
    
    #--------------------------------------------------
    public function productListAction() {
        $userForm = $this->createForm(new AlgoritumTestlType());
        $users = $this->get('user.helper.user')->findAll();
        return $this->render('LoveThatFitAdminBundle:Algoritm:product_list_index.html.twig', array(
                    'userForm' => $userForm->createView(),        
                    'users' => $users,
                ));
    }
    

    #--------------------------------------------------
    public function userProductMarathonAction() {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());        
        #return new Response($decoded['ids']);
        #if range ...................
        if (strlen(ltrim($decoded['ids']))>0){
            $ids=explode(',', $decoded['ids']);
            
            $products = $this->get('admin.helper.product')->listProductByIds($ids);
        }else{
            $products = $this->get('admin.helper.product')->listAll($decoded['page'], $decoded['limit']);
        }
        
        $pa= array(); 
        $user = $this->get('user.helper.user')->find($decoded['user_id']);
        $algo = new FitAlgorithm2($user);
                
        foreach ($products as $p) {
            $algo->setProduct($p);
            $fb = $algo->getFeedBack();
            if (array_key_exists('recommendation', $fb)) {
                $pa[$p->getId()] = array('name' => $p->getName(),
                    'fit_index'=>$fb['recommendation']['fit_index'],
                    'size'=>$fb['recommendation']['description'],
                    );
            }
        }
        return $this->render('LoveThatFitAdminBundle:Algoritm:_recommendations.html.twig', array(                    
            'products' => $pa,
                ));
    }
}
