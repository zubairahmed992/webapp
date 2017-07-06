<?php

namespace LoveThatFit\ProductIntakeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Controller\ProductController;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ProductColor;
use LoveThatFit\AdminBundle\Entity\ProductSize;
use LoveThatFit\AdminBundle\Entity\ProductItem;
use LoveThatFit\AdminBundle\Entity\ProductItemPiece;


class ServiceController extends Controller {    
         public $error_string;
#------------------------/product_intake/product_specification
      
    public function getProductSpecificationAction($brand_name, $style_id_number) {
        
         try {
            $result = $this->get('service.repo')->getProductSpecification($brand_name, $style_id_number);   
            $data   = $this->get('service.helper')->getResultFormat($result);           
        return new JsonResponse([
            'success' => true,
            'data'    => $data 
             ]);
         } catch (\Exception $exception) {
        return new JsonResponse([
                'success' => false,
                'code'    => $exception->getCode(),
                'message' => $exception->getMessage(),
            ]);
         }
    }

#--------------->pi/ws/product_demission
    
    public function getExistingProductSpecificationAction($brand_name, $style_id_number) {    
        try {               
            $data = $this->get('service.repo')->getProductDetail($brand_name, $style_id_number);              
            if($data){ 
            foreach ($data[0][0]['product_sizes'] as $key => $product_size_value) {                  
                 foreach ($product_size_value['product_size_measurements'] as  $value) {  
                     $result[$product_size_value['title']][$value['title']]['grade_rule'] = $value['grade_rule'];
                     $result[$product_size_value['title']][$value['title']]['garment_dimension'] = $value['garment_measurement_flat'];
                   }
            }
              $message = true;
            } else{
                $message = "Record Not Found!";
                $result = null;
            }
        return new JsonResponse([
            'success' => $message,
            'data'    => $result 
             ]);
         } catch (\Exception $exception) {
        return new JsonResponse([
                'success' => false,
                'code'    => $exception->getCode(),
                'message' => $exception->getMessage(),
            ]);
         }
    }
    
    
    
#------------> /pi/ws/product_detail
    public function productDetailAction(Request $request) {
         $product_id =  $request->get('product_id');
         $server_url =  $request->get('server_url');    
         $data = $this->get('service.helper')->getProductDetails($product_id);
        
          
                       // print_r($data[0][0]['product_sizes'] );

            die;
            $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
            //$imagepath = $protocol.$_SERVER["HTTP_HOST"]. '/webapp/web/uploads/ltf/products/fitting_room/web/'; 
            $imagepath =  str_replace('\\', '/', getcwd()). '/uploads/ltf/products/fitting_room/web/'; 
            //$imagepath = 'http://192.168.0.113/webapp/web/uploads/ltf/products/fitting_room/web/'; 
            // $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
            // $prodcut_Color_pattren = $protocol.$_SERVER["HTTP_HOST"]. '/webapp/web/uploads/ltf/products/pattern/web/'; 
            $prodcut_Color_pattren = str_replace('\\', '/', getcwd()). '/uploads/ltf/products/pattern/web/';

            $postdata['imagepath'] = $imagepath;
            $postdata['prodcut_Color_pattren'] = $prodcut_Color_pattren;     

//  echo $protocol;
          //  echo $imagepath;
           // die;
            $url = $server_url.'/webapp/web/app_dev.php/pi/ws/save_product';
            $postdata['data'] =  json_encode($data);            
            //open connection
            $ch = curl_init();
            //set the url, number of POST vars, POST data
            curl_setopt($ch,CURLOPT_URL, $url);
           // curl_setopt($ch,CURLOPT_POST, count($dat));
            curl_setopt($ch,CURLOPT_POSTFIELDS, $postdata); 
            //execute post
            $result = curl_exec($ch);
            //close connection
            curl_close($ch);
           return new JsonResponse();
    }
    
    public function saveProductAction() {        
         try {              
        $message =  $this->get('service.helper')->createProduct($_POST['data'],$_POST['imagepath'], $_POST['prodcut_Color_pattren']); 
        return new JsonResponse([
            'success' => true,
            'data'    =>  $message
             ]);
         } catch (\Exception $exception) {
        return new JsonResponse([
                'success' => false,
                'code'    => $exception->getCode(),
                'message' => $exception->getMessage(),
            ]);
         } 
    }
    
    //---------------------- productImageUpload
    public function ImageUploadproductSizeItemAction(Request $request) {
        try {            
            $imageFile = $request->files->get('file');                        
            $response = $this->imageUploadProductItemSize($_FILES, $imageFile);
            return new JsonResponse($response);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'success' => false,
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
            ]);
        }
    }
     //---------------------- productImageUploadcheck
    public function itemImageExistsAction($file_name) {
        try {            
            
            $parsed_details = $this->breakFileNameProductImageUplaod($file_name);            
            $data = $this->get('service.repo')->getProductDetailOnly(str_replace('-', ' ', $parsed_details['brand']), $parsed_details['style_id_number']);                            
            
            if (count($data)==0) {
                $res=$this->responseArray('Product not found');#------------------------------------------>
                return new Response(json_encode($res));            
            }
            $parsed_details['product_id'] = $data[0]['id'];            

            $product_color = $this->get('admin.helper.productcolor')->findColorByProductTitle(strtolower($parsed_details['color_title']), $parsed_details['product_id']);
            if (count($product_color) == 0) {
                $res=$this->responseArray('color not found'); #------------------------------------------>
                return new Response(json_encode($res));            
            }

            $product_size = $this->get('admin.helper.productsizes')->findSizeByProductTitle(strtolower($parsed_details['size_title']), $parsed_details['product_id']);
            if (count($product_size) == 0) {
                $res=$this->responseArray('Size not found'); #------------------------------------------>
                return new Response(json_encode($res));            
            }

            $product_item_id = $this->get('admin.helper.productitem')->getProductItemByProductId($parsed_details['product_id'], $product_color->getId(), $product_size->getId());
            if (count($product_item_id) == 0) {
                $res=$this->responseArray('Product Item not found'); #------------------------------------------>
                return new Response(json_encode($res));            
            }
            
            $product_item = $this->get('admin.helper.product')->findProductColorSizeItemViewByTitle($parsed_details);                                
            $request= $this->get('request');
            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
            $baseurl .'/'. $product_item->getWebPath();                
            $uploaded=false;                                
            if(file_exists($product_item->getAbsolutePath())) {
                $res=$this->responseArray('File Exists', true, $baseurl .'/'. $product_item->getWebPath());
            }else{
                $res=$this->responseArray('File Not Found!');
            }
            
            return new Response(json_encode($res));            
            
        } catch (\Exception $exception) {
            return new JsonResponse([
                'success' => false,
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
            ]);
        }
    }

    #---------------------------------------Image Uplaod Function -------------------------------
      public function imageUploadProductItemSize($FILES , $image_file)    {  
        #$allowed = array('png', 'jpg');        
        $request = $this->getRequest();
        if (isset($FILES['file']) && $_FILES['file']['error'] == 0) {            
            $file_name = $FILES['file']['name'];            
            $parsed_details = $this->breakFileNameProductImageUplaod($file_name);            
            #--------------------------------------------------------------
            if ($parsed_details['success'] == 'false') {                
                return $this->responseArray('invalid file naming format');
            } else {                
                $image_name_break = explode('_', $_FILES['file']['name']);
                $data = $this->get('service.repo')->getProductDetailOnly(str_replace('-', ' ', $image_name_break[0]), $image_name_break[1]);                            
                if (count($data)==0) {
                    return $this->responseArray('Product not found');#------------------------------------------>
                }
                $parsed_details['product_id'] = $data[0]['id'];
                $product = $this->get('admin.helper.product')->find($parsed_details['product_id']);                
                $product_color = $this->get('admin.helper.productcolor')->findColorByProductTitle(strtolower($parsed_details['color_title']), $parsed_details['product_id']);
                $product_size = $this->get('admin.helper.productsizes')->findSizeByProductTitle(strtolower($parsed_details['size_title']), $parsed_details['product_id']);                
                $product_item_id = $this->get('admin.helper.productitem')->getProductItemByProductId($parsed_details['product_id'], $product_color->getId(), $product_size->getId());
                
                if (count($product_color) == 0) {
                    return $this->responseArray('color not found');#------------------------------------------>
                }
                if(count($product_size) == 0){
                    return $this->responseArray('Size not found');#------------------------------------------>
                }                
                if (count($product_item_id) == 0) {
                    $this->get('admin.helper.productitem')->addItem($product, $product_color, $product_size);
                }
                $product_item = $this->get('admin.helper.product')->findProductColorSizeItemViewByTitle($parsed_details);                                
                $product_item->file = $image_file;
                $product_item->upload();
                $this->get('admin.helper.productitem')->save($product_item);                
                $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
                $baseurl .'/'. $product_item->getWebPath();                
                $uploaded=false;                
                
                if(file_exists($product_item->getAbsolutePath())) {
                    $uploaded=true;
                }
                
                return $this->responseArray('File uploaded', $uploaded, $baseurl .'/'. $product_item->getWebPath());
            }
        }        
        return $this->responseArray('File is missing');

    }   //------------- End Image Uplaod Function
    

    
     #------------------------------------------------------------------------
     private function breakFileNameProductImageUplaod($request_array) {
        #Format: Regular_XL_Darl-Gray_Front-Open.png
        #Format: AG-Jeans_LSS1288_SBA_R_25.png
        #"Origami_001010_Regular_14_black.png
        # 2 Body Type
        # 3 Size Tilte
        # 4 Color Title and file Extention
        #last bit, view is optional
        #Citizens-of-Humanity_001n-001_brown_Regular_26
        
        $_exploded = explode("_", strtolower($request_array));
        if (count($_exploded) == 5) {
            $a['brand'] = str_replace("-", " ", $_exploded[0]);
            $a['style_id_number'] = str_replace("-", " ", $_exploded[1]);
            $a['color_title'] = str_replace("-", " ", $_exploded[2]);
            $a['body_type'] = !($this->container->get('admin.helper.utility')->isBodyType($_exploded[2])) ? "regular" : $_exploded[2];
            $_file_name = explode(".", $_exploded[4]);
            $a['size_title'] = str_replace("-", "_", $_file_name[0]);
            $a['success'] = 'true';
            return $a;
        } else {
            return array('message' => 'Invalid Format!', 'success' => 'false');
        }
    }

//----------------- End function Brack File Name
     private function responseArray($message, $success = false, $url = null) {
        return array(
            'success' => $success,
            'url' => $url,
            'message' => $message,
        );
    }

}
