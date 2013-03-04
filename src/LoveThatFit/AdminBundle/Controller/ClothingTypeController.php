<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Entity\ClothingType;

class ClothingTypeController extends Controller {
//------------------------------------------------------------------------------------------
    public function indexAction() {
        $clothing_types = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ClothingType')
                ->findAll();

        return $this->render('LoveThatFitAdminBundle:ClothingType:index.html.twig', array('clothing_types' => $clothing_types));
    }
//------------------------------------------------------------------------------------------
    public function showAction($id) {
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ClothingType')
                ->findOneById($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Clothing Type.');
        }

        return $this->render('LoveThatFitAdminBundle:ClothingType:show.html.twig', array('clothing_type' => $entity));
    }

   //------------------------------------------------------------------------------------------
    public function newAction() {

        $clothing_type = new ClothingType();

         $form = $this->createFormBuilder($clothing_type)
                ->add('name', 'text')
                ->add('target', 'choice', array('choices'=> array('Top'=>'Top','Bottom'=>'Bottom', 'dress'=>'dress')))
                ->getForm();

           return $this->render('LoveThatFitAdminBundle:ClothingType:new.html.twig', array(
                        'form' => $form->createView()));
    }
    
    //------------------------------------------------------------------------------------------
    public function createAction(Request $request)
    {
        $clothing_type = new ClothingType();

         $form = $this->createFormBuilder($clothing_type)
                ->add('name', 'text')
                ->add('target', 'choice', array('choices'=> array('Top'=>'Top','Bottom'=>'Bottom', 'dress'=>'dress')))
                ->getForm();
        
        $form->bind($request);

            if ($form->isValid()) {

                $clothing_type->setCreatedAt(new \DateTime('now'));
                $clothing_type->setUpdatedAt(new \DateTime('now'));

                $em = $this->getDoctrine()->getManager();
                $em->persist($clothing_type);
                $em->flush();

                return $this->redirect($this->generateUrl('admin_clothing_types'));
            }

        return $this->render('LoveThatFitAdminBundle:ClothingType:new.html.twig', array(
                        'form' => $form->createView()));
    }
    
    
    
    
//------------------------------------------------------------------------------------------
    public function editAction($id) {
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ClothingType')
                ->findOneById($id);

        $form = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);
        
        return $this->render('LoveThatFitAdminBundle:ClothingType:edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity));
    }
//------------------------------------------------------------------------------------------
    public function updateAction(Request $request, $id) {
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:ClothingType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Clothing Type.');
        }

        $form = $this->createEditForm($entity);
        $form->bind($request);
        
         
        
if ($form->isValid()) {
    

            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('admin_clothing_types'));
}
else
{
            return $this->redirect($this->generateUrl('admin_clothing_types'));
}
    }
    
    //------------------------------------------------------------------------------------------
    
     public function deleteAction($id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LoveThatFitAdminBundle:ClothingType')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Clothing Type.');
            }

            $em->remove($entity);
            $em->flush();
        
        return $this->redirect($this->generateUrl('admin_clothing_types'));
    }
    
    //------------------------------------------------------------------------------------------
    
     private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
    }
    //------------------------------------------------------------------------------------------
    private function createEditForm($entity)
    {
        return  $this->createFormBuilder($entity)
                ->add('name', 'text')
                ->add('target', 'choice', array('choices'=> array('Top'=>'Top','Bottom'=>'Bottom', 'Dress'=>'Dress')))
                ->getForm();

    }

}

