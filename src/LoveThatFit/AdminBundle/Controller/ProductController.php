<?php

namespace LoveThatFit\AdminBundle\Controller;
use LoveThatFit\AdminBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Form\Type\ProductType;


class ProductController extends Controller {
//---------------------------------------------------------------------
    public function indexAction() {
        $products = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->findAll();

        return $this->render('LoveThatFitAdminBundle:Product:index.html.twig', array('products' => $products));
    }


    
//--------------------------------------------------------------------- 
    public function showAction($id) {
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product.');
        }

        return $this->render('LoveThatFitAdminBundle:Product:show.html.twig', array(
                    'product' => $entity
                ));
    }


    
//--------------------------------------------------------------------- 
      public function newAction() {

        $entity = new Product();

        $form = $this-> getEditForm($entity);
        

        return $this->render('LoveThatFitAdminBundle:Product:new.html.twig', array(
                    'form' => $form->createView()));
    }
    

    
//--------------------------------------------------------------------- 
    public function createAction(Request $request)
    {
        $entity  = new Product();
        
        $form = $this -> getEditForm($entity);
        
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setCreatedAt(new \DateTime('now'));
            $entity->setUpdatedAt(new \DateTime('now'));
            
            $entity->upload();//----- file upload method 
            
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_product_show', array('id' => $entity->getId())));
        }

        return $this->render('LoveThatFitAdminBundle:Product:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    
//--------------------------------------------------------------------- 
    
      public function editAction($id) {
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->findOneById($id);

        $form = $this->getEditForm($entity);
        $deleteForm = $this->getDeleteForm($id);
        
        return $this->render('LoveThatFitAdminBundle:Product:edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity));
    }
   

//--------------------------------------------------------------------- 
     public function updateAction(Request $request, $id) {
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product.');
        }

        $form = $this->getEditForm($entity);
        $form->bind($request);

        $deleteForm = $this->getDeleteForm($id);

        if ($form->isValid()) {
            
            
            $entity->setUpdatedAt(new \DateTime('now'));
            
            $entity->upload(); //----- file upload method 
            
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('admin_product_show', array('id' => $entity->getId())));
        } 

        else {
           throw $this->createNotFoundException('Unable to update Brand.');
        }
    }

    
//--------------------------------------------------------------------- 
    public function deleteAction(Request $request, $id)
    {
        $form = $this->getDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Product.');
            }

            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('admin_products'));
    }
    
   

//--------------------------------------------------------------------- 
    
    private function getEditForm($entity) {
        return  $this->createForm(new ProductType(), $entity);        
    }
    
    
//---------------------------------------------------------------------
    private function getDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm();
    }
   

//--------------------------------------------------------------------- 
    private function getBrand($id)
    {
         $em = $this->getDoctrine()->getManager();
        $brand = $em->getRepository('LoveThatFitAdminBundle:Brand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product.');
        }
        return $brand;
        
    }
   
   
   }

