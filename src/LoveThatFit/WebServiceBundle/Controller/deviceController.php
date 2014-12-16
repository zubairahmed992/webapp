<?php

namespace LoveThatFit\WebServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class DeviceController extends Controller {

    public function ConstantsAction() {
        #$data=$this->get('admin.helper.size')->getByGender();
        $data = $this->get('admin.helper.size')->getDefaultArray();
        $data['fittingStatus'] = $this->get('webservice.helper.product')->getFittingStatus();
        $data['resolution_scale'] = $this->get('admin.helper.utility')->getDeviceResolutionSpecs();
        return new response(json_encode(($data)));
    }

#-------------------------------------------------------------------------

    public function deviceImageUploadAction() {
        #check all the parameters provided??
        
         $email = $_POST['email'];
        $device_type = $_POST['deviceType'];
        $heightPerInch = $_POST['heightPerInch'];

        $user = $email != null ? $this->get('user.helper.user')->findByEmail($email) : null;

        if (!$user) {
            return new response(json_encode(array('Message' => 'Email Not Found')));
        }

        $user_device = $this->get('user.helper.userdevices')->findOneByDeviceTypeAndUser($user->getId(), $device_type);
        if (!$user_device) {
            $user_device = $this->get('user.helper.userdevices')->createNew($user);
            $user_device->setDeviceType($device_type);
        }
        $user_device->file = $_FILES["file"];

        if ($heightPerInch) {
            $user_device->setDeviceUserPerInchPixelHeight($heightPerInch);
        }

        $user_device->upload();
        $this->get('user.helper.userdevices')->saveUserDevices($user_device);
        $userinfo = array();
        $request = $this->getRequest();
        $image_path = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() .'/'. $user_device->getWebPath();
        $userinfo['heightPerInch'] = $user_device->getDeviceUserPerInchPixelHeight();
        $userinfo['iphoneImage'] = $user_device->getDeviceImage();
        $userinfo['path'] = $image_path;
        
        return new Response(json_encode($userinfo));
    }

}

// End of Class