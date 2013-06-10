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

    public function createFormAction(Request $request) {

        $defaultData = array('message' => 'Enter your email address');
        $form = $this->createFormBuilder($defaultData)
                ->add('email', 'text')
                ->add('password', 'password')
                ->getForm();
        return $this->render('LoveThatFitWebServiceBundle::loginForm.html.twig', array(
                    'form' => $form->createView()));
    }

    public function loginAction(Request $request) {
        $defaultData = array('message' => 'Enter your email address');
        $form = $this->createFormBuilder($defaultData)
                ->add('email', 'text')
                ->add('password', 'password')
                ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            $data = $form->getData();

            $email = $data['email'];
            $password = $data['password'];
            
            $em = $this->getDoctrine()->getManager();
            $entity =$em->getRepository('LoveThatFitUserBundle:User')->findOneBy(array('email'=>$email));
           
            if (count($entity) >0) {

                $user_db_password = $entity->getPassword();
                $salt_value_db = $entity->getSalt();

                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($entity);
                $password_old_enc = $encoder->encodePassword($password, $salt_value_db);
                if ($user_db_password == $password_old_enc) {
                    $first_name=$entity->getFirstName();
                    $last_name=$entity->getLastName();
                    $gender=$entity->getGender();
                    $birth_date=$entity->getBirthDate();
                   $userinfo=array();
                   $userinfo[]=$email;
                   $userinfo[]=$first_name;
                   $userinfo[]=$last_name;
                   $userinfo[]=$gender;
                   $userinfo[]=$birth_date;
                    return new Response(json_encode($userinfo));
                } else {
                     return new Response(json_encode('Login Fail'));
                }
            }
           else {
               return new Response(json_encode('Invalid Email Address'));
           }  
       }
    }

    //--------------------------------------------------Brand Type----------------------///   

    public function brandListAction(Request $request, $page_number, $sort = 'id') {
        
       $limit = 5;
        $brandObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Brand');

        $brand = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Brand')
                ->findAllBrand($page_number, $limit, $sort);
        $rec_count = count($brandObj->countAllRecord());
        $cur_page = $page_number;

        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }

         $count_rec=count($brandObj);  
         $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/uploads/ltf/brands/';
         $data=array();
   
        $data['data']=$brand;
        $data['path']=$baseurl;
        return new Response($this->json_view($count_rec,$data)); 
    }

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
    
  
    //------Proudct List By Product Detail----------------------///   
    public function productDetailListAction(Request $request,$product_id)
    {
       $em = $this->getDoctrine()->getManager();
       $products = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->productDetail($product_id);
        
        $count_rec=count($products); 
        
        $data=array();
        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/uploads/ltf/products/';
        $brand = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/uploads/ltf/brands/';
        $pattern = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/uploads/ltf/products/pattern/';
        $data['data']=$products;
        $data['product_color_path']=$baseurl;
        $data['brand_path']=$brand;
        $data['pattern_path']=$pattern;
        
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