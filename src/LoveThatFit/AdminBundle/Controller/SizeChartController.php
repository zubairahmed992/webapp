<?php

namespace LoveThatFit\AdminBundle\Controller;
use LoveThatFit\AdminBundle\Entity\SizeChart;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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
       
        return $this->createForm(new SizeChartType('edit'), $entity);
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

