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
        $total_record = count($this->getBrandArray());
        $data = array();
        $data['data'] = $this->getBrandArray();
        return new Response($this->json_view($total_record, $data));
    }

#-----------------Size Chart Against The Brand Id For Registration Step2---------------------------------------------------#

    public function sizeChartsAction() {

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
        
        $id = $request_array['id'];
        $type = $request_array['type'];
        $gender = $request_array['gender'];
        
      // $id=3;
      // $type='brand';
      // $gender='F';
        
        $products = Null;
        if ($type == "brand") {
            $products = $this->getDoctrine()
                    ->getRepository('LoveThatFitAdminBundle:Product')
                    ->findProductByBrandWebService($id, $gender);
        }
        if ($type == "clothing_type") {
            $products = $this->getDoctrine()
                    ->getRepository('LoveThatFitAdminBundle:Product')
                    ->findProductByClothingTypeWebService($id, $gender);
        }
        if ($type == "hot") {
            $products = $this->getDoctrine()
                    ->getRepository('LoveThatFitAdminBundle:Product')
                    ->findhottestProductWebService($gender);
        }
        if ($type == "new") {
            $products = $this->getDoctrine()
                    ->getRepository('LoveThatFitAdminBundle:Product')
                    ->findLattestProductWebService($gender);
        }
        $data = array();
       
        #-------Fetching The Path------------#
        if ($products) {
            /* if($products[0]['product_image'] )
              {
              $product_id=$products[0]['id'];
              $productimage_path = $this->getDoctrine()
              ->getRepository('LoveThatFitAdminBundle:Product')
              ->find($product_id);
              $images_path= $productimage_path->getdefalutImagePaths();
              $data['path']=$images_path;
              } */

           $product_color_array = array();
           $count=1;
          $product_helper =  $this->get('admin.helper.product');
          foreach ($products as $ind_product) {
                $product_id = $ind_product['id'];
                if ($product_id) {
                    $p = $product_helper->find($product_id);
                    $data['data'][$product_id]['id'] = $ind_product['id'];
                    $data['data'][$product_id]['name'] = $ind_product['name'];
                    $data['data'][$product_id]['description'] = $ind_product['description'];
                    $data['data'][$product_id]['target'] = $ind_product['target'];
                    $data['data'][$product_id]['product_image'] = $ind_product['product_image'];
                    $item = $p->getDefaultItem();
                    if ($item) {
                        $data['data'][$product_id]['fitting_room_image'] = $item->getImage();
                    }
                }
            }   
           
            //$data[] = $products;
           
            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/';
            $fitting_room = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/fitting_room/';
            $data['fitting_room_path'] = $fitting_room;
            $total_record = count($products);

            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/';
            $data['path'] = $baseurl;
            return new Response($this->json_view($total_record, $data));
        } else {
            return new Response(json_encode(array('Message' => 'We can not find Product')));
        }
    }
#--------------------Product Detail -------------------------------------------------------------#
    //------Proudct List By Product Detail----------------------///   

    public function productDetailAction() {
        
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
        
        $product_id = $request_array['id'];
        $em = $this->getDoctrine()->getManager();
        
       
        $productdetail = array();
        $products = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->productDetail($product_id);

        $product = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->find($product_id);
        
        $count_rec = count($products);
        $productdetail['product'] = $products;
        
        $product_color_array = array();

        #-- FOR COLORS AND SIZE----------
        if ($count_rec > 0) {

            $product_colors = $product->getProductColors();
            $product_size_id = null;
            $size_id = null;
            foreach ($product_colors as $product_color_value) {
                $product_color_id = $product_color_value->getId();

                $color_sizes = $this->getDoctrine()
                        ->getRepository('LoveThatFitAdminBundle:ProductColor')
                        ->getSizeItemImageUrlArray($product_color_id);

                $color_size_array = array();

                foreach ($color_sizes as $cs) {
                    $color_size_array [$cs['title']] = $cs;
                }

                $product_color_array[$product_color_value->gettitle()] = array(
                    'id' => $product_color_value->getId(),
                    'image' => $product_color_value->getImage(),
                    'pattern' => $product_color_value->getPattern(),
                    'title' => $product_color_value->getTitle(),
                    'sizes' => $color_size_array,
                );
            }
            $productdetail['product_color'] = $product_color_array;
            $data = array();
            $data['data'] = $productdetail;
            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/';
            $fitting_room = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/fitting_room/';
            $pattern = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/ltf/products/pattern/';

            $data['product_color_path'] = $baseurl;
            $data['fitting_room_path'] = $fitting_room;
            $data['pattern_path'] = $pattern;

            return new Response($this->json_view($count_rec, $data));
        } else {
            return json_encode(array('Message' => 'Record Not Found'));
        }
    }
#------------------------------Default Fitting Room Alerts --------------------------------------------------#
////-------------------------------------------------------------------
    public function defaultProductFittingAlertAction() {
        $request = $this->getRequest();
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
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

    private function getBrandArray() {

        $brands = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:SizeChart')
                ->getBrandList();

        $brands_array = array();

        foreach ($brands as $i) {
            array_push($brands_array, array('id' => $i['id'], 'brand_name' => $i['name']));
        }
        return $brands_array;
    }

#---------------------------------------------------------------------------------------------------------#
}

// End of Class