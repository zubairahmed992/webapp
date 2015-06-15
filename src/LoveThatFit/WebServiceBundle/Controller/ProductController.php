<?php

namespace LoveThatFit\WebServiceBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\UserBundle\Entity\Measurement;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use LoveThatFit\AdminBundle\Entity\Brand;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ClothingType;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use LoveThatFit\WebServiceBundle\Controller\DateTimeZone;

class ProductController extends Controller {

#-----------------Brand List Related To Size Chart For Registration Step2-------#
   # IMAGES_URL	/web_service/images_url
#------------------------------------------------------
public function imagesUrlAction(){
    $request = $this->getRequest();
    $handle = fopen('php://input', 'r');
    $jsonInput = fgets($handle);
    $request_array = json_decode($jsonInput, true);
    $product_response =  $this->get('webservice.helper.product')->imagesUrl($request,$request_array);
     return new response(json_encode($product_response));
}
#-------------------- Brand With Retailer -------------------------------------#
#2 BRANDS_URL	/web_service/brand_retailer
#------------------------------------------------------
public function brandRetailerListAction(){
      $request = $this->getRequest();
      $handle = fopen('php://input', 'r');
      $jsonInput = fgets($handle);
      $request_array = json_decode($jsonInput, true);
      // $request_array=array('date'=>'1388577600.000000');
      
      if($request_array){
            $date_fromat=$this->get('webservice.helper.product')->returnFormattedTime($request_array);
      }else{
          $date_fromat=null;
      }
   return new response(json_encode($this->get('admin.helper.brand')->getBrandRetailerList($date_fromat)));
     
     
}


#----------------------Brand List-----------------------------------------------#
public function brandListAction() {
        $request = $this->getRequest();
        $brand = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Brand')
                ->findAllBrandWebService();

        $total_record = count($brand);
         $data = array();
        $data['data'] = $brand;
        
        return new Response($this->json_view($total_record, $data));
    }
#------ Giving new product listing of product table for sotring in ipone 
    public function newproductListingAction(){
         $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
        $user = $this->get('webservice.helper.user');
      //$request_array=array('authTokenWebService'=>'e4c997f574be6c6e3f8dc6bd4286ff60');
     //  $request_array=array('authTokenWebService'=>'121c421783cd4d71d871ec16a1296091');
        $authTokenWebService = $request_array['authTokenWebService'];
    if ($authTokenWebService) {
            $tokenResponse = $user->authenticateToken($authTokenWebService);
            if ($tokenResponse['status'] == False) {
                return new Response(json_encode($tokenResponse));
            }
        } else {
            return new Response(json_encode(array('Message' => 'Please Enter the Authenticate Token')));
        }

         $product_helper =  $this->get('webservice.helper.product');
        $product_response=$product_helper->newproductListingWebService($request,$request_array);
        return new response(json_encode($product_response));
       
        
    }
    
    #--------------------Product Detail -------------------------------------------#
    public function newproductDetailAction() {
        
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
        $user = $this->get('webservice.helper.user');
       // $request_array=array('authTokenWebService'=>'123');
       //$request_array=array('authTokenWebService'=>'46ed5a3aa2f09ba0436612289b93aee5');
         //$request_array=array('authTokenWebService'=>'121c421783cd4d71d871ec16a1296091');
        $authTokenWebService = $request_array['authTokenWebService'];
       if ($authTokenWebService) {
            $tokenResponse = $user->authenticateToken($authTokenWebService);
            if ($tokenResponse['status'] == False) {
                return new Response(json_encode($tokenResponse));
            }
        } else {
            return new Response(json_encode(array('Message' => 'Please Enter the Authenticate Token')));
        }
     // $request_array=array('id'=>186,'user_id'=>1);

        $product_helper =  $this->get('webservice.helper.product');
        $product_response=$product_helper->newproductDetailWebService($request,$request_array);
        
       return new response(json_encode($product_response));
    }

#-------------Productlist Against Brand or Clothing Type ----------------------#   

#       PRODUCT_TYPE_URL *~~~~~~~~~~>||
#---------------------------  
    public function productlistAction() {
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
        $user = $this->get('webservice.helper.user');
     // $request_array=array('authTokenWebService'=>'46ed5a3aa2f09ba0436612289b93aee5','brandId'=>5,'type'=>'brand');
        $authTokenWebService = $request_array['authTokenWebService'];
    
      
      if ($authTokenWebService) {
            $tokenResponse = $user->authenticateToken($authTokenWebService);
            if ($tokenResponse['status'] == False) {
                return new Response(json_encode($tokenResponse));
            }
        } else {
            return new Response(json_encode(array('Message' => 'Please Enter the Authenticate Token')));
        }

/*$request_array=array('authTokenWebService'=>'7823fa718ffc2aab541de9c960efc2fd','id'=>1,'type'=>'brand','gender'=>'F');*/
        $product_helper =  $this->get('webservice.helper.product');
        $product_response=$product_helper->productListWebService($request,$request_array);
        return new response(json_encode($product_response));
    }
#-------Proudct List By Clothing Type and By Brand  With Gender----------------#
    public function byBrandClothingTypeAction() {
        $request = $this->getRequest();
        $brand = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Brand')
                ->findAllBrandWebService();
        $clothing_types = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ClothingType')
                ->findAllBrandWebService();
        $total_record = count($brand);
        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/brands/iphone/';
        $data = array();
        $data['data'] = array_merge($brand, $clothing_types);
        $data['brand_image_path'] = $baseurl;
        return new Response($this->json_view($total_record, $data));
    } 

#!!! yet not used ----------------Product Detail -------------------------------------------#
    public function productDetailAction() {
        
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);

        $user = $this->get('webservice.helper.product');
       $authTokenWebService = $request_array['authTokenWebService'];
       if ($authTokenWebService) {
            $tokenResponse = $user->authenticateToken($authTokenWebService);
            if ($tokenResponse['status'] == False) {
                return new Response(json_encode($tokenResponse));
            }
        } else {
            return new Response(json_encode(array('Message' => 'Please Enter the Authenticate Token')));
        }
     // $request_array=array('id'=>186,'user_id'=>1);

        $product_helper =  $this->get('webservice.helper.product');
        $product_response=$product_helper->productDetailWebService($request,$request_array);
        
       return new response(json_encode($product_response));
    }
#-----------------------End Of Product Detail----------------------------------#    
    public function brandListSizeChartAction() {
        $size_chart_helper = $this->get('admin.helper.sizechart');
        $brand_list = $size_chart_helper->getBrandArraySizeChart();
        $total_record = count($brand_list);
        $data = array();
        $data['data'] = $brand_list;
        return new Response($this->json_view($total_record, $data));
    }

#-----------------Size Chart Against The Brand Id For Registration Step2---------------------------------------------------#

    public function sizeChartsAction() {

        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
        $size_chart_helper = $this->get('admin.helper.sizechart');
           
//$request_array=array('gender'=>'f','body_type'=>'Petite','target_top'=>1,'top_size'=>1,'target_bottom'=>2,'bottom_size'=>2,'target_dress'=>3,'dress_size'=>3);
        $size_chart = $size_chart_helper->sizeChartList($request_array);
        if ($size_chart) {
            $size_chart_data = array();
            $size_chart_data = $size_chart;
            $total_record = count($size_chart);
            return new Response($this->json_view($total_record, $size_chart_data));
        } else {
            return new response(json_encode(array('Message' => 'Can not find Size Chart')));
        }
    }

   

#------------------------------Default Fitting Room Alerts --------------------------------------------------#
# FITTING_ALERT_URL	/web_service/default_fitting_alerts
    public function defaultProductFittingAlertAction() {
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
        
        if($request_array ==null) #if null (to be used for web service testing))
            $request_array   = $request->request->all();
        
        $authTokenWebService = $request_array['authTokenWebService'];
        if ($authTokenWebService) {
            $tokenResponse = $this->get('user.helper.user')->authenticateToken($authTokenWebService);
            if ($tokenResponse['status'] == False) {
                return new Response(json_encode($tokenResponse));
            }
        } else {
            return new Response(json_encode(array('Message' => 'Please Enter the Authenticate Token')));
        }
 
        $msg=$this->get('webservice.helper.product')->getDefaultFittingAlerts($request_array);
        return new Response(json_encode($msg));
   }  
    //-------------------------------------------------------------------
    private function getProductItemById($id) {
        $product_item = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ProductItem')
                ->find($id);
        return $product_item;
    }
 
    
#---------------------Like/Love Item-----------------------------------------------------------------------#
    # MAKE_FAVOURITE_URL	/web_service/love_item	
 public function loveItemAction() {
     
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
        $user_id = $request_array['userId'];
        $product_item_id = $request_array['productItemId'];
       //$user_id=17;
       // $product_item_id=2;
        //$request_array['like']='like';
      // $request_array=array('authTokenWebService'=>'121c421783cd4d71d871ec16a1296091','productItemId'=>12,'userId'=>17,'like'=>'like');
       $authTokenWebService = $request_array['authTokenWebService'];
  #------------------------------Authentication of Token--------------------------------------------#
        $user_helper = $this->get('webservice.helper.user');
          if ($authTokenWebService) {
            $tokenResponse = $user_helper->authenticateToken($authTokenWebService);
            if ($tokenResponse['status'] == False) {
                return new Response(json_encode($tokenResponse));
            }
        } else {
            return new Response(json_encode(array('Message' => 'Please Enter the Authenticate Token')));
        }
        
     
        $msg=$this->get('webservice.helper.product')->loveItem($request_array);
        return new Response(json_encode($msg));
        
      
    }
#------------------------------------------------------------End of Love/Like------------------------------#   
#--------------------------------------Try On History Service----------------------------------------------#
#RECENTLY_TRIED_URL	/web_service/user_try_history
    public function userTryHistoryAction()
    {
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
 #------------------------------Authentication of Token-------------------------#
        $user = $this->get('user.helper.user');
     $authTokenWebService = $request_array['authTokenWebService'];//'e4c997f574be6c6e3f8dc6bd4286ff60';
        if ($authTokenWebService) {
            $tokenResponse = $user->authenticateToken($authTokenWebService);
            if ($tokenResponse['status'] == False) {
                return new Response(json_encode($tokenResponse));
            }
        } else {
            return new Response(json_encode(array('Message' => 'Please Enter the Authenticate Token')));
        }
#------------------------------End Of Authentication Token---------------------#
        $user_id = $request_array['userId'];
        //$user_id=117;/
     
        $product_helper =  $this->get('webservice.helper.product');
        $msg=$product_helper->getUserTryHistoryWebService($request,$user_id);
        return new Response(json_encode($msg));
    }
#---------------------------User Favourite Item List Return SErvice-----------------#    
    
    public function favouriteByUserAction(){
        
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
        $user_id=$request_array['user_id'];
       #------------------------------Authentication of Token--------------------------------------------#
        $user = $this->get('user.helper.user');
        $authTokenWebService = $request_array['authTokenWebService'];
       if ($authTokenWebService) {
            $tokenResponse = $user->authenticateToken($authTokenWebService);
            if ($tokenResponse['status'] == False) {
                return new Response(json_encode($tokenResponse));
            }
        } else {
            return new Response(json_encode(array('Message' => 'Please Enter the Authenticate Token')));
        }
 #-------------------------------End Of Authentication Token--------------------------------------#
        //$user_id=1;
        if($user_id){
        $product_helper =  $this->get('webservice.helper.product')->favouriteByUser($user_id,$request);
        return new response(json_encode($product_helper));
        }else{
            return new Response(json_encode(array('Message'=>'User cannot find')));
        }
    }
    
    
 #----- Work for Product Synchronization  -------------------------------------#
    #DATE_SYNC_URL	/web_service/product_sync
    public function getProductSynAction(){
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
         
        if($request_array==null) #if null (to be used for web service testing))
            $request_array  = $request->request->all();
         
        $user = $this->get('webservice.helper.user');
      // $request_array=array('authTokenWebService'=>'567a31256454a1dd8157eba6ddfc5447','date'=>'1357041600');
     // $request_array=array('authTokenWebService'=>'e4c997f574be6c6e3f8dc6bd4286ff60','date'=>'1357041600');
        $authTokenWebService = $request_array['authTokenWebService'];
    if ($authTokenWebService) {
            $tokenResponse = $user->authenticateToken($authTokenWebService);
            if ($tokenResponse['status'] == False) {
                return new Response(json_encode($tokenResponse));
            }
        } else {
            return new Response(json_encode(array('Message' => 'Please Enter the Authenticate Token')));
        }
        //  return new response(json_encode($request_array));
        $product_helper =  $this->get('webservice.helper.product');
        $product_response=$product_helper->newproductListingWebService($request,$request_array);
        return new response(json_encode($product_response));
        
    }
 #--------------------Get product Detail Sunc-----------------------------------#
    # PRODUCT_DETAIL_URL	/web_service/product_detail_sync
    public function getProductDetailSynAction() {
        
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
        $user = $this->get('webservice.helper.user');
       // $request_array=array('authTokenWebService'=>'567a31256454a1dd8157eba6ddfc5447','date'=>'1388577600.000000');
       //$request_array=array('authTokenWebService'=>'46ed5a3aa2f09ba0436612289b93aee5');
         //$request_array=array('authTokenWebService'=>'121c421783cd4d71d871ec16a1296091');
        $authTokenWebService = $request_array['authTokenWebService'];
       if ($authTokenWebService) {
            $tokenResponse = $user->authenticateToken($authTokenWebService);
            if ($tokenResponse['status'] == False) {
                return new Response(json_encode($tokenResponse));
            }
        } else {
            return new Response(json_encode(array('Message' => 'Please Enter the Authenticate Token')));
        }
      
        $product_helper =  $this->get('webservice.helper.product');
        $product_response=$product_helper->newproductDetailWebService($request,$request_array);
        
       return new response(json_encode($product_response));
    }

  
 #-------------------Get Brand Sync-------------------------------------------#
# BRANDS_URL	/web_service/brand_sync
    
    
 public function getBrandSyncAction() {
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
      // $request_array=array('date'=>'1388577600.000000');
        if($request_array){
       $date_fromat=$this->get('webservice.helper.product')->returnFormattedTime($request_array);
      
       if($date_fromat){
        $brand = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Brand')
                ->findAllBrandWebService($date_fromat);
        }
        $total_record = count($brand);
         $data = array();
        $data['data'] = $brand;
        
        return new Response($this->json_view($total_record, $data));
        
        }else{
            return new response(json_encode(array("Message"=>"No Data Found")));
        }
    }
    #----------------Get Product Data for user synchorization ----------------#
# FITTING_ROOM_PRODUCT_URL	/web_service/product_user_sync
# PRODUCT_URL	/web_service/product_user_sync

     public function getProductForUserSynAction(){
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
        $user = $this->get('webservice.helper.user');
        //$request_array=array('authTokenWebService'=>'123','date'=>'1388577600');
       // $request_array=array('authTokenWebService'=>'e4c997f574be6c6e3f8dc6bd4286ff60');
        
        $authTokenWebService = $request_array['authTokenWebService'];
    if ($authTokenWebService) {
            $tokenResponse = $user->authenticateToken($authTokenWebService);
            if ($tokenResponse['status'] == False) {
                return new Response(json_encode($tokenResponse));
            }
        } else {
            return new Response(json_encode(array('Message' => 'Please Enter the Authenticate Token')));
        }
        //  return new response(json_encode($request_array));
        $product_helper =  $this->get('webservice.helper.product');
        $product_response=$product_helper->productListingForUserSync($request,$request_array);
        return new response(json_encode($product_response));
        
    }
 
#------------------ End of Product Synchronization-----------------------------#
#----------------- Push notification Start-------------------------------------#
public function sendPushNotifcationAction(){
   $request = $this->getRequest();
   $msg= $this->get('push_notification_helper')->sendPushNotification('5df5813920c2716badb4a90c81551276ae96cb60cf4a19a52399d1d407991f93',"12","test",$request);
   return new response($msg);
   
}
###Testing prupose ------------------#
public function getNotificationTypeAction(){
 return new response(json_encode($this->get('push_notification_helper')->getCroneJob()));
}
#------ Run Crone job Action -----------------#
public function runCroneJobAction(){
    return new response(json_encode($this->get('push_notification_helper')->getCroneJob()));
    
}


#---------------------------Render Json--------------------------------------------------------------------#

    private function json_view($rec_count, $entity) {
        if ($rec_count > 0) {
            $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
                        JsonEncoder()));
            return $serializer->serialize($entity, 'json');
        } else {
            return json_encode(array('Message' => 'Record Not Found'));
        }
    }

#---------------------------------------------------------------------------------------------------------#

#-----------------------Testing Web Services----------------------------------#
    #----------Test Product Web Service ------------------------------# 
    public function getTestProductSynAction($authTokenWebService,$date){
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
        $user = $this->get('webservice.helper.user');
        $request_array=array('authTokenWebService'=>$authTokenWebService,'date'=>$date);
        //$request_array=array('authTokenWebService'=>'123','date'=>'1388577600');
    //  $request_array=array('authTokenWebService'=>'121c421783cd4d71d871ec16a1296091');
        $authTokenWebService = $request_array['authTokenWebService'];
    if ($authTokenWebService) {
            $tokenResponse = $user->authenticateToken($authTokenWebService);
            if ($tokenResponse['status'] == False) {
                return new Response(json_encode($tokenResponse));
            }
        } else {
            return new Response(json_encode(array('Message' => 'Please Enter the Authenticate Token')));
        }
        //  return new response(json_encode($request_array));
        $product_helper =  $this->get('webservice.helper.product');
        $product_response=$product_helper->newproductListingWebService($request,$request_array);
        return new response(json_encode($product_response));
        
    }
    
    
    #----------------------Get Product Detail Test Service ----------------#
    public function getTestProductDetailSynAction($authTokenWebService,$date) {
        
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
        $request_array=array('authTokenWebService'=>$authTokenWebService,'date'=>$date);
        $user = $this->get('webservice.helper.user');
       // $request_array=array('authTokenWebService'=>'123','date'=>'1388577600.000000');
       //$request_array=array('authTokenWebService'=>'46ed5a3aa2f09ba0436612289b93aee5');
         //$request_array=array('authTokenWebService'=>'121c421783cd4d71d871ec16a1296091');
        $authTokenWebService = $request_array['authTokenWebService'];
       if ($authTokenWebService) {
            $tokenResponse = $user->authenticateToken($authTokenWebService);
            if ($tokenResponse['status'] == False) {
                return new Response(json_encode($tokenResponse));
            }
        } else {
            return new Response(json_encode(array('Message' => 'Please Enter the Authenticate Token')));
        }
      
        $product_helper =  $this->get('webservice.helper.product');
        $product_response=$product_helper->newproductDetailWebService($request,$request_array);
        
       return new response(json_encode($product_response));
    }
   #---------------------Test Brand Synchorization Web Service -------#
    public function getTestBrandSyncAction($date) {
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
         $request_array=array('date'=>$date);
      // $request_array=array('date'=>'1388577600.000000');
        if($request_array){
       $date_fromat=$this->get('webservice.helper.product')->returnFormattedTime($request_array);
      
       if($date_fromat){
        $brand = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Brand')
                ->findAllBrandWebService($date_fromat);
        }
        $total_record = count($brand);
         $data = array();
        $data['data'] = $brand;
        
        return new Response($this->json_view($total_record, $data));
        
        }else{
            return new response(json_encode(array("Message"=>"No Data Found")));
        }
    }
    
}

// End of Class