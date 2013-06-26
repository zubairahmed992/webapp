<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ClothingTypeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ClothingTypeRepository extends EntityRepository
{
	
/*-----------------------------------------------------------------
Written:Suresh
Description: Find all product with limit and sort 
param:limit, page_number,limit,sort	 
------------------------------------------------------------------*/
	 public function findAllClothingType($page_number = 0, $limit = 0 ,$sort='id'  ) {
				   
             if ($page_number <= 0 || $limit <= 0) {
            $query = $this->getEntityManager()
                    ->createQuery('SELECT c FROM LoveThatFitAdminBundle:ClothingType c ORDER BY c.' . $sort . ' ASC');
        } else {
            $query = $this->getEntityManager()
                    ->createQuery('SELECT c FROM LoveThatFitAdminBundle:ClothingType c ORDER BY c.' . $sort . ' ASC')
                    ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        }
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
   
  /*-----End Of Function-----------------*/
	
	 /*-----------------------------------------------------------------
      Written:Suresh
	  Description:Count all Records
	  param:limit:
	 ------------------------------------------------------------------*/ 
     public function countAllRecord()
	 {
	  $total_record= $this->getEntityManager()
	   ->createQuery('SELECT c FROM LoveThatFitAdminBundle:ClothingType c');
	  try 
	    {
		 return $total_record->getResult();
		}
		catch (\Doctrine\ORM\NoResultException $e) 
		 {
		   return null;
		 }						
	  }   
	 
	public function findClothingTypeBy($name,$target) {
        $total_record = $this->getEntityManager()
        ->createQuery("SELECT ct FROM LoveThatFitAdminBundle:ClothingType ct      
        WHERE
        ct.name = :name
        AND ct.target=:target"
                        )->setParameters(array('name' => $name, 'target' => $target));
        try {
            return $total_record->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
        
    public function findAllRecord()
    {
      $query = $this->getEntityManager()
                ->createQuery('SELECT c FROM LoveThatFitAdminBundle:ClothingType c');
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    public function findStatisticsBy($target)
    {
     $query = $this->getEntityManager()
        ->createQuery("SELECT ct FROM LoveThatFitAdminBundle:ClothingType ct      
        WHERE        
        ct.target=:target"
                        )
             ->setParameter('target',$target);
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    
  #-------------Find All Clothing type for Web Service -----#
    public function findAllBrandWebService() {

        $query = $this->getEntityManager()
                ->createQuery("SELECT c.id as id ,c.name as name ,'clothing_type' AS type
                    FROM LoveThatFitAdminBundle:ClothingType c
                    WHERE c.disabled=0 ");
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

}
