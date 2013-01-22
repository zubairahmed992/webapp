<?php

namespace LoveThatFit\SiteBundle\Controller;
use LoveThatFit\SiteBundle\comparison;
use LoveThatFit\AdminBundle\Entity\ClothingType;
use LoveThatFit\AdminBundle\Entity\Product;
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
    
      public function productsLatestAction($gender) {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT p FROM LoveThatFitAdminBundle:Product p WHERE p.gender = :gender ORDER BY p.created_at DESC' )->setParameter('gender', $gender)->setMaxResults(20);
        return $this->renderList($query);
    }

        public function productsByBrandAction($gender, $brand_id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findByGenderBrand($gender, $brand_id);
        return $this->render('LoveThatFitSiteBundle:InnerSite:products.html.twig', array('products' => $entity));
    }

        public function productsByClothingTypeAction($gender, $clothing_type_id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findByGenderClothingType($gender, $clothing_type_id);
        return $this->render('LoveThatFitSiteBundle:InnerSite:products.html.twig', array('products' => $entity));
    }

        public function productsClothingTypeAction($gender) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findSampleClothingTypeGender($gender);
        return $this->render('LoveThatFitSiteBundle:InnerSite:products.html.twig', array('products' => $entity));
    }
        
    public function productDetailAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()
            ->getRepository('LoveThatFitAdminBundle:Product')
            ->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product.');
        }
        
        return $this->render('LoveThatFitSiteBundle:InnerSite:productDetail.html.twig', array('product' => $entity));
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
        return $this->render('LoveThatFitSiteBundle:InnerSite:products.html.twig', array('products' => $entity));
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
        #$user = $em->getRepository('LoveThatFitUserBundle:User')->find($id);
        #return $user->getMeasurement();
        return $em->getRepository('LoveThatFitUserBundle:Measurement')->findOneByUserId($id);
        
    }

}

?>