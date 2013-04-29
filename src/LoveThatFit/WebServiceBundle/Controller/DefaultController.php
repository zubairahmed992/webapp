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
            $id = 2;
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);


            $user_old_password = $entity->getPassword();

            return new response(json_encode($user_old_password));
            $salt_value_old = $entity->getSalt();


            $userForm = $this->createForm(new UserPasswordReset(), $entity);
            $userForm->bind($request);
            $data = $userForm->getData();

            $oldpassword = $data->getOldpassword();

            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($entity);
            $password_old_enc = $encoder->encodePassword($oldpassword, $salt_value_old);
        }

        return new response(json_encode($email));
    }

    //--------------------------------------------------Brand Type----------------------///   

    public function brandListAction(Request $request, $page_number, $sort = 'id') {
        $em = $this->getDoctrine()->getManager();
        $limit = 5;
        $brandObj = $em->getRepository('LoveThatFitAdminBundle:Brand');
        $entity = $em
                ->getRepository('LoveThatFitAdminBundle:Brand')
                ->findAll($page_number, $limit, $sort);
        $rec_count = count($brandObj->countAllRecord());
        $cur_page = $page_number;


        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }

        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        $count_rec=count($entity);  

        return new Response($this->json_view($count_rec,$entity));
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
     return new Response($this->json_view($count_rec,$products)); 
    }
  //--------------------------Proudct List By Brand Wtih Gender----------------------///   
    public function productListByBrandAction(Request $request,$brand_id,$gender)
    {
       $em = $this->getDoctrine()->getManager();
       $products = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->productListByBrand($brand_id,$gender);
        
        $count_rec=count($products); 
        return new Response($this->json_view($count_rec,$products));
    }

    //--------------------------Proudct List By Clothing Type With Gender----------------------///   
    public function productListByClothingTypeAction(Request $request,$clothing_type_id,$gender)
    {
       $em = $this->getDoctrine()->getManager();
       $products = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->productListByClothingType($clothing_type_id,$gender);
        
        $count_rec=count($products); 
        return new Response($this->json_view($count_rec,$products));
    }
 //------Proudct List By Clothing Type and By Brand  With Gender----------------------///   
    public function productListByBrandClothingTypeAction(Request $request,$brand_id,$clothing_type_id,$gender)
    {
       $em = $this->getDoctrine()->getManager();
       $products = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->productListByBrandClothingType($brand_id,$clothing_type_id,$gender);
        
        $count_rec=count($products); 
       
       return new Response($this->json_view($count_rec,$products));
    }
    
  
    //------Proudct List By Product Detail----------------------///   
    public function productDetailListAction(Request $request,$product_id)
    {
       $em = $this->getDoctrine()->getManager();
       $products = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->productDetail($product_id);
        
        $count_rec=count($products); 
        return new Response($this->json_view($count_rec,$products));
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