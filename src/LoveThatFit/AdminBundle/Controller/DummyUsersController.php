<?php

namespace LoveThatFit\AdminBundle\Controller;

use LoveThatFit\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DummyUsersController extends Controller
{

    #--------------------------->   /admin/dummy_users/index
    
    public function indexAction(){
        $users = $this->container->get('user.helper.user')->dummyUsersData();
      //  return new Response(json_encode(array_keys($users)));
        return $this->render('LoveThatFitAdminBundle:DummyUser:index_new.html.twig',
            array('users'   => array_keys($users),
            )
        );
    }
    #-------------------------------->>   /admin/dummy_users/copy/{user_id}/{dummy}
    
    public function copyDummyToUserAction(Request $request){
        $user = $this->container->get('user.helper.user')->copyDummyUserData($request->get('user_id'), $request->get('dummy_user'));
       if(empty($user)){
           $this->get('session')->setFlash('warning', 'Oops! user not found.');
       } else{
           $this->get('session')->setFlash('success', 'Successfully Dummy User data Copy');
       }
        $users = $this->container->get('user.helper.user')->dummyUsersData();
        return $this->render('LoveThatFitAdminBundle:DummyUser:index_new.html.twig',
            array('users'   => array_keys($users),
            )
        );
    }

}
