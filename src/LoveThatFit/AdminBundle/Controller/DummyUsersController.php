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
        return new Response(json_encode(array_keys($users)));
        return $this->render('LoveThatFitAdminBundle:DummyUser:index.html.twig',
            array('users'   => array_keys($users),
            )
        );
    }
    #-------------------------------->>   /admin/dummy_users/copy/{user_id}/{dummy}
    
    public function copyDummyToUserAction($user_id, $dummy){
        $this->container->get('user.helper.user')->copyDummyUserData($user_id, $dummy);
        return new Response('copied');        
        $users = $this->container->get('user.helper.user')->dummyUsersData();
        return new Response(json_encode(array_keys($users[$dummy])));        
        return $this->render('LoveThatFitAdminBundle:DummyUser:index.html.twig',
            array('users'   => array_keys($users),
            )
        );
    }

}
