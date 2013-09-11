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
use LoveThatFit\UserBundle\Form\Type\RegistrationType;
use LoveThatFit\UserBundle\Form\Type\RegistrationMeasurementMaleType;
use LoveThatFit\UserBundle\Form\Type\RegistrationMeasurementFemaleType;
use LoveThatFit\SiteBundle\Algorithm;

class ProductController extends Controller {
#-----------------Brand List Related To Size Chart For Registration Step2---------------------------------------------------#

 
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

#--------------------Brand List-------------------------------------------------------------------------------#

    public function brandListAction() {
        $request = $this->getRequest();
        $brand = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Brand')
                ->findAllBrandWebService();

        $total_record = count($brand);
        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/brands/iphone/';
        $data = array();
        $data['data'] = $brand;
        $data['path'] = $baseurl;
        return new Response($this->json_view($total_record, $data));
    }

#----------------------Service for Product --------------------------------------#
    //------Proudct List By Clothing Type and By Brand  With Gender----------------------///   
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

    #-----------------------------Productlist Against Brand or Clothing Type -----------------------------------#   

    public function productlistAction() {
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
        
 #------------------------------Authentication of Token---------------------------------------------#
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
      //  $request_array=array('authTokenWebService'=>'f7737d3dd7293d035cf48a6b2353505e');
        $product_helper =  $this->get('admin.helper.product');
        $product_response=$product_helper->productListWebService($request,$request_array);
        return new response(json_encode($product_response));
        
    }
#--------------------Product Detail -------------------------------------------------------------#
    //------Proudct List By Product Detail----------------------///   

    public function productDetailAction() {
        
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
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
        $product_helper =  $this->get('admin.helper.product');
        $product_response=$product_helper->productDetailWebService($request,$request_array);
        
       return new response(json_encode($product_response));
    }
#------------------------------Default Fitting Room Alerts --------------------------------------------------#
////-------------------------------------------------------------------
    public function defaultProductFittingAlertAction() {
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
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
        $msg=$this->get('admin.helper.product')->getDefaultFittingAlerts($request_array);
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
 public function loveItemAction() {
     
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);


        $user_id = $request_array['user_id'];
        $product_item_id = $request_array['product_item_id'];
     /*   $user_id=2;
        $product_item_id=2;
        $request_array['like']='like';*/
       
       $authTokenWebService = $request_array['authTokenWebService'];
  #------------------------------Authentication of Token--------------------------------------------#
        $user_helper = $this->get('user.helper.user');
          if ($authTokenWebService) {
            $tokenResponse = $user_helper->authenticateToken($authTokenWebService);
            if ($tokenResponse['status'] == False) {
                return new Response(json_encode($tokenResponse));
            }
        } else {
            return new Response(json_encode(array('Message' => 'Please Enter the Authenticate Token')));
        }
        
     
        $msg=$this->get('admin.helper.product')->loveItem($request_array);
        return new Response(json_encode($msg));
        
      
    }
#------------------------------------------------------------End of Love/Like------------------------------#   
#--------------------------------------Try On History Service----------------------------------------------#
     public function userTryHistoryAction()
    {
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
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
        $user_id = $request_array['user_id'];
        
        $product_helper =  $this->get('admin.helper.product');
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
        $product_helper =  $this->get('admin.helper.product')->favouriteByUser($user_id,$request);
        return new response(json_encode($product_helper));
        }else{
            return new Response(json_encode(array('Message'=>'User cant find')));
        }
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
}

// End of Class