<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ClothingTypeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BrandSpecificationRepository extends EntityRepository
{
	public function findByBrand($entity){            
            $query = $this->getEntityManager()
                    ->createQuery("SELECT bs,b FROM LoveThatFitAdminBundle:Brand b
                    JOIN b.brandspecification bs   
                    WHERE bs.brand =:brand")
                   ->setParameters(array('brand' => $entity));
                     try {
                     return $query->getResult();
                } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
                } 
        }
 #-----------------Get Brand Specification base on Brand Id -------------------#
 public function getBrandSpecifications($id){
     $query = $this->getEntityManager()
                    ->createQuery("SELECT bs,b FROM LoveThatFitAdminBundle:Brand b
                    JOIN b.brandspecification bs   
                    WHERE bs.brand =:id")
                   ->setParameters(array('id' => $id));

                     try {
                     return $query->getResult();
                } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
                } 
 }        

}
