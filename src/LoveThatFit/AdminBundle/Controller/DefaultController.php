<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

    public function indexAction() {
        $first = strtotime('first day this month');
        $months = array();
        for ($i = 6; $i >= 1; $i--) {
            $months = date('M', strtotime("-$i months", $first));
            // $result=$this->getLastSixMonthSignUps($months);
        }
        $months='Month Name';
        $totalusers='Users';
        $agegopus="Age in years";

        $ProductByBrand = $this->getProductByBrand();
        return $this->render('LoveThatFitAdminBundle:Default:index.html.twig', array(
                    'totalclotingtypes' => $this->countAllClothingType(),
                    'criteriaTop' => $this->countStatistics('Top'),
                    'criteriaBottom' => $this->countStatistics('Bottom'),
                    'criteriaDress' => $this->countStatistics('Dress'),
                    'totalbrands' => $this->countAllBrands(),
                    'totalproducts' => $this->countAllListProduct(),
                    'femaleProduct' => $this->countProductsByGender('f'),
                    'maleProduct' => $this->countProductsByGender('m'),
                    'topProduct' => $this->countProductsByType('Top'),
                    'bottomProduct' => $this->countProductsByType('Bottom'),
                    'dressProduct' => $this->countProductsByType('Dress'),
                    'brandproduct' => $ProductByBrand,
                    'users' => $this->getUsersData(),   
                    'noofmonths'=>$months,
                    'totalusers'=>$totalusers,
                    'ageinyears'=>$agegopus,
                ));
    }

    //--------------------------------------------------------------------------
    private function calculateStatistics($countResult, $totalRecord) {
        $statistics = ($countResult / $totalRecord) * 100;
        return $statistics;
    }

    //--------------------------------------------------------------------------
    private function countAllClothingType() {
        $em = $this->getDoctrine()->getManager();
        $ClothingTypeObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:ClothingType');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ClothingType')
                ->findAllRecord();
        $totalRecord = count($ClothingTypeObj->findAllRecord());
        return $totalRecord;
    }

    //--------------------------------------------------------------------------
    private function countStatistics($target) {
        $em = $this->getDoctrine()->getManager();
        $ClothingTypeObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:ClothingType');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ClothingType')
                ->findStatisticsBy($target);
        $rec_count = count($ClothingTypeObj->findStatisticsBy($target));
        return $rec_count;
    }

    //--------------------------------------------------------------------------


    private function countAllBrands() {
        $em = $this->getDoctrine()->getManager();
        $BrandTypeObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Brand');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Brand')
                ->countAllRecord();
        $rec_count = count($BrandTypeObj->countAllRecord());
        return $rec_count;
    }

    //--------------------------------------------------------------------------


    private function countAllListProduct() {
        $em = $this->getDoctrine()->getManager();
        $ProductTypeObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->countAllRecord();
        $rec_count = count($ProductTypeObj->countAllRecord());
        return $rec_count;
    }

    //--------------------------------------------------------------------------
    private function countProductsByGender($gender) {
        $em = $this->getDoctrine()->getManager();
        $ProductTypeObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->findPrductByGender($gender);
        $rec_count = count($ProductTypeObj->findPrductByGender($gender));
        return $rec_count;
    }

//--------------------------------------------------------------------------
    private function countProductsByType($target) {
        $em = $this->getDoctrine()->getManager();
        $ProductTypeObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->findPrductByType($target);
        $rec_count = count($ProductTypeObj->findPrductByType($target));
        return $rec_count;
    }

    //--------------------------------------------------------------------------
    private function getProductByBrand() {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product')
                ->findPrductByBrand();
        return $entity;
    }

    //--------------------------------------------------------------------------
    private function getUserByGender($gender) {
        $em = $this->getDoctrine()->getManager();
        $UserTypeObj = $this->getDoctrine()->getRepository('LoveThatFitUserBundle:User');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitUserBundle:User')
                ->findUserByGender($gender);
        $rec_count = count($UserTypeObj->findUserByGender($gender));
        return $rec_count;
    }

    //--------------------------------------------------------------------------
    private function getAllUserList() {
        $em = $this->getDoctrine()->getManager();
        $UserTypeObj = $this->getDoctrine()->getRepository('LoveThatFitUserBundle:User');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitUserBundle:User')
                ->findAll();
        $rec_count = count($UserTypeObj->findAll());
        return $rec_count;
    }

    //--------------------------------------------------------------------------
    private function getSignUpCount() {
        $conn = $this->get('database_connection');
        $monthly_users_signups = $conn->fetchAll("SELECT DATE_FORMAT(us.created_at, '%m') as month_num, DATE_FORMAT(us.created_at, '%M') as month,COUNT(id) as total FROM ltf_users us  GROUP BY DATE_FORMAT(us.created_at, '%Y%M') order by month_num");
        return $monthly_users_signups;
    }

    //--------------------------------------------------------------------------
    private function getUsersData() {
        $users = array();
        $users['total_signups'] = $this->getSignUpCount();
        $users['total_count'] = $this->getAllUserList();
        $users['total_woman'] = $this->getUserByGender('f');
        $users['total_man'] = $this->getUserByGender('m');
        $users['age_group_count'] = $this->getUsersAgeGroupCount();

        return $users;
    }

    public function getUsersAgeGroupCount() {
        $conn = $this->get('database_connection');
        $users_age_group_counts = $conn->fetchAll(
                "SELECT age, count(*) total FROM
(SELECT CASE 
         WHEN YEAR(CURDATE())-YEAR(u.birth_date) <= 10 THEN '1-15' 
         WHEN YEAR(CURDATE())-YEAR(u.birth_date)  <= 20 THEN '16-30' 
         WHEN YEAR(CURDATE())-YEAR(u.birth_date)  <= 30 THEN '31-45'
         WHEN YEAR(CURDATE())-YEAR(u.birth_date)  <= 40 THEN '46-60' 
         WHEN YEAR(CURDATE())-YEAR(u.birth_date)  <= 50 THEN '61-75'         
         ELSE '76+' 
       END AS age
    FROM  ltf_users u 
          ) t group by age");

        return $users_age_group_counts;
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
