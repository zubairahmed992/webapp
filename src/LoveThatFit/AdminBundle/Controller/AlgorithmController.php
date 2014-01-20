<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Form\Type\DeleteType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use LoveThatFit\AdminBundle\Form\Type\AlgoritumTestlType;
use LoveThatFit\AdminBundle\Form\Type\AlgoritumProductTestlType;


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
    
    public function getUserForTestAlgorithmAction($id)
    {
        $user=$this->get('user.helper.user')->find($id);               
        return $this->render('LoveThatFitAdminBundle:Algoritm:usertestalgorithm.html.twig',array('user'=>$user));
    }
    
    public function getProductForTestAlgorithmAction($id)
    {
        $entity = $this->get('admin.helper.product')->find($id);                
        return $this->render('LoveThatFitAdminBundle:Algoritm:producttestalgorithm.html.twig',array(                   
                    'product'=>$entity,
                ));
    }

}
