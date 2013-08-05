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
        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/brands/';
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

        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/brands/';
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
        
        
        $user_id = $request_array['user_id'];
        $product_id = $request_array['product_id'];
        
       // find product
         if($product_id){ 
        $product = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->find($product_id);
      
         $product_color = $product->getDisplayProductColor();
          $product_color_id = $product_color->getId();
          
            //get color size array, sizes that are available in this color 

            $color_sizes_array = $this->getDoctrine()
                    ->getRepository('LoveThatFitAdminBundle:ProductColor')
                    ->getSizeArray($product_color_id);
            $size_id = null;

            // find size id is not in param gets the first size id for this color

            $psize = array_shift($color_sizes_array);
            $size_id = $psize['id'];
            $product_size_id = $size_id;
            $product_size = $this->getDoctrine()
                    ->getRepository('LoveThatFitAdminBundle:ProductSize')
                    ->find($product_size_id);

         
            //2) color & size can get an item

            if ($product_size && $product_color) {
                $product_item = $this->getDoctrine()
                        ->getRepository('LoveThatFitAdminBundle:ProductItem')
                        ->findByColorSize($product_color->getId(), $product_size->getId());
               $product_item_id = $product_item->getId();
        } 
        
       
        if ($user_id && $product_item_id) {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('LoveThatFitUserBundle:User')->find($user_id);
            $productItem = $this->getProductItemById($product_item_id);

            if (!$user)
                return new Response(json_encode(array('Message' => 'User not found')));

            if (!$productItem)
                return new Response(json_encode(array('Message' => 'Product not found')));

            $fit = new Algorithm($user, $productItem);
            $data = array();
            $data['data'] = $fit->getFeedBackArray();
           
            return new Response(json_encode($data));
        }
        else {
            return new Response(json_encode(array('Message' => 'Missing User/Item')));
        }
        
        }//End of If      
        else {

            return new Response(json_encode(array('Message' => 'Can not find')));
        }
      
        
    }  
    //-------------------------------------------------------------------
    private function getProductItemById($id) {
        $product_item = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ProductItem')
                ->find($id);
        return $product_item;
    }
 //-------------------------------------------------------------------
    public function getFeedBackJSONAction() {
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
        $user_id = $request_array['user_id'];
        $product_item_id = $request_array['product_item_id'];
        if ($user_id && $product_item_id) {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('LoveThatFitUserBundle:User')->find($user_id);
            $productItem = $this->getProductItemById($product_item_id);

            if (!$user)
                return new Response(json_encode(array('Message' => 'User not found')));

            if (!$productItem)
                return new Response(json_encode(array('Message' => 'Product not found')));

            $fit = new Algorithm($user, $productItem);
            $data = array();
            $data['data'] = $fit->getFeedBackJson();
            $total_record = count($data);
            return new Response($this->json_view($total_record, $data));
        }
        else {

            return new Response(json_encode(array('Message' => 'Missing User/Item')));
        }
    }
    
    
#---------------------Like/Love Item-----------------------------------------------------------------------#
 public function loveItemAction() {
       $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);


        $user_id = $request_array['user_id'];
        $product_item_id = $request_array['product_item_id'];
        
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
        

        if ($user_id && $product_item_id) {
            if ($request_array['like']=='like') {
                $em = $this->getDoctrine()->getManager();
                $productObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product');
                $entity = $this->getDoctrine()
                        ->getRepository('LoveThatFitAdminBundle:Product')
                        ->countMyCloset($user_id);
                $rec_count = count($productObj->countMyCloset($user_id));

                if ($rec_count >= 25) {

                    return new Response(json_encode(array('Message' => 'Please delete some products (limit exceeds)')));
                } else {


                    $em = $this->getDoctrine()->getManager();
                    $user = $em->getRepository('LoveThatFitUserBundle:User')->find($user_id);

                    $product_item = $this->getProductItemById($product_item_id);
                    $em = $this->getDoctrine()->getManager();
                    $product_item->addUser($user);
                    $user->addProductItem($product_item);
                    $em->persist($product_item);
                    $em->persist($user);
                    $em->flush();
                    return new Response(json_encode(array('Message' => 'Item has been successfully liked!')));
                }
            } else {

                $em = $this->getDoctrine()->getManager();
                $user = $em->getRepository('LoveThatFitUserBundle:User')->find($user_id);
                $product_item = $this->getProductItemById($product_item_id);
                $product_item->removeUser($user);
                $user->removeProductItem($product_item);
                $em->persist($product_item);
                $em->persist($user);
                $em->flush();
                return new Response(json_encode(array('Message' => 'Item has been successfully unliked!')));
            }
        } else {
            return new Response(json_encode(array('Message' => 'User/Item Missing')));
        }
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