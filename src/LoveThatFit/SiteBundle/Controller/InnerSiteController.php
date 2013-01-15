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
        $query = $em->createQuery('SELECT p FROM LoveThatFitAdminBundle:Product p WHERE p.gender = :gender' )->setParameter('gender', $gender)->setMaxResults(20);
        return $this->renderList($query);
    }

        public function productsByBrandAction($gender, $brand_id) {
        $em = $this->getDoctrine()->getManager();
#        $query = $em->createQuery('SELECT p FROM LoveThatFitAdminBundle:Product p WHERE p.brand_id = :brand_id AND  p.gender = :gender')->setParameters(array('gender' => $gender, 'brand_id' => $brand_id));
        $query = $em->createQuery('SELECT p FROM LoveThatFitAdminBundle:Product p WHERE p.gender = :gender')->setParameter('gender', $gender);
        return $this->renderList($query);
    }

        public function productsByClothingTypeAction($gender, $clothing_type_id) {
        $em = $this->getDoctrine()->getManager();
#        $query = $em->createQuery('SELECT p FROM LoveThatFitAdminBundle:Product p WHERE p.clothing_type_id = :clothing_type_id AND  p.gender = :gender')->setParameters(array('gender' => $gender, 'clothing_type_id' => $clothing_type_id));
                $query = $em->createQuery('SELECT p FROM LoveThatFitAdminBundle:Product p WHERE p.gender = :gender')->setParameter('gender', $gender);
        return $this->renderList($query);
    }

    
    
    public function ajaxAction() {
        
        return $this->render('LoveThatFitSiteBundle:InnerSite:ajax.html.twig');
        
    }
    
    private function renderList($query) {
        $entity = $query->getResult();
        return $this->render('LoveThatFitSiteBundle:InnerSite:products.html.twig', array('products' => $entity));
    }

}

?>