<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Form\Type\DeleteType;
use LoveThatFit\AdminBundle\Form\Type\SizeChartDynamicType;
use LoveThatFit\AdminBundle\Form\Type\BrandSizeChartType;
use Symfony\Component\HttpFoundation\Response;

class SizeChartController extends Controller {

//---------------------------------------------------------------------


    public function indexAction() {
        $form = $this->createForm(new BrandSizeChartType());
        return $this->render('LoveThatFitAdminBundle:SizeChart:index.html.twig', array('form' => $form->createView(),                   
                ));
    }
#------------------------------------------------------------
    public function getBrandSizeChartListAction($brand_id) {
        $brand = $this->get('admin.helper.brand')->find($brand_id);
        $sizechart = $this->get('admin.helper.sizechart')->getSizeChartByBrand($brand);
        if ($sizechart) {
            return $this->render('LoveThatFitAdminBundle:SizeChart:brand_sizechart_list.html.twig', array('sizechart' => $sizechart, 'brand' => $brand->getName()));
        } else {
            return new Response('Unable to find size chart');
        }
    }

#------------------------------------------------------------
    public function newAction() {
        $size_specs = $this->get('admin.helper.size')->getDefaultArray();
        $entity = $this->get('admin.helper.sizechart')->createNew();
        $form = $this->createForm(new SizeChartDynamicType($size_specs), $entity);
        return $this->render('LoveThatFitAdminBundle:SizeChart:new.html.twig', array('form' => $form->createView(),
                    'size_specs' => $size_specs,
                ));   
    }
    
#------------------------------------------------------------
    public function createAction(Request $request) {
        
        $data = $request->request->all();
        $new_size_chart = $this->get('admin.helper.sizechart')->fillInRequest($data['sizechart']);
        $size_specs = $this->get('admin.helper.size')->getDefaultArray();
        
        if ($new_size_chart->getTarget() == 'Dress' and $new_size_chart->getGender() == 'm') {
            $this->get('session')->setFlash('warning', 'Dresses can not be selected  for Male');
        } else {
            $message_array = $this->get('admin.helper.sizechart')->save($new_size_chart);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            if ($message_array['success']) {
                return $this->redirect($this->generateUrl('admin_size_charts'));
            }            
        }        
        $form = $this->createForm(new SizeChartDynamicType($size_specs), $new_size_chart);
        $form->bindRequest($request);

        return $this->render('LoveThatFitAdminBundle:SizeChart:new.html.twig', array(
                    'entity' => $new_size_chart,
                    'form' => $form->createView(),
                     'size_specs' => $size_specs,
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
    //------------------------------------------------------------------------------
   public function editAction($id) {
        $specs = $this->get('admin.helper.sizechart')->findWithSpecs($id);
        $entity = $specs['entity'];
        $size_specs = $this->get('admin.helper.size')->getDefaultArray();
        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
        }
        $form = $this->createForm(new SizeChartDynamicType($size_specs), $entity);
        return $this->render('LoveThatFitAdminBundle:SizeChart:edit.html.twig', array(
                    'form' => $form->createView(),
                    'entity' => $entity,
                    'size_specs' => $size_specs,
                ));
    }
   
    
#------------------------------------------------------------
    public function updateAction(Request $request, $id) {
       $specs = $this->get('admin.helper.sizechart')->findWithSpecs($id);
       $data = $request->request->all();
      
       $size_chart = $specs['entity'];
       $size_chart = $this->get('admin.helper.sizechart')->fillInRequest($data['sizechart'],$size_chart);
       $size_specs = $this->get('admin.helper.size')->getDefaultArray();
        if ($size_chart->getTarget() == 'Dress' and $size_chart->getGender() == 'm') {
            $this->get('session')->setFlash('warning', 'Dresses can not be selected  for Male');
        } else {
            $message_array = $this->get('admin.helper.sizechart')->save($size_chart);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            if ($message_array['success']) {
                return $this->redirect($this->generateUrl('admin_size_charts'));
            }            
        }
        
        $form = $this->createForm(new SizeChartDynamicType($size_specs), $size_chart);
        $form->bindRequest($request);
        $deleteForm = $this->createForm(new DeleteType(), $size_chart);
        return $this->render('LoveThatFitAdminBundle:SizeChart:new.html.twig', array(
                    'entity' => $size_chart,
                    'form' => $form->createView(),
                     'size_specs' => $size_specs,
                    'delete_form' => $deleteForm->createView(),
                ));
    }

}

