<?php

namespace LoveThatFit\WebServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use LoveThatFit\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use LoveThatFit\AdminBundle\Entity\Brand;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ClothingType;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;


class DefaultController extends Controller {

   //--------------------------------------------------Clothing Type----------------------///   
    public function clothingTypeListAction(Request $request, $page_number, $sort = 'id') {
        $limit = 5;
        $clothingObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:ClothingType');

        $clothing_types = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ClothingType')
                ->findAllClothingType($page_number, $limit, $sort);
        $rec_count = count($clothingObj->countAllRecord());
        $cur_page = $page_number;

        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }

        $count_rec=count($clothing_types);  
        return new Response($this->json_view($count_rec,$clothing_types));
    }

    //--------------------------------------------------Proudct List----------------------///   
    public function productListAction(Request $request)
    {
       $em = $this->getDoctrine()->getManager();
       $products = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->productList();
     $count_rec=count($products); 
     $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/uploads/ltf/products/';
     $data=array();
   
     $data['data']=$products;
     $data['path']=$baseurl;
     return new Response($this->json_view($count_rec,$data)); 
    }
  //--------------------------Proudct List By Brand Wtih Gender----------------------///   
    public function productListByBrandAction(Request $request,$brand_id,$gender)
    {
       $em = $this->getDoctrine()->getManager();
       $products = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->productListByBrand($brand_id,$gender);
        
        $count_rec=count($products); 
        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/uploads/ltf/products/';
        $data=array();
   
        $data['data']=$products;
        $data['path']=$baseurl;
        return new Response($this->json_view($count_rec,$data));
    }

    //--------------------------Proudct List By Clothing Type With Gender----------------------///   
    public function productListByClothingTypeAction(Request $request,$clothing_type_id,$gender)
    {
       $em = $this->getDoctrine()->getManager();
       $products = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->productListByClothingType($clothing_type_id,$gender);
        $data=array();
        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/uploads/ltf/products/';
        $data['data']=$products;
        $data['path']=$baseurl;
        $count_rec=count($products); 
        return new Response($this->json_view($count_rec,$data));
    }
 //------Proudct List By Clothing Type and By Brand  With Gender----------------------///   
    public function productListByBrandClothingTypeAction(Request $request,$brand_id,$clothing_type_id,$gender)
    {
       $em = $this->getDoctrine()->getManager();
       $products = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->productListByBrandClothingType($brand_id,$clothing_type_id,$gender);
        
        $count_rec=count($products); 
         $data=array();
        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/uploads/ltf/products/';
        $data['data']=$products;
        $data['path']=$baseurl;
       return new Response($this->json_view($count_rec,$data));
    }
    
  
   
    //---------My Closet listing--------------------------------------------//
    public function myClostListAction($user_id)
    {
        
       $em = $this->getDoctrine()->getManager();
       $brandObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product');
                $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                 ->countMyCloset($user_id);
      $rec_count = count($brandObj->countMyCloset($user_id)); 
       return new Response($this->json_view($rec_count,$brandObj));          
        
        
    }
    #---------------------------Render Json--------------------------------------------------------------------#

    private function json_view($rec_count,$entity) {
         if ($rec_count > 0) {
            $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
                        JsonEncoder()));
            return $serializer->serialize($entity, 'json');
        } else {
            return json_encode(array('msg'=>'Record Not Found'));
        }
    }

}