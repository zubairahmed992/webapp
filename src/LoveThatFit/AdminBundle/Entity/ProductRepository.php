<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ProductRepository
 */
class ProductRepository extends EntityRepository {
    
    public function findAllProduct($page_number = 0, $limit = 0, $sort = 'id') {

        if ($page_number <= 0 || $limit <= 0) {
            $query = $this->getEntityManager()
                    ->createQuery('SELECT p FROM LoveThatFitAdminBundle:Product p ORDER BY p.' . $sort . ' ASC');
        } else {
            $query = $this->getEntityManager()
                    ->createQuery('SELECT p FROM LoveThatFitAdminBundle:Product p ORDER BY p.' . $sort . ' ASC')
                    ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        }
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    /* ------------------------------------------------------------------ */

    
    public function countAllRecord() {

        $total_record = $this->getEntityManager()
                ->createQuery('SELECT p FROM LoveThatFitAdminBundle:Product p');
        try {
            return $total_record->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    /* --------------------------------------------------------- */

    public function findByGender($gender, $page_number = 0, $limit = 0) {
        if ($page_number <= 0 || $limit <= 0) {

            $query = $this->getEntityManager()
                            ->createQuery('
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            WHERE p.gender = :gender'
                            )->setParameter('gender', $gender);
        } else {
            $query = $this->getEntityManager()
                    ->createQuery('
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            WHERE p.gender = :gender'
                    )->setParameter('gender', $gender)
                    ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        }

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    //-----------------------------------------------------------------

    public function findByGenderLatest($gender, $page_number = 0, $limit = 0) {
        if ($page_number <= 0 || $limit <= 0) {

            $query = $this->getEntityManager()
                            ->createQuery('
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            WHERE p.gender = :gender 
            ORDER BY p.created_at DESC'
                            )->setParameter('gender', $gender);
        } else {
            $query = $this->getEntityManager()
                    ->createQuery('
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            WHERE p.gender = :gender 
            ORDER BY p.created_at DESC'
                    )->setParameter('gender', $gender)
                    ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        }

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

//-----------------------------------------------------------------

    public function findByGenderBrand($gender, $brand_id, $page_number = 0, $limit = 0) {
        if ($page_number <= 0 || $limit <= 0) {
            $query = $this->getEntityManager()
                            ->createQuery('
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            JOIN p.brand b
            WHERE b.id = :id
            AND p.gender = :gender'
                            )->setParameters(array('id' => $brand_id, 'gender' => $gender));
        } else {
            $query = $this->getEntityManager()
                    ->createQuery('
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            JOIN p.brand b
            WHERE b.id = :id
            AND p.gender = :gender'
                    )->setParameters(array('id' => $brand_id, 'gender' => $gender))
                    ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        }


        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

//-----------------------------------------------------------------

    public function findByGenderClothingType($gender, $clothing_type_id, $page_number = 0, $limit = 0) {
        if ($page_number <= 0 || $limit <= 0) {

            $query = $this->getEntityManager()
                            ->createQuery('
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            JOIN p.clothing_type ct
            WHERE ct.id = :clothing_type_id
            AND p.gender = :gender'
                            )->setParameters(array('clothing_type_id' => $clothing_type_id, 'gender' => $gender));
        } else {
            $query = $this->getEntityManager()
                    ->createQuery('
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            JOIN p.clothing_type ct
            WHERE ct.id = :clothing_type_id
            AND p.gender = :gender'
                    )->setParameters(array('clothing_type_id' => $clothing_type_id, 'gender' => $gender))
                    ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        }

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

//-----------------------------------------------------------------

    public function findSampleClothingTypeGender($gender) {
        $query = $this->getEntityManager()
                        ->createQuery('
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            JOIN p.clothing_type ct
            WHERE p.gender = :gender'
                        )->setParameter('gender', $gender);

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
//-----------------------------------------------------------------
public function productList() {

      $query = $this->getEntityManager()
            ->createQuery("
      SELECT p.id,p.name,p.adjustment,p.waist,p.hip,p.bust,p.sku,p.arm,p.leg,p.inseam,p.outseam,p.hem,p.back,ct.name as clothing_type , b.name as brand_name FROM LoveThatFitAdminBundle:Product p 
      JOIN p.clothing_type ct
      JOIN p.brand b 
      ");
     
       try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
 //-------------------------------------------------------------------------
 public function productListByBrand($brand_id,$gender)
 {
      $query = $this->getEntityManager()
                        ->createQuery("
      SELECT p.id,p.name,p.adjustment,p.waist,p.hip,p.bust,p.sku,p.arm,p.leg,
      p.inseam,p.outseam,p.hem,p.back,ct.name as clothing_type ,p.gender,
      b.name as brand_name,b.id as brand_id
      FROM LoveThatFitAdminBundle:Product p 
      JOIN p.clothing_type ct
      JOIN p.brand b 
      WHERE
       p.gender = :gender
       AND b.id = :brand_id"                          
                        )->setParameters(array('gender' => $gender, 'brand_id' => $brand_id)) ;
                        
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    
  
  

}
