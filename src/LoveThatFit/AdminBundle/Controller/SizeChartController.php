<?php

namespace LoveThatFit\AdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Form\Type\DeleteType;
use LoveThatFit\AdminBundle\Form\Type\SizeChartType;


class SizeChartController extends Controller {
//---------------------------------------------------------------------
    
    public function indexAction($page_number, $sort = 'id') {
        $size_with_pagination = $this->get('admin.helper.sizechart')->getListWithPagination($page_number, $sort);
        return $this->render('LoveThatFitAdminBundle:SizeChart:index.html.twig', $size_with_pagination);
    }
    
    
    
    public function showAction($id) {

        $specs = $this->get('admin.helper.sizechart')->findWithSpecs($id);
        $entity = $specs['entity'];
        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
        }
        return $this->render('LoveThatFitAdminBundle:SizeChart:show.html.twig', array(
                    'sizechart' => $entity
                ));
    }    
    
   
            


    
     public function newAction() {
        $entity = $this->get('admin.helper.sizechart')->createNew();
        $form = $this->createForm(new SizeChartType('add'), $entity);      
      
       return $this->render('LoveThatFitAdminBundle:SizeChart:new.html.twig', array(
                    'form' => $form->createView()));
    }
    
    public function createAction(Request $request)
    {
       
        $entity = $this->get('admin.helper.sizechart')->createNew();
        $form = $this->createForm(new SizeChartType('add'), $entity);
        $form->bind($request);
        if ($form->isValid()) {

            $message_array = $this->get('admin.helper.sizechart')->save($entity);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);

            if ($message_array['success']) {
                return $this->redirect($this->generateUrl('admin_size_chart_show', array('id' => $entity->getId())));
            }
        } else {
            $this->get('session')->setFlash('warning', 'The Size chart can not be Created!');
        }

        return $this->render('LoveThatFitAdminBundle:SizeChart:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
        
    }
    
    
    //--------------------------Delete Size Chart-------------------
    public function deleteAction($id) {
        try {

            $message_array = $this->get('admin.helper.sizechart')->delete($id);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);

            return $this->redirect($this->generateUrl('admin_size_charts'));
        } catch (\Doctrine\DBAL\DBALException $e) {

            $this->get('session')->setFlash('warning', 'This Size cannot be deleted!');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }
    
    
    
    
    
    
    
    public function editAction($id)
    {
        $specs = $this->get('admin.helper.sizechart')->findWithSpecs($id);
        $entity = $specs['entity'];
        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
        }
        $form = $this->createForm(new SizeChartType('edit'), $entity);
        return $this->render('LoveThatFitAdminBundle:SizeChart:edit.html.twig', array(
                    'form' => $form->createView(),                   
                    'entity' => $entity));
    }
    
    
    public function updateAction(Request $request, $id)
    {
       $specs = $this->get('admin.helper.sizechart')->findWithSpecs($id);
       $entity = $specs['entity'];   
       $form = $this->createForm(new SizeChartType('edit'), $entity);
        $form->bind($request);
      if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
            return $this->redirect($this->generateUrl('admin_size_charts'));
        } 
        
        if ($form->isValid()) {
            $message_array = $this->get('admin.helper.sizechart')->update($entity);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            if ($message_array['success'] == true) {
                return $this->redirect($this->generateUrl('admin_size_chart_show', array('id' => $entity->getId())));
            }
        } else {
            $this->get('session')->setFlash('warning', 'Unable to update Size Chart!');
        }
        $deleteForm = $this->createForm(new DeleteType(), $entity);
        return $this->render('LoveThatFitAdminBundle:SizeChart:edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity));
    }

}

