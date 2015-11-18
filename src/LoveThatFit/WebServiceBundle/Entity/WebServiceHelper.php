<?php

namespace LoveThatFit\WebServiceBundle\Entity;
use LoveThatFit\SiteBundle\DependencyInjection\FitAlgorithm2;
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
                    $response_array['user'] = $user->toDataArray(true, $request_array['device_type'], $request_array['base_path']);
                }
                if (array_key_exists('retailer_brand', $request_array) && $request_array['retailer_brand'] == 'true') {
                    $retailer_brands = $this->container->get('admin.helper.brand')->getBrandListForService();
                    $response_array['retailer'] = $retailer_brands['retailer'];
                    $response_array['brand'] = $retailer_brands['brand'];
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
            $data['user'] = $user->toDataArray(true, $request_array['device_type'], $request_array['base_path']);            
            return $this->response_array(true, 'member found', true, $data);
        } else {
            return $this->response_array(false, 'Member not found');
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
            $measurement = $this->createUserMeasurementWithParams($request_array, $user);

            #--- 5) Device
            $user_device = $this->createUserDeviceWithParams($request_array, $user);
            $detail_array = array_merge($user->toArray(true, $request_array['base_path'] ), $measurement->toArray(), $user_device->toArray());            
            unset($detail_array['per_inch_pixel_height']);
            unset($detail_array['deviceType']);
            unset($detail_array['auth_token_web_service']);
            return $this->response_array(true, 'User created', true, array('user' => $detail_array));
        }
    }
  #------------------------ measurementUpdate -----------------------

    public function measurementUpdate($ra) {
        $user = $this->findUserByAuthToken($ra['auth_token']);
        $measurement = $this->setUserMeasurementWithParams($ra, $user);
        $this->container->get('user.helper.measurement')->saveMeasurement($measurement);
        return $this->response_array(true, 'measurement updated', true, array('user' => $user->toDataArray(true,null, $ra['base_path'])));        
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

    private function merge_objects_to_array($objs) {
        $ar = array();

        if (array_key_exists('user', $objs)) {
            $ar = array_merge($ar, $objs['user']);
            unset($ar['auth_token_web_service']);
        } elseif (array_key_exists('measurement', $objs)) {
            $ar = array_merge($ar, $objs['measurement']);
        } elseif (array_key_exists('device', $objs)) {
            $ar = array_merge($ar, $objs['device']);
            unset($ar['per_inch_pixel_height']);
            unset($ar['deviceType']);
        }
        return $ar;
    }

    #-------------------------------------------------------

    private function createUserWithParams($request_array) {
        $user = $this->container->get('user.helper.user')->createNewUser();
        $user->setEmail($request_array['email']);
        $user->setPassword($request_array['password']);
        $user->setGender(array_key_exists('gender', $request_array) ? $request_array['gender'] : null);
        $user->setZipcode(array_key_exists('zipcode', $request_array) ? $request_array['zipcode'] : null);
        $user->setBirthDate(array_key_exists('dob', $request_array) ? new \DateTime($request_array['dob']) : null);
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
        array_key_exists('dob', $request_array) ? $user->setBirthDate(new \DateTime($request_array['dob'])) :  null;                        
        return $user;
    }

#-------------------------------------------------------
    private function createUserDeviceWithParams($request_array, $user) {
        $userDevice = $this->container->get('user.helper.userdevices')->createNew($user);
        $userDevice->setDeviceName($request_array['device_id']);
        $userDevice->setDeviceType($request_array['device_type']);
        $px_height=$request_array['device_type']=='iphone5'?6.891:7.797;
        $userDevice->setDeviceUserPerInchPixelHeight($px_height); #default value 7            
        $this->container->get('user.helper.userdevices')->saveUserDevices($userDevice);
        return $userDevice;
    }
#-------------------------------------------------------
    private function createUserMeasurementWithParams($request_array, $user) {
        if ($user->getMeasurement()) {
            $measurement = $user->getMeasurement();
        } else {
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
        
        #*Since size charts has been removed from Registration process
        #1* #$measurement = $this->setSizeChartToUserMeasurement($measurement, $request_array);

        $ar['manual'] = $measurement->getArray();
        #21* #$ar['size_charts'] = $this->container->get('admin.helper.sizechart')->measurementFromSizeCharts($measurement);
        $measurement->setMeasurementJson(json_encode($ar));
        #3* #$measurement = $this->container->get('admin.helper.sizechart')->evaluateWithSizeChart($measurement);
        # calculating shoulder_across_back & buse measurement
        $this->setBraRelatedMeasurements($measurement);
        $this->container->get('user.helper.measurement')->saveMeasurement($measurement);
        return $measurement;
    }
    #-------------------------------------------------------
    private function setBraRelatedMeasurements($measurement){
        $bra_specs=$this->container->get('admin.helper.size')->getWomanBraSpecs($measurement->getBraSize());
        if($bra_specs){
            $measurement->setBust($bra_specs['average']);
            $measurement->setShoulderAcrossBack($bra_specs['shoulder_across_back']);
        }
    }
#-------------------------------------------------------
    private function setUserMeasurementWithParams($request_array, $user) {
        $measurement = $user->getMeasurement();
        if (!$measurement) {
            $measurement = $this->container->get('user.helper.measurement')->createNew($user);
        }

        
        array_key_exists('bust', $request_array) ? $measurement->setBust($request_array['bust']) : '';        
        if (array_key_exists('bra_size', $request_array)) {
            $measurement->setBraSize(trim($request_array['bra_size']));
            #if bust measurement is manually provided, it will still prefers the value
            #calculated from bra-size
            $this->setBraRelatedMeasurements($measurement);
        }
        
        #shoulder_across_back value if manually provided will be prefered over
        #value calculated from bra-size
        array_key_exists('shoulder_across_back', $request_array) ? $measurement->setShoulderAcrossBack($request_array['shoulder_across_back']) : '';
        
        array_key_exists('body_type', $request_array) ? $measurement->setBodyTypes($request_array['body_type']) : '';
        array_key_exists('body_shape', $request_array) ? $measurement->setBodyShape($request_array['body_shape']) : '';
        array_key_exists('weight', $request_array) ? $measurement->setWeight($request_array['weight']) : '';
        array_key_exists('height', $request_array) ? $measurement->setHeight($request_array['height']) : '';
        array_key_exists('neck', $request_array) ? $measurement->setNeck($request_array['neck']) : '';
        array_key_exists('shoulder_across_front', $request_array) ? $measurement->setShoulderAcrossFront($request_array['shoulder_across_front']) : '';
        array_key_exists('shoulder_height', $request_array) ? $measurement->setShoulderHeight($request_array['shoulder_height']) : '';
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
        array_key_exists('hip_height', $request_array) ? $measurement->setHipHeight($request_array['hip_height']) : '';
        array_key_exists('knee', $request_array) ? $measurement->setKnee($request_array['knee']) : '';
        array_key_exists('calf', $request_array) ? $measurement->setCalf($request_array['calf']) : '';
        array_key_exists('ankle', $request_array) ? $measurement->setAnkle($request_array['ankle']) : '';
        array_key_exists('iphone_foot_height', $request_array) ? $measurement->setIphoneFootHeight($request_array['iphone_foot_height']) : '';

        #$ar = json_decode($measurement->getMeasurementJson());
        #$ar['manual'] = $measurement->getArray();
        #$measurement->setMeasurementJson(json_encode($ar));

        return $measurement;
    }

#*Since size charts has been removed from Registration process
    #---------------------------------------------------------------------
/*
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
*/
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
            return $this->response_array(true, 'Size charts', true, array('size_charts' => $sc));
        } else {
            return $this->response_array(false, 'Size Charts not found');
        }
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

                if (move_uploaded_file($files["image"]["tmp_name"], $user->getOriginalImageAbsolutePath())) {
                    $this->container->get('user.helper.userdevices')->updateDeviceDetails($user, $ra['device_type'], $ra['height_per_inch']);
                    copy($user->getOriginalImageAbsolutePath(), $user->getAbsolutePath());
                } else {
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
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $user->getUploadRootDir().'/'.$random_name)) {                
                    return $this->response_array(true, 'Image uploaded',true, $ra['base_path'] . $random_name);
                } else {                
                    return $this->response_array(false, 'Image not uploaded');
                }                
                
            } else {#~~~~~~~~~~~~~> anyother image type
                return $this->response_array(false, 'invalid upload type');
            }

            $this->container->get('user.helper.user')->saveUser($user);
            $userinfo = array();
            $userinfo['user'] = $user->toDataArray(true, $ra['device_type'], $ra['base_path']);            
            return $this->response_array(true, 'User Image Uploaded', true, $userinfo);
        } else {
            return $this->response_array(false, 'member not found');
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
        $default_item = $algo->getRecommendedFromStrippedFeedBack($fb);
        $p['sizes'] = $fb['feedback'];
        $recommended_product_item = null;
        foreach ($product->getProductItems() as $pi) {
            $pc_id = $pi->getProductColor()->getId();
            $ps_id = $pi->getProductSize()->getId();

            $p['items'][$pi->getId()] = array(
                'item_id' => $pi->getId(),
                'product_id' => $product->getId(),
                'color_id' => $pc_id,
                'size_id' => $ps_id,
                'sku' => $pi->getSku() == null ? 'no' : $pi->getSku(),
                'image' => $pi->getImage() == null ? 'no-data' : $pi->getImage(),
                'recommended' => $default_color_id == $pc_id && $default_item && $default_item['size_id'] == $ps_id ? true : false,
                'price' => $pi->getPrice()?$pi->getPrice():0,
            );
         
            if ($default_color_id == $pc_id && $default_item && $default_item['size_id'] == $ps_id) {
            $recommended_product_item = $pi;
        }
        }
        

        $default_size_fb = array();
        $default_size_fb['feedback'] = FitAlgorithm2::getDefaultSizeFeedback($fb);
        $this->container->get('site.helper.usertryitemhistory')->createUserItemTryHistory($user, $product->getId(), $recommended_product_item, $default_size_fb);
        return $this->response_array(true, "Product Detail ", true, $p);
    }

    #------------------------------------------------------------------------------

     public function likeUnlikeItem($user, $ra) {
         #$default_item = $this->container->get('admin.helper.productitem')->find($ra['item_id']);
         #$this->container->get('user.helper.user')->makeFavourite($user, $default_item);
         
         
        if ($ra['like'] == 'true') {
            if (count($user->getProductItems()) < 25) {# check limit
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
                if (count($user->getProductItems()) < 25) {# check limit
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
}