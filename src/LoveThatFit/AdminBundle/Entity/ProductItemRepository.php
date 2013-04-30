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
     SELECT p FROM LoveThatFitAdminBundle:Product p
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
}
