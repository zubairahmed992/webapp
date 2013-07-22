<?php

namespace LoveThatFit\AdminBundle\Controller;
use LoveThatFit\AdminBundle\Entity\SizeChart;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Form\Type\SizeChartType;


class SizeChartController extends Controller {
//---------------------------------------------------------------------
    public function indexAction($page_number=1 , $sort='id') {
        $limit = 30;
        $sizechartObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:SizeChart');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:SizeChart')
                 ->findAllSizeChart($page_number, $limit, $sort);
		$rec_count = count($sizechartObj->countAllSizeChartRecord());
		$cur_page = $page_number;
        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }
        return $this->render('LoveThatFitAdminBundle:SizeChart:index.html.twig',
		       array(	
                           'sizechart'=>$entity,
			   'rec_count' => $rec_count, 
                           'no_of_pagination' => $no_of_paginations, 
                           'limit' => $cur_page, 
                           'per_page_limit' => $limit,
                           'maleSizeChart'=>$this->getSizeChartByGender('m'),
                           'femaleSizeChart'=>$this->getSizeChartByGender('f'),
                           'topSizeChart'=>$this->getSizeChartByTarget('Top'),
                           'bottomSizeChart'=>  $this->getSizeChartByTarget('Bottom'),
                           'dressSizeChart'=>  $this->getSizeChartByTarget('Dress'),
			));
    }
        
        
         
   
    
   public function showAction($id) {
       $em = $this->getDoctrine()->getManager();
       $entity = $this->getSizeChartById($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Size Chart.');        }
        else{
        return $this->render('LoveThatFitAdminBundle:SizeChart:show.html.twig', array(
                    'sizechart' => $entity
                ));
        }
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
       $title = $entity->getTitle();
      
       if($title==="00")
       {
           $title="00";
       }
      else if($title=="0"){
         $title="0";
        
       }    
       $brand = $entity->getBrand()->getId();       
       $gender = $entity->getGender();       
       $target = $entity->getTarget();
       $bodytype=$entity->getBodytype();
       if($gender!=null and $target!=null and $bodytype!=null)
       {
       $sizechart=  $this->getBrandSize($brand,$title,$gender,$target,$bodytype);
       if($sizechart>0)
       {
           $this->get('session')->setFlash('warning','The Size : ' .$title. ', Gender: ' .$gender. ', Brand: '.$this->getBrandById($brand)->getName().' , Target: ' .$target.  ' , Body Type: ' .$bodytype. ' already exits!');
            return $this->render('LoveThatFitAdminBundle:SizeChart:new.html.twig', array(
                    'form' => $form->createView()));
       }else
       {
       if ($form->isValid()) {
           $em = $this->getDoctrine()->getManager();
           $em->persist($entity);
           $em->flush();
           $this->get('session')->setFlash('success','The Size Chart has been Created!');
           return $this->redirect($this->generateUrl('admin_size_charts'));
            //return $this->render('LoveThatFitAdminBundle:SizeChart:index.html.twig', array(
              //      'form' => $form->createView(),'sizechart' => $entity)); 
       }
       }
       }else
       {
       $this->get('session')->setFlash('warning','Please Enter Values Correctly.');
       return $this->render('LoveThatFitAdminBundle:SizeChart:new.html.twig', array(
                    'form' => $form->createView()));
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
       return $this->redirect($this->generateUrl('admin_size_charts'));
        }catch (\Doctrine\DBAL\DBALException $e)
        {
             $this->get('session')->setFlash('warning','This Size Chart cannot be deleted!');
             return $this->redirect($this->generateUrl('admin_size_charts'));          
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
            return $this->redirect($this->generateUrl('admin_size_charts', array('id' => $entity->getId())));
        } 
        else {
         //  $this->get('warning')->setFlash('warning','Unable to update Size Chart!');   
             return $this->render('LoveThatFitAdminBundle:SizeChart:new.html.twig', array(
                    'form' => $form->createView()));
        }
    }


    
    
   
    private function getAddSizeChartForm($entity) {
       
        return $this->createForm(new SizeChartType(), $entity);
    }
    
    private function getSizeChartById($id) {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:SizeChart');
        $sizeChart = $repository->find($id);
        return $sizeChart;
    }
    
    private function getBrandSize($brand,$title,$gender,$target,$bodytype)
    {
        $em = $this->getDoctrine()->getManager();
        $sizechartsObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:SizeChart');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:SizeChart')
                 ->findBrandSizeBy($brand,$title,$gender,$target,$bodytype);
		$rec_count = count($sizechartsObj->findBrandSizeBy($brand,$title,$gender,$target,$bodytype));
        return $rec_count;
    }
    
    private function getBrandById($brand)
    {
      $repository = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Brand');
        $brand = $repository->find($brand);
        return $brand;
    }
    
    private function getSizeChartByGender($gender)
    {
        $em = $this->getDoctrine()->getManager();
        $sizechartsObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:SizeChart');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:SizeChart')
                 ->findSizeChartByGender($gender);
		$rec_count = count($sizechartsObj->findSizeChartByGender($gender));
        return $rec_count;
    }
    
    private function getSizeChartByTarget($target)
    {
        $em = $this->getDoctrine()->getManager();
        $sizechartsObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:SizeChart');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:SizeChart')
                 ->findSizeChartByTarget($target);
		$rec_count = count($sizechartsObj->findSizeChartByTarget($target));
        return $rec_count;
    }
    
    
#--------------Testing of Utility Helper ---------------------#
    public function testHelperAction() {
        $utility_helper = $this->get('admin.helper.utility');
        $genders = $utility_helper->getGenders();
        #------Accessing the Single Gender----------------------#
        $man=$genders['men'];
        #-----------------SizeCharts-----------------------------#
        $sizeCharts = $utility_helper->getSizeCharts(); 
        #-----------------Target---------------------------------#
        $targets = $utility_helper->getTargets(); 
        #-------------Titles-------------------------------------#
        $sizeTtiles=$utility_helper->getSizeTitle(); 
        #-------------Size Numbers-------------------------------#
        $sizeNumbers=$utility_helper->getSizeNumbers(); 
        #------------BodyTypes-----------------------------------#
        
        $bodyTypes=$utility_helper->getBodyTypes(); 
        
        Return new Response(json_encode($bodyTypes));
    } 
}

