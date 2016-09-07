<?php

namespace LoveThatFit\SupportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\UserBundle\Entity\User;
class DuplicateUserController extends Controller {

//---------------------------------------------------------------------

    public function indexAction($id) {
	  $entity = $this->get('user.helper.user')->find($id);
	  $user_limit = $this->get('user.helper.user')->getRecordsCountWithCurrentUserLimit($id);
	  $page_number = ceil($this->get('admin.helper.utility')->getPageNumber($user_limit[0]['id']));
	  $page_number=$page_number==0?1:$page_number;
	  if(!$entity){
		$this->get('session')->setFlash('warning', 'User not found!');
	  }
	  return $this->render('LoveThatFitSupportBundle:DuplicateUser:_duplicate.html.twig', array(
		'user' => $entity,
		'page_number' => $page_number,
		'product'=>$this->get('site.helper.usertryitemhistory')->countUserTiredProducts($entity),
		'brand'=>$this->get('site.helper.usertryitemhistory')->findUserTiredBrands($entity),
		'brandtried'=>count($this->get('site.helper.usertryitemhistory')->findUserTiredBrands($entity)),
	  ));
    }

    public function updateUserAction(Request $request) {
	  $data = $request->request->all();
	  $user = $this->get('user.helper.user')->find($data["user_id"]);
	  $response = $this->get('user.helper.user')->duplicateUser($user,$data);
	  if($response == 'Email already exist'){
		$this->get('session')->setFlash('warning', 'User already exists with this email address');
		return $this->redirect($this->generateUrl('support_duplicate_user_index', array("id" => $data["user_id"])));
	  }else{
		$this->get('session')->setFlash('success', 'Duplicate User Created');
		return $this->redirect(
		  $this->generateUrl("support_user_detail_show", array("id" => $response))
		);
	  }
	}
}
