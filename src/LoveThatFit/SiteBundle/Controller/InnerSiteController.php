<?php

namespace LoveThatFit\SiteBundle\Controller;

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
    
    public function determineTestAction() {
    
    $form = $this->createFormBuilder()
        ->add('user', 'text')
        ->add('product', 'text')
        ->getForm();
        
        return $this->render('LoveThatFitSiteBundle:InnerSite:determine.html.twig', array(
                    'form' => $form->createView()));        
    }
    
    public function determineAction(Request $request) {
    
        
    $form = $this->createFormBuilder()
        ->add('user', 'text')
        ->add('product', 'text')
        ->getForm();
     
    if ($request->isMethod('POST')) {
            $form->bind($request);
            $data = $form->getData();            
            return $this->render('LoveThatFitSiteBundle:InnerSite:determine.html.twig', array(
                    'form' => $form->createView(), 'data'=>"sdsd"));        
        }
        return $this->render('LoveThatFitSiteBundle:InnerSite:determine.html.twig', array(
                    'form' => $form->createView(), 'data'=>"tt"));        
    }
    
    private function renderList($query) {
        $entity = $query->getResult();
        return $this->render('LoveThatFitSiteBundle:InnerSite:products.html.twig', array('products' => $entity));
    }
    

}

?>