<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ProductColorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductColorRepository extends EntityRepository {

    public function getRelatedSizeTitleArray($id) {
        $query = $this->getEntityManager()
                        ->createQuery('
            SELECT ps.title FROM LoveThatFitAdminBundle:ProductColor pc 
            JOIN pc.product_items pi
            JOIN pi.product_size ps
            WHERE pc.id = :id'
                        )->setParameter('id', $id);

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function getSizeArray($id) {
        $query = $this->getEntityManager()
                        ->createQuery('
            SELECT ps.id as id, ps.title as title FROM LoveThatFitAdminBundle:ProductColor pc 
            JOIN pc.product_items pi
            JOIN pi.product_size ps
            WHERE pc.id = :id ORDER BY ps.title'
                        )->setParameter('id', $id);

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function findColorByProductTitle($title,$productid) {
        $record = $this->getEntityManager()
                ->createQuery("SELECT pc FROM LoveThatFitAdminBundle:ProductColor pc                                    
                                WHERE pc.title = :title
                                AND pc.product=:product")
                ->setParameters(array('title' => ucwords($title), 'product' => $productid));
        try {
            return $record->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    public function findColorByProduct($productid) {
        $record = $this->getEntityManager()
                ->createQuery("SELECT pc FROM LoveThatFitAdminBundle:ProductColor pc                                    
                                WHERE pc.product=:product")
                ->setParameters(array('product' => $productid));
        try {
            return $record->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    

    public function getSizeItemImageUrlArray($id) {
        $query = $this->getEntityManager()
                        ->createQuery('
            SELECT pi.id as itemId,ps.id as sizeId, ps.title as sizeTitle,pi.image as itemImage,ps.body_type  as bodyType FROM LoveThatFitAdminBundle:ProductColor pc 
            JOIN pc.product_items pi
            JOIN pi.product_size ps
            WHERE pc.id = :id'
                        )->setParameter('id', $id);
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    #------------Fetching image url only ---------------------------------------------------------#
    public function getSingleSizeItemImageUrlArray($id) {
        $query = $this->getEntityManager()
                        ->createQuery('
            SELECT pi.image as image_url FROM LoveThatFitAdminBundle:ProductColor pc 
            JOIN pc.product_items pi
            JOIN pi.product_size ps
            WHERE pc.id = :id'
                        )->setParameter('id', $id);
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function findUniqueColor() {
        $record = $this->getEntityManager()
            ->createQuery("SELECT pc.title FROM LoveThatFitAdminBundle:ProductColor pc ORDER BY pc.title ASC");
        try {
            return $record->getArrayResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function findProductColorIDsByTitle($title) {
        $record = $this->getEntityManager()
            ->createQuery("SELECT pc.id FROM LoveThatFitAdminBundle:ProductColor pc WHERE pc.title IN :title ORDER BY pc.title ASC")
            ->setParameter('title', $title);;
        try {
            return $record->getArrayResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
}