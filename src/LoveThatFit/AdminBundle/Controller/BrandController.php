<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\AdminBundle\Entity\BrandSpecification;
use LoveThatFit\AdminBundle\Form\Type\DeleteType;
use LoveThatFit\AdminBundle\Form\Type\BrandType;
use LoveThatFit\AdminBundle\Form\Type\BrandSpecificationType;

class BrandController extends Controller {

    //------------------------------------------------------------------------------------------
    public function indexAction($page_number, $sort = 'id') {
        $brands_with_pagination = $this->get('admin.helper.brand')->getListWithPagination($page_number, $sort);
        return $this->render('LoveThatFitAdminBundle:Brand:index.html.twig', $brands_with_pagination);
    }

//------------------------------------------------------------------------------------------

    public function productsAction($id) {
        $brand = $this->get('admin.helper.brand')->find($id);
        $products = $brand->getProducts();
        return new Response(var_dump($products));
    }

//------------------------------------------------------------------------------------------

    public function showAction($id) {

        $specs = $this->get('admin.helper.brand')->findWithSpecs($id);
        $entity = $specs['entity'];
        $brand_limit = $this->get('admin.helper.brand')->getRecordsCountWithCurrentBrandLimit($id);
        $page_number = ceil($this->get('admin.helper.utility')->getPageNumber($brand_limit[0]['id']));
        if ($page_number == 0) {
            $page_number = 1;
        }
        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
        }
        //return new response(json_encode($entity->getBrandSpecification()->getMaleFitType()));
        return $this->render('LoveThatFitAdminBundle:Brand:show.html.twig', array(
                    'brand' => $entity,
                    'page_number' => $page_number,
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
     if ($form->isValid()) {#!!!! set in separat methods
            $message_array = $this->get('admin.helper.brand')->save($entity);
            $msg=$this->get('push_notification_helper')->getNotificationType('brand_create');
           
            if($msg['status']=='true'){
            $this->get('push_notification_helper')->setNotificationInDB($msg['type'],$msg['message']);
            }
           
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
    
//---------------------------------------------Brand Specification add new----------------------------
  
 public function newBrandSpecificationAction($id)
 {
     $allSizes=$this->get('admin.helper.size')->getAllSizes();
     
    //return new response(json_encode($allSizes));
     $specs = $this->get('admin.helper.brand')->findWithSpecs($id);
     //return new response(json_encode($specs));
     $entity = $specs['entity'];       
     $brandspecification=new BrandSpecification();
     $form=$this->createForm(new BrandSpecificationType($allSizes,$this->get('admin.helper.size')),$brandspecification);
      return $this->render('LoveThatFitAdminBundle:Brand:new_brand_specification.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
 }
 
public function createBrandSpecificationAction($id,Request $request)
{
    $allSizes=$this->get('admin.helper.size')->getAllSizes();
    $entity = $this->get('admin.helper.brand')->find($id);     
    $brandspecification=new BrandSpecification();    
    $form=$this->createForm(new BrandSpecificationType($allSizes,$this->get('admin.helper.size')),$brandspecification);
    $form->bind($request);     
    $brandspecification->setBrand($entity);
    $data = $request->request->all();    
    
    $brandArray= $this->get('admin.helper.brand.specification')->brandDetailArray($data,$brandspecification);         
  
    $this->get('session')->setFlash($brandArray['message_type'], $brandArray['message']);
     return $this->redirect($this->generateUrl('admin_brand_show', array('id' => $entity->getId())));
}
 
public function showBrandSpecificationAction($id)
{
     $entity = $this->get('admin.helper.brand')->find($id);        
     return new response($entity->getBrandspecification());
     return $this->render('LoveThatFitAdminBundle:Brand:show_brand_specification.html.twig', array(
                    'brand' => $entity,                    
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
    public function editBrandSpecificationAction($id,$brand_id)
    {
     $entity = $this->get('admin.helper.brand')->find($brand_id);  
    // $brandspecification=$this->get('admin.helper.brand.specification')->find($id);
     $specArray=$this->get('admin.helper.brand.specification')->getArrayBrandSpecifcation($this->get('admin.helper.brand.specification')->find($id));
     
     $allSizes=$this->get('admin.helper.size')->getAllSizes();
     $form=$this->createForm(new BrandSpecificationType($allSizes,$this->get('admin.helper.size')));
     
     
        if (array_key_exists('gender', $specArray)) {
            $form->get('gender')->setData(json_decode($specArray['gender']));
        }
        if (array_key_exists('female_fit_type', $specArray)) {
            $form->get('female_fit_type')->setData(json_decode($specArray['female_fit_type']));
        }
        if (array_key_exists('male_fit_type', $specArray)) {
            $form->get('male_fit_type')->setData(json_decode($specArray['male_fit_type']));
        }
        if (array_key_exists('female_size_title_type', $specArray)) {
            $form->get('female_size_title_type')->setData(json_decode($specArray['female_size_title_type']));
        }
        if (array_key_exists('male_size_title_type', $specArray)) {
            $form->get('male_size_title_type')->setData(json_decode($specArray['male_size_title_type']));
        }
        if (array_key_exists('male_chest', $specArray)) {
            $form->get('male_chest')->setData(json_decode($specArray['male_chest']));
        }
        if (array_key_exists('male_letter', $specArray)) {
            $form->get('male_letter')->setData(json_decode($specArray['male_letter']));
        }
        if (array_key_exists('male_shirt', $specArray)) {
            $form->get('male_shirt')->setData(json_decode($specArray['male_shirt']));
        }
        if (array_key_exists('male_waist', $specArray)) {
            $form->get('male_waist')->setData(json_decode($specArray['male_waist']));
        }

        if (array_key_exists('male_neck', $specArray)) {
            $form->get('male_neck')->setData(json_decode($specArray['male_neck']));
        }
        if (array_key_exists('female_number', $specArray)) {
            $form->get('female_number')->setData(json_decode($specArray['female_number']));
        }
        if (array_key_exists('female_letter', $specArray)) {
            $form->get('female_letter')->setData(json_decode($specArray['female_letter']));
        }
        if (array_key_exists('female_waist', $specArray)) {
            $form->get('female_waist')->setData(json_decode($specArray['female_waist']));
        }
        if (array_key_exists('female_bra', $specArray)) {
            $form->get('female_bra')->setData(json_decode($specArray['female_bra']));
        }
        
     return $this->render('LoveThatFitAdminBundle:Brand:brand_specification_edit_detail.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'id'=>$id
        ));
    }
    
    public function updateBrandSpecificationAction(Request $request,$id,$brand_id)
    {
     $allSizes=$this->get('admin.helper.size')->getAllSizes();
     $entity = $this->get('admin.helper.brand')->find($brand_id);  
     $brandspecification=$this->get('admin.helper.brand.specification')->find($id);
     $form=$this->createForm(new BrandSpecificationType($allSizes,$this->get('admin.helper.size')));
     $form->bind($request);     
     $brandspecification->setBrand($entity);
     $data = $request->request->all();    
     $brandArray= $this->get('admin.helper.brand.specification')->brandSpscificationDetailArray($data,$brandspecification);         
     $this->get('session')->setFlash($brandArray['message_type'], $brandArray['message']);
     return $this->redirect($this->generateUrl('admin_brand_show', array('id' => $entity->getId())));
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
    
    
 //----------------------------------------------------------------------------------------------
    public function deleteBrandSpecificationAction($id)
    {
        try {

            $message_array = $this->get('admin.helper.brand.specification')->delete($id);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            return $this->redirect($this->generateUrl('admin_brands'));
        } catch (\Doctrine\DBAL\DBALException $e) {

            $this->get('session')->setFlash('warning', 'This Brand specification  cannot be deleted!');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }
    

}
