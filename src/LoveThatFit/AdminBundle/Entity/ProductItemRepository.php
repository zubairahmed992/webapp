<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ProductItemRepository 
 */
class ProductItemRepository extends EntityRepository
{
    public function findProductItemByUser($user_id , $page_number=0 , $limit=0) {
            $query = $this->getEntityManager()
                        ->createQuery("
     SELECT p,pi FROM LoveThatFitAdminBundle:Product p
      JOIN p.product_items pi
      JOIN pi.users u
      WHERE
      u.id = :id"                         
                        )->setParameters(array('id' => $user_id)) ;
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    
    public function findByColorSize($color_id , $size_id) {
            $query = $this->getEntityManager()
                        ->createQuery("
     SELECT pi FROM LoveThatFitAdminBundle:ProductItem pi
      JOIN pi.product_size ps
      JOIN pi.product_color pc
      WHERE
      pc.id = :color_id AND
      ps.id = :size_id"                         
                        )->setParameters(array('color_id' => $color_id, 'size_id' => $size_id)) ;
        try {
            return $query->getOneOrNullResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    
    public function findProductByItemId($productItem) {
     $query = $this->getEntityManager()
                        ->createQuery("
     SELECT p.id FROM LoveThatFitAdminBundle:Product p
     JOIN p.product_items pi     
     WHERE
     pi.id=:product_id"  )->setParameters(array('product_id' => $productItem)) ;
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
}
