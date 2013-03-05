<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * BrandRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BrandRepository extends EntityRepository
{
	 /*-----------------------------------------------------------------
      Written:Suresh
	  Description: Find all Brands with limit and sort 
	  param:limit, page_number,limit,sort	 
	 ------------------------------------------------------------------*/
	 public function findAllBrand($page_number = 0, $limit = 0 ,$sort='id'  ) {
				   
	  if ($page_number <= 0 || $limit <= 0){       
	   $query = $this->getEntityManager()
					 ->createQuery('SELECT b FROM LoveThatFitAdminBundle:Brand b ORDER BY b.'.$sort.' ASC');
	    }else{
			  $query = $this->getEntityManager()
						  ->createQuery('SELECT b FROM LoveThatFitAdminBundle:Brand b ORDER BY b.'.$sort.' ASC')
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
	   ->createQuery('SELECT b FROM LoveThatFitAdminBundle:Brand b');
	  try 
	    {
		 return $total_record->getResult();
		}
		catch (\Doctrine\ORM\NoResultException $e) 
		 {
		   return null;
		 }						
	  }   
	 
	

}
