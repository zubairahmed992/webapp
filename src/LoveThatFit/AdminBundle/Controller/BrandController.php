<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Entity\Brand;
use Symfony\Component\Form\FormError;
use Symfony\Component\Yaml\Parser;
use LoveThatFit\AdminBundle\Form\Type\DeleteType;
use LoveThatFit\AdminBundle\Form\Type\BrandType;

class BrandController extends Controller {

    //------------------------------------------------------------------------------------------
    public function indexAction($page_number, $sort = 'id') {
        $brands_with_pagination = $this->get('admin.helper.brand')->getListWithPagination($page_number, $sort);
        return $this->render('LoveThatFitAdminBundle:Brand:index.html.twig', $brands_with_pagination);
    }

//------------------------------------------------------------------------------------------

    public function showAction($id) {
        
        $specs = $this->get('admin.helper.brand')->findWithSpecs($id);
        $entity = $specs['entity'];
        
        if ($specs['success']==false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
        }
        return $this->render('LoveThatFitAdminBundle:Brand:show.html.twig', array(
                    'brand' => $entity
                ));
    }

//------------------------------------------------------------------------------------------
    public function newAction() {

        $entity = $this->get('admin.helper.brand')->createNew();
        $form = $this->createForm(new BrandType(), $entity);

        return $this->render('LoveThatFitAdminBundle:Brand:new.html.twig', array(
                    'form' => $form->createView()));
    }

    //------------------------------------------------------------------------------------------
    public function createAction(Request $request) {

        $entity = $this->get('admin.helper.brand')->createNew();
        $form = $this->createForm(new BrandType(), $entity);
        $form->bind($request);

        
        if ($form->isValid()) {
            
            $message_array = $this->get('admin.helper.brand')->save($entity);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            
            if($message_array['success']){
                return $this->redirect($this->generateUrl('admin_brand_show', array('id' => $entity->getId())));
            }else{
                $form->get($message_array['field'])->addError(new FormError($message_array['message']));
                $form->addError(new FormError($message_array['message']));
            }
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

        $specs = $this->get('admin.helper.brand')->findWithSpecs($id);
        $entity = $specs['entity'];
        
        if ($specs['success']==false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
        }

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

        $entity = $this->get('admin.helper.brand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Brand.');
        }

        $form = $this->getEditForm($entity);
        $form->bind($request);
        
        if ($form->isValid()) {

            $message_array = $this->get('admin.helper.brand')->update($entity);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            
            if($message_array['success']){
                return $this->redirect($this->generateUrl('admin_brand_show', array('id' => $entity->getId())));
            }
            
        } else {
            $this->get('session')->setFlash('warning', 'Unable to update Brand!');
            
        }
        $deleteForm = $this->createForm(new DeleteType(), $entity);
        return $this->render('LoveThatFitAdminBundle:Brand:edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity));
    }

//------------------------------------------------------------------------------------------

    public function deleteAction($id) {
        try {
            
            $message_array = $this->get('admin.helper.brand')->delete($id);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            
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

    

}
