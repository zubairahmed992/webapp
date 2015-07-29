<?php
namespace LoveThatFit\WebServiceBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class WSUserController extends Controller {

     
    
    private function process_request(){
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
        
        if($decoded==null) #if null (to be used for web service testing))
            $decoded  = $request->request->all();
        
        return $decoded;
    }
#~~~~~~~~~~~~~~~~~~~ ws_user_Login   /ws/login

    public function loginAction() {
        $decoded  = $this->process_request();                         
        $user_info = $this->get('webservice.helper')->loginService($decoded);        
        
        return new Response($user_info);
    }

#~~~~~~~~~~~~~~~~~~~ ws_email_exists   /ws/email_exists

    public function emailExistsAction() {
        $decoded  = $this->process_request();                         
        $exists = $this->get('webservice.helper')->emailExists($decoded['email']);
        return new Response($exists?'true':'false');
    }
    
#~~~~~~~~~~~~~~~~~~~ ws_user_registeration   /ws/user_registeration

    public function registrationAction() {
        $decoded  = $this->process_request();
        $json_data = $this->get('webservice.helper')->registrationService($decoded);
        return new Response($json_data);      
    }    
#~~~~~~~~~~~~~~~~~~~ ws_size_charts   /ws/size_charts
    public function sizeChartsAction(){
       $decoded  = $this->process_request();
       $json_data=$this->get('webservice.helper')->sizeChartsService($decoded);
        return new response($json_data);
       
   } 
#~~~~~~~~~~~~~~~~~~~ ws_image_uploader   /ws/image_uploader
    public function imageUploaderAction() {
        $decoded = $this->process_request();
        $request = $this->getRequest();
        $decoded['base_path'] = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/users/';
        return new Response($this->get('webservice.helper')->uploadUserImage($decoded, $_FILES));
    }

    public function __imageUploaderAction() {
        $decoded = $this->process_request();
        $user = $this->container->get('user.helper.user')->findByEmail($decoded['email']);

        if ($user) {
            $file_name = $_FILES["image"]["name"];
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $newFilename = 'original' . "." . $ext;
            $user->setImage($newFilename);
            if (!is_dir($user->getUploadRootDir())) {
                @mkdir($user->getUploadRootDir(), 0700);
            }
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $user->getAbsoluteIphonePath())) {
                $this->get('webservice.helper.user')->setMarkingDeviceType($user, $decoded['device_type'], $decoded['pixel_per_inch']);

                $em->persist($user);
                $em->flush();
                //  $image_path = $entity->getWebPath(); 
                $userinfo = array();
                $userimage = $user->getIphoneImage();
                $request = $this->getRequest();
                $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/users/' . $user->getId() . "/";
                $userinfo['heightPerInch'] = $this->get('webservice.helper.user')->getUserDeviceTypeAndMarking($user, $decoded['device_type']); //$entity->getDeviceUserPerInchPixelHeight();
                $userinfo['iphoneImage'] = $userimage;
                $userinfo['path'] = $baseurl;
                return new Response(json_encode($userinfo));
            } else {
                return new response(json_encode(array('Message' => 'Image not uploaded')));
            }
        } else {
            return new response($this->get('webservice.helper')->response_array(false, 'user not found'));
        }
    }

}

