<?php
namespace LoveThatFit\WebServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WSUserController extends Controller
{

    private function process_request()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $decoded['base_path'] = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';
        return $decoded;
    }

#~~~~~~~~~~~~~~~~~~~ ws_user_Login   /ws/login

    public function loginAction()
    {
        $decoded = $this->process_request();
        $user_info = $this->get('webservice.helper')->loginService($decoded);
        return new Response($user_info);
    }

    public function logoutAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if ($user) {
            $user_info = $this->get('webservice.helper')->logoutService($user, $decoded);
            if (!empty($user_info)) {
                if ($user_info['success'])
                    $res = $this->get('webservice.helper')->response_array(true, $user_info['msg']);
                else
                    $res = $this->get('webservice.helper')->response_array(false, $user_info['msg']);
            } else {
                $res = $this->get('webservice.helper')->response_array(false, 'session id or appname is empty.');
            }
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not found.');
        }

        return new Response($res);
    }

#~~~~~~~~~~~~~~~~~~~ ws_size_charts   /ws/size_charts
    public function sizeChartsAction()
    {
        $decoded = $this->process_request();
        $json_data = $this->get('webservice.helper')->sizeChartsService($decoded);
        return new response($json_data);

    }

#~~~~~~~~~~~~~~~~~~~ ws_email_exists   /ws/email_exists

    public function emailExistsAction()
    {
        $decoded = $this->process_request();
        $exists = $this->get('webservice.helper')->emailExists($decoded['email']);
        return new Response($exists ? 'true' : 'false');
    }

#~~~~~~~~~~~~~~~~~~~ ws_user_detail   /ws/user_detail

    public function detailAction()
    {
        $decoded = $this->process_request();
        $json_data = $this->get('webservice.helper')->userDetail($decoded);
        return new Response($json_data);
    }

#~~~~~~~~~~~~~~~~~~~ ws_push_notification   /ws/push_notification

    public function sendPushNotificationAction()
    {
        $decoded = $this->process_request();
        $json_data = $this->get('webservice.helper')->userDetail($decoded);
        $user = $this->get('user.helper.user')->findByAuthToken($decoded['auth_token']);
        if ($decoded["auth_token"] != '' && $decoded["device_token"] == '') {
            $push_response = $this->get('pushnotification.helper')->sendPushNotification($user, $json_data);
        } elseif ($decoded["auth_token"] == '' && $decoded["device_token"] != '') {
            $push_response = $this->get('pushnotification.helper')->sendPushNotificationWithDeviceToken($decoded["device_token"]);
        } else {
            $push_response = $this->get('pushnotification.helper')->sendPushNotificationWithDeviceToken($decoded["device_token"], $json_data);
        }
        return new Response(json_encode($push_response));
    }

    #~~~~~~~~~~~~~~~~~~~ ws_push_notification_clothing type   /ws/push_notification_clothing_type

    public function sendPushNotificationClothingTypeAction()
    {
        $decoded = $this->process_request();
        $json_data = $this->get('webservice.helper')->userDetail($decoded);
        $user = $this->get('user.helper.user')->findByAuthToken($decoded['auth_token']);
        $push_response = $this->get('pushnotification.helper')->sendNotifyClothingType($decoded["device_token"]);
        return new Response(json_encode($push_response));
    }

#~~~~~~~~~~~~~~~~~~~ ws_user_registeration   /ws/user_registeration

    public function registrationAction()
    {
        $decoded = $this->process_request();
        if (isset($decoded['imc']) && $decoded['imc'] == "true") {
            $json_data = $this->get('webservice.helper')->registrationWithDefaultValuesSupport($decoded);
        } else {
            $json_data = $this->get('webservice.helper')->registrationWithDefaultValues($decoded);
        }
        return new Response($json_data);
    }

#~~~~~~~~~~~~~~~~~~~ ws_user_measurement_update   /ws/user_measurement_update
    public function measurementUpdateAction()
    {
        $decoded = $this->process_request();
        $json_data = $this->get('webservice.helper')->measurementUpdate($decoded);
        return new Response($json_data);
    }

#~~~~~~~~~~~~~~~~~~~ ws_image_uploader   /ws/image_uploader
    public function imageUploaderAction()
    {
        $decoded = $this->process_request();
        #if email index exists check required....
        $user = $this->get('user.helper.user')->findByEmail($decoded['email']);
        $decoded['base_path'] = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/uploads/ltf/users/' . $user->getId() . "/";
        return new Response($this->get('webservice.helper')->uploadUserImage($user, $decoded, $_FILES));
    }

#~~~~~~~~~~~~~~~~~~~ ws_file_uploader   /ws/file_uploader
    public function fileUploaderAction()
    {
        $decoded = $this->process_request();
        //$decoded = array("auth_token" =>'76aff354be53cc674748e0601b81f113',"device_type" => "iphone5");
        #if email index exists check required....
        $user = $this->get('user.helper.user')->findByAuthToken($decoded['auth_token']);
        return new Response($this->get('webservice.helper')->uploadUserFile($user, $decoded, $_FILES));
    }

#-------------------- ws_change_user_status
    public function changeUserStatusAction($token, $status)
    {
        $user = $this->get('user.helper.user')->findByAuthToken($token);
        $old_status = $user->getStatus();
        $user->setStatus($status);
        $this->get('user.helper.user')->saveUser($user);
        return new Response('Status updated from (' . $old_status . ') to (' . $status . ')');
    }

#------------------------- ws_image_uploader   /ws/image_uploader

    public function passwordChangeAction()
    {
        return new response($this->get('webservice.helper')->changePassword($this->process_request()));
    }

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~>  
#    FORGET_PASSWORD_SENDEMAIL	/ws/forget_password
    public function forgotPasswordAction()
    {
        $decoded = $this->process_request();
        $res = '';
        $user = $this->get('user.helper.user')->findByEmail($decoded['email']);

        if ($user) {
            $user->setAuthToken(uniqid());
            $this->get('user.helper.user')->saveUser($user);
            $baseurl = $this->getRequest()->getHost();
            $link = $baseurl . $this->generateUrl('forgot_password_reset_form', array('email_auth_token' => $user->getAuthToken()));
            #return new Response($this->get('webservice.helper')->response_array(true, " Email has been sent with reset password link (".$link .") to " . $user->getEmail()));

            $defaultData = $this->get('mail_helper')->sendPasswordResetLinkEmail($user, $link);

            if ($defaultData[0]) {
                #$res= $this->get('webservice.helper')->response_array(true, " Email has been sent with reset password link to " . $user->getEmail());
                $res = $this->get('webservice.helper')->response_array(true, " Email has been sent with reset password link (" . $link . ") to " . $user->getEmail());

            } else {

                $res = $this->get('webservice.helper')->response_array(false, " Email not sent due to some problem, please try again later.");
            }

        } else {
            $res = $this->get('webservice.helper')->response_array(false, " User not found.");

        }
        return new Response($res);
    }

#----------------------------------------------------------
    public function forgotPasswordTokenAuthAction()
    {
        return new response($this->get('webservice.helper')->matchAlternateToken($this->process_request()));
    }

#----------------------------------------------------------
    public function forgotPasswordUpdateAction()
    {
        return new response($this->get('webservice.helper')->forgotPasswordUpdate($this->process_request()));
    }

#------------------------- ws_user_profile_update:  /ws/user_profile_update

    public function profileUpdateAction()
    {
        return new response($this->get('webservice.helper')->updateProfile($this->process_request()));
    }

#------------------------- ws_user_list:  /ws/user_list
    public function userAdminListAction()
    {
        $users = $this->get('webservice.helper')->userAdminList();
        return new response($users);
    }

#~~~~~~~~~~~~~~~~~~~ ws_user_feedback_add   /ws/user_feedback_add

    public function feedbackAddAction()
    {

        $ra = $this->process_request();
        if (!array_key_exists('auth_token', $ra)) {
            return new Response($this->get('webservice.helper')->response_array(false, 'Auth token Not provided.'));
        }

        $user = $this->get('webservice.helper')->findUserByAuthToken($ra['auth_token']);

        if (count($user) > 0) {
            $ufb = new \LoveThatFit\UserBundle\Entity\UserFeedback();
            $ufb->setUser($user);
            //Added for debugging To make sure which environment we are wokring
            $environmentURL = "<p>" . 'Environment: ' . $this->getRequest()->getHost() . "</p>";
            // Concatenate with environment we are working now!
            $message = $ra['message'] . $environmentURL;
            $category = $ra['category'];

            $ufb->setMessage($message);
            $ufb->setCategory($category);
            $ufb->setCreatedAt(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($ufb);
            $em->flush();

            #feedback email
            $this->get('webservice.helper')->feedbackService($user, $ra['message'] . $environmentURL);
            #feedback email end

            return new Response($this->get('webservice.helper')->response_array(true, 'Feedback added'));
        } else {
            return new Response($this->get('webservice.helper')->response_array(false, 'User Not found!'));
        }

    }

    #~~~~~~~~~~~~~~~~~~~ ws_user_default_values   /ws/user_default_values

    public function defaultValuesAction()
    {
        $yaml = new Parser();
        $conf = $yaml->parse(file_get_contents('../src/LoveThatFit/UserBundle/Resources/config/dummy_users.yml'));
        return new Response(json_encode($conf));

    }

    #~~~~~~~~~~~~~~~~~~~~~~ ws_selfieshare_create	/ws/selfieshare/create


    public function selfieshareCreateAction()
    {
        $ra = $this->process_request();
        #return new Response(json_encode($ra));
        if (!array_key_exists('auth_token', $ra)) {
            return new Response($this->get('webservice.helper')->response_array(false, 'Auth token Not provided.'));
        }

        $user = $this->get('webservice.helper')->findUserByAuthToken($ra['auth_token']);

        if (count($user) > 0) {
            $ss = $this->get('user.selfieshare.helper')->createWithParam($ra, $user);
            if (array_key_exists('message_type', $ra) && $ra['message_type'] == 'sms') {
                $baseurl = "http://" . $this->getRequest()->getHost();
                $share_url = array();
                $share_url["url"] = $baseurl . $this->generateUrl('selfieshare_provide_feedback', array('ref' => $ss->getRef()));
                $share_url_response = json_encode($share_url);
                return new Response($share_url_response);
            } else {

                $ss_ar['to_email'] = $ss->getFriendEmail();
                $ss_ar['template'] = 'LoveThatFitAdminBundle::email/selfieshare_friend.html.twig';
                $ss_ar['template_array'] = array('user' => $user, 'selfieshare' => $ss, 'link_type' => 'edit');
                $ss_ar['subject'] = 'SelfieStyler friend share';
                $this->get('mail_helper')->sendEmailWithTemplate($ss_ar);
                return new Response($this->get('webservice.helper')->response_array(true, 'selfieshare created'));
            }
        } else {
            return new Response($this->get('webservice.helper')->response_array(false, 'User Not found!'));
        }
    }

    public function feedbackCategoryListAction()
    {
        $yaml = new Parser();
        $conf = $yaml->parse(file_get_contents('../src/LoveThatFit/WebServiceBundle/Resources/config/category.yml'));
        return new Response(json_encode($conf));
    }

    public function selfieshareCreateNewAction()
    {
        $ra = $this->process_request();
        #return new Response(json_encode($ra));
        if (!array_key_exists('auth_token', $ra)) {
            return new Response($this->get('webservice.helper')->response_array(false, 'Auth token Not provided.'));
        }

        $user = $this->get('webservice.helper')->findUserByAuthToken($ra['auth_token']);

        if (count($user) > 0) {
            $ss = $this->get('user.selfieshare.helper')->createWithParam($ra, $user);
            if (array_key_exists('message_type', $ra) && $ra['message_type'] == 'sms') {
                $baseurl = "http://" . $this->getRequest()->getHost();
                $share_url = array();
                $share_url["url"] = $baseurl . $this->generateUrl('selfieshare_provide_feedback', array('ref' => $ss->getRef()));
                $share_url_response = json_encode($share_url);
                return new Response($share_url_response);
            } else {

                $ss_ar['to_email'] = $ss->getFriendEmail();
                $ss_ar['template'] = 'LoveThatFitAdminBundle::email/selfieshare_friend.html.twig';
                $ss_ar['template_array'] = array('user' => $user, 'selfieshare' => $ss, 'link_type' => 'edit');
                $ss_ar['subject'] = 'SelfieStyler friend share';
                $this->get('mail_helper')->sendEmailWithTemplate($ss_ar);
                return new Response($this->get('webservice.helper')->response_array(true, 'selfieshare created'));
            }
        } else {
            return new Response($this->get('webservice.helper')->response_array(false, 'User Not found!'));
        }
    }

    public function regsiterUserDeviceTokenAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if ($user) {
            $userDeviceToken = (isset($decoded['device_token'])) ? $decoded['device_token'] : "";
            if(!empty($userDeviceToken)){
                $registerToken = $this->get('webservice.helper')->registerUserDeviceToken( $userDeviceToken, $user);
                if($registerToken > 0)
                    $res = $this->get('webservice.helper')->response_array(false, 'Device token exists.');
                else
                    $res = $this->get('webservice.helper')->response_array(true, 'Successfully registed user device.');
            }else{
                $res = $this->get('webservice.helper')->response_array(false, 'User device token is empty.');
            }
        }else{
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }

        return new Response( $res );
    }



    #~~~~~~~~~~~~~~~~~~~ ws_user_mask_marker_detail   /ws/ws_user_mask_marker_detail

    public function userMaskMarkerdetailAction()
    {
       
        $decoded = $this->process_request();
        $json_data = $this->get('webservice.helper')->userDetailMaskMarker($decoded);
        return new Response($json_data);
    }  
    
    public function userOriginalImageAction()
    {
       
        $yaml   = new Parser();
        $mcp_auth_token = $yaml->parse(file_get_contents('../src/LoveThatFit/WebServiceBundle/Resources/config/mcp_token.yml'))['mcp_token']['token'];
        $decoded = $this->process_request();
        if($decoded['mcp_auth_token']==$mcp_auth_token) {       
            $json_data = $this->get('webservice.helper')->userOriginalImage($decoded);
            return new Response($json_data);
        }
        else
        {            
           return new Response(json_encode(array("success"=>'0',"Error"=>'Invalid token')));
        }    
    }

     public function mcpArchiveSaveMarkerAction() {
        $params = $this->process_request();   
        $params['image_actions'] = json_encode($params['image_actions']);     
        $params['mask_y'] = json_encode($params['mask_y']);     
        $params['mask_x'] = json_encode($params['mask_x']);     
        $yaml   = new Parser();
        $mcp_auth_token = $yaml->parse(file_get_contents('../src/LoveThatFit/WebServiceBundle/Resources/config/mcp_token.yml'))['mcp_token']['token'];
        if($params['mcp_auth_token']==$mcp_auth_token) {       
        $archiveData=$this->get('user.helper.userarchives')->getArchiveId($params['user_id']);
        $archive = $this->get('user.helper.userarchives')->find($archiveData['id']);
        $this->get('user.helper.userarchives')->saveArchivesSupport($archive, $params);

            if(!empty($_POST['imageData'])){

                $image_actions = json_decode($archive->getImageActions());
                $device_type = $image_actions->device_type;
                if (!$archive) {
                    throw $this->createNotFoundException('Unable to find archive.');
                }
                $response = $archive->writeImageFromCanvas($_POST['imageData']);
                #if not from mask marker adjustment interface then resize
                $archive->resizeImageSupport($device_type); 

            }
             return new Response(json_encode(array("success"=>'1',"Description"=>'archive saved')));
        }else{

            return new Response(json_encode(array("success"=>'0',"error"=>'Invalid token or user id')));
        }    

       
    }
    
    public function mcpArchiveToLiveAction() {
        $yaml   = new Parser();
        $mcp_auth_token = $yaml->parse(file_get_contents('../src/LoveThatFit/WebServiceBundle/Resources/config/mcp_token.yml'))['mcp_token']['token'];
        $decoded = $this->process_request();
        if($decoded['mcp_auth_token']==$mcp_auth_token) {       
            try {

                $archiveData=$this->get('user.helper.userarchives')->getArchiveId($decoded['user_id']);     
               

                $archive=$this->get('user.helper.userarchives')->makeArchiveToCurrentSupport($archiveData['id']);               
                //update podio user member calibrated to yes
                $this->updatePodioUserMemberCalibrated($archive->getUser()->getId());
                return new Response(json_encode(array("success"=>'1',"Description"=>'archive status updated')));
            } catch(\Exception $e) {
                // log $e->getMessage()
                return new Response(json_encode(array("success"=>'0',"Error"=>'Invalid user id')));
            }
        }    
        else
        {
          return new Response(json_encode(array("success"=>'0',"Error"=>'Invalid token')));  
        }    
        
        
    }
    
    private function updatePodioUserMemberCalibrated($user_id){
        ##send update to podio that the user is activated
        $data = $this->container->get('user.helper.podioapi')->updateUserPodio($user_id);
    }
    
    public function pendingUsersRevertedImageAction()
    {     
        
        $yaml   = new Parser();
        $mcp_auth_token = $yaml->parse(file_get_contents('../src/LoveThatFit/WebServiceBundle/Resources/config/mcp_token.yml'))['mcp_token']['token'];
        $decoded = $this->process_request();
        if($decoded['mcp_auth_token']==$mcp_auth_token) {                  
            $this->get('user.helper.userarchives')->updateRevertedImageStatus($decoded['user_id'],$decoded['image_hash']);
            return new Response(json_encode(array("success" => 1,"description" => "Image status has been Reverted")));
        } else {
            return new Response(json_encode(array("success" => "0","description" => "Invaild Token")));
        }    
    }    

    public function renderImageBySessionIdAction($ref = null)
    {
        $isLogin = $this->get('userlog.helper.userlog')->checkForUserSession(array(
            'session_id' => $ref,
        ));

        if($isLogin){
            $user = $this->get('userlog.helper.userlog')->userBySessionId(array(
                'session_id' => $ref,
            ));

            // IOSV3-350 - Create image instances, If background available then background will be set.
            // Otherwise without Background
            $ufile = getcwd()."/uploads/ltf/users/".$user->getId()."/cropped.png";
            $bfile = getcwd()."/uploads/ltf/background/fitting_background.png";

            if(file_exists($bfile)){
                $user_image = imagecreatefrompng($ufile);
                $background_image = imagecreatefrompng($bfile);
                list($width, $height) = getimagesize($ufile);
                // Copy
                $dest = imagecreatetruecolor($width, $height);
                imagecopy($dest, $background_image, 0, 0, 0, 0, $width, $height);
                imagecopy($dest, $user_image, 0, 0, 0, 0, $width, $height);

                // Output and free from memory
                header('Content-Type: image/png');
                imagepng($dest);
                header('Content-Length: ' . filesize($dest));

                imagedestroy($dest);
                imagedestroy($user_image);
                imagedestroy($background_image);
            }else{
                $file = getcwd()."/uploads/ltf/users/".$user->getId()."/cropped.png";
                $type = 'image/png';
                header('Content-Type:'.$type);
                header('Content-Length: ' . filesize($file));
                readfile($file);
            }

        }else{
            throw new NotFoundHttpException('Sorry not existing!');
        }
    }

    public function renderAvatarBySessionIdAction($ref = null)
    {
        $isLogin = $this->get('userlog.helper.userlog')->checkForUserSession(array(
            'session_id' => $ref,
        ));

        if($isLogin){
            $user = $this->get('userlog.helper.userlog')->userBySessionId(array(
                'session_id' => $ref,
            ));

            $file = getcwd()."/uploads/ltf/users/".$user->getId()."/avatar.png";
            $type = 'image/png';
            header('Content-Type:'.$type);
            header('Content-Length: ' . filesize($file));
            readfile($file);
        }else{
            throw new NotFoundHttpException('Sorry not existing!');
        }
    }

    public function selfieshareCreateV3Action()
    {
        $ra = $this->process_request();
        #return new Response(json_encode($ra));
        if (!array_key_exists('auth_token', $ra)) {
            return new Response($this->get('webservice.helper')->response_array(false, 'Auth token Not provided.'));
        }

        $user = $this->get('webservice.helper')->findUserByAuthToken($ra['auth_token']);

        if (count($user) > 0) {
            $ss = $this->get('user.selfieshare.helper')->createWithParam($ra, $user);
            if (array_key_exists('message_type', $ra) && $ra['message_type'] == 'sms') {
                $baseurl = "http://" . $this->getRequest()->getHost();
                $share_url = array();
                $share_url["url"] = $baseurl . $this->generateUrl('selfieshare_provide_feedback_v3', array('ref' => $ss->getRef()));
                $share_url_response = json_encode($share_url);
                return new Response($share_url_response);
            } else {
                $ss_ar['to_email'] = $ss->getFriendEmail();
                $ss_ar['template'] = 'LoveThatFitAdminBundle::email/selfieshare_friend.html.twig';
                $ss_ar['template_array'] = array('user' => $user, 'selfieshare' => $ss, 'link_type' => 'edit');
                $ss_ar['subject'] = 'SelfieStyler friend share';
                $this->get('mail_helper')->sendEmailWithTemplate($ss_ar);
                return new Response($this->get('webservice.helper')->response_array(true, 'selfieshare created'));
            }
        } else {
            return new Response($this->get('webservice.helper')->response_array(false, 'User Not found!'));
        }
    }


    public function nwsUpdatePrimaryEmailAction()
    {     
                
        $decoded = $this->process_request();
         $user = $this->container->get('user.helper.user')->findByEmail($decoded['email']);
        if($user) {
            $email_auth_token = $user->getAuthToken();
            if($email_auth_token==$decoded['auth_token'])
            { 
                if($decoded['email']==$decoded['new_primary_email']){

                    return new Response(json_encode(array("success" => "0","description" => "Both emails are same.")));    

                } else {    


        
                    $newUser = $this->container->get('user.helper.user')->findByEmail($decoded['new_primary_email']);
                    if($newUser){
                        return new Response(json_encode(array("success" => "0","description" => "This email already assigned to other member.")));    
                    }else{

                        $this->get('user.helper.user')->saveNewPrimaryEmail($user,$decoded['email'],$decoded['new_primary_email']);
                        $this->container->get('user.helper.user')->sendUpdatedPrimaryEmailToUser($user,$decoded);
                        $user_podio = array(
                            'current_email' => ($decoded['email']) ? $decoded['email'] : '',
                            'new_email' => ($decoded['new_primary_email']) ? $decoded['new_primary_email']: ''
                        );                   

                        $podio_results = $this->container->get('user.helper.podioapi')->updateUserPrimaryEmailPodio($user_podio);

                        if($podio_results['podio_id'] > 0){
                           
                            $podioData = $this->container->get('user.helper.podio')->findPrimaryKeybyPodioId($podio_results['podio_id']);
                            $id = $podioData->getId();
                            $this->container->get('user.helper.podio')->updatePriamryEmaril($id,$podio_results['podio_id'],$podio_results['is_podio_updated']);
                        }
                      
                        return new Response(json_encode(array("success" => 1,"description" => "Your email has been successfully changed! <Perfect, thanks!>")));
                    }
                }

            } else {

            return new Response(json_encode(array("success" => "0","description" => "Invalid Token")));
            }      

        } else {
            return new Response(json_encode(array("success" => "0","description" => "Invalid Email")));
        }    
    }

#~~~~~~~~~~~~~~~~~~~ ws_user_registeration   /ws/user_registeration

    public function registrationFromWebAction()
    {
        $decoded = $this->process_request();
        $decoded['imc'] = true;
        $decoded['device_type'] = 'iphone6';
        $decoded['create_default_user'] = true;
        $json_data = $this->get('webservice.helper')->registrationWithDefaultValuesSupportWeb($decoded);

        return new Response($json_data);
    }
  
}

