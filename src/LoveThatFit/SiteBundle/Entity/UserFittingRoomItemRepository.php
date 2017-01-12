<?php

namespace LoveThatFit\SiteBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UserItemTryHistoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserFittingRoomItemRepository extends EntityRepository {

    public function findByUserItemId($user_id, $item_id) {
        $total_record = $this->getEntityManager()
                        ->createQuery("SELECT ut 
                                        FROM LoveThatFitSiteBundle:UserFittingRoomItem ut
                                        WHERE
                                        ut.user=:user_id AND 
                                        ut.productitem=:product_item_id
                                        "
                        )->setParameters(array('user_id' => $user_id, 'product_item_id' => $item_id));
        try {
            return $total_record->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    public function findByUserId($user_id) {
        $total_record = $this->getEntityManager()
                        ->createQuery("SELECT ut 
                                        FROM LoveThatFitSiteBundle:UserFittingRoomItem ut
                                        WHERE
                                        ut.user=:user_id"
                        )->setParameters(array('user_id' => $user_id));
        try {
            return $total_record->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }


    //Get the Count of the added item
    public function findByUserItemIdNew($user_id, $item_id) {
        $total_record = $this->getEntityManager()
            ->createQuery("SELECT count(ut)
                                FROM LoveThatFitSiteBundle:UserFittingRoomItem ut
                                WHERE
                                ut.user=:user_id AND ut.productitem=:product_item_id"
            )->setParameters(array('user_id' => $user_id, 'product_item_id' => $item_id));
        try {
            return $total_record->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

}
