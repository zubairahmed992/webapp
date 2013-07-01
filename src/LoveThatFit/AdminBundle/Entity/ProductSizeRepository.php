<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ProductSizeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductSizeRepository extends EntityRepository
{
      
    
    public function getProductSizeTitleArray($id) {
        $query = $this->getEntityManager()
                        ->createQuery('
            SELECT ps.title FROM LoveThatFitAdminBundle:Product p 
            JOIN p.product_sizes ps
            WHERE p.id = :id'
                        )->setParameter('id', $id);

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    public function findProductSizeByProductTitle($name,$productid) {
        $record = $this->getEntityManager()
                        ->createQuery("SELECT p FROM LoveThatFitAdminBundle:ProductSize p                                   
                                WHERE p.title = :title
                                AND p.product=:product")
                        ->setParameters(array('title' => ucwords($name),'product'=>$productid));
        try {
            return $record->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
}
