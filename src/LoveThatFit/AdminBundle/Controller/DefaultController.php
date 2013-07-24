<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {       
        $first  = strtotime('first day this month');
        $months = array();
        for($i=6;$i>=1;$i--) {
        $months=date('M', strtotime("-$i months", $first));
       // $result=$this->getLastSixMonthSignUps($months);
    }

    
 
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
                    'users'=>$this->getUsersData(),
		));
        
        
    }
    
     private function calculateStatistics($countResult,$totalRecord)
    {
       $statistics=($countResult/$totalRecord)*100;
       return $statistics;
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
    
    private function getLastSixMonthSignUps()
    {
        $monthly_users_signups = $this->getDoctrine()
                ->getRepository('LoveThatFitUserBundle:User')
                 ->findUserByMonth();
	
        
        
        return $monthly_users_signups;
    }
    
    private function getUsersData()
    {
        $users=array();
        $six_months_signups=$this->getLastSixMonthSignUps();
        $users['six_months_signups']=$six_months_signups[0]['total'];
    return $users;
        
    }
    /*
    private function getUserByAgeGroup($startage,$endage)
    {
        $em = $this->getDoctrine()->getManager();
        $UserTypeObj = $this->getDoctrine()->getRepository('LoveThatFitUserBundle:User');
         $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitUserBundle:User')
                 ->findUserByAgeGroup($startage,$endage);
		$rec_count = count($UserTypeObj->findUserByAgeGroup($startage,$endage));
        return $rec_count;
    }
     */
    
}
