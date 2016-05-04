<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\UserBundle\Entity\User;
class CopyUserController extends Controller {

//---------------------------------------------------------------------

    public function indexAction($id) {
	  $entity = $this->get('user.helper.user')->find($id);
	  //$log_count = $this->get('user.helper.userappaccesslog')->getAppAccessLogCount($entity);
	  $user_limit = $this->get('user.helper.user')->getRecordsCountWithCurrentUserLimit($id);
	  $page_number = ceil($this->get('admin.helper.utility')->getPageNumber($user_limit[0]['id']));
	  $page_number=$page_number==0?1:$page_number;
	  if(!$entity){
		$this->get('session')->setFlash('warning', 'User not found!');
	  }
	  return $this->render('LoveThatFitAdminBundle:User:copy_user.html.twig', array(
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

	  //die;
	  //$user_devices = $this->get('user.helper.userdevices')->findAllDeviceTypeBaseOnUserId($user);
	 //print_r($user_devices);

	  //die;
	  //$user->getUserImageSpec();
	  //$image_spec = $this->get('user.helper.userimagespec')->findByUser($user);
	  //print_r($image_spec[0]);die;
	  //die;
	  //echo $image_spec[0]["id"];die;
	  //$image_spec_array = $this->get('user.helper.userimagespec')->getArray($image_spec);
	  //print_r($image_spec_array);die;
	  //$image_spec = $this->get('user.marker.helper')->createNew();
	  //$marker->setSvgPaths($maskMarker->getSvgPaths());
	  //print_r($request_array);die;
	  //$this->get('user.helper.userimagespec')->findDeviceTypeBaseOnUserId($user);

	  $this->get('user.helper.user')->duplicateUser($user,$data);
	  $this->get('session')->setFlash('success', 'Duplicate User Created');
	  return $this->redirect($this->generateUrl('admin_users'));
	  //print_r($measurement);die;
	  //echo "<pre>";
	  //print_r($user);die;

	}
}
