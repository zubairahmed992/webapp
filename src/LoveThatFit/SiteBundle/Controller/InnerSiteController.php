<?php

namespace LoveThatFit\SiteBundle\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Yaml\Dumper;
use LoveThatFit\SiteBundle\Comparison;
use LoveThatFit\SiteBundle\Algorithm;
use LoveThatFit\SiteBundle\Cart;
use LoveThatFit\AdminBundle\ImageHelper;
use LoveThatFit\AdminBundle\Entity\ClothingType;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ProductItem;
use LoveThatFit\AdminBundle\Entity\Brand;
use LoveThatFit\UserBundle\Entity\Measurement;
use LoveThatFit\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InnerSiteController extends Controller {

    //-------------------------------------------------------------------------

    public function indexAction() {
        return $this->render('LoveThatFitSiteBundle:InnerSite:index.html.twig');
    }
////////////////////////////////// Product Slider /////////////////////////////////////////////////////////////////
    public function productsAction($gender, $page_number=0, $limit=0) {
        $em = $this->getDoctrine()->getManager();        
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findByGender($gender, $page_number, $limit);
        return $this->renderProductTemplate($entity, $page_number, $limit);
    }
    
    //----------------------------------- Whats New ..............
      public function productsLatestAction($gender, $page_number=0, $limit=0) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findByGenderLatest($gender, $page_number, $limit);
        return $this->renderProductTemplate($entity, $page_number, $limit);
    }

    //----------------------------------- by Brand ..............
        public function productsByBrandAction($gender, $brand_id, $page_number=0, $limit=0) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findByGenderBrand($gender, $brand_id, $page_number, $limit);
        return $this->renderProductTemplate($entity, $page_number, $limit);
        
        }

    //----------------------------------- By Clothing Type ..............
        public function productsByClothingTypeAction($gender, $clothing_type_id, $page_number=0, $limit=0) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findByGenderClothingType($gender, $clothing_type_id, $page_number, $limit);
        return $this->renderProductTemplate($entity, $page_number, $limit);
    }
//------------------------------------------- render method ----------------------------------------
        private function renderProductTemplate($entity, $page_number, $limit){        
            return $this->render('LoveThatFitSiteBundle:InnerSite:_products.html.twig', array('products' => $entity, 'page_number'=>$page_number, 'limit'=> $limit, 'row_count'=>count($entity)));
        }
///////////////////////////////////////////////////////////////////////////////////////////////////

    
    //----------------------------------- Sample Clothing Type ..............
        public function productsClothingTypeAction($gender) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findSampleClothingTypeGender($gender);
        return $this->render('LoveThatFitSiteBundle:InnerSite:_products.html.twig', array('products' => $entity));
    }
    
    
    //----------------------------------- List Clothing Types ..............
        public function clothingTypesAction() {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:ClothingType')->findAll();
        return $this->render('LoveThatFitSiteBundle:InnerSite:_clothingTypes.html.twig', array('clothing_types' => $entity));
    }
    
//----------------------------------- List Brands ..............
        public function brandsAction() {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Brand')->findAll();
        return $this->render('LoveThatFitSiteBundle:InnerSite:_brands.html.twig', array('brands' => $entity));
    }

//----------------------------------- Product Detail ..............        
    public function productDetailAction($id, $product_color_id, $product_size_id) {
        $product_color = null;
        $product_size = null;
        $product_item = null;
        
        $product = $this->getDoctrine()
            ->getRepository('LoveThatFitAdminBundle:Product')
            ->find($id);
        
        if ($product_color_id){
            $product_color = $this->getDoctrine()
            ->getRepository('LoveThatFitAdminBundle:ProductColor')
            ->find($product_color_id);
        }else{
            $product_color = $product->getDisplayProductColor();            
        }
        
        
        if ($product_size_id){
        $product_size = $this->getDoctrine()
            ->getRepository('LoveThatFitAdminBundle:ProductSize')
            ->find($product_size_id);
        }else{
            $color_sizes_array = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ProductColor')
                ->getSizeArray($product_color->getId());
            $psize=array_shift($color_sizes_array);
            $product_size = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ProductSize')
                ->find($psize['id']);
               //->getRepository('LoveThatFitAdminBundle:ProductSize')
           //->find(array_shift($color_sizes_array)['id']);
     
                        
        }
        
        //2) else condition for random size of that color
        
        if ($product_size && $product_color){            
        $product_item = $this->getDoctrine()
            ->getRepository('LoveThatFitAdminBundle:ProductItem')
            ->findByColorSize($product_color->getId(), $product_size->getId());                 
        }
        
        if (!$product) {
            throw $this->createNotFoundException('Unable to find Product.');
        }
        
        return $this->render('LoveThatFitSiteBundle:InnerSite:_product_detail.html.twig', 
                array('product' => $product,
                    'productColor' => $product_color,
                    'productSize' => $product_size,
                    'productItem' => $product_item,
                    ));
    }
    
    
   
//----------------------------------------------------------------------------------    
    public function productsByMyClosetAction($page_number=0 , $limit=0)
    {
        $user_id=$this->get('security.context')->getToken()->getUser()->getId(); 
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findProductItemByUser($user_id , $page_number , $limit);
        return $this->render('LoveThatFitSiteBundle:InnerSite:_closet_products.html.twig', array('product' => $entity));
               
    }
//----------------------------------------------------------------------------------    
    public function countMyColosetAction()
    {
       $user_id=$this->get('security.context')->getToken()->getUser()->getId();
       $em = $this->getDoctrine()->getManager();
       $brandObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product');
                $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                 ->countMyCloset($user_id);
		$rec_count = count($brandObj->countMyCloset($user_id));
       return new Response($rec_count);
    }
//----------------------------------------------------------------------------------
        public function deleteMyClosetAction($id)
    {
        $user=$this->get('security.context')->getToken()->getUser();         
        $product_item = $this->getProductItemById($id);
        $em = $this->getDoctrine()->getManager();        
        $product_item->removeUser($user); 
        $user->removeProductItem($product_item); 
        $em->persist($product_item);        
        $em->persist($user);       
        $em->flush();
        $this->get('session')->setFlash('success', 'Product Item Successfuly Deleted.');
        return $this->getMyClosetList();
    }
    
        
    //-------------------------------------------------------------------
    public function ajaxAction() {
        return $this->render('LoveThatFitSiteBundle:InnerSite:ajax.html.twig');        
    }
    //-------------------------------------------------------------------
    public function emailAction($id) {    
       
       // $user= $this->get('security.context')->getToken()->getUser();
        $product = $this->getProduct($id);
    
        $session = $this->get("session");
        
    }
    //-------------------------------------------------------------------
    public function getFeedBackJSONAction($user_id, $product_item_id) {        
    
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('LoveThatFitUserBundle:User')->find($user_id);
        $productItem = $this->getProductItemById($product_item_id);
        
          if (!$user)
            return new Response("User Not found!");
        
        if (!$productItem)
            return new Response("Product not found!");
        
        $fit = new Algorithm($user, $productItem);
        
        return $this->render('LoveThatFitSiteBundle:InnerSite:determine.html.twig', 
                array('data' => $fit->getFeedBackJson(),
                    ));
    }
    //-------------------------------------------------------------------
   public function getFeedBackListAction($product_item_id) {        
        $user= $this->get('security.context')->getToken()->getUser();
        $productItem = $this->getProductItemById($product_item_id);
                
        if (!is_object($this->get('security.context')->getToken()->getUser()))
            return new Response("User Not found, Log in required!");
        
        if (!$productItem)
            return new Response("Product not found!");
        
        $fit = new Algorithm($user, $productItem);
        return $this->render('LoveThatFitSiteBundle:InnerSite:_fitting_feedback.html.twig', array('product' => $productItem->getProduct(), 'product_item' => $productItem, 'data' => $fit->getFeedBackArray()));        
    }
   //-------------------------------------------------------------------
    
    public function addToCloestAction($product_item_id)
    {
       $user_id=$this->get('security.context')->getToken()->getUser()->getId();
       $em = $this->getDoctrine()->getManager();
       $brandObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product');
                $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                 ->countMyCloset($user_id);
		$rec_count = count($brandObj->countMyCloset($user_id));        
                if($rec_count>25)
                {
                   $this->get('session')->setFlash('warning', 'Please Remove Some Like You can not like more than 25.');
                    return new response('try');
                }else
                {
                    $user=$this->get('security.context')->getToken()->getUser();         
                    $product_item = $this->getProductItemById($product_item_id);        
                    $em = $this->getDoctrine()->getManager();        
                    $product_item->addUser($user); 
                    $user->addProductItem($product_item); 
                    $em->persist($product_item);        
                    $em->persist($user);       
                    $em->flush();                    
                    return new response('success');
                }
        
        
        
        
    }

    //-------------------------------------------------------------------
    private function getProduct($id)
    {
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->find($id);
        return $entity;
    }
    
    //-------------------------------------------------------------------
    private function getMeasurement($id)
    {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('LoveThatFitUserBundle:Measurement')->findOneByUserId($id);        
    }
    //-------------------------------------------------------------------
    private function getProductItemById($id)
    {       
            $product_item = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ProductItem')
                ->find($id);
        return $product_item;
    }
//-------------------------------------------------------------------
    private function getMyClosetList($page_number=0 , $limit=0)
    {
        $user_id=$this->get('security.context')->getToken()->getUser()->getId(); 
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findProductItemByUser($user_id , $page_number=0 , $limit=0);
        return $this->render('LoveThatFitSiteBundle:InnerSite:_closet_products.html.twig', array('product' => $entity));
    }
    
    
}



?>

