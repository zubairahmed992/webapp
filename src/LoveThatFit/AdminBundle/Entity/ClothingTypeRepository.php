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
    
    
    public function listAllClothingType($page_number = 0, $limit = 0, $sort = 'id') {


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
            return "null";
        }
    }
    
    
        
    public function findAllRecord()
    {
      $query = $this->getEntityManager()
                ->createQuery('SELECT c FROM LoveThatFitAdminBundle:ClothingType c where c.disabled=0  order by c.id,c.gender, c.target, c.name');
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    #-----------------------------------------------------------------------------
  /*  public function findAllRecordDistinct(){
      $query = $this->getEntityManager()
      ->createQuery('SELECT DISTINCT(c.name) as name,c.id as id,c.target as target FROM LoveThatFitAdminBundle:ClothingType c  group by c.name order by c.id');
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }  
        
    }*/
    
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
                    WHERE c.disabled=0 ORDER BY name asc ");
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    
    public function findOneByName($name) {
        $record = $this->getEntityManager()
                        ->createQuery("SELECT c FROM LoveThatFitAdminBundle:ClothingType c    
                                WHERE c.name = :name")
                        ->setParameters(array('name' => $name));
        try {
            return $record->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    
    public function findClothingTypeByName($name) {
        $record = $this->getEntityManager()
                        ->createQuery("SELECT c FROM LoveThatFitAdminBundle:ClothingType c    
                                WHERE c.name = :name")
                        ->setParameters(array('name' =>$name));
        try {
            return $record->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
     public function findOneByGenderName($gender, $name) {
        $record = $this->getEntityManager()
                        ->createQuery("SELECT c FROM LoveThatFitAdminBundle:ClothingType c    
                                WHERE c.name = :name AND c.gender = :gender")
                        ->setParameters(array('name' =>$name, 'gender' => $gender));
        try {
            return $record->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    public function findClothingTypeByProduct($product)
  {
      $query = $this->getEntityManager()
                        ->createQuery("
     SELECT b FROM LoveThatFitAdminBundle:ClothingType b     
     WHERE
     b.id=:id     
    "  )->setParameters(array('id' => $product)) ;
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
  }
  
  
  
  
  public function findClothingTypsByGender($gender)
  {
      $query = $this->getEntityManager()
                        ->createQuery("
     SELECT b FROM LoveThatFitAdminBundle:ClothingType b     
     WHERE
     b.gender=:gender   
    "  )->setParameters(array('gender' => $gender)) ;
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
  }
  
#------------------------------------------------------------------------------#  
  public function getRecordsCountWithCurrentClothingTYpeLimit($clothing_type){
     $query = $this->getEntityManager()
                    ->createQuery("SELECT count(c.id) as id  FROM LoveThatFitAdminBundle:ClothingType c WHERE c.id <=:clothing_type")
                   ->setParameters(array('clothing_type' => $clothing_type));
                     try {
                     return $query->getResult();
                } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
                }
  }  
 #--------------Find Clothing Type By Gender---------------------------------#
  public function findByGender($gender){
       $query = $this->getEntityManager()
                        ->createQuery("
     SELECT ct.id as id,ct.name as name FROM LoveThatFitAdminBundle:ClothingType ct     
     WHERE
     ct.gender=:gender     
    "  )->setParameters(array('gender' => $gender)) ;
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
      
  }
#--------------Find Clothing Type By ID---------------------------------#
  public function findById($id){
       $query = $this->getEntityManager()
                        ->createQuery("
     SELECT ct.name as name,ct.target as target FROM LoveThatFitAdminBundle:ClothingType ct     
     WHERE
     ct.id=:id     
    "  )->setParameters(array('id' => $id)) ;
        try {
            return $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
      
  }
}
