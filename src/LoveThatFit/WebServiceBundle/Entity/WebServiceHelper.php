<?php

namespace LoveThatFit\WebServiceBundle\Entity;

use LoveThatFit\SiteBundle\DependencyInjection\FitAlgorithm2;
use LoveThatFit\UserBundle\Entity\User;
use sandeepshetty\shopify_api\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Yaml\Parser;
use LoveThatFit\WebServiceBundle\Event\CalibrationEvent;

class WebServiceHelper
{

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

#----------------------------------------------------

    private function user_array($user, $request_array = null)
    {
        $request_array['device_type'] = is_array($request_array) && array_key_exists('device_type', $request_array) ? $request_array['device_type'] : $user->getImageDeviceType();
        $request_array['device_model'] = is_array($request_array) && array_key_exists('device_model', $request_array) ? $request_array['device_model'] : $request_array['device_type'];
        $request_array['base_path'] = is_array($request_array) && array_key_exists('base_path', $request_array) ? $request_array['base_path'] : null;

        ##modify by umer for new app/config/config_device_support.yml file start code
        $version = $this->container->get('user.helper.userarchives')->getVersion($user->getId());
        if (!isset($version['version']) || $version['version'] == "") {
            $version = $this->container->get('user.helper.user')->getVersion($user->getId());
        }

        if (isset($version['version']) && $version['version'] == 1) {

            $device_config = $this->container->get('admin.helper.device_support')->getDeviceConfig($request_array['device_model']);

            $device_config['conversion_ratio'] = $this->container->get('admin.helper.device_support')->getScreenConversionRatio($user->extractImageDeviceModel(), $request_array['device_model']);
            
        } else {
            $device_config = $this->container->get('admin.helper.device')->getDeviceConfig($request_array['device_model']);
            
            $device_config['conversion_ratio'] = $this->container->get('admin.helper.device')->getScreenConversionRatio($user->extractImageDeviceModel(), $request_array['device_model']);
        }
        ##modify by umer for new app/config/config_device_support.yml file end code

        #$device_config['conversion_ratio'] = $this->container->get('admin.helper.device')->getConversionRatio($user->extractImageDeviceModel(),$request_array['device_model']);
        $device_config['image_device_model'] = $user->extractImageDeviceModel();

        if (isset($version['version']) && $version['version'] == 1) {
            return $user->toDataArraySupport(true, $request_array['device_model'], $request_array['base_path'], $device_config);
        } else {

            return $user->toDataArray(true, $request_array['device_model'], $request_array['base_path'], $device_config);
        }
    }


    #------------------------ User -----------------------
    public function logoutService(User $user, $request_array)
    {
        if (isset($request_array['session_id']) && isset($request_array['appname'])) {
            $logObject = $this->container->get('userlog.helper.userlog')->findUserBySessionId($user, $request_array);
            if (is_object($logObject)) {
                return array(
                    'success' => true,
                    "msg" => "user has been successfully logout"
                );
            } else {
                return array(
                    'success' => false,
                    "msg" => "some thing went wrong"
                );
            }
        }

        return array();
    }

    public function loginService($request_array)
    {
        $user = $this->container->get('user.helper.user')->findByEmail($request_array['email']);
        if (count($user) > 0) {
            if ($this->container->get('user.helper.user')->matchPassword($user, $request_array['password'])) {
                $userLogs = $this->container->get('userlog.helper.userlog')->findUserIsLoginBefore($user, $request_array);
                $logObject = $this->container->get('userlog.helper.userlog')->logUserLoginTime($user, $request_array);
                $response_array = null;
                if (array_key_exists('user_detail', $request_array) && $request_array['user_detail'] == 'true') {
                    #$response_array['user'] = $user->toDataArray(true, $request_array['device_type'], $request_array['base_path']);
                    $response_array['user'] = $this->user_array($user, $request_array);
                    $response_array['user']['sessionId'] = (is_object($logObject)) ? $logObject->getSessionId() : null;
                    $response_array['user']['isNewUser'] = (empty($userLogs)) ? 1 : 0;
                    $response_array['user']['image_path'] = "/render/image/";
                    $response_array['user']['avatar_path'] = "/render/avatar/";
                    $defaultProducts = $this->container->get('admin.helper.product')->findDefaultProduct();
                    $response_array['user']['defaultProduct'] = $defaultProducts;
                }
                if (array_key_exists('retailer_brand', $request_array) && $request_array['retailer_brand'] == 'true') {
                    $retailer_brands = $this->container->get('admin.helper.brand')->getBrandListForService();
                    $response_array['retailer'] = $retailer_brands['retailer'];
                    $response_array['brand'] = $retailer_brands['brand'];
                    $response_array['brand_top'] = $this->container->get('admin.helper.brand')->getBrandListWithBannerForService(1);
                    $response_array['brand_bottom'] = $this->container->get('admin.helper.brand')->getBrandListWithBannerForService(0);
                }

                if (array_key_exists('device_token', $request_array)) {
                    $this->container->get('user.helper.user')->updateDeviceToken($user, $request_array);
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

    public function userDetail($request_array)
    {
        $user = $this->findUserByAuthToken($request_array['auth_token']);
        $data = array();
        if ($user) {
            #$device_type =  array_key_exists('device_type', $request_array)?$request_array['device_type']:null;
            #$data['user'] = $user->toDataArray(true, $device_type, $request_array['base_path']);
            $data['user'] = $this->user_array($user, $request_array);

            $defaultProducts = $this->container->get('admin.helper.product')->findDefaultProduct();
            $data['user']['defaultProduct'] = $defaultProducts;
            $data['user']['image_path'] = "/render/image/";
            $data['user']['avatar_path'] = "/render/avatar/";

            return $this->response_array(true, 'member found', true, $data);
        } else {
            return $this->response_array(false, 'Member not found');
        }
    }

    #------------------------ User -----------------------
    public function registrationWithDefaultValues($request_array)
    {
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
            try {
                //create podio users entity
                $this->createPodioUser($user->getId());
            } catch(\Exception $e) {
                // log $e->getMessage()
            }

            #$detail_array = $user->toDataArray(true, $request_array['device_type'], $request_array['base_path']); 
            $detail_array = $this->user_array($user, $request_array);

            try{
                $logObject = $this->container->get('userlog.helper.userlog')->logUserLoginTime($user, $request_array);
                $detail_array['sessionId'] = (is_object($logObject)) ? $logObject->getSessionId() : null;
                $detail_array['image_path'] = "/render/image/";
                $detail_array['avatar_path'] = "/render/avatar/";

            }catch (Exception $e){}

            unset($detail_array['per_inch_pixel_height']);
            unset($detail_array['deviceType']);
            unset($detail_array['auth_token_web_service']);
            return $this->response_array(true, 'User created', true, array('user' => $detail_array));
        }
    }

    #------------------------ User -----------------------
    public function registrationWithDefaultValuesSupport($request_array)
    {
        if (!array_key_exists('email', $request_array)) {
            return $this->response_array(false, 'Email Not provided.');
        }

        $user = $this->container->get('user.helper.user')->findByEmail($request_array['email']);

        if (count($user) > 0) {
            return $this->response_array(false, 'Email already exists.');
        } else {
            $user = $this->createUserWithParams($request_array);            
            #--- 3) default user values added
            $measurement = $this->container->get('user.helper.user')->copyDefaultUserDataSupport($user, $request_array);

            $user = $this->container->get('user.helper.user')->findByEmail($request_array['email']);

            ##email not send if the event is available against user
            if (!array_key_exists("event_name", $request_array)) {
                #---- 2) send registration email ....
                $this->container->get('mail_helper')->sendRegistrationEmail($user);
            }

            try {
                //create podio users entity
                $this->createPodioUser($user->getId());
            } catch(\Exception $e) {
                // log $e->getMessage()
            }

            #$detail_array = $user->toDataArray(true, $request_array['device_type'], $request_array['base_path']); 
            $detail_array = $this->user_array($user, $request_array);

            try{
                $logObject = $this->container->get('userlog.helper.userlog')->logUserLoginTime($user, $request_array);
                $detail_array['sessionId'] = (is_object($logObject)) ? $logObject->getSessionId() : null;
                $detail_array['image_path'] = "/render/image/";
                $detail_array['avatar_path'] = "/render/avatar/";

            }catch (Exception $e){}

            unset($detail_array['per_inch_pixel_height']);
            unset($detail_array['deviceType']);
            unset($detail_array['auth_token_web_service']);
            return $this->response_array(true, 'User created', true, array('user' => $detail_array));
        }
    }

    private function createPodioUser($user_id){
        ## add user podio log data
        if ($user_id) {
            $user_entity = $this->container->get('user.helper.user')->find($user_id);
            $save_user_podio = $this->container->get('user.helper.podio')->savePodioUsers($user_entity);
        }
    }

    #------------------------ User -----------------------

    public function userAdminList()
    {
        $users = $this->container->get('webservice.repo')->userAdminList();
        return $this->response_array(true, 'measurement updated', true, array('user' => $users));
    }

    #------------------------ measurementUpdate -----------------------

    public function measurementUpdate($ra)
    {
        $user = $this->findUserByAuthToken($ra['auth_token']);
        $measurement = $user->getMeasurement();
        $base_path = $ra['base_path'];
        if ($user->getUserMarker() && $user->getUserMarker()->getDefaultUser()) {
            if (array_key_exists('base_path', $ra)) unset($ra['base_path']);
            if (array_key_exists('email', $ra)) unset($ra['email']);
            if (array_key_exists('auth_token', $ra)) unset($ra['auth_token']);
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
    public function updateProfile($ra)
    {
        $user = $this->findUserByAuthToken($ra['auth_token']);
        if ($user) {
            $user = $this->setUserWithParams($user, $ra);
            $this->container->get('user.helper.user')->saveUser($user);
            return $this->response_array(true, 'Member profile updated', true, array('user' => $user->toArray(true, $ra['base_path'])));
        } else {
            return $this->response_array(false, 'Member not found');
        }
    }

    #-------------------------------------------------------

    private function createUserWithParams($request_array)
    {

        $user = $this->setUserWithParams($this->container->get('user.helper.user')->createNewUser(), $request_array);
        if (isset($request_array['imc']) && $request_array['imc'] == "true") {
            $user->setVersion(1);
        } else {
            $user->setVersion(0);
        }
        $user->setPassword($request_array['password']);
        $user = $this->container->get('user.helper.user')->getPasswordEncoded($user);
        $user->generateAuthenticationToken();

        $this->container->get('user.helper.user')->saveUser($user);
        return $user;
    }

    #-------------------------------------------------------

    private function setUserWithParams($user, $request_array)
    {
        array_key_exists('email', $request_array) ? $user->setEmail($request_array['email']) : null;
        array_key_exists('gender', $request_array) ? $user->setGender($request_array['gender']) : null;
        array_key_exists('zipcode', $request_array) ? $user->setZipcode($request_array['zipcode']) : null;
        array_key_exists('first_name', $request_array) ? $user->setFirstName($request_array['first_name']) : null;
        array_key_exists('last_name', $request_array) ? $user->setLastName($request_array['last_name']) : null;
        array_key_exists('release_name', $request_array) ? $user->setReleaseName($request_array['release_name']) : null;
        array_key_exists('event_name', $request_array) ? $user->setEventName($request_array['event_name']) : null;
        
        if (array_key_exists('device_token', $request_array) && array_key_exists('device_type', $request_array)) {
            $user->addDeviceToken($request_array['device_type'], $request_array['device_token']);
        }

        #this dob line will be removed with the new build
        $user->setBirthDate(array_key_exists('dob', $request_array) ? new \DateTime($request_array['dob']) : null);
        array_key_exists('birth_date', $request_array) ? $user->setBirthDate(new \DateTime($request_array['birth_date'])) : null;

        array_key_exists('phone_number', $request_array) ? $user->setPhoneNumber($request_array['phone_number']) : null;

        return $user;
    }

    #-------------------------------------------------------
    private function setBraRelatedMeasurements($measurement)
    {
        $bra_specs = $this->container->get('admin.helper.size')->getWomanBraSpecs($measurement->getBraSize());
        if ($bra_specs) {
            $measurement->setBust($bra_specs['average']);
            $measurement->setShoulderAcrossBack($bra_specs['shoulder_across_back']);
            $measurement->setShoulderAcrossFront($bra_specs['shoulder_across_front']);
        }
    }

    #-------------------------------------------------------
    public function setUserMeasurementWithParams($request_array, $user)
    {
        $measurement = $user->getMeasurement();

        if (!$measurement) {
            $measurement = $this->container->get('user.helper.measurement')->createNew($user);
        }

        if (!is_array($request_array)) {
            return $measurement;
        }

        array_key_exists('bust', $request_array) ? $measurement->setBust($request_array['bust']) : '';
        if (array_key_exists('bra_size', $request_array)) {

            $str = str_replace(' ', '', $request_array['bra_size']);
            preg_match_all('/^(\d+)(\w+)$/', $str, $bra_cup);
            $b_size = trim($bra_cup[1][0] . " " . $bra_cup[2][0]);
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
        //new fileds
        array_key_exists('belt_waist', $request_array) ? $measurement->setBeltWaist($request_array['belt_waist']) : '';
        array_key_exists('abdomen', $request_array) ? $measurement->setAbdomen($request_array['abdomen']) : '';
        array_key_exists('high_hip', $request_array) ? $measurement->setHighHip($request_array['high_hip']) : '';
        array_key_exists('low_hip', $request_array) ? $measurement->setLowHip($request_array['low_hip']) : '';
        array_key_exists('torso_height', $request_array) ? $measurement->setTorsoHeight($request_array['torso_height']) : '';

        array_key_exists('inseam', $request_array) ? $measurement->setInseam($request_array['inseam']) : '';
        array_key_exists('thigh', $request_array) ? $measurement->setThigh($request_array['thigh']) : '';
        array_key_exists('high_thigh', $request_array) ? $measurement->setHighThigh($request_array['high_thigh']) : '';
        array_key_exists('low_thigh', $request_array) ? $measurement->setLowThigh($request_array['low_thigh']) : '';
        array_key_exists('bust_height', $request_array) ? $measurement->setBustHeight($request_array['bust_height']) : '';
        array_key_exists('waist_height', $request_array) ? $measurement->setWaistHeight($request_array['waist_height']) : '';
        array_key_exists('knee', $request_array) ? $measurement->setKnee($request_array['knee']) : '';
        array_key_exists('calf', $request_array) ? $measurement->setCalf($request_array['calf']) : '';
        array_key_exists('ankle', $request_array) ? $measurement->setAnkle($request_array['ankle']) : '';
        array_key_exists('iphone_foot_height', $request_array) ? $measurement->setIphoneFootHeight($request_array['iphone_foot_height']) : '';

        array_key_exists('shoulder_height', $request_array) ? $measurement->setShoulderHeight($request_array['shoulder_height']) : '';
        array_key_exists('shoulder_length', $request_array) ? $measurement->setShoulderLength($request_array['shoulder_length']) : '';
        array_key_exists('hip_height', $request_array) ? $measurement->setHipHeight($request_array['hip_height']) : '';
        $user_device_model = isset($request_array['device_model']) ? $request_array['device_model'] : $user->getImageDeviceType();
        #calculating top & bottom position in inches
        #$device_config = $this->container->get('admin.helper.device')->getDeviceConfig($user->getImageDeviceModel());
        #$device_config['conversion_ratio'] = $this->container->get('admin.helper.device')->getConversionRatio($device_config['image_device_model'],$user_device_model);

        ##modify by umer for new app/config/config_device_support.yml file start code
        $version = $this->container->get('user.helper.userarchives')->getVersion($user->getId());
        if (isset($version['version']) && $version['version'] == 1) {

            $device_config = $this->container->get('admin.helper.device_support')->getDeviceConfig($user_device_model);
            
            $device_config['image_device_model'] = $user->extractImageDeviceModel();

            $device_config['conversion_ratio'] = $this->container->get('admin.helper.device_support')->getScreenConversionRatio($device_config['image_device_model'], $user_device_model);
        } else {
            $device_config = $this->container->get('admin.helper.device')->getDeviceConfig($user_device_model);
            
            $device_config['image_device_model'] = $user->extractImageDeviceModel();

            $device_config['conversion_ratio'] = $this->container->get('admin.helper.device')->getScreenConversionRatio($device_config['image_device_model'], $user_device_model);
        }
        ##modify by umer for new app/config/config_device_support.yml file end code
        
        if (is_array($device_config) && array_key_exists('pixel_per_inch', $device_config)) {
            $measurement->calculatePlacementPositions($device_config['conversion_ratio']);
        }

        #$ar = json_decode($measurement->getMeasurementJson());
        #$ar['manual'] = $measurement->getArray();
        #$measurement->setMeasurementJson(json_encode($ar));

        return $measurement;
    }

    #--------------------------------User Detail Array -----------------------------#
    private function getBasePath($request)
    {
        return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
    }

    #----------------------------------------------------------------------------------------
    public function response_array($success, $message = null, $json = true, $data = null)
    {
        $ar = array(
            'data' => $data,
            'count' => $data ? count($data) : 0,
            'message' => $message,
            'success' => $success,
        );
        return $json ? json_encode($ar) : $ar;
    }

    #----------------------------------------------------------------------------------------
    public function emailExists($email)
    {
        $user = $this->container->get('user.helper.user')->findByEmail($email);
        return $user ? true : false;
    }

    #----------------------------------------------------------------------------------------

    public function uploadUserImage($user, $ra, $files)
    {
        if ($user) {
            #----get file name & create dir            
            $ext = pathinfo($files["image"]["name"], PATHINFO_EXTENSION);
            if (!is_dir($user->getUploadRootDir())) {
                @mkdir($user->getUploadRootDir(), 0775);
            }
            #______________________________________> Fitting Room image

            if ($ra['upload_type'] == 'fitting_room') {
                $user->setImage('cropped' . "." . $ext);
                $user->setImageDeviceType($ra['device_type']);

                if (array_key_exists('device_model', $ra)) {
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
                $user_archive->setImage(uniqid() . '.' . $ext);

                if (move_uploaded_file($files["image"]["tmp_name"], $user_archive->getAbsolutePath('original'))) {
                    $actual_measurement = $user->getMeasurement()->getJSONMeasurement('actual_user');
                    $ra['measurement'] = is_array($actual_measurement) ? json_encode($actual_measurement) : null;
                    $parsed_array = $this->parse_request_for_archive($ra);

                    if (isset($ra['version']) && $ra['version'] == 1) {
                        $this->container->get('user.helper.userarchives')->saveArchivesSupport($user_archive, $parsed_array);
                    } else {
                        $this->container->get('user.helper.userarchives')->saveArchives($user_archive, $parsed_array);
                    }

                    $this->container->get('user.helper.userarchives')->saveArchives($user_archive, $parsed_array);

                    $user->setStatus(-1);
                    //Here we going to add new triggeer
                    //This code will new entry in node-js database
                    $user_id = $user->getId();
                    $email = $user->getEmail();
                    $status = 'New';
                    $dispatcher = $this->container->get('event_dispatcher');
                    $event = new CalibrationEvent($user_id, $email, $status);
                    $dispatcher->dispatch(CalibrationEvent::NAME, $event);
                    //Code End for calibration node js.
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

    private function save_user_image()
    {

    }

    #----------------------------------------------------------------------------------------

    private function parse_request_for_archive($ra)
    {
        #$device_type = $this->container->get('user.marker.helper')->getDeviceTypeForModel($ra['device_type']);
        $arr = array(
            'measurement' => $ra['measurement'],
            'device_type' => $ra['device_type'],
            'device_model' => $ra['device_model'],
            'height_per_inch' => $ra['height_per_inch'],
            'version' => $ra['version'],
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

    public function uploadUserfile($user, $ra, $files)
    {
        if ($user) {
            #----get file name & create dir
            $ext = pathinfo($files["file"]["name"], PATHINFO_EXTENSION);
            $file = 'logs.txt';
            if (!is_dir($user->getUploadRootDir())) {
                @mkdir($user->getUploadRootDir(), 0775);
            }
            if ($ext == 'txt') {
                $path = $user->getUploadRootDir();
                if (file_exists($path . "/" . $file)) {
                    // Open the file to get existing content
                    $current = file_get_contents($path . "/" . $file);
                    // store file content as a string in $str
                    $current .= "\n\n-------------------------------------------------------------" . date("Y-m-d") . "-----" . $ra["device_type"] . "------------------------------\n\n";
                    $current .= "\n" . file_get_contents($files["file"]["tmp_name"]);
                    file_put_contents($path . "/" . $file, $current);
                    //method will call here which will update the db log table
                    $this->container->get('user.helper.userappaccesslog')->saveLogs($user);
                    return $this->response_array(false, 'File uploaded Successfully');
                } else {
                    $current = file_get_contents($files["file"]["tmp_name"]);
                    file_put_contents($path . "/" . $file, $current);
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
    public function changePassword($ra)
    {
        $user = $this->findUserByAuthToken($ra['auth_token']);

        if ($user) {
            if (array_key_exists('password', $ra)) {
                if ($this->container->get('user.helper.user')->matchPassword($user, $ra['password'])) {
                    if (array_key_exists('new_password', $ra)) {
                        $user->setPassword($ra['new_password']);
                        $user = $this->container->get('user.helper.user')->getPasswordEncoded($user);
                        $this->container->get('user.helper.user')->saveUser($user);
                        return $this->response_array(true, 'Password saved');
                    }
                    return $this->response_array(false, 'new password not provided');
                } else {
                    return $this->response_array(false, 'password did not match');
                }
                return $this->response_array(false, 'old password not provided');
            }
        } else {
            return $this->response_array(false, 'Member not found');
        }
    }

#-------------------------------------------------------------
    public function forgotPasswordUpdate($ra)
    {
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
    public function matchAlternateToken($ra)
    {
        if (!array_key_exists('auth_token', $ra)) {
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

    public function processRequest($request)
    {
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);

        if ($decoded == null) #if null (to be used for web service testing))
            $decoded = $request->request->all();

        return $decoded;
    }

    #-------------------------------------------------------------

    public function findUserByAuthToken($token)
    {
        return $this->container->get('user.helper.user')->findByAuthToken($token);
    }

    #------------------------------------------------------------------------------
    #------------------------------------------------------------------------------
    #------------------------------------------------------------------------------

    public function productSync($gender, $date = null, $user = null)
    {
        if ($user != null) {
            $user_id = $user->getId();
            $products = $this->container->get('webservice.repo')->productSyncWithFavouriteItem($gender, $date, $user_id);
            // Favourite will be converted in to true and false
            foreach ($products as $key => $value) {
                if ($products[$key]['favourite'] == 0) {
                    $products[$key]['favourite'] = FALSE;
                } else {
                    $products[$key]['favourite'] = TRUE;
                }
            }


        } else {
            $products = $this->container->get('webservice.repo')->productSync($gender, $date);
        }

        return $this->response_array(true, "products list", true, $products);
    }

    #------------------------------------------------------------------------------

    public function productList($user, $list_type = null)
    {
        $products = $this->container->get('webservice.repo')->productList($user, $list_type);
        return $this->response_array(true, "products list", true, $products);
    }

#------------------------------------------------------------------------------

    public function productDetail($id, $user)
    {
        $product = $this->container->get('admin.helper.product')->find($id, true);
        if (count($product) == 0) {
            return $this->response_array(false, 'Product Coming Soon');
        }
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
        $favouriteItemIds = $user->getFavouriteItemIdArray();

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
                $s_desc = $pi->getProductSize()->getBodyType() . ' ' . $pi->getProductSize()->getTitle();
                if (array_key_exists('price', $p['sizes'][$s_desc])) {
                    $p['sizes'][$s_desc]['price'] = ($pi->getPrice() && $p['sizes'][$s_desc]['price'] < $pi->getPrice()) ? $pi->getPrice() : $p['sizes'][$s_desc]['price'];
                } else {
                    $p['sizes'][$s_desc]['price'] = $pi->getPrice() ? $pi->getPrice() : 0;
                }

                $width = 0;
                $height = 0;
                if ($pi->getImage() != null) {
                    $webpath = str_ireplace('web', 'iphone5', $pi->getWebPath());
                    $info = getimagesize($webpath);
                    list($width, $height) = $info;
                }

                $p['items'][$pi->getId()] = array(
                    'item_id' => $pi->getId(),
                    'product_id' => $product->getId(),
                    'color_id' => $pc_id,
                    'size_id' => $ps_id,
                    'sku' => $pi->getSku() == null ? 'no' : $pi->getSku(),
                    'image' => $pi->getImage() == null ? 'no-data' : $pi->getImage(),
                    'width' => (float)$width,
                    'height' => (float)$height,
                    'recommended' => $default_color_id == $pc_id && $default_item && $default_item['size_id'] == $ps_id ? true : false,
                    'price' => $pi->getPrice() ? $pi->getPrice() : 0,
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

    public function userLikedProductIds($user)
    {
        $product_ids = $this->container->get('webservice.repo')->userLikedProductIds($user);
        return $this->response_array(true, "favourite product ids", true, $product_ids);
    }

    #------------------------------------------------------------------------------
    public function likeUnlikeItem($user, $ra)
    {

        $page = ($ra['page'] != "") ? $ra['page'] : null;
        if ($ra['like'] == 'true') {
            if (count($user->getProductItems()) < 50) {# check limit
                $default_item = null;
                if (array_key_exists('item_id', $ra) && $ra['item_id'] != null) {
                    if (!is_array($ra['item_id'])) {
                        $default_item = $this->container->get('admin.helper.productitem')->find($ra['item_id']);
                        $this->container->get('user.helper.user')->makeLike($user, $default_item, 1, $page);
                    } else {
                        foreach ($ra['item_id'] as $items) {
                            $default_item = $this->container->get('admin.helper.productitem')->find($items);
                            $this->container->get('user.helper.user')->makeLike($user, $default_item, 1, $page);
                        }
                    }
                } else {
                    $p = $this->container->get('admin.helper.product')->find($ra['product_id']);
                    $default_item = $p->getDefaultItem($user);
                    $this->container->get('user.helper.user')->makeLike($user, $default_item, 1, $page);
                }
                return $this->response_array(true, "Updated");
            } else {
                return $this->response_array(false, "Favourite items reached max limit");
            }
        } else {

            ##-------- product_id
            if (array_key_exists('product_id', $ra) && $ra['product_id'] != null) {
                $p = $this->container->get('admin.helper.product')->find($ra['product_id']);
                foreach ($user->getProductItems() as $pi) {
                    if ($pi->getProduct()->getId() == $p->getId()) {

                        $itemID = $pi->getID();
                        if (
                            (is_array($ra['item_id']) && in_array($itemID, $ra['item_id']))
                            || (isset($ra['item_id']) && is_numeric(intval($ra['item_id'])) && $ra['item_id'] == $itemID)
                        ) {
                            #remove specific items of the same product
                            $pi->removeUser($user);
                            $user->removeProductItem($pi);
                            $this->container->get('admin.helper.productitem')->save($pi);
                            $this->container->get('user.helper.user')->saveUser($user);
                            $this->container->get('site.helper.userfavitemhistory')->createUserItemFavHistory($user, $p, $pi, 0, $page);
                        }

                    }
                }

            }
            if (array_key_exists('item_id', $ra) && $ra['item_id'] != null) {
                ##----------items_id array
                foreach ($user->getProductItems() as $pi) {
                    if ($pi->getId() == $ra['item_id']) {
                        $pi = $this->container->get('admin.helper.productitem')->find($ra['item_id']);
                        $user->removeProductItem($pi);
                        $pi->removeUser($user);
                        $this->container->get('user.helper.user')->saveUser($user);
                        $this->container->get('admin.helper.productitem')->save($pi);
                        $p = $pi->getProduct();
                        $this->container->get('site.helper.userfavitemhistory')->createUserItemFavHistory($user, $p, $pi, 0, $page);
                    } elseif (is_array($ra['item_id']) && (count($ra['item_id']) > 0)) {
                        foreach ($ra['item_id'] as $pi_aray) {
                            if ($pi->getId() == $pi_aray) {
                                $pi = $this->container->get('admin.helper.productitem')->find($pi_aray);
                                $user->removeProductItem($pi);
                                $pi->removeUser($user);
                                $this->container->get('user.helper.user')->saveUser($user);
                                $this->container->get('admin.helper.productitem')->save($pi);
                                $p = $pi->getProduct();
                                $this->container->get('site.helper.userfavitemhistory')->createUserItemFavHistory($user, $p, $pi, 0, $page);
                            }

                        }
                    }
                }
            }
            ###############################################################
            ########################################################
            return $this->response_array(true, "Item removed");

        }


    }

    #------------------------------------------------------------------------------
    public function __likeUnlikeItem($user, $ra)
    {
        if ($ra['like'] == 'true') {
            if (count($user->getProductItems()) < 50) {# check limit
                $default_item = null;
                if (array_key_exists('item_id', $ra) && $ra['item_id'] != null && !is_array($ra['item_id'])) {
                    $default_item = $this->container->get('admin.helper.productitem')->find($ra['item_id']);
                }
                if (array_key_exists('item_id', $ra) && $ra['item_id'] != null && is_array($ra['item_id'])) {
                    foreach ($ra['item_id'] as $items) {
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

    public function _likeUnlikeItem($user, $ra)
    {
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
    public function loveItem($user, $ra)
    {
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
        } else {
            return $this->response_array(false, "product not found");
        }
    }

    #----------------------------------------------------------------------------------------

    public function sizeChartsService($request_array)
    {
        $sc = $this->container->get('admin.helper.sizechart')->getBrandSizeTitleArrayByGender($request_array['gender']);
        if (count($sc) > 0) {
            return $this->response_array(true, 'Size charts', true, array('size_charts' => $sc));
        } else {
            return $this->response_array(false, 'Size Charts not found');
        }
    }

    #feedback service
    #------------------------ User -----------------------

    public function feedbackService($user, $content)
    {
        $this->container->get('mail_helper')->sendFeedbackEmail($user, $content);
    }

    #end feedback service
    public function getProductListByCategoryBanner($gender, array $id, $user_id, $page_no = 1)
    {
        $yaml = new Parser();
        $conf = $yaml->parse(file_get_contents('../src/LoveThatFit/WebServiceBundle/Resources/config/products.yml'));
        /* Get all Categies with Layer name */
        $getcategorieswithlayername = $this->container->get('admin.helper.Categories')->getAllCategoriesWithLayerName();

        $records_per_page = $conf['nws_products_list_pagination']['records_per_page'];
        $limit = $records_per_page * $page_no;
        $offset = $limit - $records_per_page;
        $productlist = $this->container->get('webservice.repo')->productListCategory($gender, $id, $user_id);
        $page_count = (int)(count($productlist) / $records_per_page);
        $page_count = (count($productlist) % $records_per_page != 0) ? $page_count + 1 : $page_count;
        if (($page_count != 0 && $page_no < 1) || ($page_count != 0 && $page_no > $page_count)) {
            return $this->response_array(false, 'Invalid Page No');
        }
        $productlist = array_slice($productlist, $offset, $records_per_page);
        foreach ($productlist as $key => $product) {

            /* Selected Layer Name */
            $getselectedcategories = $this->container->get('admin.helper.Categories')->getSelectedCategories($product['product_id']);
            $selectedcategories = array_column($getselectedcategories, 'id');
            $selectedlayername = "";
            foreach($getcategorieswithlayername as $key_withlayer => $value_withlayer){
                if(( (in_array($value_withlayer['id'], $selectedcategories)) && (in_array($value_withlayer['parent_id'], $selectedcategories)) )){
                    $selectedlayername = $value_withlayer['layer_name'];
                    break;
                }

            }
            /* Selected Layer Name */

            if (($productlist[$key]['uf_user'] != null) && ($productlist[$key]['uf_user'] == $user_id)) {
                $productlist[$key]['fitting_room_status'] = true;
                $productlist[$key]['qty'] = $productlist[$key]['uf_qty'];
            } else {
                $productlist[$key]['fitting_room_status'] = false;
                $productlist[$key]['qty'] = 0;
            }

            /* Selected Layer Name */
            if(!empty($selectedlayername)){
                $productlist[$key]['layer_name'] = (int)$selectedlayername;
            }
            /* Selected Layer Name */

            //Color Count
            $color_count = $this->container->get('admin.helper.ProductColor')->findColorByProduct($product['product_id']);
            $productlist[$key]['color_count'] = count($color_count);
        }
        return array('product_list' => $productlist, 'page_count' => $page_count);
    }

    //$gender,array $id
    public function getProductListByCategory($gender, array $id, $user_id, $page_no = 1)
    {
        $yaml = new Parser();
        $conf = $yaml->parse(file_get_contents('../src/LoveThatFit/WebServiceBundle/Resources/config/products.yml'));
        /* Get all Categies with Layer name */
        $getcategorieswithlayername = $this->container->get('admin.helper.Categories')->getAllCategoriesWithLayerName();

        $records_per_page = $conf['nws_products_list_pagination']['records_per_page'];
        $limit = $records_per_page * $page_no;
        $offset = $limit - $records_per_page;
        $productlist = $this->container->get('webservice.repo')->productListCategory($gender, $id, $user_id);
        $page_count = (int)(count($productlist) / $records_per_page);
        $page_count = (count($productlist) % $records_per_page != 0) ? $page_count + 1 : $page_count;
        if (($page_count != 0 && $page_no < 1) || ($page_count != 0 && $page_no > $page_count)) {
            return $this->response_array(false, 'Invalid Page No');
        }
        $productlist = array_slice($productlist, $offset, $records_per_page);
        foreach ($productlist as $key => $product) {

            /* Selected Layer Name */
            $getselectedcategories = $this->container->get('admin.helper.Categories')->getSelectedCategories($product['product_id']);
            $selectedcategories = array_column($getselectedcategories, 'id');
            $selectedlayername = "";
            foreach($getcategorieswithlayername as $key_withlayer => $value_withlayer){
                if(( (in_array($value_withlayer['id'], $selectedcategories)) && (in_array($value_withlayer['parent_id'], $selectedcategories)) )){
                    $selectedlayername = $value_withlayer['layer_name'];
                    break;
                }

            }
            /* Selected Layer Name */

            if (($productlist[$key]['uf_user'] != null) && ($productlist[$key]['uf_user'] == $user_id)) {
                $productlist[$key]['fitting_room_status'] = true;
                $productlist[$key]['qty'] = $productlist[$key]['uf_qty'];
            } else {
                $productlist[$key]['fitting_room_status'] = false;
                $productlist[$key]['qty'] = 0;
            }

            /* Selected Layer Name */
            if(!empty($selectedlayername)){
                $productlist[$key]['layer_name'] = (int)$selectedlayername;
            }
            /* Selected Layer Name */

            //Color Count
            $color_count = $this->container->get('admin.helper.ProductColor')->findColorByProduct($product['product_id']);
            $productlist[$key]['color_count'] = count($color_count);
        }

        return $this->response_array(true, 'Product List', true, array('product_list' => $productlist, 'page_count' => $page_count));

    }




//*********************************************
// Webservice For 3.0
//**********************************************

    //Method is using Version 3
    public function productDetailWithImages($id, $user)
    {
        $product = $this->container->get('admin.helper.product')->find($id);
        /* Get all Categies with Layer name */
        $getcategorieswithlayername = $this->container->get('admin.helper.Categories')->getAllCategoriesWithLayerName();


        if (count($product) == 0) {
            return $this->response_array(false, 'Product Coming Soon');
        }
        $p = array();
        $default_color_id = $product->getDisplayProductColor()->getId();
        foreach ($product->getProductColors() as $pc) {
            //$pc->getTitle()
            if (count($pc->getProductItems()) > 0) {
                $p['colors'][] = array(
                    'color_id' => $pc->getId(),
                    'product_id' => $product->getId(),
                    'title' => $pc->getTitle(),
                    'image' => $pc->getImage() == null ? 'no-data' : $pc->getImage(),
                    'pattern' => $pc->getPattern() == null ? 'no-data' : $pc->getPattern(),
                    'recommended' => $default_color_id == $pc->getId() ? true : false,
                );
            }
        }

        $algo = new FitAlgorithm2($user, $product);
        $fb = $algo->getStrippedFeedBack();
        $default_item = $algo->getRecommendedFromStrippedFeedBack($fb);
        $p['sizes'] = $fb['feedback'];
        $recommended_product_item = null;
        $favouriteItemIds = $user->getFavouriteItemIdArray();
        foreach ($product->getProductItems() as $pi) {
            $pc_id = $pi->getProductColor()->getId();
            $ps_id = $pi->getProductSize()->getId();
            # get the highest price of all the items/color for a particular size
            $s_desc = $pi->getProductSize()->getBodyType() . ' ' . $pi->getProductSize()->getTitle();
            if($pi->getProductSize()->getDisabled() != 1) {
                if (array_key_exists('price', $p['sizes'][$s_desc])) {
                    $p['sizes'][$s_desc]['price'] = ($pi->getPrice() && $p['sizes'][$s_desc]['price'] < $pi->getPrice()) ? $pi->getPrice() : $p['sizes'][$s_desc]['price'];
                } else {
                    $p['sizes'][$s_desc]['price'] = $pi->getPrice() ? $pi->getPrice() : 0;
                }
            }
            //Added new Array Sizes clone where we will add sizes_clone without Keys, We are doing this because
            //Dont want to change the Algorithem functionalities
            $p['sizes_clone'] = array_values($p['sizes']);
            $fitting_room_status_result = $this->container->get('site.helper.userfittingroomitem')->findByUserItemByProductWithItemId($user->getId(), $product->getId(), $pi->getId());
            $fitting_room_status = false;
            $qty = 0;
            if ($fitting_room_status_result[0][1] != "0") {
                $fitting_room_status = true;
                $qty = $fitting_room_status_result[0]['qty'];
            }

            $width = 0;
            $height = 0;
            if ($pi->getImage() != null) {
                $webpath = str_ireplace('web', 'iphone5', $pi->getWebPath());
                $info = getimagesize($webpath);
                list($width, $height) = $info;
            }

            $p['items'][] = array(
                'item_id' => $pi->getId(),
                'product_id' => $product->getId(),
                'color_id' => $pc_id,
                'size_id' => $ps_id,
                'sku' => $pi->getSku() == null ? 'no' : $pi->getSku(),
                'image' => $pi->getImage() == null ? 'no-data' : $pi->getImage(),
                'width' => (float)$width,
                'height' => (float)$height,
                'recommended' => $default_color_id == $pc_id && $default_item && $default_item['size_id'] == $ps_id ? true : false,
                'price' => $pi->getPrice() ? $pi->getPrice() : 0,
                'favourite' => in_array($pi->getId(), $favouriteItemIds),
                'fitting_room_status' => $fitting_room_status,
                'qty' => $qty,
                'color_image' => $pi->getProductColor()->getImage(),
                'disabled' => $product->getDisabled(),
                'deleted' => $product->getDeleted(),
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

        $p['model_height'] = "Height of model: " . $product->getProductModelHeight();
        $p['description_html'] = $product->getDescription();
        $p['description_html'] = str_ireplace('<li>','<li style="font-family:lato !important;font-size:12px !important;">', $p['description_html']);
        $p['description_html'] = '<span style="font-family:lato !important;font-size:12px !important;">'.$p['description_html'].'</span>';
        $product_description = $product->getDescription();
        $product_description_without_html = preg_replace('#<[^>]+>#', ' ', $product_description);
        $p['description'] = rtrim(ltrim($product_description_without_html));

        $product_items_details = $product->getItemDetails();
        $product_country_origin = $product->getCountryOrigin();
        //Added Country origin under the Product item detail
        if($product_country_origin == ''){
            $p['item_details'] = $product_items_details;
        }else{
            if($product_items_details == ''){
                $p['item_details'] = "<ul><li>".$product_country_origin."</li></ul>";
            }else{
                $p['item_details'] = str_ireplace('</ul>','<li>'.$product_country_origin.'</li></ul>', $product_items_details);
            }
        }


        /* Selected Layer Name */
        $getselectedcategories = $this->container->get('admin.helper.Categories')->getSelectedCategories($product->getId());
        $selectedcategories = array_column($getselectedcategories, 'id');
        $selectedlayername = "";
        foreach($getcategorieswithlayername as $key_withlayer => $value_withlayer){
            if(( (in_array($value_withlayer['id'], $selectedcategories)) && (in_array($value_withlayer['parent_id'], $selectedcategories)) )){
                $selectedlayername = $value_withlayer['layer_name'];
                break;
            }

        }
        /* Selected Layer Name */

        $p['item_details'] = str_ireplace('<li>','<li style="font-family:lato !important;font-size:12px !important;">', $p['item_details']);
        $p['item_details'] = '<span style="font-family:lato !important;font-size:12px !important;">'.$p['item_details'].'</span>';

        $p['care_label'] = $product->getCareLabel();
        $p['care_label'] = str_ireplace('<li>','<li style="font-family:lato !important;font-size:12px !important;">', $p['care_label']);
        $p['care_label'] = '<span style="font-family:lato !important;font-size:12px !important;">'.$p['care_label'].'</span>';
        $p['title'] = $product->getName();
        if(!empty($selectedlayername)){
            $p['layer_name'] = (int)$selectedlayername;
        }
        $p['target'] = $product->getclothingType()->getTarget();
        $p['item_name'] = $product->getItemName();
        $p['disabled'] = $product->getDisabled();
        $p['deleted'] = $product->getDeleted();

        $default_size_fb = array();
        $default_size_fb['feedback'] = FitAlgorithm2::getDefaultSizeFeedback($fb);
        $this->container->get('site.helper.usertryitemhistory')->createUserItemTryHistory($user, $product->getId(), $recommended_product_item, $default_size_fb);
        return $this->response_array(true, "Product Detail ", true, $p);
    }

    public function parseUserSaveLooksData($user_id = 0, $base_path)
    {
        // echo $baseURL = $this->container->getParameter('base_url'); die;
        $responseArray = array();
        $entities = $this->container->get('savelook.helper.savelook')->getLooksByUserId($user_id);

        foreach ($entities as $entity) {
            $items = array();
            $saveLookArray = array();
            $totalPrice = 0;
            $url = $base_path . $entity->getUploadDir();
            $saveLookArray['image'] = $url . "/" . $entity->getUserLookImage();
            $saveLookArray['user_id'] = $entity->getUsers()->getId();
            $saveLookArray['look_id'] = $entity->getId();


            foreach ($entity->getSaveLookItem() as $saveLookItem) {
                $temp['image'] = $saveLookItem->getItems()->getImage();
                $temp['product_id'] = $saveLookItem->getItems()->getProduct()->getId();
                $temp['product_image'] = $saveLookItem->getItems()->getProduct()->getDisplayProductColor()->getImage();
                $temp['title'] = $saveLookItem->getItems()->getProduct()->getName();
                $temp['item_name'] = $saveLookItem->getItems()->getProduct()->getItemName();
                $temp['description'] = $saveLookItem->getItems()->getProduct()->getDescription();
                $temp['item_id'] = $saveLookItem->getItems()->getId();
                $temp['price'] = $saveLookItem->getItems()->getPrice();
                $temp['color_image'] = $saveLookItem->getItems()->getProductColor()->getImage();
                $temp['disabled'] = $saveLookItem->getItems()->getProduct()->getDisabled();
                $temp['deleted'] = $saveLookItem->getItems()->getProduct()->getDeleted();
                $totalPrice = $totalPrice + $saveLookItem->getItems()->getPrice();

                array_push($items, $temp);
            }
            $saveLookArray['items'] = $items;
            $saveLookArray['totalPrice'] = "$" . number_format($totalPrice) . " USD";
            array_push($responseArray, $saveLookArray);
        }

        return $this->response_array(true, "Product Items ", true, $responseArray);
    }

    //Method is using Version 3 - Calling FitAlgo class has been removed.
    public function productDetailWithImagesForFitRoom($id, $product_item, $qty, $user, $fittingRoomId = 0)
    {
        $product = $this->container->get('admin.helper.product')->find($id);

        /* Get all Categies with Layer name */
        $getcategorieswithlayername = $this->container->get('admin.helper.Categories')->getAllCategoriesWithLayerName();

        if (count($product) == 0) {
            return $this->response_array(false, 'Product Coming Soon');
        }
        $p = array();
        $default_color_id = $product->getDisplayProductColor()->getId();
        foreach ($product->getProductColors() as $pc) {
            //$pc->getTitle()
            if (count($pc->getProductItems()) > 0) {
                $p['colors'][] = array(
                    'color_id' => $pc->getId(),
                    'product_id' => $product->getId(),
                    'title' => $pc->getTitle(),
                    'image' => $pc->getImage() == null ? 'no-data' : $pc->getImage(),
                    'pattern' => $pc->getPattern() == null ? 'no-data' : $pc->getPattern(),
                    'recommended' => $default_color_id == $pc->getId() ? true : false,
                );
            }
        }

        $recommended_product_item = null;
        $favouriteItemIds = $user->getFavouriteItemIdArray();
        foreach ($product->getProductItems() as $pi) {
            $pc_id = $pi->getProductColor()->getId();
            $ps_id = $pi->getProductSize()->getId();
            $ps_title = $pi->getProductSize()->getTitle();
            # get the highest price of all the items/color for a particular size
            $s_desc = $pi->getProductSize()->getBodyType() . ' ' . $pi->getProductSize()->getTitle();

            $product_qty = 0;
            if (in_array($pi->getId(), $product_item)) {
                $original_quantity = $this->container->get('site.helper.userfittingroomitem')->findByUserItemByProductWithItemId($user->getId(), $product->getId(), $pi->getId());
                if (count($original_quantity) > 0) {
                    $product_qty = (int)$original_quantity[0]['qty'];
                } else {
                    $product_qty = (int)$product_qty;
                }
            }

            $width = 0;
            $height = 0;
            if ($pi->getImage() != null) {
                $webpath = str_ireplace('web', 'iphone5', $pi->getWebPath());
                $info = getimagesize($webpath);
                list($width, $height) = $info;
            }

            $disabled = $product->getDisabled();
            $status = $product->getStatus();
            if($status != 'complete'){
                $disabled = true;
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
                'height' => (float)$height,
                //'recommended' => $default_color_id == $pc_id && $default_item && $default_item['size_id'] == $ps_id ? true : false,
                'price' => $pi->getPrice() ? $pi->getPrice() : 0,
                'favourite' => in_array($pi->getId(), $favouriteItemIds),
                //'fitting_room_status' => $product_item == $pi->getId() ? true : false,
                'fitting_room_status' => in_array($pi->getId(), $product_item) ? true : false,
                'fitting_room_id' => (in_array($pi->getId(), $product_item) &&  (int) $fittingRoomId > 0) ? (int) $fittingRoomId : 0,
                'qty' => $product_qty,
                'color_image' => $pi->getProductColor()->getImage(),
                'disabled' => $disabled,
                'deleted' => $product->getDeleted(),
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

        $detail_disabled = $product->getDisabled();
        $detail_status = $product->getStatus();
        if($status != 'complete'){
            $detail_disabled = true;
        }
        /* Selected Layer Name */
        $getselectedcategories = $this->container->get('admin.helper.Categories')->getSelectedCategories($product->getId());
        $selectedcategories = array_column($getselectedcategories, 'id');
        $selectedlayername = "";
        foreach($getcategorieswithlayername as $key_withlayer => $value_withlayer){
            if(( (in_array($value_withlayer['id'], $selectedcategories)) && (in_array($value_withlayer['parent_id'], $selectedcategories)) )){
                $selectedlayername = $value_withlayer['layer_name'];
                break;
            }

        }
        /* Selected Layer Name */
        $p['categories'] = $selectedcategories;
        $p['model_height'] = "Height of model: " . $product->getProductModelHeight();
        $p['description'] = $product->getDescription();
        $p['title'] = $product->getName();
        if(!empty($selectedlayername)){
            $p['layer_name'] = (int)$selectedlayername;
        }
        $p['target'] = $product->getclothingType()->getTarget();
        $p['item_name'] = $product->getItemName();
        $p['disabled'] = $detail_disabled;
        $p['deleted'] = $product->getDeleted();
        return $p;
    }

    public function productImageById($product_id)
    {
        $products = $this->container->get('webservice.repo')->productImageById($product_id);
        return $products;
    }

    public function getProductItemWeight( array $userCart){
        $total = 0.00;
        $box_weight = 12.5;
        foreach($userCart['item_id'] as $item_id){
            $itemObject = $this->container->get('admin.helper.productitem')->find($item_id);
            if($itemObject->getWeight() > 0){
                $itemWeight = $itemObject->getWeight();
                $total = $total + $itemWeight;
            }else if($itemObject->getWeight() == null){
                $clothing_type = $itemObject->getProduct()->getClothingType();
                $heaviest_weight = $clothing_type->getHeaviestWeight();

                $total = $total + $heaviest_weight;
            }
        }

        return $total + $box_weight;
    }

    public function registerUserDeviceToken( $deviceToken, User $user){
        $usertokens = json_decode($user->getDeviceTokens());

        if($usertokens != null){
            $userTokenArray = array();
            $deviceTokenFound = 0;
            foreach ($usertokens as $tokens){
                foreach ($tokens as $token){
                    if($deviceToken == $token){
                        return 1;
                    }
                }
            }

            $userTokenArray = $usertokens->iphone;
            array_push($userTokenArray, $deviceToken);
            $userTokenToSave['iphone'] = $userTokenArray;
        }else{
            $userTokenToSave['iphone'] = array(
                $deviceToken
            );
        }


        $user->setDeviceTokens(
            json_encode($userTokenToSave)
        );

        $this->container->get('user.helper.user')->saveUser($user);
        return 0;
    }

    public function getShippintType(){
        $yaml = new Parser();
        $shippmentType  = $yaml->parse(file_get_contents('../src/LoveThatFit/CartBundle/Resources/config/config.yml'))['stamps_com_dev']['shippment'];

        return $shippmentType;
    }

    #--------------Get Product list By Category and Gender -----------------------------------------------------
    public function getBannerBrandProduct($brand_id, $user_id)
    {
        $productlist = $this->container->get('webservice.repo')->productListBrand($brand_id, $user_id);

        foreach ($productlist as $key => $product) {

            //Color Count
            $color_count = $this->container->get('admin.helper.ProductColor')->findColorByProduct($product['product_id']);
            $productlist[$key]['color_count'] = count($color_count);
            $productlist[$key]['fitting_room_status'] = ($product['uf_user'] != null || $product['uf_user'] != "") ? true : false;
        }


        return $this->response_array(true, 'Product List By Brand', true, array('brand_product_list' => $productlist));
    }


    public function getFilterProductList($filter, $user_id)
    {
        return $this->container->get('webservice.repo')->productListBannerFilter($filter, $user_id);
    }

    public function getProductListBannerFilter($filter, $user_id)
    {
        $filtered_products = $this->container->get('webservice.repo')->productListBannerFilter($filter, $user_id);
        foreach ($filtered_products as $keyed => $valprod) {
            if (($valprod['uf_user'] != null) && ($valprod['uf_user'] == $user_id)) {
                $filtered_products[$keyed]['fitting_room_status'] = true;
                $filtered_products[$keyed]['qty'] = $filtered_products[$keyed]['uf_qty'];
            } else {
                $filtered_products[$keyed]['fitting_room_status'] = false;
                $filtered_products[$keyed]['qty'] = 0;
            }

            //Color Count
            $color_count = $this->container->get('admin.helper.ProductColor')->findColorByProduct($valprod['product_id']);
            $filtered_products[$keyed]['color_count'] = count($color_count);
        }
        return $this->response_array(true, 'Product List By Specific Filter', true, array('product_list' => $filtered_products));
    }
    

    public function userDetailMaskMarker($request_array)
    { 

        $yaml   = new Parser();
        $mcp_auth_token = $yaml->parse(file_get_contents('../src/LoveThatFit/WebServiceBundle/Resources/config/mcp_token.yml'))['mcp_token']['token'];

        if($request_array['auth_token']==$mcp_auth_token)
        {
            
        $data = array();
         //$user = $this->container->get('webservice.repo')->userDetailMaskMarker('1355dd07ad8b9ce1075ba919798ffe1f','afaquetest17@test.com');
         //print_r($user);
         //exit;
         $user = $this->container->get('webservice.repo')->userDetailMaskMarker($request_array['email']); 
        if ($user) {



            $device = json_decode($user[0]['image_actions']);
            $measurement = json_decode($user[0]['measurement_json']);
            $markers = json_decode($user[0]['marker_params']);

            //$measurementArchive = $this->get('webservice.helper')->setUserMeasurementWithParams($measurement, $user);
            $bra_size_body_shape = $this->container->get('admin.helper.size')->getWomanBraSizeBodyShape(strtolower($measurement->bra_size),$measurement->body_shape);

           

           


            $mask_x =  '';
            $mask_y =  ''; 

            if(count($markers) > 0)
            {

                 $mask_x =  $markers->mask_x;
                 $mask_y =  $markers->mask_y; 

            }
           


        $data['device'] ['dv_type'] =  $device->device_type;
        $data['device'] ['dv_px_per_inch_ratio'] = "15.29166666666667";  
        $data['device'] ['globle_pivot'] =  "64";
        $data['device'] ['dv_model'] =  $device->device_model;
        if (!empty($user[0]['svg_paths'])) {
            $data['device'] ['dv_edit_type'] = 'edit';
        }else{
            $data['device'] ['dv_edit_type'] = 'registration';     
        }    
        
        $data['device'] ['hdn_serverpath'] = "/";
        $data['device'] ['dv_scr_h'] =  $device->height_per_inch;
        $data['device'] ['dv_scr_w'] =  "960";
        $data['device'] ['dv_scr_h_st'] = "1280"; 

        $data['img'] ['image_actions'] =  json_decode($user[0]['image_actions']);
        $data['img'] ['img_path_json'] =  $user[0]['marker_json'];
        $data['img'] ['img_path_paper'] = $user[0]['svg_paths'];
        //$data['img'] ['hdn_user_cropped_image_url'] = "/uploads/ltf/users/".$user[0]['id']."/original_".$user[0]['image']."?rand=".$user[0]['image'];
        
       
        $data['img'] ['hdn_user_cropped_image_url'] = "//".$_SERVER['HTTP_HOST']."/uploads/ltf/users/".$user[0]['id']."/original_".$user[0]['image']."?rand=".$user[0]['image'];

        $data['img'] ['hdn_image_update_url'] = "//".$_SERVER['HTTP_HOST']."/mcp/save_marker_image";

        $data['img'] ['hdn_user_original_image_url'] = "//".$_SERVER['HTTP_HOST']."/uploads/ltf/users/".$user[0]['id']."/original.png";

         $data['img'] ['hdn_inner_site_index_url'] = "//".$_SERVER['HTTP_HOST']."/inner_site/index";    
         
         $data['img'] ['hdn_post_update_url'] = "//".$_SERVER['HTTP_HOST']."/registration/step_four_create/".$user[0]['id'];       

         $data['img'] ['hdn_entity_id'] = $user[0]['id'];



        
       


        

        $data['user'] ['user_height_frm_3'] = $measurement->height; 
        $data['user'] ['user_auth_token'] =  $user[0]['authToken'];
        $data['user'] ['dm_body_parts_details_json'] = $bra_size_body_shape; 
        $data['user'] ['default_user_path'] =  $user[0]['svg_paths'];
        $data['user'] ['user_hip_px'] =  "424";
        $data['user'] ['user_bust_px'] = "392";
        $data['user'] ['user_waist_px'] = "319"; 
        $data['user'] ['default_user_mask_height_px'] = "430"; 
        $data['user'] ['head_percent'] = "12"; 
        $data['user'] ['gender'] =  $user[0]['gender']; 

        $data['user'] ['neck_percent'] =  "4";
        $data['user'] ['torso_percent'] = "42";
        $data['user'] ['inseam_percent'] = "42"; 
        $data['user'] ['arm_percent'] =  "46";

        $data['marker'] ['marker_update_url'] =  "//".$_SERVER['HTTP_HOST']."/mcp/save_marker";
        $data['marker'] ['default_marker_json'] =  $user[0]['default_marker_json'];
        $data['marker'] ['default_marker_svg'] =  $user[0]['default_marker_svg'];

        $data['ids'] ['hdn_archive_id'] = $user[0]['archive_id']; 
       

       $data['mask'] ['mask_x'] =  $mask_x;
       $data['mask'] ['mask_y'] =  $mask_y; 

       //print_r($data);
       //exit;


       



        
   
           

            return $this->response_array(true, 'member found', true, $data);
        } else {
            return $this->response_array(false, 'Member not found');
        }

      }else {

        return $this->response_array(false, 'Member not found');
      }        
    }
    
    
    public function userOriginalImage($request_array)
    {
        $yaml   = new Parser();
        $mcp_auth_token = $yaml->parse(file_get_contents('../src/LoveThatFit/WebServiceBundle/Resources/config/mcp_token.yml'))['mcp_token']['token'];
      
        if($request_array['mcp_auth_token']==$mcp_auth_token){
        $data = array();
        $user = $this->container->get('webservice.repo')->userDetailMaskMarker($request_array['email']); 
            if ($user) {
                $data['img'] ['hdn_user_cropped_image_url'] = "//".$_SERVER['HTTP_HOST']."/uploads/ltf/users/".$user[0]['id']."/original_".$user[0]['image']."?rand=".$user[0]['image'];
                $data['img'] ['hdn_user_original_image_url'] = "//".$_SERVER['HTTP_HOST']."/uploads/ltf/users/".$user[0]['id']."/original.png";
                return $this->response_array(true, 'member found', true, $data);
            } else {
                return $this->response_array(false, 'Member not found');
            }
          } else {
            return $this->response_array(false, 'Member not found');
          }         
     }

    public function getProductListByBrand($search_text, $user_id, $page_no = 1)
    {
        $yaml = new Parser();
        $conf = $yaml->parse(file_get_contents('../src/LoveThatFit/WebServiceBundle/Resources/config/products.yml'));
        $records_per_page = $conf['nws_products_list_pagination']['records_per_page'];
        $limit = $records_per_page * $page_no;
        $offset = $limit - $records_per_page;

        /* Search text on brand to fetch products */
        $productlist = $this->container->get('webservice.repo')->getProductListByBrand($search_text, $user_id);

        /* Search text on Products style name */
        if(count($productlist) == 0 ){
            $productlist = $this->container->get('webservice.repo')->getProductListByStyleText($search_text, $user_id);
        }

        $page_count = (int)(count($productlist) / $records_per_page);
        $page_count = (count($productlist) % $records_per_page != 0) ? $page_count + 1 : $page_count;
        if (($page_count != 0 && $page_no < 1) || ($page_count != 0 && $page_no > $page_count)) {
            return $this->response_array(false, 'Invalid Page No');
        }
        $productlist = array_slice($productlist, $offset, $records_per_page);
        foreach ($productlist as $key => $product) {
            if (($productlist[$key]['uf_user'] != null) && ($productlist[$key]['uf_user'] == $user_id)) {
                $productlist[$key]['fitting_room_status'] = true;
                $productlist[$key]['qty'] = $productlist[$key]['uf_qty'];
            } else {
                $productlist[$key]['fitting_room_status'] = false;
                $productlist[$key]['qty'] = 0;
            }

            //Color Count
            $color_count = $this->container->get('admin.helper.ProductColor')->findColorByProduct($product['product_id']);
            $productlist[$key]['color_count'] = count($color_count);
        }


        $data = array('product_list' => $productlist, 'page_count' => $page_count);
        $ar = array(
            'data' => $data,
            'count' => $data['product_list'] ? count($data['product_list']) : 0,
            'message' => $data['product_list'] ? 'Product List' : 'Uh oh! There are no items for this brand currently in stock. Tap here to go back to shop',
            'success' => true,
        );
        return json_encode($ar);
    }

    public function getOrderSalesTaxUserAction($callby=0,$decoded) {
        //$decoded = $this->processRequest($this->getRequest());
        $user    = array_key_exists('auth_token', $decoded) ? $this->findUserByAuthToken($decoded['auth_token']) : null;
        if ($user) {
            if(isset($decoded['billing_address']) && !empty($decoded['billing_address'])) {
                //user cart items
                $amount = 0;
                $user_cart = $this->container->get('cart.helper.cart')->getUserCart($user);
                $order_line_items = array();
                $c=0;
                foreach($user_cart as $cart){
                    $amount = ($cart['qty']*$cart['price']) + $amount;
                    $order_line_items[$c]['qty'] = $cart['qty'];
                    $order_line_items[$c]['price'] = ($cart['qty']*$cart['price']);
                    $c++;
                }

                //order line items
                //$order_line_items = [['quantity' => 1,'unit_price' => 15.0],['quantity' => 1,'unit_price' => 10.0]];

                //get fnf user discount if exists
                $fnfUser = $this->container->get('fnfuser.helper.fnfuser')->getApplicableFNFUser($user);
                if(is_array($fnfUser)){
                    if( $fnfUser['group_type'] == 1 ){
                        $amount = $amount - $fnfUser['discount'];
                    } else if( $fnfUser['group_type'] == 2 ){                        
                        $discount_amount = (string) $this->getUserDiscountAmount($fnfUser['discount'], $fnfUser['token']);
                        $amount = $amount - $discount_amount;
                    }
                }
                
                //order salex tax
                $yaml = new Parser();
                $parse = $yaml->parse(file_get_contents('../src/LoveThatFit/CartBundle/Resources/config/config.yml'));
                //$from_country = $parse["stamps_com_dev"]["fromcountry"];
                //$from_zip = $parse["stamps_com_dev"]["fromzipcode"];
                //$from_state = $parse["stamps_com_dev"]["fromstate"];
                //$shippment = $parse["stamps_com_dev"]["shippment"];
                //$from_country = 'US';
                //$from_zip = '53202';
                //$from_state = 'WI';
                $shippment = 0;
                $data_order_sales = array(
                    //'from_country' => ($from_country) ? $from_country : '',
                    //'from_zip' => ($from_zip) ? $from_zip : '',
                    //'from_state' => ($from_state) ? $from_state : '',
                    'to_country' => ($decoded['billing_address']['to_country']) ? $decoded['billing_address']['to_country'] : '',
                    'to_zip' => ($decoded['billing_address']['to_zip']) ? $decoded['billing_address']['to_zip'] : '',
                    'to_state' => ($decoded['billing_address']['to_state']) ? $decoded['billing_address']['to_state'] : '',
                    'amount' => ($amount) ? number_format((float)$amount, 2, '.', '') : '0.00',
                    'shipping' => $shippment/*,
                    'order_line_items' => $order_line_items*/
                );
                
                $order_sales_tax = $this->container->get('taxjar.helper.salestaxapi')->createOrderSalesTax($data_order_sales);
                if(is_float($order_sales_tax) || is_numeric($order_sales_tax)) {
                    $order_sales_tax = $order_sales_tax;
                } else {
                    $taxjar_error_messages = array(400 => 'Please enter a valid zip code.', 
                                        401 => 'Unauthorized – Your API key is wrong.', 
                                        403 => 'Forbidden – The resource requested is not authorized for use.',
                                        404 => 'Not Found – The specified resource could not be found.',
                                        405 => 'Method Not Allowed – You tried to access a resource with an invalid method.',
                                        406 => 'Not Acceptable – Your request is not acceptable.',
                                        410 => 'Gone – The resource requested has been removed from our servers.',
                                        422 => 'Unprocessable Entity – Your request could not be processed.',
                                        429 => 'Too Many Requests – You’re requesting too many resources! Slow down!',
                                        500 => 'Internal Server Error – We had a problem with our server. Try again later.',
                                        503 => 'Service Unavailable – We’re temporarily offline for maintenance. Try again later.');

                    return $this->response_array(false, ''.htmlspecialchars($taxjar_error_messages[$order_sales_tax['error_code']]).'', true, array(
                        'sales_tax' => 0,
                        'error' => ''.htmlspecialchars($order_sales_tax['error_message']).'',
                        'code' => $order_sales_tax['error_code']
                    ));
                }

                if($callby == 1){
                    return $order_sales_tax;
                }

                return $this->response_array(true, 'Order sales tax', true, array(
                    'sales_tax' => $order_sales_tax
                ));
            } else {
                if($callby == 1){
                    return 0;
                }
                return $this->response_array(true, 'Order sales tax', true, array(
                    'sales_tax' => 0
                ));
            }
        } else {
            return $this->response_array(false, 'User not authenticated.');
        }
    }


    #------------------------ User -----------------------
    public function registrationWithDefaultValuesSupportWeb($request_array)
    {
        if (!array_key_exists('email', $request_array)) {
            return $this->response_array(false, 'Email Not provided.');
        }

        $user = $this->container->get('user.helper.user')->findByEmail($request_array['email']);

        if (count($user) > 0) {
            return $this->response_array(false, 'Email already exists.');
        } else {
            $user = $this->createUserWithParamsForWeb($request_array);
            #--- 3) default user values added
            $measurement = $this->container->get('user.helper.user')->copyDefaultUserDataSupport($user, $request_array);

            $user = $this->container->get('user.helper.user')->findByEmail($request_array['email']);

            ##email not send if the event is available against user
            if (!array_key_exists("event_name", $request_array)) {
                #---- 2) send registration email ....
                $this->container->get('mail_helper')->sendWebRegistrationEmail($user);
            }

            try {
                //create podio users entity
                $this->createPodioUser($user->getId());
            } catch(\Exception $e) {
                // log $e->getMessage()
            }

            #$detail_array = $user->toDataArray(true, $request_array['device_type'], $request_array['base_path']);
            $detail_array = $this->user_array($user, $request_array);

            try{
                $logObject = $this->container->get('userlog.helper.userlog')->logUserLoginTime($user, $request_array);
                $detail_array['sessionId'] = (is_object($logObject)) ? $logObject->getSessionId() : null;
                $detail_array['image_path'] = "/render/image/";
                $detail_array['avatar_path'] = "/render/avatar/";

            }catch (Exception $e){}

            unset($detail_array['per_inch_pixel_height']);
            unset($detail_array['deviceType']);
            unset($detail_array['auth_token_web_service']);
            return $this->response_array(true, 'User created', true, array('user' => $detail_array));
        }
    }


    #-------------------------------------------------------

    private function createUserWithParamsForWeb($request_array)
    {

        $user = $this->setUserWithParamsForWeb($this->container->get('user.helper.user')->createNewUser(), $request_array);
        if (isset($request_array['imc']) && $request_array['imc'] == "true") {
            $user->setVersion(1);
        } else {
            $user->setVersion(0);
        }
        $user->setPassword($request_array['password']);
        $user = $this->container->get('user.helper.user')->getPasswordEncoded($user);
        $user->generateAuthenticationToken();

        $this->container->get('user.helper.user')->saveUser($user);
        return $user;
    }

    #-------------------------------------------------------

    private function setUserWithParamsForWeb($user, $request_array)
    {
        array_key_exists('email', $request_array) ? $user->setEmail($request_array['email']) : null;
        array_key_exists('gender', $request_array) ? $user->setGender($request_array['gender']) : null;
        array_key_exists('zipcode', $request_array) ? $user->setZipcode($request_array['zipcode']) : null;
        array_key_exists('first_name', $request_array) ? $user->setFirstName($request_array['first_name']) : null;
        array_key_exists('last_name', $request_array) ? $user->setLastName($request_array['last_name']) : null;
        array_key_exists('release_name', $request_array) ? $user->setReleaseName($request_array['release_name']) : null;
        array_key_exists('event_name', $request_array) ? $user->setEventName($request_array['event_name']) : null;
        array_key_exists('friend_name', $request_array) ? $user->setFriendName($request_array['friend_name']) : null;
        array_key_exists('friend_email', $request_array) ? $user->setFriendEmail($request_array['friend_email']) : null;

        if (array_key_exists('device_token', $request_array) && array_key_exists('device_type', $request_array)) {
            $user->addDeviceToken($request_array['device_type'], $request_array['device_token']);
        }

        #this dob line will be removed with the new build
        $user->setBirthDate(array_key_exists('dob', $request_array) ? new \DateTime($request_array['dob']) : null);
        array_key_exists('birth_date', $request_array) ? $user->setBirthDate(new \DateTime($request_array['birth_date'])) : null;

        array_key_exists('phone_number', $request_array) ? $user->setPhoneNumber($request_array['phone_number']) : null;

        return $user;
    }

    public function getUserDiscountAmount( $discount, $token)
    {
        $amount = 0;
        $user = $this->container->get('webservice.helper')->findUserByAuthToken($token);
        $user_cart = $this->container->get('cart.helper.cart')->getUserCart($user);
        foreach($user_cart as $cart){
            //$amount = $cart['price'] + $amount;
            $amount = ($cart['qty']*$cart['price']) + $amount;
        }

        $dicount_amount = ($discount / 100) * $amount;

        return $dicount_amount;
    }

}