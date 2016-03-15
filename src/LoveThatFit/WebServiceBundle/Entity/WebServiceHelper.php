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

    #------------------------ User -----------------------

    public function loginService($request_array) {
        $user = $this->container->get('user.helper.user')->findByEmail($request_array['email']);
        if (count($user) > 0) {
            if ($this->container->get('user.helper.user')->matchPassword($user, $request_array['password'])) {
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
     public function registrationWithDefaultValues($request_array) {

        if (!array_key_exists('email', $request_array)) {
            return $this->response_array(false, 'Email Not provided.');
        }

        $user = $this->container->get('user.helper.user')->findByEmail($request_array['email']);

        if (count($user) > 0) {
            return $this->response_array(false, 'Email already exists.');
        } else {
            #--- 1) User
            $user = $this->createUserWithParams($request_array);
            #---- 2) send registration email ....            
            $this->container->get('mail_helper')->sendRegistrationEmail($user);                    
            #--- 3) default user values added
            $measurement = $this->container->get('user.helper.user')->copyDefaultUserData($user, $request_array);
            
            $user = $this->container->get('user.helper.user')->findByEmail($request_array['email']);
            $detail_array = $user->toDataArray(true, $request_array['device_type'], $request_array['base_path']);            
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
            $ar['actual_user'] = $ra;
            $measurement->setMeasurementJson(json_encode($ar));
        } else {
            $measurement = $this->setUserMeasurementWithParams($ra, $user);
        }
        $this->container->get('user.helper.measurement')->saveMeasurement($measurement);
        return $this->response_array(true, 'measurement updated', true, array('user' => $user->toDataArray(true, null, $base_path)));
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
        #this dob line will be removed with the new build
        $user->setBirthDate(array_key_exists('dob', $request_array) ? new \DateTime($request_array['dob']) : null);
        array_key_exists('birth_date', $request_array) ? $user->setBirthDate(new \DateTime($request_array['birth_date'])) :  null;                        
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
        array_key_exists('shoulder_height', $request_array) ? $measurement->setShoulderHeight($request_array['shoulder_height']) : '';
        array_key_exists('shoulder_length', $request_array) ? $measurement->setShoulderLength($request_array['shoulder_length']) : '';
        
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
               
                    $actual_measurement = $user->getMeasurement()->getJSONMeasurement('actual_user');
                    $ra['measurement'] = is_array($actual_measurement) ? json_encode($actual_measurement) : null;
                    $parsed_array = $this->parse_request_for_archive($ra);

                    $user_archive = $this->container->get('user.helper.userarchives')->createNew($user);
                    $this->container->get('user.helper.userarchives')->saveArchives($user_archive, $parsed_array);                    
                    #set image name with id
                    $user_archive->setImage($user_archive->getId().'_original.'.$ext);                    
                    if (move_uploaded_file($files["image"]["tmp_name"], $user_archive->getAbsolutePath('original'))) {                                        
                       $user->setStatus(-1);                    
                   } else {
                       return $this->response_array(false, 'Image not uploaded');
                   }
                   $this->container->get('user.helper.userarchives')->saveArchives($user_archive, $parsed_array);
                
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
            $userinfo['user'] = $user->toDataArray(true, $ra['device_type'], $ra['base_path']);
            return $this->response_array(true, 'User Image Uploaded', true, $userinfo);
        } else {
            return $this->response_array(false, 'member not found');
        }
    }
    #----------------------------------------------------------------------------------------

    private function parse_request_for_archive($ra) {
        $arr = array(
            'measurement' => $ra['measurement'],
            'device_type' => $ra['device_type'],
            'height_per_inch' => $ra['height_per_inch'],
            'image_actions' =>  json_encode(array( #json encoded image specs
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
            
            
            $p['items'][$pi->getId()] = array(
                'item_id' => $pi->getId(),
                'product_id' => $product->getId(),
                'color_id' => $pc_id,
                'size_id' => $ps_id,
                'sku' => $pi->getSku() == null ? 'no' : $pi->getSku(),
                'image' => $pi->getImage() == null ? 'no-data' : $pi->getImage(),
                'recommended' => $default_color_id == $pc_id && $default_item && $default_item['size_id'] == $ps_id ? true : false,
                'price' => $pi->getPrice()?$pi->getPrice():0,
                'favourite' => in_array($pi->getId(), $favouriteItemIds),
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

     public function userLikedProductIds($user) {
        $product_ids = $this->container->get('webservice.repo')->userLikedProductIds($user);
        return $this->response_array(true, "favourite product ids", true, $product_ids);        
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

  #feedback service
  #------------------------ User -----------------------

  public function feedbackService($user,$content) {
	$this->container->get('mail_helper')->sendFeedbackEmail($user,$content);
  }
	#end feedback service
}