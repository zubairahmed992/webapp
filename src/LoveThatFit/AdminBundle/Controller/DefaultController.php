<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {       
        $ProductByBrand=  $this->getProductByBrand();
        return $this->render('LoveThatFitAdminBundle:Default:index.html.twig', array(
		    'totalclotingtypes'=>$this->countAllClothingType(),
                    'criteriaTop'=>$this->countStatistics('Top'),
                    'criteriaBottom'=>$this->countStatistics('Bottom'),
                    'criteriaDress'=>$this->countStatistics('Dress'),
		    'totalbrands' => $this->countAllBrands(),
                    'totalproducts'=>  $this->countAllListProduct(),
                    'femaleProduct'=>  $this->countProductsByGender('f'),
                    'maleProduct'=>  $this->countProductsByGender('m'),
                    'topProduct'=>$this->countProductsByType('Top'),
                    'bottomProduct'=>$this->countProductsByType('Bottom'),
                    'dressProduct'=>$this->countProductsByType('Dress'),
                    'brandproduct'=>$ProductByBrand,
                    'totalusers'=>  $this->getAllUserList(),
                    'femaleUsers'=>$this->getUserByGender('f'),
                    'maleUsers'=>$this->getUserByGender('m'),
		));
        
        
    }
    
    
    private function countAllClothingType()
    {
        $em = $this->getDoctrine()->getManager();
        $ClothingTypeObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:ClothingType');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ClothingType')
                 ->findAllRecord();
		$totalRecord = count($ClothingTypeObj->findAllRecord());
        return $totalRecord;
    }
    
    
    private function countStatistics($target)
    {
        $em = $this->getDoctrine()->getManager();
        $ClothingTypeObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:ClothingType');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ClothingType')
                 ->findStatisticsBy($target);
		$rec_count = count($ClothingTypeObj->findStatisticsBy($target));
        return $rec_count;
    }
    
    
    
    
    private function countAllBrands()
    {
        $em = $this->getDoctrine()->getManager();
        $BrandTypeObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Brand');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Brand')
                 ->countAllRecord();
		$rec_count = count($BrandTypeObj->countAllRecord());
        return $rec_count;
    }
    
    
    
    
    
    
   private function countAllListProduct()
   {
      $em = $this->getDoctrine()->getManager();
      $ProductTypeObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product');
       $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                 ->countAllRecord();
		$rec_count = count($ProductTypeObj->countAllRecord());
        return $rec_count; 
   }
    
   private function countProductsByGender($gender)
    {
        $em = $this->getDoctrine()->getManager();
        $ProductTypeObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                 ->findPrductByGender($gender);
		$rec_count = count($ProductTypeObj->findPrductByGender($gender));
        return $rec_count;
    }

    private function countProductsByType($target)
    {
        $em = $this->getDoctrine()->getManager();
        $ProductTypeObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                 ->findPrductByType($target);
		$rec_count = count($ProductTypeObj->findPrductByType($target));
        return $rec_count;
    }    
    
    private function getProductByBrand()
    {
      $em = $this->getDoctrine()->getManager();     
      $entity = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product')
                 ->findPrductByBrand();		
        return $entity;
    }
    
    private function getUserByGender($gender)
    {
        $em = $this->getDoctrine()->getManager();
        $UserTypeObj = $this->getDoctrine()->getRepository('LoveThatFitUserBundle:User');
         $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitUserBundle:User')
                 ->findUserByGender($gender);
		$rec_count = count($UserTypeObj->findUserByGender($gender));
        return $rec_count;
    }
    
    private function getAllUserList()
    {
        $em = $this->getDoctrine()->getManager();
        $UserTypeObj = $this->getDoctrine()->getRepository('LoveThatFitUserBundle:User');
         $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitUserBundle:User')
                 ->findAll();
		$rec_count = count($UserTypeObj->findAll());
        return $rec_count;
    }
}
