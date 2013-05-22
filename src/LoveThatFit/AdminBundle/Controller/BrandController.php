<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Entity\Brand;

class BrandController extends Controller {

    //------------------------------------------------------------------------------------------
    public function indexAction($page_number, $sort = 'id') {
		$limit = 5;
		$brandObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Brand');
                $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Brand')
                 ->findAllBrand($page_number, $limit, $sort);
		$rec_count = count($brandObj->countAllRecord());
		$cur_page = $page_number;

        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }
        return $this->render('LoveThatFitAdminBundle:Brand:index.html.twig',
		       array(
			    	'brands' => $entity,
					'rec_count' => $rec_count, 
                    'no_of_pagination' => $no_of_paginations, 
                    'limit' => $cur_page, 
                    'per_page_limit' => $limit,
					));
    }
//------------------------------------------------------------------------------------------
    
    public function showAction($id) {
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Brand')
                ->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Brand.');
        }

        return $this->render('LoveThatFitAdminBundle:Brand:show.html.twig', array(
                    'brand' => $entity
                ));
    }
//------------------------------------------------------------------------------------------
    public function newAction() {

        $entity = new Brand();

        $form = $this->createFormBuilder($entity,  array(
    'validation_groups' => array('brand_create')))
                ->add('name', 'text')
                ->add('file')
                ->add('disabled', 'hidden', array('data' => '0',))
                ->getForm();

        return $this->render('LoveThatFitAdminBundle:Brand:new.html.twig', array(
                    'form' => $form->createView()));
    }
    
    //------------------------------------------------------------------------------------------
    public function createAction(Request $request)
    {
        $entity  = new Brand();        
        $form = $this->createFormBuilder($entity)
                ->add('name', 'text')
                ->add('file')
                ->add('disabled','hidden', array('data' => '0',))
                ->getForm();
        
        $form->bind($request);
        $name = $entity->getName();       
        $brand=  $this->getBrandByName($name);
        if($brand>0)
       {
            $this->get('session')->setFlash('warning','The Brand:' .$name. ' already exits!');
            return $this->render('LoveThatFitAdminBundle:Brand:new.html.twig', array(
                'entity' => $entity,    
                'form' => $form->createView()));            
        }else
        {
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setCreatedAt(new \DateTime('now'));
            $entity->setUpdatedAt(new \DateTime('now'));
            
            $entity->upload();
            
            $em->persist($entity);
            $em->flush();
            $this->get('session')->setFlash('success','The Brand has been Created!');
            return $this->redirect($this->generateUrl('admin_brand_show', array('id' => $entity->getId())));
        }else
        {
            $this->get('warning')->setFlash('warning','The Brand can not be Created!');
        }
        
        }
        return $this->render('LoveThatFitAdminBundle:Brand:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    
//------------------------------------------------------------------------------------------
      public function editAction($id) {
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Brand')
                ->findOneById($id);

        $form = $this->createFormBuilder($entity,  array(
    'validation_groups' => array('brand_update')))
                ->add('name')
                ->add('file', null, array('required' => false))
                ->add('disabled', 'checkbox',array('label' =>'Disabled','required'=> false,))                
                ->getForm();
        $deleteForm = $this->getDeleteForm($id);
        
        return $this->render('LoveThatFitAdminBundle:Brand:edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity));
    }
    
    //------------------------------------------------------------------------------------------
    
     public function updateAction(Request $request, $id) {
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Brand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Brand.');
        }

        $form = $this->getEditForm($entity);
        $form->bind($request);

        $deleteForm = $this->getDeleteForm($id);

        if ($form->isValid()) {
            $entity->setUpdatedAt(new \DateTime('now'));
 
            $entity->upload();
            
            $em->persist($entity);
            $em->flush();
            $this->get('session')->setFlash('success','The Brand has been update!');
            return $this->redirect($this->generateUrl('admin_brand_show', array('id' => $entity->getId())));
        } 
        else {
           $this->get('warning')->setFlash('warning','Unable to update Brand!');
            //throw $this->createNotFoundException('Unable to update Brand.');
        }
    }

//------------------------------------------------------------------------------------------
    
    public function deleteAction($id)
    {
        try{
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LoveThatFitAdminBundle:Brand')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Brand.');
            }
            $em->remove($entity);
            $em->flush();
            $this->get('session')->setFlash('success','The Brand has been deleted!');
       return $this->redirect($this->generateUrl('admin_brands'));
        }catch (\Doctrine\DBAL\DBALException $e)
        {
             $this->get('session')->setFlash('warning','This Brand cannot be deleted!');
             return $this->redirect($this->getRequest()->headers->get('referer'));
             
        }
        
        
    }

//------------------------------------------------------------------------------------------    
      private function getDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
    }
//------------------------------------------------------------------------------------------    
    private function getEditForm($entity)
    {
        return $this->createFormBuilder($entity)
                ->add('name')
                ->add('file')
                ->add('disabled', 'checkbox',array('label' =>'Disabled','required'=> false,)) 
                ->getForm();
    }
      
    private function getBrandByName($name)
    {
        $em = $this->getDoctrine()->getManager();
        $BrandTypeObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Brand');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Brand')
                 ->findBrandBy($name);
		$rec_count = count($BrandTypeObj->findBrandBy($name));
        return $rec_count;
    }
}
