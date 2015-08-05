<?php

namespace LoveThatFit\WebServiceBundle\Entity;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class WebServiceHelper {

    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    #------------------------ User -----------------------

    public function loginService($request_array) {
        $user = $this->container->get('user.helper.user')->findByEmail($request_array['email']);
        if (count($user) > 0) {
            if ($this->container->get('webservice.helper.user')->matchPassword($user, $request_array['password'])) {
                $response_array = null;
                if (array_key_exists('user_detail', $request_array) && $request_array['user_detail'] == 'true') {
                    $response_array['user'] = $user->toDataArray(true, $request_array['deviceType']);
                    $response_array['user']['user_id'] = $response_array['user']['id'];
                    unset($response_array['user']['id']);
                }
                if (array_key_exists('retailer_brand', $request_array) && $request_array['retailer_brand'] == 'true') {
                    $retailer_brands = $this->container->get('admin.helper.brand')->getBrandListForService();
                    $response_array['retailer'] = $retailer_brands['retailer'];
                    $response_array['brand'] = $retailer_brands['brand'];
                }

                return $this->response_array(true, 'user found', true, $response_array);
            } else {
                return $this->response_array(false, 'Invalid Password');
            }
        } else {
            return $this->response_array(false, 'Invalid Email');
        }
    }
    #------------------------ User -----------------------

    public function registrationService($request_array) {
        
        if (!array_key_exists('email', $request_array)) {
            return $this->response_array(false, 'Email Not provided.');
        }
        
        $user = $this->container->get('user.helper.user')->findByEmail($request_array['email']);
        
        if (count($user) > 0) {
            #$measurement=$this->createUserMeasurementWithParams($request_array,$user);
            #return $this->response_array(true, 'moka', true, $measurement->getCompleteArray());
            return $this->response_array(false, 'Email already exists.');
        } else {
            #--- 1) User
            $user = $this->createUserWithParams($request_array);
            #---- 2) send registration email ....            
            # $this->container->get('mail_helper')->sendRegistrationEmail($user);                    
            #--- 3) Size charts
                #size charts brands & size being saved
                //sizecharts measurement extraction
            #--- 4) Measurement
            $measurement=$this->createUserMeasurementWithParams($request_array,$user);
            
            #--- 5) Device
            $user_device=$this->createUserDeviceWithParams($request_array,$user);
            
            #$user->setBirthDate(array_key_exists('dob', $request_array)?new \DateTime($request_array['dob']):null);
            #$user->setDeviceType(array_key_exists('deviceType', $request_array)?$request_array['deviceType']:null);
       
            return $this->response_array(true, 'Proceed', true, array('user'=>$user->toArray(true)));
        }
    }
    private function createUserWithParams($request_array){
            $user = $this->container->get('user.helper.user')->createNewUser();
            $user->setEmail($request_array['email']);
            $user->setPassword($request_array['password']);
            $user->setGender(array_key_exists('gender', $request_array)?$request_array['gender']:null);    
            $user->setZipcode(array_key_exists('zipcode', $request_array)?$request_array['zipcode']:null);
            $user=$this->container->get('user.helper.user')->getPasswordEncoded($user);
            $user->generateAuthenticationToken();
            $this->container->get('user.helper.user')->saveUser($user);             
            return $user;
    }
    
    private function createUserDeviceWithParams($request_array, $user){            
            $userDevice= $this->container->get('user.helper.userdevices')->createNew($user);
            $userDevice->setDeviceName($request_array['device_id']);
            $userDevice->setDeviceType($request_array['device_type']);
            $userDevice->setDeviceUserPerInchPixelHeight(7); #default value 7            
            $this->container->get('user.helper.userdevices')->saveUserDevices($userDevice);
            return $userDevice;
    }
    
    private function createUserMeasurementWithParams($request_array, $user) {
        if($user->getMeasurement()){
            $measurement = $user->getMeasurement();
        }else{
            $measurement = $this->container->get('user.helper.measurement')->createNew($user);
        }
        
        $measurement->setWeight(array_key_exists('weight', $request_array) ? $request_array['weight'] : $measurement->getWeight());
        $measurement->setHeight(array_key_exists('height', $request_array) ? $request_array['height'] : $measurement->getHeight());
        $measurement->setWaist(array_key_exists('waist', $request_array) ? $request_array['waist'] : $measurement->getWaist());
        $measurement->setHip(array_key_exists('hip', $request_array) ? $request_array['hip'] : $measurement->getHip());
        $measurement->setBust(array_key_exists('bust', $request_array) ? $request_array['bust'] : $measurement->getBust());
        $measurement->setInseam(array_key_exists('inseam', $request_array) ? $request_array['inseam'] : $measurement->getInseam());
        $measurement->setChest(array_key_exists('chest', $request_array) ? $request_array['chest'] : $measurement->getChest());
        $measurement->setNeck(array_key_exists('neck', $request_array) ? $request_array['neck'] : $measurement->getNeck());
        $measurement->setBodyTypes(array_key_exists('body_type', $request_array) ? $request_array['body_type'] : $measurement->getBodyTypes());
        $measurement->setBodyShape(array_key_exists('body_shape', $request_array) ? $request_array['body_shape'] : $measurement->getBodyShape());
        $measurement->setBraSize(array_key_exists('bra_size', $request_array) ? $request_array['bra_size'] : $measurement->getBraSize());
        $measurement->setThigh(array_key_exists('thigh', $request_array) ? $request_array['thigh'] : $measurement->getThigh());
        $measurement->setCenterFrontWaist(array_key_exists('center_front_waist', $request_array) ? $request_array['center_front_waist'] : $measurement->getCenterFrontWaist());
        $measurement->setBackWaist(array_key_exists('back_waist', $request_array) ? $request_array['back_waist'] : $measurement->getBackWaist());
        $measurement->setShoulderAcrossFront(array_key_exists('shoulder_across_front', $request_array) ? $request_array['shoulder_across_front'] : $measurement->getShoulderAcrossFront());
        $measurement->setShoulderAcrossBack(array_key_exists('shoulder_across_back', $request_array) ? $request_array['shoulder_across_back'] : $measurement->getShoulderAcrossBack());
        $measurement->setSleeve(array_key_exists('sleeve', $request_array) ? $request_array['sleeve'] : $measurement->getSleeve());
        $measurement->setBicep(array_key_exists('bicep', $request_array) ? $request_array['bicep'] : $measurement->getBicep());
        $measurement->setTricep(array_key_exists('tricep', $request_array) ? $request_array['tricep'] : $measurement->getTricep());
        $measurement->setWrist(array_key_exists('wrist', $request_array) ? $request_array['wrist'] : $measurement->getWrist());
        $measurement->setShoulderWidth(array_key_exists('shoulder_width', $request_array) ? $request_array['shoulder_width'] : $measurement->getShoulderWidth());
        $measurement->setBustHeight(array_key_exists('bust_height', $request_array) ? $request_array['bust_height'] : $measurement->getBustHeight());
        $measurement->setWaistHeight(array_key_exists('waist_height', $request_array) ? $request_array['waist_height'] : $measurement->getWaistHeight());
        $measurement->setHipHeight(array_key_exists('hip_height', $request_array) ? $request_array['hip_height'] : $measurement->getHipHeight());
        $measurement->setBustWidth(array_key_exists('bust_width', $request_array) ? $request_array['bust_width'] : $measurement->getBustWidth());
        $measurement->setWaistWidth(array_key_exists('waist_width', $request_array) ? $request_array['waist_width'] : $measurement->getWaistWidth());
        $measurement->setHipWidth(array_key_exists('hip_width', $request_array) ? $request_array['hipt_width'] : $measurement->getHipWidth());
        $measurement->setWaistHip(array_key_exists('waist_hip', $request_array) ? $request_array['waist_hip'] : $measurement->getWaistHip());
        $measurement->setKnee(array_key_exists('knee', $request_array) ? $request_array['knee'] : $measurement->getKnee());
        $measurement->setCalf(array_key_exists('calf', $request_array) ? $request_array['calf'] : $measurement->getCalf());
        $measurement->setAnkle(array_key_exists('ankle', $request_array) ? $request_array['ankle'] : $measurement->getAnkle());
        $measurement->setIphoneFootHeight(array_key_exists('iphone_foot_height', $request_array) ? $request_array['iphone_foot_height'] : $measurement->getIphoneFootHeight());
        $measurement=$this->setSizeChartToUserMeasurement($measurement, $request_array);
        
        $ar['manual']=$measurement->getArray();
        $ar['size_charts']=$this->container->get('admin.helper.sizechart')->measurementFromSizeCharts($measurement);
        $measurement->setMeasurementJson(json_encode($ar));
        $measurement = $this->container->get('admin.helper.sizechart')->evaluateWithSizeChart($measurement);
            
        $this->container->get('user.helper.measurement')->saveMeasurement($measurement);
        return $measurement;
    }
    #---------------------------------------------------------------------
    private function setSizeChartToUserMeasurement($measurement, $request_array) {
        $gender = $request_array['gender'];
        $body_type = isset($request_array['bodyType']) ? $request_array['bodyType'] : 'regular';

        if (isset($request_array['top_brand'])) {
            $top_brand = $this->container->get('admin.helper.brand')->findOneByName($request_array['top_brand']);
            if ($top_brand) {
                $measurement->setTopBrand($top_brand);
                $top_size_chart = $this->container->get('admin.helper.sizechart')->findOneByMatchingParams($request_array['top_fitting_size'], $request_array['top_brand'], $gender, $body_type, 'top');
                if ($top_size_chart) {
                    $measurement->setTopFittingSizeChart($top_size_chart);
                }
            }
        }
        if (isset($request_array['bottom_brand'])) {
            $bottom_brand = $this->container->get('admin.helper.brand')->findOneByName($request_array['bottom_brand']);
            if ($bottom_brand) {
                $measurement->setBottomBrand($bottom_brand);
                $bottom_size_chart = $this->container->get('admin.helper.sizechart')->findOneByMatchingParams($request_array['bottom_fitting_size'], $request_array['bottom_brand'], $gender, $body_type, 'bottom');
                if ($bottom_size_chart) {
                    $measurement->setBottomFittingSizeChart($bottom_size_chart);
                }
            }
        }
        if (isset($request_array['dress_brand'])) {
            $dress_brand = $this->container->get('admin.helper.brand')->findOneByName($request_array['dress_brand']);
            if ($dress_brand) {
                $measurement->setDressBrand($dress_brand);
                $dress_size_chart = $this->container->get('admin.helper.sizechart')->findOneByMatchingParams($request_array['dress_fitting_size'], $request_array['dress_brand'], $gender, $body_type, 'dress');
                if ($dress_size_chart) {
                    $measurement->setDressFittingSizeChart($dress_size_chart);
                }
            }
        }
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
    
    public function sizeChartsService($request_array) {
        $sc = $this->container->get('admin.helper.sizechart')->getBrandSizeTitleArrayByGender($request_array['gender']);
        if (count($sc) > 0) {
                return $this->response_array(true, 'Size charts', true, array('size_charts'=>$sc));
        } else {
            return $this->response_array(false, 'Size Charts not found');            
        }
    }
     #----------------------------------------------------------------------------------------
    
    public function uploadUserImage($user, $request_array, $files) {
        if ($user) {
            $file_name = $files["image"]["name"];
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $newFilename = 'original' . "." . $ext;
            $user->setImage($newFilename);
            if (!is_dir($user->getUploadRootDir())) {
                @mkdir($user->getUploadRootDir(), 0700);
            }
            if (move_uploaded_file($files["image"]["tmp_name"], $user->getAbsolutePath())) {
                $this->container->get('webservice.helper.user')->setMarkingDeviceType($user, $request_array['device_type'], $request_array['pixel_per_inch']);
                $this->container->get('user.helper.userdevices')->updateDeviceDetails($user, $request_array['device_type'], $request_array['pixel_per_inch']);
                $this->container->get('user.helper.user')->saveUser($user);
                $userinfo = array();
                $userinfo['user'] = $user->toDataArray(true, $request_array['device_type']);
                $userinfo['user']['path']=$request_array['base_path'];                
                
                return $this->response_array(true, 'User Image Uploaded', true, $userinfo);
            } else {                
                return $this->response_array(false, 'Image not uploaded');
            }
        } else {
            return $this->response_array(false, 'user not found');
        }
        
    }
    #------------------------------------------------------------------------------
    
    public function productSync($user, $date=null) {        
        $products = $this->container->get('admin.helper.product')->productSync($user->getGender(),$date);
        return $this->response_array(true,"products list",true,$products);        
    }
    
     #--------------------------------------------------------------------
     
     public function processRequest($request){         
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $decoded = json_decode($jsonInput, true);
        
        if($decoded==null) #if null (to be used for web service testing))
            $decoded  = $request->request->all();
        
        return $decoded;
    }
    #-------------------------------------------------------------
    
     public function findUserByAuthToken($token) {        
        return $this->get('user.helper.user')->findByAuthToken($token);               
    }
}