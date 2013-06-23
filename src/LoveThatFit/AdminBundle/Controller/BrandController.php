<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Entity\Brand;
use Symfony\Component\Form\FormError;
use Symfony\Component\Yaml\Parser;
use LoveThatFit\AdminBundle\Form\Type\DeleteType;

class BrandController extends Controller {

    //------------------------------------------------------------------------------------------
    public function indexAction($page_number, $sort = 'id') {
        
       return $this->render('LoveThatFitAdminBundle:Brand:index.html.twig', 
               $this->get('admin.helper.brand')->getListWithPagination($page_number, $sort));
    }

//------------------------------------------------------------------------------------------

    public function showAction($id) {
        $brand = $this->get('admin.helper.brand')->find($id);

        if (!$brand) {
            $brand = $this->get('admin.helper.brand')->createNew();
            $this->get('session')->setFlash('warning', 'The Brand not found!');
        }

        return $this->render('LoveThatFitAdminBundle:Brand:show.html.twig', array(
                    'brand' => $brand
                ));
    }

//------------------------------------------------------------------------------------------
    public function newAction() {

        $entity = $this->get('admin.helper.brand')->createNew();

        $form = $this->createFormBuilder($entity, array(
                    'validation_groups' => array('brand_create')))
                ->add('name', 'text')
                ->add('file')
                ->add('disabled', 'hidden', array('data' => '0',))
                ->getForm();

        return $this->render('LoveThatFitAdminBundle:Brand:new.html.twig', array(
                    'form' => $form->createView()));
    }

    //------------------------------------------------------------------------------------------
    public function createAction(Request $request) {
        $entity = $this->get('admin.helper.brand')->createNew();
        $form = $this->createFormBuilder($entity, array(
                    'validation_groups' => array('brand_create')))
                ->add('name', 'text')
                ->add('file')
                ->add('disabled', 'hidden', array('data' => '0',))
                ->getForm();

        $form->bind($request);
        
        $validation_array=$this->get('admin.helper.brand')->isValid($entity);
        
        if ($validation_array['valid']==false){
            $form->get($validation_array['field'])->addError(new FormError($validation_array['message']));
            $form->addError(new FormError($validation_array['message']));
        }
        
        if ($form->isValid()) {
            $message_array = $this->get('admin.helper.brand')->save($entity);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            return $this->redirect($this->generateUrl('admin_brand_show', array('id' => $entity->getId())));
        } else {
            $this->get('session')->setFlash('warning', 'The Brand can not be Created!');
        }
        return $this->render('LoveThatFitAdminBundle:Brand:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                ));
    }

//------------------------------------------------------------------------------------------
    public function editAction($id) {
       
$entity = $this->get('admin.helper.brand')->find($id);
        $form = $this->createFormBuilder($entity, array(
                    'validation_groups' => array('brand_update')))
                ->add('name')
                ->add('file', null, array('required' => false))
                ->add('disabled', 'checkbox', array('label' => 'Disabled', 'required' => false,))
                ->getForm();
        $deleteForm = $this->createForm(new DeleteType(), $entity);
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

        $deleteForm = $this->createForm(new DeleteType(), $entity);

        if ($form->isValid()) {
            $entity->setUpdatedAt(new \DateTime('now'));

            $entity->upload();

            $em->persist($entity);
            $em->flush();
            $this->get('session')->setFlash('success', 'The Brand has been update!');
            return $this->redirect($this->generateUrl('admin_brand_show', array('id' => $entity->getId())));
        } else {
            $this->get('warning')->setFlash('warning', 'Unable to update Brand!');
            //throw $this->createNotFoundException('Unable to update Brand.');
        }
    }

//------------------------------------------------------------------------------------------

    public function deleteAction($id) {
        try {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LoveThatFitAdminBundle:Brand')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Brand.');
            }
            $em->remove($entity);
            $em->flush();
            $this->get('session')->setFlash('success', 'The Brand has been deleted!');
            return $this->redirect($this->generateUrl('admin_brands'));
        } catch (\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->setFlash('warning', 'This Brand cannot be deleted!');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }


//------------------------------------------------------------------------------------------    
    private function getEditForm($entity) {
        return $this->createFormBuilder($entity)
                        ->add('name')
                        ->add('file')
                        ->add('disabled', 'checkbox', array('label' => 'Disabled', 'required' => false,))
                        ->getForm();
    }

    private function getBrandByName($name) {
        $em = $this->getDoctrine()->getManager();
        $BrandTypeObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Brand');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Brand')
                ->findBrandBy($name);
        $rec_count = count($BrandTypeObj->findBrandBy($name));
        return $rec_count;
    }

}
