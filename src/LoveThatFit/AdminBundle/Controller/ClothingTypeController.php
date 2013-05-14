<?php
namespace LoveThatFit\AdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Entity\ClothingType;

class ClothingTypeController extends Controller {
//------------------------------------------------------------------------------------------
    public function indexAction($page_number, $sort = 'id') {
		$limit = 5;
		$clothingObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:ClothingType');
		
		$clothing_types = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ClothingType')
               ->findAllClothingType($page_number, $limit, $sort);
	 	$rec_count = count($clothingObj->countAllRecord());
		$cur_page = $page_number;

        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }
		//return new Response(json_encode($clothing_types));	   
	 return $this->render('LoveThatFitAdminBundle:ClothingType:index.html.twig', 
  					array(
                    'clothing_types' => $clothing_types, 
                    'rec_count' => $rec_count, 
                    'no_of_pagination' => $no_of_paginations, 
                    'limit' => $cur_page, 
                    'per_page_limit' => $limit,
            		));
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
                 ->add('disabled', 'hidden', array('data' => '0',))
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
                ->add('disabled', 'hidden', array('data' => '0',))
                ->getForm();
        
        $form->bind($request);

            if ($form->isValid()) {

                $clothing_type->setCreatedAt(new \DateTime('now'));
                $clothing_type->setUpdatedAt(new \DateTime('now'));

                $em = $this->getDoctrine()->getManager();
                $em->persist($clothing_type);
                $em->flush();
                $this->get('session')->setFlash('success','The Clothing Type has been Created!');
                return $this->redirect($this->generateUrl('admin_clothing_types'));
            }else
            {
                $this->get('warning')->setFlash('warning','The Clothing Type can not be Created!');
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
            $this->get('session')->setFlash('success','The Clothing Type has been Update!');
            return $this->redirect($this->generateUrl('admin_clothing_types'));
}
else
{
    $this->get('warning')->setFlash('warning','The Clothing Type cant Update!');       
    return $this->redirect($this->generateUrl('admin_clothing_types'));
}
    }
    
    //------------------------------------------------------------------------------------------
    
     public function deleteAction($id)
    {
          
         try{
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LoveThatFitAdminBundle:ClothingType')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Clothing Type.');
            }
            $em->remove($entity);
            $em->flush();
            $this->get('session')->setFlash('success','The Clothing Type has been deleted!');
            return $this->redirect($this->generateUrl('admin_clothing_types'));
        }catch (\Doctrine\DBAL\DBALException $e)
        {
             $this->get('session')->setFlash('warning','This Clothing Type cannot be deleted!');
             return $this->redirect($this->getRequest()->headers->get('referer'));
             
        }
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
                ->add('disabled', 'checkbox',array('label' =>'Disabled','required'=> false,)) 
                ->getForm();

    }

}

