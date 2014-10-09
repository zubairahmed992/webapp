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
     $brandspecification=$this->get('admin.helper.brand.specification')->find($id);
      $gender=json_encode(json_decode($brandspecification->getGender()));
     $female_fit_type=json_encode(json_decode($brandspecification->getFemaleFitType()));
     $male_fit_type=json_encode(json_decode($brandspecification->getMaleFitType()));
     $female_size_title_type=json_encode(json_decode($brandspecification->getFemaleSizeTitleType()));
     $male_size_title_type=json_encode(json_decode($brandspecification->getMaleSizeTitleType()));
     $male_chest=json_encode(json_decode($brandspecification->getMaleChest()));
     $male_shirt=json_encode(json_decode($brandspecification->getMaleShirt()));
     $male_letter=json_encode(json_decode($brandspecification->getMaleLetter()));
     $male_waist=json_encode(json_decode($brandspecification->getMaleWaist()));
     $male_neck=json_encode(json_decode($brandspecification->getMaleNeck()));
     
     $female_number=json_encode(json_decode($brandspecification->getFemaleNumber()));
     $female_letter=json_encode(json_decode($brandspecification->getFemaleLetter()));
     $female_waist=json_encode(json_decode($brandspecification->getFemaleWaist()));    
     $female_bra=json_encode(json_decode($brandspecification->getFemaleBra()));    
    // $form=$this->createForm(new BrandSpecificationType($gender,$fit_type,$size_title_type,$size_title_type,$male_numbers,$male_letters,$male_waists,$female_numbers,$female_letters,$female_waists));

     $allSizes=$this->get('admin.helper.size')->getAllSizes();
     $form=$this->createForm(new BrandSpecificationType($allSizes,$this->get('admin.helper.size')));
     if (isset($gender)) {
            $form->get('gender')->setData(json_decode($gender));
        } 
     if (isset($female_fit_type)) {
            $form->get('female_fit_type')->setData(json_decode($female_fit_type));
        }
    if (isset($male_fit_type)) {
            $form->get('male_fit_type')->setData(json_decode($male_fit_type));
        }    
     if (isset($female_size_title_type)) {
            $form->get('female_size_title_type')->setData(json_decode($female_size_title_type));
        }
     if (isset($male_size_title_type)) {
            $form->get('male_size_title_type')->setData(json_decode($male_size_title_type));
        }   
     if (isset($male_chest)) {
            $form->get('male_chest')->setData(json_decode($male_chest));
        }
        
     if (isset($male_letter)) {
            $form->get('male_letter')->setData(json_decode($male_letter));
        }
     if (isset($male_shirt)) {
            $form->get('male_shirt')->setData(json_decode($male_shirt));
        }   
     if (isset($male_waist)) {
            $form->get('male_waist')->setData(json_decode($male_waist));
        }
    if (isset($male_neck)) {
            $form->get('male_neck')->setData(json_decode($male_neck));
        }    
     if (isset($female_number)) {
            $form->get('female_number')->setData(json_decode($female_number));
        }
     if (isset($female_letter)) {
            $form->get('female_letter')->setData(json_decode($female_letter));
        }        
     if (isset($female_waist)) {
            $form->get('female_waist')->setData(json_decode($female_waist));
        }
    if (isset($female_bra)) {
            $form->get('female_bra')->setData(json_decode($female_bra));
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
