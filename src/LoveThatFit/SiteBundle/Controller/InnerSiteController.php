<?php

namespace LoveThatFit\SiteBundle\Controller;
use LoveThatFit\SiteBundle\comparison;
use LoveThatFit\AdminBundle\Entity\ClothingType;
use LoveThatFit\AdminBundle\Entity\Product;
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

    public function productsAction($gender) {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT p FROM LoveThatFitAdminBundle:Product p WHERE p.gender = :gender')->setParameter('gender', $gender);
        return $this->renderList($query);
    }
    //----------------------------------- Whats New ..............
      public function productsLatestAction($gender) {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT p FROM LoveThatFitAdminBundle:Product p WHERE p.gender = :gender ORDER BY p.created_at DESC' )->setParameter('gender', $gender)->setMaxResults(20);
        return $this->renderList($query);
    }

    //----------------------------------- by Brand ..............
        public function productsByBrandAction($gender, $brand_id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findByGenderBrand($gender, $brand_id);
        return $this->render('LoveThatFitSiteBundle:InnerSite:_products.html.twig', array('products' => $entity));
    }

    //----------------------------------- By Clothing Type ..............
        public function productsByClothingTypeAction($gender, $clothing_type_id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findByGenderClothingType($gender, $clothing_type_id);
        return $this->render('LoveThatFitSiteBundle:InnerSite:_products.html.twig', array('products' => $entity));
    }

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
    public function productDetailAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()
            ->getRepository('LoveThatFitAdminBundle:Product')
            ->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product.');
        }
        
        return $this->render('LoveThatFitSiteBundle:InnerSite:_productDetail.html.twig', array('product' => $entity));
    }
    
    public function ajaxAction() {
        return $this->render('LoveThatFitSiteBundle:InnerSite:ajax.html.twig');        
    }
    
    public function compareAction($user_id, $product_id) {    
        $fit=new comparison($this->getMeasurement($user_id),  $this->getProduct($product_id));    
        return $this->render('LoveThatFitSiteBundle:InnerSite:determine.html.twig', array('data'=>$fit->determine(), 'json'=>  json_encode($fit->getDifference()), 'msg'=>  json_encode($fit->getMessageArray())));        
    }
    
    public function determineAction($user_id, $product_id) {
        
        $uid=$this->get('request')->request->get('user');
    $fit=new comparison($this->getMeasurement($user_id),  $this->getProduct($product_id));    
    return $this->render('LoveThatFitSiteBundle:InnerSite:determine.html.twig', array('data'=>$fit->determine()));        
    }
    
    private function renderList($query) {
        $entity = $query->getResult();
        return $this->render('LoveThatFitSiteBundle:InnerSite:_products.html.twig', array('products' => $entity));
    }
    
    
    private function getProduct($id)
    {
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->find($id);
        return $entity;
    }
    private function getMeasurement($id)
    {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('LoveThatFitUserBundle:Measurement')->findOneByUserId($id);
        
    }

}

?>