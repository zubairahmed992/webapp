<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Entity\Brand;

class BrandController extends Controller {

    public function indexAction() {
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Brand')
                ->findAll();
        return $this->render('LoveThatFitAdminBundle:Brand:index.html.twig', array('brands' => $entity));
    }

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

    public function newAction() {

        $entity = new Brand();

        $form = $this->createFormBuilder($entity)
                ->add('name', 'text')
                ->add('file')
                ->getForm();

        return $this->render('LoveThatFitAdminBundle:Brand:new.html.twig', array(
                    'form' => $form->createView()));
    }
    
    
    public function createAction(Request $request)
    {
        $entity  = new Brand();
        
        $form = $this->createFormBuilder($entity)
                ->add('name', 'text')
                ->add('file')
                ->getForm();
        
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setCreatedAt(new \DateTime('now'));
            $entity->setUpdatedAt(new \DateTime('now'));
            
            $entity->upload();
            
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_brand_show', array('id' => $entity->getId())));
        }

        return $this->render('LoveThatFitAdminBundle:Brand:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    
      public function editAction($id) {
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Brand')
                ->findOneById($id);

        $form = $this->getEditForm($entity);
        $deleteForm = $this->getDeleteForm($id);
        
        return $this->render('LoveThatFitAdminBundle:Brand:edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity));
    }
    
    
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
            return $this->redirect($this->generateUrl('admin_brand_show', array('id' => $entity->getId())));
        } 

        else {
           throw $this->createNotFoundException('Unable to update Brand.');
        }
    }
    
    public function deleteAction(Request $request, $id)
    {
        $form = $this->getDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LoveThatFitAdminBundle:Brand')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Brand.');
            }

            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('admin_brands'));
    }
    
      private function getDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
    }
    
    private function getEditForm($entity)
    {
        return $this->createFormBuilder($entity)
                ->add('name')
                ->add('file')
                ->getForm();
    }
    
    private function getCreateForm()
    {
        return $this->createFormBuilder()
                ->add('name')
                ->add('file')
                ->getForm();
    }
    

}
