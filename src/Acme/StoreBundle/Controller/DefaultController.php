<?php

namespace Acme\StoreBundle\Controller;

use Acme\StoreBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;



use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


use Acme\StoreBundle\Entity\Merch;
use Acme\StoreBundle\Form\MerchType;


class DefaultController extends Controller
{
    public function indexAction()
    {
         
        return $this->render('AcmeStoreBundle:Default:index.html.twig',array(
            'entities' => $this->getList(),
              'entity' => $this->getNewEntity(),
            'form'   => $this->getNewForm(),
        ));
    }
    
    
    #---------------------------------------------------------------------------
 private function getNewForm()
    {
         return $this->createForm(new MerchType(), new Merch())->createView();

    }
    private function getForm($entity)
    {
         return $this->createForm(new MerchType(), $entity)->createView();
    }
    private function getList()
    {
          return $this->getDoctrine()->getRepository('AcmeStoreBundle:Merch')->findAll();
    }
      private function getNewEntity()
    {
        return new Merch();

    }    
}
