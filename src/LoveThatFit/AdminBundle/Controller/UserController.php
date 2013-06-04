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
//------------------------------------------------------------------------------------------
    
    private function getUsersListById($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository('LoveThatFitUserBundle:User');
        $entity = $repository->find($id);
        return $entity;
    }
    
    
}
