<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ProductColorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductColorRepository extends EntityRepository
{
    
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
            WHERE pc.id = :id'
                        )->setParameter('id', $id);

        try {
            return $query->getResult();
            
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    public function findProductByTitle($name) {
        $record = $this->getEntityManager()
                        ->createQuery("SELECT pc FROM LoveThatFitAdminBundle:ProductColor pc    
                                WHERE c.title = :title")
                        ->setParameters(array('title' => ucwords($name)));
        try {
            return $record->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    #-------------------WEb Service for item------------------------------------------#
     public function getSizeItemImageUrlArray($id) {
        $query = $this->getEntityManager()
                        ->createQuery('
            SELECT ps.id as id, ps.title as title,pi.image as image_url FROM LoveThatFitAdminBundle:ProductColor pc 
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
}

