<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Form\Type\DeleteType;
use LoveThatFit\AdminBundle\Form\Type\ClothingTypes;
use Symfony\Component\HttpFoundation\Response;

class ClothingTypeController extends Controller {

//-----------------------------Clothing Type List-------------------------------------------------------------

    public function indexAction($page_number, $sort = 'id') {

        $clothing_types = $this->get('admin.helper.clothingtype')->findAll();
        return $this->render('LoveThatFitAdminBundle:ClothingType:index.html.twig', array('clothing_types' => $clothing_types));
    }

//-------------------------------Clothing Type display-----------------------------------------------------------
    
    public function showAction($id) {
        $entity = $this->get('admin.helper.clothingtype')->find($id);
        $clothing_type_limit = $this->get('admin.helper.clothingtype')->getRecordsCountWithCurrentClothingTYpeLimit($id);
        $page_number = ceil($this->get('admin.helper.utility')->getPageNumber($clothing_type_limit[0]['id']));
        $page_number = $page_number == 0?1:$page_number;
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Clothing type not found!');
        }
        return $this->render('LoveThatFitAdminBundle:ClothingType:show.html.twig', array(
                    'clothing_type' => $entity,
                    'page_number' => $page_number,
        ));
    }

    //------------------------------Create New Clothing Type------------------------------------------------------------
    public function newAction() {

        $entity = $this->get('admin.helper.ClothingType')->createNew();
        $form = $this->createForm(new ClothingTypes('add',$entity), $entity);
        return $this->render('LoveThatFitAdminBundle:ClothingType:new.html.twig', array(
                    'form' => $form->createView()));
    }

    //-------------------------------Save Clothing type in database-----------------------------------------------------------
    public function createAction(Request $request) {
        $entity = $this->get('admin.helper.ClothingType')->createNew();
        $form = $this->createForm(new ClothingTypes('add',$entity), $entity);
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

//----------------------------------------Edit Clothing Type--------------------------------------------------
    public function editAction($id) {

        $entity = $this->get('admin.helper.ClothingType')->find($id);      
        if(!$entity){       
        $this->get('session')->setFlash('warning', 'The ClothingType can not be Created!');
        }else{
        $form = $this->createForm(new ClothingTypes('edit',$entity), $entity);
            //echo $entity->getTarget();die;
        $form->get('target')->setData($entity->getTarget());
        $deleteForm = $this->createForm(new DeleteType(), $entity);
        }
        return $this->render('LoveThatFitAdminBundle:ClothingType:edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity));
    }

//------------------------------------Update Clothing Type------------------------------------------------------
    public function updateAction(Request $request, $id) {

        $entity = $this->get('admin.helper.ClothingType')->find($id);
        if(!$entity)
        {
            $this->get('session')->setFlash('warning', 'The ClothingType not found!');
        }else{
        $form = $this->createForm(new ClothingTypes('edit',$entity), $entity);
        $form->get('target')->setData($entity->getTarget());
        $form->bind($request);

        if ($form->isValid()) {

            $message_array = $this->get('admin.helper.ClothingType')->update($entity);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            if ($message_array['success'] == true) {              
                return $this->redirect($this->generateUrl('admin_clothing_types'));
            }
        } else {
            $this->get('session')->setFlash('warning', 'Unable to update Clothing Type!');
        }
        $deleteForm = $this->createForm(new DeleteType(), $entity);        
        }
        return $this->render('LoveThatFitAdminBundle:ClothingType:edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity));
    }

    //----------------------------------------Delete Clothing Type--------------------------------------------------

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
    public function standardsAction(){
       $standards = $this->get('admin.helper.size')->getDefaultArray();       
       return $this->render('LoveThatFitAdminBundle:ClothingType:standards.html.twig', array(
                    'specs' => $standards,
                    ));
       
       return new \Symfony\Component\HttpFoundation\Response (json_encode($standards));
    }

    public function sendNotificationsAction()
    {
        //$device_token = "5ae55eedf9a0f50135fff762a1753ca54840b9c873dc673a5d3990e0ba67d39e";//kamran
        $decoded = $this->get('user.helper.user')->findAllUsersAuthDeviceToken();
        foreach ($decoded as $user) {
            $tokens = json_decode($user['device_tokens'], true);
            if (!empty($tokens['iphone'][0])) {
                $device_token =  $tokens['iphone'][0];
                if ($device_token != "") {
                    $push_response = $this->get('pushnotification.helper')
                        ->sendNotifyClothingType($device_token);
                }
            }
        }
        return new Response("true");
    }
}