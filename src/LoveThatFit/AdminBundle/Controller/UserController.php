<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\AdminBundle\Form\Type\UserMeasurementType;
use LoveThatFit\AdminBundle\Form\Type\UserProfileSettingsType;
use LoveThatFit\AdminBundle\Form\Type\MannequinTestType;
use LoveThatFit\AdminBundle\Form\Type\RetailerSiteUserType;


class UserController extends Controller {

    //--------------------------User List-------------------------------------------------------------
    public function indexAction($page_number, $sort = 'id') {
        $size_with_pagination = $this->get('user.helper.user')->getListWithPagination($page_number, $sort);
        return $this->render('LoveThatFitAdminBundle:User:index.html.twig', array('pagination' => $size_with_pagination, 'searchform' => $this->userSearchFrom()->createView()));
    }

    //-------------------------Show user detail-------------------------------------------------------
    public function showAction($id) {
        $entity = $this->get('user.helper.user')->find($id);        
        $user_limit = $this->get('user.helper.user')->getRecordsCountWithCurrentUserLimit($id);
        $page_number = ceil($this->get('admin.helper.utility')->getPageNumber($user_limit[0]['id']));
        $page_number=$page_number==0?1:$page_number;        
        if(!$entity){        
            $this->get('session')->setFlash('warning', 'User not found!');
        }     
        return $this->render('LoveThatFitAdminBundle:User:show.html.twig', array(
                    'user' => $entity,
                    'page_number' => $page_number,
                    'product'=>$this->get('site.helper.usertryitemhistory')->countUserTiredProducts($entity),
                    'brand'=>$this->get('site.helper.usertryitemhistory')->findUserTiredBrands($entity),
                    'brandtried'=>count($this->get('site.helper.usertryitemhistory')->findUserTiredBrands($entity)),
                ));
    }

    //--------------------------User Serach------------------------------------------------------------
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $gender = $data['form']['gender'];
        $firstname = $data['form']['firstname'];
        $lastname = $data['form']['firstname'];
        if ($data['form']['age'] == '') {
            $age = '';
        } else {
            $age = $data['form']['age'];
            $endDate = $this->get('user.helper.user')->getUserBirthDate($age);
            $new_timestamp = strtotime('-12 months', strtotime($endDate));
            $beginDate = date("Y-m-d", $new_timestamp);
        }
        if ($firstname == '' and $gender == '') {
            $entity = $this->get('user.helper.user')->findByBirthDateRange($beginDate, $endDate);
        }
        if ($firstname == '' and $age == '') {
            $entity = $this->get('user.helper.user')->findByGender($gender);
        }
        if ($gender == '' and $age == '') {
            $entity = $this->get('user.helper.user')->findByName($firstname, $lastname);
        }
        if ($gender != '' and $firstname != '') {
            $entity = $this->get('user.helper.user')->findByGenderName($firstname, $lastname, $gender);
        }
        if ($gender != '' and $firstname != '' and $age != '') {
            $entity = $this->get('user.helper.user')->findByNameGenderBirthDateRange($firstname, $lastname, $gender, $beginDate, $endDate);
        }
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Unable to find User.');
            return $this->render('LoveThatFitAdminBundle:User:search.html.twig', array(
                        'user' => $entity,
                        'searchform' => $this->userSearchFrom()->createView(),
                    ));
        } else {
            return $this->render('LoveThatFitAdminBundle:User:search.html.twig', array(
                        'user' => $entity,
                        'searchform' => $this->userSearchFrom()->createView(),
                    ));
        }
    }

    //-------------------------------Edit User-----------------------------------------------------------
    public function editAction($id) {
        $entity = $this->get('user.helper.user')->find($id);
        $measurement = $entity->getMeasurement();
        $measurementForm = $this->createForm(new UserMeasurementType(), $measurement);
        $userForm=$this->createForm(new UserProfileSettingsType(), $entity);
        return $this->render('LoveThatFitAdminBundle:User:edit.html.twig', array(
                    'form' => $measurementForm->createView(),
                    'userform' => $userForm->createView(),
                    'measurement' => $measurement,
                    'entity' => $entity,
                ));
    }
    
    //--------------------------Update User----------------------------------------------------------------
    public function updateAction($id) {
        $entity = $this->get('user.helper.user')->find($id);
        $measurement = $entity->getMeasurement();
        $measurementForm = $this->createForm(new UserMeasurementType(), $measurement);
        $measurementForm->bind($this->getRequest());
        $measurement->setUpdatedAt(new \DateTime('now'));
        $this->get('user.helper.measurement')->saveMeasurement($measurement);
        $this->get('session')->setFlash('success', 'Updated Successfuly');
        $userForm=$this->createForm(new UserProfileSettingsType(), $entity);
        return $this->render('LoveThatFitAdminBundle:User:edit.html.twig', array(
                    'form' => $measurementForm->createView(),
                    'userform' => $userForm->createView(),
                    'measurement' => $measurement,
                    'entity' => $entity,
                ));
    }    
    
    //-----------------------------Delete User-----------------------------------------------------------
   public  function  deleteAction($id)
    {
          try {
            $message_array = $this->get('user.helper.user')->delete($id);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);

            return $this->redirect($this->generateUrl('admin_users'));
        } catch (\Doctrine\DBAL\DBALException $e) {

            $this->get('session')->setFlash('warning', 'This Size cannot be deleted!');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }
    
//----------------------User profile update-------------------------------------------------------------
    public function updateUserProfileAction($id)
    {
        $entity = $this->get('user.helper.user')->find($id);
        $measurement = $entity->getMeasurement();
        $measurementForm = $this->createForm(new UserMeasurementType(), $measurement);
        $userForm=$this->createForm(new UserProfileSettingsType(), $entity);
        $userForm->bind($this->getRequest());       
        $this->get('user.helper.user')->saveUser($entity);
        $this->get('session')->setFlash('success', 'Updated Successfuly');
        return $this->render('LoveThatFitAdminBundle:User:edit.html.twig', array(
                    'form' => $measurementForm->createView(),
                    'userform' => $userForm->createView(),
                    'measurement' => $measurement,
                    'entity' => $entity,
                ));
    }
    
    //-------------------------------------Compare User Form--------------------------------------------
    public function comapareUserAction()
    {
        $form=$this->createForm(new MannequinTestType());
        return $this->render('LoveThatFitAdminBundle:User:compare.html.twig', array(
                    'form' => $form->createView(),                   
                ));
        
    }
    //---------------------Compare User Mannequin-------------------------------------------------------
    
    public function comapareUserSizeAction(Request $request)
    {        
        $form=$this->createForm(new MannequinTestType());                
        $data = $request->request->all();
        $email = $data['user']['User'];
        if (strlen($email)==0){
            $form=$this->createForm(new MannequinTestType());
            return $this->render('LoveThatFitAdminBundle:User:compare.html.twig', array(
                    'form' => $form->createView(),                   
                ));
        }
        $entity = $this->get('user.helper.user')->find($email);       
        $manequin_size=$this->get('admin.helper.user.mannequin')->userMannequin($entity);        
        return new Response(json_encode($manequin_size));
        return $this->render('LoveThatFitAdminBundle:User:compare.html.twig', array(
                    'form' => $form->createView(),                   
         ));
    }
    
  //-----------------------Add New Retailer Site user---------------------------------------------------  
   public function newRetailerSiteUserAction($id)
   {
       $entity = $this->get('user.helper.user')->find($id);
       $RetailerSiteUser = $this->get('admin.helper.retailer.site.user')->createNew();
       $RetailerSiteUserForm=$this->createForm(new RetailerSiteUserType('add'), $RetailerSiteUser);
       return $this->render('LoveThatFitAdminBundle:User:new_retailer_site_user.html.twig', array(
                    'user' => $entity,                   
                    'form' => $RetailerSiteUserForm->createView(),
                ));

   }
  
   
   //-----------------------Create New Retailer Site user---------------------------------------------------
   public function createRetailerSiteUserAction(Request $request,$id)
   {        
       $data = $request->request->all();    
       $retailerId=$data['retailer_site_user']['Retailer'];
       $user_reference_id=$data['retailer_site_user']['user_reference_id'];
       $retailer=$this->get('admin.helper.retailer')->find($retailerId);         
       $user= $this->get('user.helper.user')->find($id);
       $this->get('admin.helper.retailer.site.user')->addNew($user, $user_reference_id, $retailer);
        return $this->redirect($this->generateUrl('admin_user_detail_show', array('id' => $user->getId())));
   }
   
   //-------------------------------Edit Retailer Site Users--------------------------------------------
   public function editRetailerSiteUserAction($user_id,$id)
   {
       $entity=$this->get('admin.helper.retailer.site.user')->find($id);
       if (!$entity) {
            throw $this->createNotFoundException('Unable to find Retailer Site User.');
        }
        $user=$this->get('user.helper.user')->find($user_id);
        $form = $this->createForm(new RetailerSiteUserType('edit'), $entity);
        return $this->render('LoveThatFitAdminBundle:User:edit_retailer_site_user.html.twig', array(
                    'user' => $user,                   
                    'form' => $form->createView(),
                    'entity'=>$entity, 
                ));
   }
   //-------------------------------Update Retailer Site Users--------------------------------------------
   public function updateRetailerSiteUserAction(Request $request,$user_id,$id)
   {
       $data = $request->request->all();    
       $retailerId=$data['retailer_site_user']['Retailer'];
       $user_reference_id=$data['retailer_site_user']['user_reference_id'];
       $retailer=$this->get('admin.helper.retailer')->find($retailerId);       
       $entity= $this->get('user.helper.user')->find($user_id);
       $retailerSiteUser=$this->get('admin.helper.retailer.site.user')->find($id);
       $this->get('admin.helper.retailer.site.user')->update($retailerSiteUser,$retailer,$entity,$user_reference_id);
        return $this->redirect($this->generateUrl('admin_user_detail_show', array('id' => $entity->getId())));
   }
   
   //----------------------------Delete Retailer Site Users---------------------------------------------
    public function deleteRetailerSiteUserAction($id)
    {
        try {
            $message_array = $this->get('admin.helper.retailer.site.user')->delete($id);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);

            return $this->redirect($this->generateUrl('admin_users'));
        } catch (\Doctrine\DBAL\DBALException $e) {

            $this->get('session')->setFlash('warning', 'This Site user cannot be deleted!');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }
    
    
    
    //-------------------------------User Search Form-----------------------------------------------------------

    private function userSearchFrom() {
        $user = new User();
        $gender = array('' => 'Select Gender', 'm' => 'Male', 'f' => 'Female');
        $age = array('' => 'Select Age', '15' => 15, '16' => 16, '17' => 17, '18' => 18, '19' => 19, '20' => 20, '21' => 21, '22' => 22, '23' => 23, '24' => 24, '25' => 25, '26' => 26, '27' => 27, '28' => 28, '29' => 29, '30' => 30, '31' => 31, '32' => 32, '33' => 33, '34' => 34, '35' => 35, '36' => 36, '37' => 37, '38' => 38, '39' => 39, '40' => 40, '41' => 41, '42' => 42, '43' => 43, '44' => 44, '45' => 45, '46' => 46, '47' => 47, '48' => 48, '49' => 49, '50' => 50,);
        return $this->createFormBuilder($user)
                        ->add('firstname', 'text', array('required' => false))
                        ->add('gender', 'choice', array('choices' => $gender,
                            'multiple' => False,
                            'expanded' => False,
                            'required' => false
                        ))
                        ->add('age', 'choice', array('choices' => $age,
                            'multiple' => False,
                            'expanded' => False,
                            'required' => false
                        ))
                        ->getForm();
    }
}
