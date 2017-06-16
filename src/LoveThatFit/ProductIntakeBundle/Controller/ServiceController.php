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
    public function productImageUploadAction(Request $request) {
        
        //$image_name_break = explode('_',  $request->get('image_name'));$_FILES['file']['name']
        $image_name_break = explode('_',  $_FILES['file']['name']);
        $data = $this->get('service.repo')->getProductDetailOnly(str_replace('-',' ',$image_name_break[0]), $image_name_break[1]); 
        $rsult = $this->imageUploadProduct($_FILES, $data[0]['id']);
         try { 
        return new JsonResponse([
            'success' => true,
            'data'    =>  $rsult
             ]);
         } catch (\Exception $exception) {
        return new JsonResponse([
                'success' => false,
                'code'    => $exception->getCode(),
                'message' => $exception->getMessage(),
            ]);
         } 
        return "okay Boss";
    }
    
    
      #---------------------------------------Image Uplaod Function -------------------------------
    public function imageUploadProduct($FILES , $product_id)
    {  
        $allowed = array('png', 'jpg');
        //$product_id = $_POST['product_id'];
        $request = $this->getRequest();
        if (isset($FILES['file']) && $_FILES['file']['error'] == 0) {
            $extension = pathinfo($FILES['file']['name'], PATHINFO_EXTENSION);
            $file_name = $FILES['file']['name'];

            #--------------------------------------------------------------

            $parsed_details = $this->breakFileNameProductImageUplaod($file_name, $product_id);
           // return $parsed_details ;
            
            
            if ($parsed_details['success'] == 'false') {
                //return new Response($file_name.$parsed_details['message']);
                $this->error_string .= $this->get('session')->getFlash('error-on-addcolor') . $file_name . ' : ' . $parsed_details['message']. ' , ';
            } else {

                $product = $this->get('admin.helper.product')->find($parsed_details['product_id']);

                /* Checked Color are available for this product */
                $color_id_result = $this->get('admin.helper.productcolor')->findColorByProductTitle(strtolower($parsed_details['color_title']), $parsed_details['product_id']);
                if (count($color_id_result) == 0) {
                    /* Create Product Item for this product */
                    $color_id_result = new ProductColor();
                    $color_id_result->setProduct($product);
                    $color_id_result->setTitle(strtolower(strtolower($parsed_details['color_title'])));
                    $this->get('admin.helper.productcolor')->save($color_id_result);
                }

                // Find if the new color has been successfully added
                $added_color_id_result = $this->get('admin.helper.productcolor')->findColorByProductTitle(strtolower($parsed_details['color_title']), $parsed_details['product_id']);

                //Make Default Color
                if($parsed_details['set_default'] == "yes"){
                    $this->get('admin.helper.product')->updateDisplayColor($product, $added_color_id_result);
                }

                /*Add color pattern and color image*/
                if(array_key_exists('image_type',$parsed_details)){
                    $imageFile = $FILES;//$request->files->get('upl');
                    $updated_image_message = '';

                    /*Color Pattern Type */
                    if($parsed_details['image_type'] == 'colorpatterntype'){
                        $added_color_id_result->file = $imageFile;
                        $pattern_type = $added_color_id_result->uploadTemporaryImage();
                        $added_color_id_result->tempPattern = $pattern_type['image_name'];
                        $added_color_id_result->savePattern();
                        $this->get('admin.helper.productcolor')->save($added_color_id_result);
                        $updated_image_message = 'Pattern Image Updated';
                    }
                    /*Color Image Type */
                    if($parsed_details['image_type'] == 'colorimagetype'){
                        $added_color_id_result->file = $imageFile;
                        $image_type = $added_color_id_result->uploadTemporaryImage();
                        $added_color_id_result->tempImage = $image_type['image_name'];
                        $added_color_id_result->saveImage();
                        $this->get('admin.helper.productcolor')->save($added_color_id_result);
                        $updated_image_message = 'Image Updated';
                    }
                    $this->get('session')->setFlash('success', $updated_image_message);
                    return new response('{"status":'.$updated_image_message.'}');

                }
                /*Add color pattern and color image*/
                $find_size_by_title_productid = $this->get('admin.helper.productsizes')->findSizeByProductTitle(strtolower($parsed_details['size_title']), $parsed_details['product_id']);

                /* If size not available then show error */
                if(count($find_size_by_title_productid) == 0){
                    $this->error_string .= $this->get('session')->getFlash('error-on-addcolor') . $file_name . ' : Size not available ,';
                    $this->get('session')->setFlash('error-on-addcolor', $this->error_string);
                    return new response('{"status":"error"}');
                }
                /* Checked Product item are available for this product */
                $product_id_result = $this->get('admin.helper.productitem')->getProductItemByProductId($parsed_details['product_id'], $added_color_id_result->getId(), $find_size_by_title_productid->getId());

                if (count($product_id_result) == 0) {
                    //**/
                    /* Create Product Item for this product */
                    $p_color = $this->get('admin.helper.productcolor')->findColorByProductTitle(strtolower($parsed_details['color_title']), $parsed_details['product_id']);
                    $p_size = $this->get('admin.helper.productsizes')->findSizeByProductTitle(strtolower($parsed_details['size_title']), $parsed_details['product_id']);
                    $this->get('admin.helper.productitem')->addItem($product, $p_color, $p_size);
                }

                $product_item = $this->get('admin.helper.product')->findProductColorSizeItemViewByTitle($parsed_details);
                $imageFile = $request->files->get('upl');

                if (array_key_exists('view_title', $parsed_details)) {

                    #find matching color view object
                    $product_color_view = $product_item->getProductColorViewByTitle($parsed_details['view_title']);
                    #create new piece & set item & color view
                    $product_item_piece = $this->get('admin.helper.product.item.piece')->findOrCreateNew($product_item, $product_color_view);
                    #set file
                    $product_item_piece->file = $imageFile;
                    #save & upload
                    $this->get('admin.helper.product.item.piece')->save($product_item_piece);
                    $this->get('session')->setFlash('success', 'Product Item has been created');
                    return new response('{"status":"view updated"}');
                } else {
                    $product_item->file = $imageFile;
                    $product_item->upload();
                    $this->get('admin.helper.productitem')->save($product_item);
                    $this->get('session')->setFlash('success', 'Remaining Product Items and Colors have been added/updated');
                    return new response('{"status":"Remaining Product Items and Colors have been added"}');
                }
            }
        }
        $this->get('session')->setFlash('error-on-addcolor', $this->error_string);
        return new response('{"status":"error"}');

    }   //------------- End Image Uplaod Function
    

    
     #------------------------------------------------------------------------
    public function breakFileNameProductImageUplaod($request_array, $product_id) {

        #Format: Regular_XL_Darl-Gray_Front-Open.png
        #Format: AG-Jeans_LSS1288_SBA_R_25.png
        #"Origami_001010_Regular_14_black.png
        # 2 Body Type
        # 3 Size Tilte
        # 4 Color Title and file Extention
        #last bit, view is optional
        $request_array = strtolower($request_array);
        $_exploded = explode("_", $request_array);
        $color_tilte = explode(".", $_exploded[4]);
        $a = array('product_id' => $product_id);        
        $a['set_default'] = 'Yes';      
        if (count($_exploded) == 5) {
        $a['color_title'] = str_replace("-", " ", $color_tilte[0]);
        $a['body_type'] = !($this->container->get('admin.helper.utility')->isBodyType($_exploded[2])) ? "regular" : $_exploded[2];
        $a['file_name'] = 'item_image.' . $color_tilte[1];
        $a['size_title'] = str_replace("-", "_", $_exploded[3]);
        $a['message'] = 'Done';
        $a['success'] = 'true';
        return $a;
         } else {
               return array('message' => 'Invalid Format!', 'success' => 'false');
          }
                  
      
    
    } //----------------- End function Brack File Name
}
