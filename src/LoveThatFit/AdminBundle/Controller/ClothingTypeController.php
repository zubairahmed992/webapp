<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Entity\ClothingType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Yaml\Parser;
use LoveThatFit\AdminBundle\Form\Type\DeleteType;
use LoveThatFit\AdminBundle\Form\Type\ClothingTypes;

class ClothingTypeController extends Controller {

//------------------------------------------------------------------------------------------


    public function indexAction($page_number, $sort = 'id') {
        $clothing_types_with_pagination = $this->get('admin.helper.clothingtype')->getListWithPagination($page_number, $sort);
        return $this->render('LoveThatFitAdminBundle:ClothingType:index.html.twig', $clothing_types_with_pagination);
    }

//------------------------------------------------------------------------------------------
    public function showAction($id) {

        $specs = $this->get('admin.helper.clothingtype')->findWithSpecs($id);
        $entity = $specs['entity'];

        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
        }
        return $this->render('LoveThatFitAdminBundle:ClothingType:show.html.twig', array(
                    'clothing_type' => $entity
        ));
    }

    //------------------------------------------------------------------------------------------
    public function newAction() {

        $entity = $this->get('admin.helper.ClothingType')->createNew();
        $form = $this->createForm(new ClothingTypes('add'), $entity);
        return $this->render('LoveThatFitAdminBundle:ClothingType:new.html.twig', array(
                    'form' => $form->createView()));
    }

    //------------------------------------------------------------------------------------------
    public function createAction(Request $request) {
        $entity = $this->get('admin.helper.ClothingType')->createNew();
        $form = $this->createForm(new ClothingTypes('add'), $entity);
        $form->bind($request);
        if ($entity->getName() != null and $form->isValid()) {
            $message_array = $this->get('admin.helper.ClothingType')->save($entity);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            if ($message_array['success']) {
                return $this->redirect($this->generateUrl('admin_clothing_type_show', array('id' => $entity->getId())));
            }
        } else {
            $this->get('session')->setFlash('warning', 'The ClothingType can not be Created!');
        }

        return $this->render('LoveThatFitAdminBundle:ClothingType:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

//------------------------------------------------------------------------------------------
    public function editAction($id) {

        $specs = $this->get('admin.helper.ClothingType')->findWithSpecs($id);
        $entity = $specs['entity'];

        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
        }

        $form = $this->createForm(new ClothingTypes('edit'), $entity);

        $deleteForm = $this->createForm(new DeleteType(), $entity);
        return $this->render('LoveThatFitAdminBundle:ClothingType:edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity));
    }

//------------------------------------------------------------------------------------------
    public function updateAction(Request $request, $id) {

        $specs = $this->get('admin.helper.ClothingType')->findWithSpecs($id);
        $entity = $specs['entity'];

        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
            return $this->redirect($this->generateUrl('admin_clothing_types'));
        }

        $form = $this->createForm(new ClothingTypes('edit'), $entity);
        $form->bind($request);

        if ($form->isValid()) {

            $message_array = $this->get('admin.helper.ClothingType')->update($entity);

            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);

            if ($message_array['success'] == true) {
                return $this->redirect($this->generateUrl('admin_clothing_type_show', array('id' => $entity->getId())));
            }
        } else {
            $this->get('session')->setFlash('warning', 'Unable to update Clothing Type!');
        }
        $deleteForm = $this->createForm(new DeleteType(), $entity);
        return $this->render('LoveThatFitAdminBundle:ClothingType:edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity));
    }

    //------------------------------------------------------------------------------------------

    public function deleteAction($id) {
        try {
            $message_array = $this->get('admin.helper.ClothingType')->delete($id);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);

            return $this->redirect($this->generateUrl('admin_clothing_types'));
        } catch (\Doctrine\DBAL\DBALException $e) {

            $this->get('session')->setFlash('warning', 'This Clothing cannot be deleted!');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }

    //------------------------------------------------------------------------------------------
}

