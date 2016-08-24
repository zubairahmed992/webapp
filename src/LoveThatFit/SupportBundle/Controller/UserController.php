<?php

namespace LoveThatFit\SupportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\UserBundle\Entity\User;

class UserController extends Controller {

    public function indexAction()
    {
    	$totalRecords = $this->get('user.helper.user')->countAllUserRecord();
    	$femaleUsers  = $this->get('user.helper.user')->countByGender('f');
    	$maleUsers    = $this->get('user.helper.user')->countByGender('m');
    	
		return $this->render('LoveThatFitSupportBundle:User:index.html.twig',
                array('rec_count' => count($totalRecords),
                    'femaleUsers' => $femaleUsers,
                    'maleUsers'   => $maleUsers
                    )
        );
    }

    public function paginateAction(Request $request)
    {
        $requestData = $this->get('request')->request->all();
        $output = $this->get('user.helper.user')->search($requestData);
        
        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']); 
    }

    public function pendingUsersAction()
    {
        $totalRecords = $this->get('user.helper.userarchives')->countAllRecord();

        return $this->render('LoveThatFitSupportBundle:PendingUser:index.html.twig',
                array('rec_count' => $totalRecords)
            );
    }

    public function pendingUsersPaginateAction(Request $request)
    {
        $requestData = $this->get('request')->request->all();
        $output = $this->get('user.helper.userarchives')->search($requestData);
        
        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']); 
    }
}
