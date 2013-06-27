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

class ProductController extends Controller {
#-----------------Brand List Related To Size Chart For Registration Step2---------------------------------------------------#

    public function brandListSizeChartAction() {
        $total_record = count($this->getBrandArray());
        $data=array();
        $data['data']=$this->getBrandArray();
        return new Response($this->json_view($total_record, $data));
    }
#-----------------Size Chart Against The Brand Id For Registration Step2---------------------------------------------------#
    public function sizeChartsAction(){
        
         $handle = fopen('php://input','r');
         $jsonInput = fgets($handle);
         $request_array  = json_decode($jsonInput,true);
         $size_chart_helper=$this->get('admin.helper.sizechart');
         $size_chart=$size_chart_helper->sizeChartList($request_array);
         if($size_chart) {
             $size_chart_data=array();
             $size_chart_data=$size_chart;
         $total_record = count($size_chart);
         return new Response($this->json_view($total_record,$size_chart_data));
        }  
        else {
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
    public function byBrandClothingTypeAction()
    {
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
       
        $data['data']=array_merge($brand,$clothing_types);
       
        $data['brand_image_path'] = $baseurl;
       return new Response($this->json_view($total_record, $data));  
        
    }
 #-----------------------------Productlist Against Brand or Clothing Type -----------------------------------#   
 public function productlistAction()
 {
     $request = $this->getRequest();
     $handle = fopen('php://input','r');
     $jsonInput = fgets($handle);
     $request_array  = json_decode($jsonInput,true);
     $id=$request_array['id'];
     $type=$request_array['type'];
     $gender=$request_array['gender'];
    
    $products=Null;
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
                    ->findLattestProductWebService($gender);
        }
        if ($type == "new") {
            $products = $this->getDoctrine()
                    ->getRepository('LoveThatFitAdminBundle:Product')
                    ->findLattestProductWebService($gender);
        }    
       $data=array();
   #-------Fetching The Path------------#
     if($products) {
       /* if($products[0]['product_image'] )
       { 
          $product_id=$products[0]['id'];
          $productimage_path = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                 ->find($product_id);
        $images_path= $productimage_path->getdefalutImagePaths(); 
        $data['path']=$images_path;
       }*/
   
      
      $total_record=count($products);
      $data['data']=$products;
      $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/uploads/ltf/products/';
      $data['path']=$baseurl;
      return new Response($this->json_view($total_record, $data)); 
    }
    else
    {
          return new Response(json_encode(array('Message'=>'We can not find Product'))); 
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
            array_push( $brands_array,array('id'=>$i['id'],'brand_name'=>$i['name']));
        }
        return $brands_array;
    }

#---------------------------------------------------------------------------------------------------------#
}

// End of Class