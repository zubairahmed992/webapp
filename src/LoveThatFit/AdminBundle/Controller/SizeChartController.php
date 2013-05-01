<?php

namespace LoveThatFit\AdminBundle\Controller;
use LoveThatFit\AdminBundle\Entity\SizeChart;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Form\Type\SizeChartType;


class SizeChartController extends Controller {
//---------------------------------------------------------------------
    public function indexAction() {
        return $this->render('LoveThatFitAdminBundle:SizeChart:index.html.twig',array('sizechart'=>  $this->getSizeChartList()));
    }    
   
    
   public function showAction($id) {
       $em = $this->getDoctrine()->getManager();
       $entity = $this->getSizeChartById($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Size Chart.');        }

        return $this->render('LoveThatFitAdminBundle:SizeChart:show.html.twig', array(
                    'sizechart' => $entity
                ));
    }
            


    
     public function newAction() {

       $entity = new SizeChart();
       $form = $this->getAddSizeChartForm($entity);
       return $this->render('LoveThatFitAdminBundle:SizeChart:new.html.twig', array(
                    'form' => $form->createView()));
    }
    
    public function createAction(Request $request)
    {
       $entity = new SizeChart();
       $form = $this->getAddSizeChartForm($entity);
       $form->bind($request);
       if ($form->isValid()) {
           $em = $this->getDoctrine()->getManager();
           $em->persist($entity);
           $em->flush();
           $this->get('session')->setFlash('success','The Size Chart has been Created!');
            return $this->redirect($this->generateUrl('admin_size_chart'));
       }
    }
    
    
    public function deleteAction($id)
    {
        try{
            $em = $this->getDoctrine()->getManager();
            $entity =  $this->getSizeChartById($id) ;
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find SizeChart.');
            }
            $em->remove($entity);
            $em->flush();
            $this->get('session')->setFlash('success','The Size Chart has been deleted!');
       return $this->redirect($this->generateUrl('admin_size_chart'));
        }catch (\Doctrine\DBAL\DBALException $e)
        {
             $this->get('session')->setFlash('warning','This Size Chart cannot be deleted!');
             return $this->redirect($this->getRequest()->headers->get('referer'));             
        }
    }
    
    
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getSizeChartById($id);
        $form = $this->getAddSizeChartForm($entity);
        return $this->render('LoveThatFitAdminBundle:SizeChart:edit.html.twig', array(
                    'form' => $form->createView(),                   
                    'entity' => $entity));
    }
    
    
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
       $entity = $this->getSizeChartById($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Size Chart.');
        }
        $form = $this->getAddSizeChartForm($entity);
        $form->bind($request);
        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->get('session')->setFlash('success','The Size Chart has been update!');
            return $this->redirect($this->generateUrl('admin_size_chart', array('id' => $entity->getId())));
        } 
        else {
           $this->get('warning')->setFlash('warning','Unable to update Size Chart!');          
        }
    }


    
    
    
    
    
    
    
    private function getAddSizeChartForm($entity) {
        return $this->createForm(new SizeChartType(), $entity);
    }
    
    private function getSizeChartList()
    {
       $sizeChart = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:SizeChart')->findAll();
       $rec_count = count($sizeChart);       
       return $sizeChart; 
    }
    
    private function getSizeChartById($id) {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:SizeChart');
        $sizeChart = $repository->find($id);
        return $sizeChart;
    }
    
}

