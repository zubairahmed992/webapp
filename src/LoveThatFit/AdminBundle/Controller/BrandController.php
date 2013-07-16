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

        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
        }
        return $this->render('LoveThatFitAdminBundle:Brand:show.html.twig', array(
                    'brand' => $entity
        ));
    }

//------------------------------------------------------------------------------------------
    public function newAction() {

        $entity = $this->get('admin.helper.brand')->createNew();
        $form = $this->createForm(new BrandType('add'), $entity);

        return $this->render('LoveThatFitAdminBundle:Brand:new.html.twig', array(
                    'form' => $form->createView()));
    }

    //------------------------------------------------------------------------------------------
    public function createAction(Request $request) {

        $entity = $this->get('admin.helper.brand')->createNew();
        $form = $this->createForm(new BrandType('add'), $entity);
        $form->bind($request);




        if ($form->isValid()) {

            $message_array = $this->get('admin.helper.brand')->save($entity);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);

            if ($message_array['success']) {
                return $this->redirect($this->generateUrl('admin_brand_show', array('id' => $entity->getId())));
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

        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
        }

        $form = $this->createForm(new BrandType('edit'), $entity);

        $deleteForm = $this->createForm(new DeleteType(), $entity);
        return $this->render('LoveThatFitAdminBundle:Brand:edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity));
    }

    //------------------------------------------------------------------------------------------

    public function updateAction(Request $request, $id) {

        $specs = $this->get('admin.helper.brand')->findWithSpecs($id);
        $entity = $specs['entity'];

        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
            return $this->redirect($this->generateUrl('admin_brands'));
        }

        $form = $this->createForm(new BrandType('edit'), $entity);
        $form->bind($request);

        if ($form->isValid()) {

            $message_array = $this->get('admin.helper.brand')->update($entity);

            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);

            if ($message_array['success'] == true) {
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

}
