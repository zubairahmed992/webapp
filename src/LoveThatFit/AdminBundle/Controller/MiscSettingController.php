<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MiscSettingController extends Controller {

//---------------------------------------------------------------------

	public function indexAction(Request $request) {

		$baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
		$target_path = __DIR__.'/../../../../web/uploads/ltf/background/fitting_background.png';
		$image = '';

		if (file_exists($target_path)) {
			$image = $baseurl.'/uploads/ltf/background/fitting_background.png';
		}
		return $this->render('LoveThatFitAdminBundle:MiscSetting:index.html.twig', array('image' => $image));
    }

	public function updateAction(Request $request) {
		$file = $_FILES["background_file"];
		//Uploaded Achieved Retouch email.
		$temp_name = $file['tmp_name'];
		$target_path = __DIR__.'/../../../../web/uploads/ltf/background/';
		if(!is_dir($target_path)){
			mkdir($target_path, 0777, true);
		}

		$fileName = $file['name'];
		$saved_retouch = 'fitting_background';
		$ext = pathinfo($fileName, PATHINFO_EXTENSION);
		if($ext == 'png'){
			move_uploaded_file($temp_name, $target_path.'/'.$saved_retouch.".".$ext);
			$this->get('session')->setFlash('success', 'Background Image uploaded');
		}else{
			$this->get('session')->setFlash('warning', 'Image Must be in Png Format');
		}
		return $this->redirect($this->generateUrl('admin_misc_setting_index'));
	}

	public function removeAction(Request $request) {

		$target_path = __DIR__.'/../../../../web/uploads/ltf/background/fitting_background.png';
		if(unlink($target_path)){
			$this->get('session')->setFlash('success', 'Background Image successfully deleted');
		}else{
			$this->get('session')->setFlash('warning', 'Something wrong on server, kindly retry');
		}
		return $this->redirect($this->generateUrl('admin_misc_setting_index'));
	}
}
