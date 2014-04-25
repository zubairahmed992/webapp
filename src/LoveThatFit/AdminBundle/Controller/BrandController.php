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
     $specs = $this->get('admin.helper.brand')->findWithSpecs($id);
     $entity = $specs['entity'];       
     $brandspecification=new BrandSpecification();
     $form=$this->createForm(new BrandSpecificationType($allSizes),$brandspecification);
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
    $form=$this->createForm(new BrandSpecificationType($allSizes),$brandspecification);
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
   /* $gender=json_encode(json_decode($brandspecification->getGender()));
     $fit_type=json_encode(json_decode($brandspecification->getFitType()));
     $size_title_type=json_encode(json_decode($brandspecification->getSizeTitleType()));
     $male_numbers=json_encode(json_decode($brandspecification->getMaleNumbers()));
     $male_letters=json_encode(json_decode($brandspecification->getMaleLetters()));
     $male_waists=json_encode(json_decode($brandspecification->getMaleWaists()));
     $female_numbers=json_encode(json_decode($brandspecification->getFemaleNumbers()));
     $female_letters=json_encode(json_decode($brandspecification->getFemaleLetters()));
     $female_waists=json_encode(json_decode($brandspecification->getFemaleWaists()));    
     $form=$this->createForm(new BrandSpecificationType($gender,$fit_type,$size_title_type,$size_title_type,$male_numbers,$male_letters,$male_waists,$female_numbers,$female_letters,$female_waists));*/
     $allSizes=$this->get('admin.helper.size')->getAllSizes();
     $form=$this->createForm(new BrandSpecificationType($allSizes));
     if (isset($gender)) {
            $form->get('gender')->setData(json_decode($gender));
        } 
     if (isset($fit_type)) {
            $form->get('fit_type')->setData(json_decode($fit_type));
        }
     if (isset($size_title_type)) {
            $form->get('size_title_type')->setData(json_decode($size_title_type));
        }
     if (isset($male_numbers)) {
            $form->get('male_numbers')->setData(json_decode($male_numbers));
        }
     if (isset($male_letters)) {
            $form->get('male_letters')->setData(json_decode($male_letters));
        }
     if (isset($male_waists)) {
            $form->get('male_waists')->setData(json_decode($male_waists));
        }
     if (isset($female_numbers)) {
            $form->get('female_numbers')->setData(json_decode($female_numbers));
        }
     if (isset($female_letters)) {
            $form->get('female_letters')->setData(json_decode($female_letters));
        }        
     if (isset($female_waists)) {
            $form->get('female_waists')->setData(json_decode($female_waists));
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
     /*$gender=json_encode(json_decode($brandspecification->getGender()));
     $fit_type=json_encode(json_decode($brandspecification->getFitType()));
     $size_title_type=json_encode(json_decode($brandspecification->getSizeTitleType()));
     $male_numbers=json_encode(json_decode($brandspecification->getMaleNumbers()));
     $male_letters=json_encode(json_decode($brandspecification->getMaleLetters()));
     $male_waists=json_encode(json_decode($brandspecification->getMaleWaists()));
     $female_numbers=json_encode(json_decode($brandspecification->getFemaleNumbers()));
     $female_letters=json_encode(json_decode($brandspecification->getFemaleLetters()));
     $female_waists=json_encode(json_decode($brandspecification->getFemaleWaists())); 
     $form=$this->createForm(new BrandSpecificationType($gender,$fit_type,$size_title_type,$size_title_type,$male_numbers,$male_letters,$male_waists,$female_numbers,$female_letters,$female_waists));*/
     $form=$this->createForm(new BrandSpecificationType($allSizes));
     
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
