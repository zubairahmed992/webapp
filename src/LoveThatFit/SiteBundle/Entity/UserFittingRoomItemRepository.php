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



/* ------------------ New Serrvices ---------------------------------*/
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



    //Get the Count of the added item
    public function findCountProductItemByUserid($user_id, $product_id) {
        $total_record = $this->getEntityManager()
            ->createQuery("SELECT count(ut), ut.qty
                                FROM LoveThatFitSiteBundle:UserFittingRoomItem ut
                                WHERE
                                ut.user=:user_id AND ut.product_id=:product_id"
            )->setParameters(array('user_id' => $user_id, 'product_id' => $product_id));
        try {
            return $total_record->getResult();
            //return $total_record->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }


    //Get the Count of the added item
    public function findCountProductItemByUseridWithProductItem($user_id, $product_id, $item_id) {
        $total_record = $this->getEntityManager()
            ->createQuery("SELECT count(ut), ut.qty
                                FROM LoveThatFitSiteBundle:UserFittingRoomItem ut
                                WHERE
                                ut.user=:user_id AND ut.product_id=:product_id AND ut.productitem=:productitem"
            )->setParameters(array('user_id' => $user_id, 'product_id' => $product_id, 'productitem' => $item_id));
        try {
            return $total_record->getResult();
            //return $total_record->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }



    //Get the records entity by productId
    public function findByUserItemByProduct($user_id, $product_id) {
        $total_record = $this->getEntityManager()
            ->createQuery("SELECT ut
                                FROM LoveThatFitSiteBundle:UserFittingRoomItem ut
                                WHERE
                                ut.user=:user_id AND ut.product_id=:product_id"
            )->setParameters(array('user_id' => $user_id, 'product_id' => $product_id));
        try {
            return $total_record->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }


    //Delete by Product ID
    public function deleteByUserItemByProduct($user_id, $product_id) {
        $total_record = $this->getEntityManager()
            ->createQuery("DELETE LoveThatFitSiteBundle:UserFittingRoomItem ut
                        WHERE ut.user = :user_id AND ut.product_id = :product_id"
            )->setParameters(array('user_id' => $user_id, 'product_id' => $product_id));
        try {
            return $total_record->execute();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }


    #--------------Get Product list By Category and Gender -----------------------------------------------------
    public function getAllCategoriesByProductItem($user_id = '0') {

        $userItemTableName = $this->getEntityManager()->getClassMetadata('LoveThatFitSiteBundle:UserFittingRoomItem')->getTableName();
        $categoriesTableName = $this->getEntityManager()->getClassMetadata('LoveThatFitAdminBundle:Categories')->getTableName();
        $categories_productTableName = $this->getEntityManager()->getClassMetadata('LoveThatFitAdminBundle:Product')->getTableName();

        $sql = "SELECT
                      cn.id                 AS top_category_id,
                      cn.name               AS top_category_name,
                      c.id                  AS category_id,
                      c.name                AS category_name,
                      ufri.product_item_id  AS product_item_id,
                      ufri.product_id       AS product_id,
                      ufri.qty              AS qty
                      FROM $userItemTableName ufri

                      LEFT JOIN category_products cp ON ufri.product_id = cp.product_id
                      LEFT JOIN $categoriesTableName c ON cp.categories_id = c.id
                      LEFT JOIN $categoriesTableName cn ON c.top_id = cn.id
                      where ufri.user_id = :user_id
                      GROUP BY ufri.product_item_id
                      ORDER BY cn.name";

        $params['user_id'] = $user_id;
        $query = $this->getEntityManager()->getConnection()->prepare($sql);
        $query->execute($params);
        $result_query = $query->fetchAll();
        return $result_query;
    }



}
