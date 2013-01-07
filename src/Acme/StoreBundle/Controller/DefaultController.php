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
    
    #----------------------------------------------------------
    
    
       public function newAction()
    {
        $product = new Product();
         $form = $this->createFormBuilder($product)
            ->add('name')
            ->add('price')
            ->add('description')
            ->add('file')                 
            ->getForm();
         
          return $this->render('AcmeStoreBundle:Default:new.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
      public function createAction(Request $request)
    {
        $entity  = new Product();
        
        $form = $this->createFormBuilder($entity)
                ->add('name')
                ->add('price')
                ->add('description')
                ->add('file')
                ->getForm();
        
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->upload();
            
            $em->persist($entity);
            $em->flush();
            
             return $this->render('AcmeStoreBundle:Default:edit.html.twig',array(
            'entity' => $this->getNewEntity(),
                 'id' => $entity->getId(), 
            'form'   => $this->getNewForm(),
        ));

            
        }

        return $this->render('AcmeStoreBundle:Default:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    
     public function editAction($id) {
         $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AcmeStoreBundle:Product')->find($id);

          $form = $this->createFormBuilder($entity)
               
                ->add('file')
                ->getForm();
        
          return $this->render('AcmeStoreBundle:Default:edit.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
              'id' => $entity->getId(), 
              
        ));
        
        
    }
    
    public function _updateAction(Request $request, $id) {
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AcmeStoreBundle:Product')->find($id);

       $form = $this->createFormBuilder($entity)
                ->add('file')
                ->getForm();
        $form->bind($request);
        
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $response= new Response(json_encode(array(
            'entity' => $entity,
            'imageurl' => $entity->getWebPath()
                
        )));
            
             $fp = fopen($entity->getWebPath(), "rb");
              $str = stream_get_contents($fp);
              fclose($fp);
               $response = new Response($str, 200);
                $response->headers->set('Content-Type', 'image/png');
            
            return $response;
        
    }
    
    public function updateAction(Request $request, $id) {
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AcmeStoreBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Brand.');
        }

       $form = $this->createFormBuilder($entity)
                ->add('file')
                ->getForm();
        $form->bind($request);

      

        if ($form->isValid()) {
            
            $entity->upload();
            
            $em->persist($entity);
            $em->flush();
             
            $response= new Response(json_encode(array(
            'entity' => $entity,
            'imageurl' => $entity->getWebPath()
                
        )));
            
            $response->headers->set('Content-Type', 'application/json');
            return $response;
            
            #return  new Response("<img src='{{ asset(entity.WebPath) }}' height='80' width='100' />");
        } 

        else {
           throw $this->createNotFoundException('Unable to update Product.');
        }
    }
    #---------------------------------------------------------------------------
 public function getNewProductForm() 
    {
    $product = new Product();
    $product->setName('A Foo Bar');
    $product->setPrice('19.99');
    $product->setDescription('Lorem ipsum dolor');
    $product->setImageUrl('y:/Lorem ipsum dolor');

    $em = $this->getDoctrine()->getManager();
    $em->persist($product);
    $em->flush();

    return new Response('Created product id '.$product->getId());  
     
         #return $this->createForm(new MerchType(), new Merch())->createView();
    }
    private function getProductForm($entity)
    {
         return $this->createForm(new MerchType(), $entity)->createView();
    }
    private function getProductList()
    {
          return $this->getDoctrine()->getRepository('AcmeStoreBundle:Merch')->findAll();
    }
      private function getProductNewEntity()
    {
        return new Merch();

    }
}
