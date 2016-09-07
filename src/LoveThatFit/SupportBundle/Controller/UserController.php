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

    //-------------------------Show user detail-------------------------------------------------------
    public function showAction($id) {
        $entity = $this->get('user.helper.user')->find($id);
        $log_count = $this->get('user.helper.userappaccesslog')->getAppAccessLogCount($entity);
        $user_limit = $this->get('user.helper.user')->getRecordsCountWithCurrentUserLimit($id);
        $page_number = ceil($this->get('admin.helper.utility')->getPageNumber($user_limit[0]['id']));
        $page_number=$page_number==0?1:$page_number;
        if(!$entity){
            $this->get('session')->setFlash('warning', 'User not found!');
        }
        if(!$entity->getOriginalUser()){
            $duplicate_user = '0';
            $duplicate_list = $entity->getDuplicateUsers();
            $duplicate_count = count($duplicate_list);
        }else{
            $duplicate_user = '1';
            $duplicate_list = $entity->getOriginalUser();
            $duplicate_count = 0;
        }
        return $this->render('LoveThatFitSupportBundle:User:show.html.twig', array(
            'user' => $entity,
            'duplicate_user' => $duplicate_user,
            'duplicate_list' => $duplicate_list,
            'duplicate_count' => $duplicate_count,
            'page_number' => $page_number,
            'log_count' => $log_count,
            'product'=>$this->get('site.helper.usertryitemhistory')->countUserTiredProducts($entity),
            'brand'=>$this->get('site.helper.usertryitemhistory')->findUserTiredBrands($entity),
            'brandtried'=>count($this->get('site.helper.usertryitemhistory')->findUserTiredBrands($entity)),
        ));
    }
    //-------------------------Show user detail-------------------------------------------------------

}
