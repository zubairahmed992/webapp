<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\UserBundle\Entity\User;

class UserController extends Controller {

    //------------------------------------------------------------------------------------------
    public function indexAction($page_number=1 , $sort='id') {
       
        $limit = 5;
        $usersObj = $this->getDoctrine()->getRepository('LoveThatFitUserBundle:User');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitUserBundle:User')
                 ->findAllUsers($page_number, $limit, $sort);
		$rec_count = count($usersObj->countAllUserRecord());
		$cur_page = $page_number;
        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }
        return $this->render('LoveThatFitAdminBundle:User:index.html.twig',
		       array(	
                           'users'=>$entity,
			   'rec_count' => $rec_count, 
                           'no_of_pagination' => $no_of_paginations, 
                           'limit' => $cur_page, 
                           'per_page_limit' => $limit,
                           'searchform'=>$this->userSearchFrom()->createView(),
                           'femaleUsers'=>$this->getUserByGender('f'),
                           'maleUsers'=>$this->getUserByGender('m'),
			));
    }
    
    public function showAction($id)
    {
       $em = $this->getDoctrine()->getManager();
       $entity = $this->getUsersListById($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find user.');        }
        else{
        return $this->render('LoveThatFitAdminBundle:User:show.html.twig', array(
                    'user' => $entity                
                ));
        }
    }
    
    public function searchAction(Request $request)
    {
       $em = $this->getDoctrine()->getManager();
       $data = $request->request->all();
       $gender = $data['form']['gender'];
       $firstname = $data['form']['firstname'];
       $lastname = $data['form']['firstname'];
       if($data['form']['age']=='')
       {
         $age='';         
       } else
       {
         $age=$data['form']['age'];
         $endDate=$this->getUserBirthDate($age);
         $new_timestamp = strtotime('-12 months',strtotime($endDate));
         $beginDate=date("Y-m-d",$new_timestamp);
       }
       if($firstname=='' and $gender=='')
       {
         $entity=$this->getUserByAge($beginDate,$endDate);
       }
       if($firstname=='' and $age=='')
       {
       $entity = $this->getUserSearchListByGender($gender);      
       }
       if($gender=='' and $age=='')
       {
           $entity = $this->getUserSearchListByName($firstname,$lastname);
       }
       if($gender!='' and $firstname!='')
       {
           $entity = $this->getUserSearchList($firstname,$lastname,$gender);
       }
       if($gender!='' and $firstname!='' and $age!='')
       {
           $entity = $this->getUserSearchLists($firstname,$lastname,$gender,$beginDate,$endDate);
       }
       if (!$entity) {
           $this->get('session')->setFlash('warning','Unable to find User.');
            return $this->render('LoveThatFitAdminBundle:User:search.html.twig', array(
                    'user' =>$entity,
                    'searchform'=>$this->userSearchFrom()->createView(),                    
                ));
            }
        else{
        return $this->render('LoveThatFitAdminBundle:User:search.html.twig', array(
                    'user' =>$entity,
                    'searchform'=>$this->userSearchFrom()->createView(),                    
                ));
        }
    }
    
//------------------------------------------------------------------------------------------
    
    private function getUsersListById($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository('LoveThatFitUserBundle:User');
        $entity = $repository->find($id);
        return $entity;
    }
    private function getUserSearchListByGender($gender)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitUserBundle:User')
                 ->findUserSearchListByGender($gender);
        return $entity;
    }

 private function getUserBirthDate($age)
    {
               $agedate = new \DateTime();
               $agedate->sub(new \DateInterval("P" .$age. "Y"));
               return $agedate->format("Y-m-d");
    }
    
    private function getUserSearchListByName($firstname,$lastname)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitUserBundle:User')
                 ->findUserSearchListByName($firstname,$lastname);
        return $entity;
    }
    
    private function getUserSearchList($firstname,$lastname,$gender)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitUserBundle:User')
                 ->findUserSearchListBy($firstname,$lastname,$gender);
        return $entity;
    }
    
    private function getUserSearchLists($firstname,$lastname,$gender,$beginDate,$endDate)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitUserBundle:User')
                 ->findUserSearchListsBy($firstname,$lastname,$gender,$beginDate,$endDate);
        return $entity;
    }
    
    private function getUserByAge($beginDate,$endDate)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitUserBundle:User')
                 ->findUserByAge($beginDate,$endDate);
        return $entity;
    }
    
   
    private function userSearchFrom()
    {
        $user=new User();
        $gender=array(''=>'Select Gender','m'=>'Male','f'=>'Female'); 
        $age=array(''=>'Select Age','15'=>15,'16'=>16,'17'=>17,'18'=>18,'19'=>19,'20'=>20,'21'=>21,'22'=>22,'23'=>23,'24'=>24,'25'=>25,'26'=>26,'27'=>27,'28'=>28,'29'=>29,'30'=>30,'31'=>31,'32'=>32,'33'=>33,'34'=>34,'35'=>35,'36'=>36,'37'=>37,'38'=>38,'39'=>39,'40'=>40,'41'=>41,'42'=>42,'43'=>43,'44'=>44,'45'=>45,'46'=>46,'47'=>47,'48'=>48,'49'=>49,'50'=>50,);
        return $this->createFormBuilder($user)
                ->add('firstname', 'text',array('required'  => false))        
                ->add('gender','choice', 
                        array('choices'=>$gender,
                       'multiple'  =>False,
                       'expanded'  => False, 
                       'required'  => false
                ))
                ->add('age','choice', 
                        array('choices'=>$age,
                       'multiple'  =>False,
                       'expanded'  => False, 
                       'required'  => false
                ))
                
                        ->getForm();
    }
    
    private function getUserByGender($gender)
    {
        $em = $this->getDoctrine()->getManager();
        $UserTypeObj = $this->getDoctrine()->getRepository('LoveThatFitUserBundle:User');
         $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitUserBundle:User')
                 ->findUserByGender($gender);
		$rec_count = count($UserTypeObj->findUserByGender($gender));
        return $rec_count;
    }
    
}
