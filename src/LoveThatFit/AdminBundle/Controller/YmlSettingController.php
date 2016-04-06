<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Dumper;
class YmlSettingController extends Controller {

//---------------------------------------------------------------------

    public function indexAction() {
	  #return new response(json_encode($this->get('admin.helper.product')->productDetailSizeArray($id)));
	  $yml_settings =  $this->get('admin.helper.camera_mask_specs')->getMaskSpecs();
	  return $this->render('LoveThatFitAdminBundle:YmlSetting:index.html.twig',array(
		"settings" => $yml_settings
	  ));
    }

	public function updateAction(Request $request) {
	  $decoded  = $request->request->all();
	  //print_r($decoded);
	  $val_type = $decoded["val_type"];
	  $mask_x = $decoded["mask_x_".$val_type];
	  $mask_y = $decoded["mask_y_".$val_type];
	  $pixel_per_inch = $decoded["pixel_per_inch_".$val_type];
	  $yml_settings =  $this->get('admin.helper.camera_mask_specs')->getMaskSpecs();
	  $yml_settings[$val_type]['mask_x'] = (int) $mask_x;
	  $yml_settings[$val_type]['mask_y'] = (int) $mask_y;
	  $yml_settings[$val_type]['pixel_per_inch'] = (int) $pixel_per_inch;
	  //print_r($yml_settings);die;
	  $yml_settings_new['mask_specs']=$yml_settings;
	  $dumper = new Dumper();
	  $yaml = $dumper->dump($yml_settings_new,3);
	  file_put_contents('../app/config/camera_mask_specs.yml', $yaml);
	  $this->get('session')->setFlash('success', 'Yml Settings Updated');
	  return $this->redirect($this->generateUrl('admin_yml_setting_index'));
	  #return new response(json_encode($this->get('admin.helper.product')->productDetailSizeArray($id)));
	}
}
