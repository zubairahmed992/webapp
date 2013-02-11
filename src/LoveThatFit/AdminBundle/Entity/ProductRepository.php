<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ProductRepository
 */
class ProductRepository extends EntityRepository {
    
    
     public function findByGender($gender, $page_number = 0, $limit = 0) {
        if ($page_number <= 0 || $limit <= 0) {           
        
        $query = $this->getEntityManager()
                        ->createQuery('
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            WHERE p.gender = :gender'
                        )->setParameter('gender', $gender);
        
        }else{
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
        
        }else{
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
        
        }else{
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

}
