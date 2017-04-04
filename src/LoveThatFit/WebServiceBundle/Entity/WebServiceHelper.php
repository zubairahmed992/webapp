<?php

namespace LoveThatFit\WebServiceBundle\Entity;
use LoveThatFit\SiteBundle\DependencyInjection\FitAlgorithm2;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Yaml\Parser;

class WebServiceHelper {

    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }
#----------------------------------------------------

    private function user_array($user, $request_array = null) {
        $request_array['device_type'] = is_array($request_array) && array_key_exists('device_type', $request_array) ? $request_array['device_type'] : $user->getImageDeviceType();
        $request_array['device_model'] = is_array($request_array) && array_key_exists('device_model', $request_array) ? $request_array['device_model'] : $request_array['device_type'];
        $request_array['base_path'] = is_array($request_array) && array_key_exists('base_path', $request_array) ? $request_array['base_path'] : null;
        $device_config = $this->container->get('admin.helper.device')->getDeviceConfig($request_array['device_model']);
        #$device_config['conversion_ratio'] = $this->container->get('admin.helper.device')->getConversionRatio($user->extractImageDeviceModel(),$request_array['device_model']);
        $device_config['conversion_ratio'] = $this->container->get('admin.helper.device')->getScreenConversionRatio($user->extractImageDeviceModel(), $request_array['device_model']);
        $device_config['image_device_model'] = $user->extractImageDeviceModel();
        return $user->toDataArray(true, $request_array['device_model'], $request_array['base_path'], $device_config);
    }
    #------------------------ User -----------------------

    public function loginService($request_array) {
        $user = $this->container->get('user.helper.user')->findByEmail($request_array['email']);
        if (count($user) > 0) {
            if ($this->container->get('user.helper.user')->matchPassword($user, $request_array['password'])) {
                $response_array = null;
                if (array_key_exists('user_detail', $request_array) && $request_array['user_detail'] == 'true') {
                    #$response_array['user'] = $user->toDataArray(true, $request_array['device_type'], $request_array['base_path']);
                    $response_array['user'] =  $this->user_array($user,$request_array);
                }
                if (array_key_exists('retailer_brand', $request_array) && $request_array['retailer_brand'] == 'true') {
                    $retailer_brands = $this->container->get('admin.helper.brand')->getBrandListForService();
                    $response_array['retailer'] = $retailer_brands['retailer'];
                    $response_array['brand'] = $retailer_brands['brand'];
                }

                if(array_key_exists('device_token', $request_array) ){
                    $this->container->get('user.helper.user')->updateDeviceToken($user,$request_array);
                }
                return $this->response_array(true, 'member found', true, $response_array);
            } else {
                return $this->response_array(false, 'Invalid Password');
            }
        } else {
            return $this->response_array(false, 'Invalid Email');
        }
    }

    #------------------------ User -----------------------

    public function userDetail($request_array) {
        $user = $this->findUserByAuthToken($request_array['auth_token']);
        $data = array();
        if ($user) {
            #$device_type =  array_key_exists('device_type', $request_array)?$request_array['device_type']:null;
            #$data['user'] = $user->toDataArray(true, $device_type, $request_array['base_path']); 
            $data['user'] = $this->user_array($user, $request_array);

            return $this->response_array(true, 'member found', true, $data);
        } else {
            return $this->response_array(false, 'Member not found');
        }
    }

    #------------------------ User -----------------------
    public function registrationWithDefaultValues($request_array) {

        if (!array_key_exists('email', $request_array)) {
            return $this->response_array(false, 'Email Not provided.');
        }

        $user = $this->container->get('user.helper.user')->findByEmail($request_array['email']);

        if (count($user) > 0) {
            return $this->response_array(false, 'Email already exists.');
        } else {
            $user = $this->createUserWithParams($request_array);
            #--- 3) default user values added
            $measurement = $this->container->get('user.helper.user')->copyDefaultUserData($user, $request_array);

            $user = $this->container->get('user.helper.user')->findByEmail($request_array['email']);

            ##email not send if the event is available against user
            if (!array_key_exists("event_name", $request_array)) {
                #---- 2) send registration email ....
                $this->container->get('mail_helper')->sendRegistrationEmail($user);
            }

            #$detail_array = $user->toDataArray(true, $request_array['device_type'], $request_array['base_path']); 
            $detail_array = $this->user_array($user, $request_array);

            unset($detail_array['per_inch_pixel_height']);
            unset($detail_array['deviceType']);
            unset($detail_array['auth_token_web_service']);
            return $this->response_array(true, 'User created', true, array('user' => $detail_array));
        }
    }
    #------------------------ User -----------------------

    public function userAdminList() {
        $users = $this->container->get('webservice.repo')->userAdminList();
        return $this->response_array(true, 'measurement updated', true, array('user' => $users));
    }
    #------------------------ measurementUpdate -----------------------

    public function measurementUpdate($ra) {
        $user = $this->findUserByAuthToken($ra['auth_token']);
        $measurement = $user->getMeasurement();
        $base_path=$ra['base_path'];
        if ($user->getUserMarker() && $user->getUserMarker()->getDefaultUser()) {
            if(array_key_exists('base_path', $ra)) unset($ra['base_path']);
            if(array_key_exists('email', $ra)) unset($ra['email']);
            if(array_key_exists('auth_token', $ra)) unset($ra['auth_token']);
            #$ar['actual_user'] = $ra;
            #$measurement->setMeasurementJson(json_encode($ar));
        } else {
            $measurement = $this->setUserMeasurementWithParams($ra, $user);
        }
        $ar['actual_user'] = $ra;
        $measurement->setMeasurementJson(json_encode($ar));
        $this->container->get('user.helper.measurement')->saveMeasurement($measurement);
        #return $this->response_array(true, 'measurement updated', true, array('user' => $user->toDataArray(true, null, $base_path)));
        return $this->response_array(true, 'measurement updated', true, array('user' => $this->user_array($user, $ra)));
    }
    #-------------------------------------------------------
    public function updateProfile($ra) {
        $user = $this->findUserByAuthToken($ra['auth_token']);
        if ($user) {
            $user = $this->setUserWithParams($user, $ra);
            $this->container->get('user.helper.user')->saveUser($user);
            return $this->response_array(true, 'Member profile updated', true, array('user' => $user->toArray(true,$ra['base_path'])));
        } else {
            return $this->response_array(false, 'Member not found');
        }
    }

    #-------------------------------------------------------

    private function createUserWithParams($request_array) {

        $user=$this->setUserWithParams($this->container->get('user.helper.user')->createNewUser(), $request_array);
        $user->setPassword($request_array['password']);
        $user = $this->container->get('user.helper.user')->getPasswordEncoded($user);
        $user->generateAuthenticationToken();
        $this->container->get('user.helper.user')->saveUser($user);
        return $user;
    }
    #-------------------------------------------------------

    private function setUserWithParams($user, $request_array) {
        array_key_exists('email', $request_array)?$user->setEmail($request_array['email']) : null;
        array_key_exists('gender', $request_array) ? $user->setGender($request_array['gender']) :  null;
        array_key_exists('zipcode', $request_array) ? $user->setZipcode($request_array['zipcode']) :  null;
        array_key_exists('first_name', $request_array) ? $user->setFirstName($request_array['first_name']) :  null;
        array_key_exists('last_name', $request_array) ? $user->setLastName($request_array['last_name']) :  null;
        array_key_exists('release_name', $request_array) ? $user->setReleaseName($request_array['release_name']) :  null;
        array_key_exists('event_name', $request_array) ? $user->setEventName($request_array['event_name']) :  null;
        if (array_key_exists('device_token', $request_array) && array_key_exists('device_type', $request_array)){
            $user->addDeviceToken($request_array['device_type'], $request_array['device_token']) ;
        }

        #this dob line will be removed with the new build
        $user->setBirthDate(array_key_exists('dob', $request_array) ? new \DateTime($request_array['dob']) : null);
        array_key_exists('birth_date', $request_array) ? $user->setBirthDate(new \DateTime($request_array['birth_date'])) :  null;

        array_key_exists('phone_number', $request_array) ? $user->setPhoneNumber($request_array['phone_number']) :  null;

        return $user;
    }

    #-------------------------------------------------------
    private function setBraRelatedMeasurements($measurement){
        $bra_specs=$this->container->get('admin.helper.size')->getWomanBraSpecs($measurement->getBraSize());
        if($bra_specs){
            $measurement->setBust($bra_specs['average']);
            $measurement->setShoulderAcrossBack($bra_specs['shoulder_across_back']);
            $measurement->setShoulderAcrossFront($bra_specs['shoulder_across_front']);
        }
    }
    #-------------------------------------------------------
    public function setUserMeasurementWithParams($request_array, $user) {
        $measurement = $user->getMeasurement();

        if (!$measurement) {
            $measurement = $this->container->get('user.helper.measurement')->createNew($user);
        }

        if (!is_array($request_array)){
            return $measurement;
        }

        array_key_exists('bust', $request_array) ? $measurement->setBust($request_array['bust']) : '';
        if (array_key_exists('bra_size', $request_array)) {

            $str=str_replace(' ', '', $request_array['bra_size']);
            preg_match_all('/^(\d+)(\w+)$/', $str, $bra_cup);
            $b_size=trim($bra_cup[1][0]." ".$bra_cup[2][0]);
            $measurement->setBraSize($b_size);

            #if bust measurement is manually provided, it will still prefers the value
            #calculated from bra-size
            $this->setBraRelatedMeasurements($measurement);
        }

        #shoulder_across_back value if manually provided will be prefered over
        #value calculated from bra-size
        array_key_exists('shoulder_across_back', $request_array) ? $measurement->setShoulderAcrossBack($request_array['shoulder_across_back']) : '';
        array_key_exists('shoulder_across_front', $request_array) ? $measurement->setShoulderAcrossFront($request_array['shoulder_across_front']) : '';

        array_key_exists('body_type', $request_array) ? $measurement->setBodyTypes($request_array['body_type']) : '';
        array_key_exists('body_shape', $request_array) ? $measurement->setBodyShape($request_array['body_shape']) : '';
        array_key_exists('weight', $request_array) ? $measurement->setWeight($request_array['weight']) : '';
        array_key_exists('height', $request_array) ? $measurement->setHeight($request_array['height']) : '';
        array_key_exists('neck', $request_array) ? $measurement->setNeck($request_array['neck']) : '';
        array_key_exists('center_front_waist', $request_array) ? $measurement->setCenterFrontWaist($request_array['center_front_waist']) : '';
        array_key_exists('back_waist', $request_array) ? $measurement->setBackWaist($request_array['back_waist']) : '';
        array_key_exists('chest', $request_array) ? $measurement->setChest($request_array['chest']) : '';
        array_key_exists('bicep', $request_array) ? $measurement->setBicep($request_array['bicep']) : '';
        array_key_exists('tricep', $request_array) ? $measurement->setTricep($request_array['tricep']) : '';
        array_key_exists('sleeve', $request_array) ? $measurement->setSleeve($request_array['sleeve']) : '';
        array_key_exists('arm', $request_array) ? $measurement->setArm($request_array['arm']) : '';
        array_key_exists('wrist', $request_array) ? $measurement->setWrist($request_array['wrist']) : '';
        array_key_exists('waist', $request_array) ? $measurement->setWaist($request_array['waist']) : '';
        array_key_exists('waist_hip', $request_array) ? $measurement->setWaistHip($request_array['waist_hip']) : '';
        array_key_exists('hip', $request_array) ? $measurement->setHip($request_array['hip']) : '';
        array_key_exists('inseam', $request_array) ? $measurement->setInseam($request_array['inseam']) : '';
        array_key_exists('thigh', $request_array) ? $measurement->setThigh($request_array['thigh']) : '';
        array_key_exists('bust_height', $request_array) ? $measurement->setBustHeight($request_array['bust_height']) : '';
        array_key_exists('waist_height', $request_array) ? $measurement->setWaistHeight($request_array['waist_height']) : '';
        array_key_exists('knee', $request_array) ? $measurement->setKnee($request_array['knee']) : '';
        array_key_exists('calf', $request_array) ? $measurement->setCalf($request_array['calf']) : '';
        array_key_exists('ankle', $request_array) ? $measurement->setAnkle($request_array['ankle']) : '';
        array_key_exists('iphone_foot_height', $request_array) ? $measurement->setIphoneFootHeight($request_array['iphone_foot_height']) : '';

        array_key_exists('shoulder_height', $request_array) ? $measurement->setShoulderHeight($request_array['shoulder_height']) : '';
        array_key_exists('shoulder_length', $request_array) ? $measurement->setShoulderLength($request_array['shoulder_length']) : '';
        array_key_exists('hip_height', $request_array) ? $measurement->setHipHeight($request_array['hip_height']) : '';
        $user_device_model = isset($request_array['device_model'])?$request_array['device_model']:$user->getImageDeviceType();
        #calculating top & bottom position in inches
        #$device_config = $this->container->get('admin.helper.device')->getDeviceConfig($user->getImageDeviceModel());
        $device_config = $this->container->get('admin.helper.device')->getDeviceConfig($user_device_model);
        $device_config['image_device_model'] = $user->extractImageDeviceModel();
        #$device_config['conversion_ratio'] = $this->container->get('admin.helper.device')->getConversionRatio($device_config['image_device_model'],$user_device_model);
        $device_config['conversion_ratio'] = $this->container->get('admin.helper.device')->getScreenConversionRatio($device_config['image_device_model'],$user_device_model);

        if(is_array($device_config) && array_key_exists('pixel_per_inch', $device_config)){
            $measurement->calculatePlacementPositions($device_config['conversion_ratio'] );
        }

        #$ar = json_decode($measurement->getMeasurementJson());
        #$ar['manual'] = $measurement->getArray();
        #$measurement->setMeasurementJson(json_encode($ar));

        return $measurement;
    }

    #--------------------------------User Detail Array -----------------------------#
    private function getBasePath($request) {
        return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
    }
    #----------------------------------------------------------------------------------------
    public function response_array($success, $message = null, $json = true, $data = null) {
        $ar = array(
            'data' => $data,
            'count'=>$data?count($data):0,
            'message' => $message,
            'success' => $success,
        );
        return $json ? json_encode($ar) : $ar;
    }
    #----------------------------------------------------------------------------------------
    public function emailExists($email) {
        $user = $this->container->get('user.helper.user')->findByEmail($email);
        return $user ? true : false;
    }

    #----------------------------------------------------------------------------------------

    public function uploadUserImage($user, $ra, $files) {
        if ($user) {
            #----get file name & create dir            
            $ext = pathinfo($files["image"]["name"], PATHINFO_EXTENSION);
            if (!is_dir($user->getUploadRootDir())) {
                @mkdir($user->getUploadRootDir(), 0700);
            }
            #______________________________________> Fitting Room image

            if ($ra['upload_type'] == 'fitting_room') {

                $user->setImage('cropped' . "." . $ext);
                $user->setImageDeviceType($ra['device_type']);

                if(array_key_exists('device_model', $ra)){
                    $user->setImageDeviceModel($ra['device_model']);
                }

                if (move_uploaded_file($files["image"]["tmp_name"], $user->getOriginalImageAbsolutePath())) {
                    $this->container->get('user.helper.userdevices')->updateDeviceDetails($user, $ra['device_type'], $ra['height_per_inch']);
                    copy($user->getOriginalImageAbsolutePath(), $user->getAbsolutePath());
                    #--------- create user image specs
                    $this->container->get('user.helper.userimagespec')->updateWithParam($ra, $user);

                    #~~~~~#~~~~~#~~~~~ measurement from JSON being copied

                    if ($user->getUserMarker()->getDefaultUser()) {# if demo account, then get measurement from json
                        $measurement = $user->getMeasurement();
                        $decoded = $measurement->getJSONMeasurement('actual_user');
                        if (is_array($decoded)) {
                            $measurement = $this->container->get('webservice.helper')->setUserMeasurementWithParams($decoded, $user);
                            $this->container->get('user.helper.measurement')->saveMeasurement($measurement);
                        }
                        $this->container->get('user.marker.helper')->removeDefaultAccountStatus($user);
                    }
                    #~~~~~#~~~~~#~~~~~#~~~~~#~~~~~#~~~~~
                } else {
                    return $this->response_array(false, 'Image not uploaded');
                }

                #______________________________________> upload_pending
            } elseif ($ra['upload_type'] == 'fitting_room_pending') {
                $user_archive = $this->container->get('user.helper.userarchives')->createNew($user);
                $user_archive->setImage(uniqid().'.'.$ext);

                if (move_uploaded_file($files["image"]["tmp_name"], $user_archive->getAbsolutePath('original'))) {
                    $actual_measurement = $user->getMeasurement()->getJSONMeasurement('actual_user');
                    $ra['measurement'] = is_array($actual_measurement) ? json_encode($actual_measurement) : null;
                    $parsed_array = $this->parse_request_for_archive($ra);
                    $this->container->get('user.helper.userarchives')->saveArchives($user_archive, $parsed_array);

                    $user->setStatus(-1);
                } else {
                    return $this->response_array(false, 'Image not uploaded');
                }
            } elseif ($ra['upload_type'] == 'fitting_room_back' || $ra['upload_type'] == 'fitting_room_side') {
                $user_archive = $this->container->get('user.helper.userarchives')->getPendingArchive($user->getId());
                if (!move_uploaded_file($files["image"]["tmp_name"], $user_archive->getAbsolutePath(substr($ra['upload_type'], 13)))) {
                    return $this->response_array(false, 'Image not uploaded');
                }
                #______________________________________> Avatar
            } elseif ($ra['upload_type'] == 'avatar') {
                $user->setAvatar('avatar' . "." . $ext);
                if (!move_uploaded_file($_FILES["image"]["tmp_name"], $user->getAbsoluteAvatarPath())) {
                    return new Response(json_encode(array('Message' => 'Image not uploaded')));
                }
                #---------------------------------------->Social Media
            } elseif ($ra['upload_type'] == 'social_media') {
                $random_name = uniqid() . "." . $ext;
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $user->getUploadRootDir() . '/' . $random_name)) {
                    return $this->response_array(true, 'Image uploaded', true, $ra['base_path'] . $random_name);
                } else {
                    return $this->response_array(false, 'Image not uploaded');
                }
            } else {#~~~~~~~~~~~~~> anyother image type
                return $this->response_array(false, 'invalid upload type');
            }

            $this->container->get('user.helper.user')->saveUser($user);
            $userinfo = array();
            #$userinfo['user'] = $user->toDataArray(true, $ra['device_type'], $ra['base_path']);
            $userinfo['user'] = $this->user_array($user, $ra);

            return $this->response_array(true, 'User Image Uploaded', true, $userinfo);
        } else {
            return $this->response_array(false, 'member not found');
        }
    }
    private function save_user_image(){

    }
    #----------------------------------------------------------------------------------------

    private function parse_request_for_archive($ra) {
        #$device_type = $this->container->get('user.marker.helper')->getDeviceTypeForModel($ra['device_type']);
        $arr = array(
            'measurement' => $ra['measurement'],
            'device_type' => $ra['device_type'],
            'device_model' => $ra['device_model'],
            'height_per_inch' => $ra['height_per_inch'],
            'image_actions' =>  json_encode(array( #json encoded image specs
                'device_model' => $ra['device_model'],
                'camera_angle' => $ra['camera_angle'],
                'camera_x' => $ra['camera_x'],
                'device_type' => $ra['device_type'],
                'height_per_inch' => $ra['height_per_inch'],
            )),
        );
        return $arr;
    }
    #----------------------------------------------------------------------------------------

    public function uploadUserfile($user, $ra, $files) {
        if ($user) {
            #----get file name & create dir
            $ext = pathinfo($files["file"]["name"], PATHINFO_EXTENSION);
            $file = 'logs.txt';
            if (!is_dir($user->getUploadRootDir())) {
                @mkdir($user->getUploadRootDir(), 0700);
            }
            if ($ext == 'txt') {
                $path = $user->getUploadRootDir();
                if (file_exists($path."/".$file)) {
                    // Open the file to get existing content
                    $current = file_get_contents($path."/".$file);
                    // store file content as a string in $str
                    $current.="\n\n-------------------------------------------------------------".date("Y-m-d")."-----".$ra["device_type"]."------------------------------\n\n";
                    $current.= "\n".file_get_contents($files["file"]["tmp_name"]);
                    file_put_contents($path."/".$file, $current);
                    //method will call here which will update the db log table
                    $this->container->get('user.helper.userappaccesslog')->saveLogs($user);
                    return $this->response_array(false, 'File uploaded Successfully');
                } else {
                    $current= file_get_contents($files["file"]["tmp_name"]);
                    file_put_contents($path."/".$file,$current);
                    //method will call here which will update the db log table
                    $this->container->get('user.helper.userappaccesslog')->saveLogs($user);
                    return $this->response_array(false, 'File uploaded Successfully');
                }

            } else {
                return $this->response_array(false, 'Invalid file uploaded');
            }

        }

    }
    #-------------------------------------------------------------
    public function changePassword($ra) {
        $user = $this->findUserByAuthToken($ra['auth_token']);

        if ($user) {
            if(array_key_exists('password', $ra)){
                if($this->container->get('user.helper.user')->matchPassword($user, $ra['password'])){
                    if(array_key_exists('new_password', $ra)){
                        $user->setPassword($ra['new_password']);
                        $user=$this->container->get('user.helper.user')->getPasswordEncoded($user);
                        $this->container->get('user.helper.user')->saveUser($user);
                        return $this->response_array(true, 'Password saved');
                    }
                    return $this->response_array(false, 'new password not provided');
                }else{
                    return $this->response_array(false, 'password did not match');
                }
                return $this->response_array(false, 'old password not provided');
            }
        } else {
            return $this->response_array(false, 'Member not found');
        }
    }
#-------------------------------------------------------------
    public function forgotPasswordUpdate($ra) {
        $user = $this->findUserByAuthToken($ra['auth_token']);

        if ($user) {
            if (array_key_exists('password', $ra)) {
                $user->setPassword($ra['password']);
                $user = $this->container->get('user.helper.user')->getPasswordEncoded($user);
                $this->container->get('user.helper.user')->saveUser($user);
                return $this->response_array(true, 'Password changed');
            } else {
                return $this->response_array(false, 'password not provided');
            }
        } else {
            return $this->response_array(false, 'Member not found');
        }
    }

#--------------------------------------------------------------------    
    public function matchAlternateToken($ra){
        if (!array_key_exists('auth_token', $ra)){
            return $this->response_array(false, 'Authentication token parameter not provided');
        }
        $user = $this->findUserByAuthToken($ra['auth_token']);
        if (count($user) > 0) {
            return $this->response_array(true, 'User Authenticated');
        } else {
            return $this->response_array(false, 'Authentication Failure');
        };
        return $user;
    }
#--------------------------------------------------------------------

    public function processRequest($request) {
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);

        if ($decoded == null) #if null (to be used for web service testing))
            $decoded = $request->request->all();

        return $decoded;
    }

    #-------------------------------------------------------------

    public function findUserByAuthToken($token) {
        return $this->container->get('user.helper.user')->findByAuthToken($token);
    }

    #------------------------------------------------------------------------------
    #------------------------------------------------------------------------------
    #------------------------------------------------------------------------------

    public function productSync($gender, $date = null) {
        $products = $this->container->get('webservice.repo')->productSync($gender, $date);
        return $this->response_array(true, "products list", true, $products);
    }

    #------------------------------------------------------------------------------

    public function productList($user, $list_type = null) {
        $products = $this->container->get('webservice.repo')->productList($user, $list_type);
        return $this->response_array(true, "products list", true, $products);
    }
#------------------------------------------------------------------------------

    public function productDetail($id, $user) {
        $product = $this->container->get('admin.helper.product')->find($id);
        $p = array();
        $default_color_id = $product->getDisplayProductColor()->getId();
        foreach ($product->getProductColors() as $pc) {
            $p['colors'][$pc->getTitle()] = array(
                'color_id' => $pc->getId(),
                'product_id' => $product->getId(),
                'title' => $pc->getTitle(),
                'image' => $pc->getImage() == null ? 'no-data' : $pc->getImage(),
                'pattern' => $pc->getPattern() == null ? 'no-data' : $pc->getPattern(),
                'recommended' => $default_color_id == $pc->getId() ? true : false,
            );
        }

        $algo = new FitAlgorithm2($user, $product);
        $fb = $algo->getStrippedFeedBack();

        $sizes = $product->getProductSizes();
        $default_item = $algo->getRecommendedFromStrippedFeedBack($fb);
        $p['sizes'] = $fb['feedback'];

        $recommended_product_item = null;
        $favouriteItemIds=$user->getFavouriteItemIdArray();

        foreach ($product->getProductItems() as $pi) {
            $pc_id = $pi->getProductColor()->getId();
            /*
                condition that check disable sizes bcz 
                the disable sizes should not be shown 
                on product detail service
            */
            if ($pi->getProductSize()->getDisabled() != 1) {

                $ps_id = $pi->getProductSize()->getId();
                # get the highest price of all the items/color for a particular size
                $s_desc =$pi->getProductSize()->getBodyType().' '.$pi->getProductSize()->getTitle();
                if (array_key_exists('price', $p['sizes'][$s_desc])) {
                    $p['sizes'][$s_desc]['price'] = ($pi->getPrice() && $p['sizes'][$s_desc]['price'] < $pi->getPrice()) ? $pi->getPrice() : $p['sizes'][$s_desc]['price'];
                } else {
                    $p['sizes'][$s_desc]['price'] = $pi->getPrice() ? $pi->getPrice() : 0;
                }

                $width = 0;
                $height = 0;
                if($pi->getImage() != null){
                    $webpath = str_ireplace('web','iphone5',$pi->getWebPath());
                    $info = getimagesize($webpath);
                    list($width, $height) = $info ;
                }

                $p['items'][$pi->getId()] = array(
                    'item_id' => $pi->getId(),
                    'product_id' => $product->getId(),
                    'color_id' => $pc_id,
                    'size_id' => $ps_id,
                    'sku' => $pi->getSku() == null ? 'no' : $pi->getSku(),
                    'image' => $pi->getImage() == null ? 'no-data' : $pi->getImage(),
                    'width' => (float)$width,
                    'height'    => (float)$height,
                    'recommended' => $default_color_id == $pc_id && $default_item && $default_item['size_id'] == $ps_id ? true : false,
                    'price' => $pi->getPrice()?$pi->getPrice():0,
                    'favourite' => in_array($pi->getId(), $favouriteItemIds),
                );

                if ($default_color_id == $pc_id && $default_item && $default_item['size_id'] == $ps_id) {
                    $recommended_product_item = $pi;
                }
            }#end if condition for size disable checking
        }
        $p['target'] = $product->getclothingType()->getTarget();
        $default_size_fb = array();
        $default_size_fb['feedback'] = FitAlgorithm2::getDefaultSizeFeedback($fb);
        $this->container->get('site.helper.usertryitemhistory')->createUserItemTryHistory($user, $product->getId(), $recommended_product_item, $default_size_fb);
        return $this->response_array(true, "Product Detail ", true, $p);
    }



    #------------------------------------------------------------------------------

    public function userLikedProductIds($user) {
        $product_ids = $this->container->get('webservice.repo')->userLikedProductIds($user);
        return $this->response_array(true, "favourite product ids", true, $product_ids);
    }
    #------------------------------------------------------------------------------
    public function likeUnlikeItem($user, $ra) {

        $page = ($ra['page']!="") ? $ra['page'] : null;
        if ($ra['like'] == 'true') {
            if (count($user->getProductItems()) < 50) {# check limit
                $default_item = null;
                if (array_key_exists('item_id', $ra) && $ra['item_id'] != null) {
                    if(!is_array($ra['item_id'])){
                        $default_item = $this->container->get('admin.helper.productitem')->find($ra['item_id']);
                        $this->container->get('user.helper.user')->makeLike($user, $default_item, 1, $page);
                    }else{
                        foreach($ra['item_id'] as $items){
                            $default_item = $this->container->get('admin.helper.productitem')->find($items);
                            $this->container->get('user.helper.user')->makeLike($user, $default_item, 1, $page);
                        }
                    }
                }else{
                    $p = $this->container->get('admin.helper.product')->find($ra['product_id']);
                    $default_item = $p->getDefaultItem($user);
                    $this->container->get('user.helper.user')->makeLike($user, $default_item, 1, $page);
                }
                return $this->response_array(true, "Updated");
            } else {
                return $this->response_array(false, "Favourite items reached max limit");
            }
        }else{

            ##-------- product_id
            if (array_key_exists('product_id', $ra) && $ra['product_id'] != null) {
                $p = $this->container->get('admin.helper.product')->find($ra['product_id']);
                foreach ($user->getProductItems() as $pi) {
                    if ($pi->getProduct()->getId() == $p->getId()) {

                        $itemID = $pi->getID();
                        if(
                            (is_array($ra['item_id']) && in_array($itemID, $ra['item_id']))
                            || (isset($ra['item_id']) && is_numeric(intval($ra['item_id'])) && $ra['item_id'] == $itemID)
                        ){
                            #remove specific items of the same product
                            $pi->removeUser($user);
                            $user->removeProductItem($pi);
                            $this->container->get('admin.helper.productitem')->save($pi);
                            $this->container->get('user.helper.user')->saveUser($user);
                            $this->container->get('site.helper.userfavitemhistory')->createUserItemFavHistory($user, $p, $pi, 0,$page);
                        }

                    }
                }

            }
            if (array_key_exists('item_id', $ra) && $ra['item_id'] != null) {
                ##----------items_id array
                foreach ($user->getProductItems() as $pi) {
                    if ($pi->getId() == $ra['item_id']){
                        $pi = $this->container->get('admin.helper.productitem')->find($ra['item_id']);
                        $user->removeProductItem($pi);
                        $pi->removeUser($user);
                        $this->container->get('user.helper.user')->saveUser($user);
                        $this->container->get('admin.helper.productitem')->save($pi);
                        $p = $pi->getProduct();
                        $this->container->get('site.helper.userfavitemhistory')->createUserItemFavHistory($user, $p, $pi, 0,$page);
                    }
                }
            }
            ###############################################################
            ########################################################
            return $this->response_array(true, "Item removed");

        }


    }

    #------------------------------------------------------------------------------
    public function __likeUnlikeItem($user, $ra) {
        if ($ra['like'] == 'true') {
            if (count($user->getProductItems()) < 50) {# check limit
                $default_item = null;
                if (array_key_exists('item_id', $ra) && $ra['item_id'] != null && !is_array($ra['item_id'])) {
                    $default_item = $this->container->get('admin.helper.productitem')->find($ra['item_id']);
                }
                if(array_key_exists('item_id', $ra) && $ra['item_id'] != null && is_array($ra['item_id'])){
                    foreach($ra['item_id'] as $items){
                        $default_item = $this->container->get('admin.helper.productitem')->find($items["item_id"]);
                        $this->container->get('user.helper.user')->makeFavourite($user, $default_item);
                    }
                }
                if (!$default_item) {
                    $p = $this->container->get('admin.helper.product')->find($ra['product_id']);
                    $default_item = $p->getDefaultItem($user);
                }

                $this->container->get('user.helper.user')->makeFavourite($user, $default_item);
                return $this->response_array(true, "Updated");
            } else {
                return $this->response_array(false, "Favourite items reached max limit");
            }
        }
    }
    public function _likeUnlikeItem($user, $ra) {
        #$default_item = $this->container->get('admin.helper.productitem')->find($ra['item_id']);
        #$this->container->get('user.helper.user')->makeFavourite($user, $default_item);


        if ($ra['like'] == 'true') {
            if (count($user->getProductItems()) < 50) {# check limit
                $default_item = null;
                if (array_key_exists('item_id', $ra) && $ra['item_id'] != null) {
                    $default_item = $this->container->get('admin.helper.productitem')->find($ra['item_id']);
                }
                if (!$default_item) {
                    $p = $this->container->get('admin.helper.product')->find($ra['product_id']);
                    $default_item = $p->getDefaultItem($user);
                }

                $this->container->get('user.helper.user')->makeFavourite($user, $default_item);
                return $this->response_array(true, "Updated");
            } else {
                return $this->response_array(false, "Favourite items reached max limit");
            }
        } else {
            ###############################################################
            $p = $this->container->get('admin.helper.product')->find($ra['product_id']);
            foreach ($user->getProductItems() as $pi) {
                if ($pi->getProduct()->getId() == $p->getId()) { #remove all items of the same product
                    $pi->removeUser($user);                    # hack for like an item instead of a product 
                    $user->removeProductItem($pi);              # needs to discuss & fix
                    $this->container->get('admin.helper.productitem')->save($pi);
                    $this->container->get('user.helper.user')->saveUser($user);
                }
            }
            ########################################################
            return $this->response_array(true, "product removed");
        }
    }
    #-------------------
    public function loveItem($user, $ra) {
        $p = $this->container->get('admin.helper.product')->find($ra['product_id']);
        if ($p) {
            if ($ra['like'] == 'true') {
                if (count($user->getProductItems()) < 50) {# check limit
                    $default_item = $p->getDefaultItem($user);# run algorithm get recommended item
                    if (!$user->isFavouriteItem($default_item)) { # check if already favourite
                        $user->addProductItem($default_item); #make favourite
                        $default_item->addUser($user);
                        $this->container->get('admin.helper.productitem')->save($default_item);
                        $this->container->get('user.helper.user')->saveUser($user);
                        return $this->response_array(true, "product added");
                    } else {
                        return $this->response_array(true, "already favourite");
                    }
                } else {
                    return $this->response_array(false, "Favourite items reached max limit");
                }
            } else {
                #at the backend the item is liked, not the whole product, in device the product is made like
                //
                ###############################################################
                foreach($user->getProductItems() as $pi){
                    if ($pi->getProduct()->getId()==$p->getId()){ #remove all items of the same product
                        $pi->removeUser($user);                    # hack for like an item instead of a product 
                        $user->removeProductItem($pi);              # needs to discuss & fix
                        $this->container->get('admin.helper.productitem')->save($pi);
                        $this->container->get('user.helper.user')->saveUser($user);
                    }
                }
                ########################################################
                return $this->response_array(true, "product removed");
            }
        } else {
            return $this->response_array(false, "product not found");
        }
    }

    #----------------------------------------------------------------------------------------

    public function sizeChartsService($request_array) {
        $sc = $this->container->get('admin.helper.sizechart')->getBrandSizeTitleArrayByGender($request_array['gender']);
        if (count($sc) > 0) {
            return $this->response_array(true, 'Size charts', true, array('size_charts' => $sc));
        } else {
            return $this->response_array(false, 'Size Charts not found');
        }
    }

    #feedback service
    #------------------------ User -----------------------

    public function feedbackService($user,$content) {
        $this->container->get('mail_helper')->sendFeedbackEmail($user,$content);
    }

    #end feedback service
    public function getProductListByCategoryBanner($gender,array $id, $user_id) {
        $productlist = $this->container->get('webservice.repo')->productListCategory($gender, $id, $user_id);
        foreach($productlist as $key=>$product){
            if(($productlist[$key]['uf_user'] != null) && ($productlist[$key]['uf_user'] == $user_id)) {
                $productlist[$key]['fitting_room_status'] = true;
                $productlist[$key]['qty'] = $productlist[$key]['uf_qty'];
            }else {
                $productlist[$key]['fitting_room_status'] = false;
                $productlist[$key]['qty'] = 0;
            }
        }
        return $productlist;
    }

    //$gender,array $id
    public function getProductListByCategory($gender,array $id, $user_id) {

        $productlist = $this->container->get('webservice.repo')->productListCategory($gender, $id, $user_id);
        foreach($productlist as $key=>$product){
            if(($productlist[$key]['uf_user'] != null) && ($productlist[$key]['uf_user'] == $user_id)) {
                $productlist[$key]['fitting_room_status'] = true;
                $productlist[$key]['qty'] = $productlist[$key]['uf_qty'];
            }else {
                $productlist[$key]['fitting_room_status'] = false;
                $productlist[$key]['qty'] = 0;
            }
        }
        return $this->response_array(true, 'Product List', true, array('product_list'=>$productlist));

    }


//*********************************************
// Webservice For 3.0
//**********************************************

    //Method is using Version 3
    public function productDetailWithImages($id, $user) {
        $product = $this->container->get('admin.helper.product')->find($id);
        $p = array();
        $default_color_id = $product->getDisplayProductColor()->getId();
        foreach ($product->getProductColors() as $pc) {
            //$pc->getTitle()
            $p['colors'][] = array(
                'color_id' => $pc->getId(),
                'product_id' => $product->getId(),
                'title' => $pc->getTitle(),
                'image' => $pc->getImage() == null ? 'no-data' : $pc->getImage(),
                'pattern' => $pc->getPattern() == null ? 'no-data' : $pc->getPattern(),
                'recommended' => $default_color_id == $pc->getId() ? true : false,
            );
        }

        $algo = new FitAlgorithm2($user, $product);
        $fb = $algo->getStrippedFeedBack();
        $default_item = $algo->getRecommendedFromStrippedFeedBack($fb);
        $p['sizes'] = $fb['feedback'];
        $recommended_product_item = null;
        $favouriteItemIds=$user->getFavouriteItemIdArray();
        foreach ($product->getProductItems() as $pi) {
            $pc_id = $pi->getProductColor()->getId();
            $ps_id = $pi->getProductSize()->getId();
            # get the highest price of all the items/color for a particular size
            $s_desc =$pi->getProductSize()->getBodyType().' '.$pi->getProductSize()->getTitle();

            if (array_key_exists('price', $p['sizes'][$s_desc])) {
                $p['sizes'][$s_desc]['price'] = ($pi->getPrice() && $p['sizes'][$s_desc]['price'] < $pi->getPrice()) ? $pi->getPrice() : $p['sizes'][$s_desc]['price'];
            } else {
                $p['sizes'][$s_desc]['price'] = $pi->getPrice() ? $pi->getPrice() : 0;
            }

            //Added new Array Sizes clone where we will add sizes_clone without Keys, We are doing this because
            //Dont want to change the Algorithem functionalities
            $p['sizes_clone'] = array_values($p['sizes']);
            $fitting_room_status_result =  $this->container->get('site.helper.userfittingroomitem')->findByUserItemByProductWithItemId($user->getId(), $product->getId(), $pi->getId());
            $fitting_room_status = false;
            $qty = 0;
            if($fitting_room_status_result[0][1] != "0"){
                $fitting_room_status = true;
                $qty = $fitting_room_status_result[0]['qty'];
            }

            $width = 0;
            $height = 0;
            if($pi->getImage() != null){
                $webpath = str_ireplace('web','iphone5',$pi->getWebPath());
                $info = getimagesize($webpath);
                list($width, $height) = $info ;
            }

            $p['items'][] = array(
                'item_id' => $pi->getId(),
                'product_id' => $product->getId(),
                'color_id' => $pc_id,
                'size_id' => $ps_id,
                'sku' => $pi->getSku() == null ? 'no' : $pi->getSku(),
                'image' => $pi->getImage() == null ? 'no-data' : $pi->getImage(),
                'width' => (float)$width,
                'height'    => (float)$height,
                'recommended' => $default_color_id == $pc_id && $default_item && $default_item['size_id'] == $ps_id ? true : false,
                'price' => $pi->getPrice()?$pi->getPrice():0,
                'favourite' => in_array($pi->getId(), $favouriteItemIds),
                'fitting_room_status' => $fitting_room_status,
                'qty' => $qty,
            );

            if ($default_color_id == $pc_id && $default_item && $default_item['size_id'] == $ps_id) {
                $recommended_product_item = $pi;
            }
        }

        //Just Remove Old functionality Sizes and replaced it by sizes_clone
        unset($p['sizes']);
        $p['sizes'] = $p['sizes_clone'];
        unset($p['sizes_clone']);

        foreach ($product->getProductImages() as $key => $pimage) {
            $image = $pimage->getImage();

            //$pimage->getId()
            $p['model_images'][] = array(
                'id' => $pimage->getId(),
                'image_title' => $pimage->getImagaeTitle(),
                'image' => $pimage->getImage(),
                'image_sort' => $pimage->getImageSort(),
            );
        }

        $p['model_height'] = "Height of model: ".$product->getProductModelHeight();
        $p['description'] = $product->getDescription();
        $p['title'] = $product->getName();
        $p['target'] = $product->getclothingType()->getTarget();

        $default_size_fb = array();
        $default_size_fb['feedback'] = FitAlgorithm2::getDefaultSizeFeedback($fb);
        $this->container->get('site.helper.usertryitemhistory')->createUserItemTryHistory($user, $product->getId(), $recommended_product_item, $default_size_fb);
        return $this->response_array(true, "Product Detail ", true, $p);
    }

    public function parseUserSaveLooksData( $user_id = 0, $base_path )
    {
        // echo $baseURL = $this->container->getParameter('base_url'); die;
        $responseArray = array();
        $entities = $this->container->get('savelook.helper.savelook')->getLooksByUserId($user_id);

        foreach ($entities as $entity) {
            $items = array();
            $saveLookArray = array();
            $totalPrice = 0;
            $url = $base_path.$entity->getUploadDir();
            $saveLookArray['image'] = $url . "/" . $entity->getUserLookImage();
            $saveLookArray['user_id'] = $entity->getUsers()->getId();
            $saveLookArray['look_id'] = $entity->getId();


            foreach ($entity->getSaveLookItem() as $saveLookItem) {
                $temp['image'] = $saveLookItem->getItems()->getImage();
                $temp['product_id'] = $saveLookItem->getItems()->getProduct()->getId();
                $temp['item_id'] = $saveLookItem->getItems()->getId();
                $temp['price'] = $saveLookItem->getItems()->getPrice();
                $temp['color_image'] = $saveLookItem->getItems()->getProductColor()->getImage();
                $totalPrice = $totalPrice + $saveLookItem->getItems()->getPrice();

                array_push($items, $temp);
            }
            $saveLookArray['items'] = $items;
            $saveLookArray['totalPrice'] = "$" . number_format($totalPrice) . " USD";
            array_push( $responseArray, $saveLookArray);
        }

        return $this->response_array(true, "Product Items ", true, $responseArray);
    }

    //Method is using Version 3 - Calling FitAlgo class has been removed.
    public function productDetailWithImagesForFitRoom($id, $product_item, $qty, $user) {
        $product = $this->container->get('admin.helper.product')->find($id);
        $p = array();
        $default_color_id = $product->getDisplayProductColor()->getId();
        foreach ($product->getProductColors() as $pc) {
            //$pc->getTitle()
            $p['colors'][] = array(
                'color_id' => $pc->getId(),
                'product_id' => $product->getId(),
                'title' => $pc->getTitle(),
                'image' => $pc->getImage() == null ? 'no-data' : $pc->getImage(),
                'pattern' => $pc->getPattern() == null ? 'no-data' : $pc->getPattern(),
                'recommended' => $default_color_id == $pc->getId() ? true : false,
            );
        }

        $recommended_product_item = null;
        $favouriteItemIds=$user->getFavouriteItemIdArray();
        foreach ($product->getProductItems() as $pi) {
            $pc_id = $pi->getProductColor()->getId();
            $ps_id = $pi->getProductSize()->getId();
            $ps_title = $pi->getProductSize()->getTitle();
            # get the highest price of all the items/color for a particular size
            $s_desc =$pi->getProductSize()->getBodyType().' '.$pi->getProductSize()->getTitle();

            $product_qty = 0;
            if($product_item == $pi->getId()){
                $product_qty = (int)$qty;
            }

            $width = 0;
            $height = 0;
            if($pi->getImage() != null){
                $webpath = str_ireplace('web','iphone5',$pi->getWebPath());
                $info = getimagesize($webpath);
                list($width, $height) = $info ;
            }

            $p['items'][] = array(
                'item_id' => $pi->getId(),
                'product_id' => $product->getId(),
                'color_id' => $pc_id,
                'size_id' => $ps_id,
                'size_title' => $ps_title,
                'sku' => $pi->getSku() == null ? 'no' : $pi->getSku(),
                'image' => $pi->getImage() == null ? 'no-data' : $pi->getImage(),
                'width' => (float)$width,
                'height'    => (float)$height,
                //'recommended' => $default_color_id == $pc_id && $default_item && $default_item['size_id'] == $ps_id ? true : false,
                'price' => $pi->getPrice()?$pi->getPrice():0,
                'favourite' => in_array($pi->getId(), $favouriteItemIds),
                'fitting_room_status' => $product_item == $pi->getId() ? true : false,
                'qty' => $product_qty,
            );
        }

        foreach ($product->getProductImages() as $key => $pimage) {
            $image = $pimage->getImage();

            //$pimage->getId()
            $p['model_images'][] = array(
                'id' => $pimage->getId(),
                'image_title' => $pimage->getImagaeTitle(),
                'image' => $pimage->getImage(),
                'image_sort' => $pimage->getImageSort(),
            );
        }

        $p['model_height'] = "Height of model: ".$product->getProductModelHeight();
        $p['description'] = $product->getDescription();
        $p['title'] = $product->getName();
        $p['target'] = $product->getclothingType()->getTarget();
        return $p;
    }
}