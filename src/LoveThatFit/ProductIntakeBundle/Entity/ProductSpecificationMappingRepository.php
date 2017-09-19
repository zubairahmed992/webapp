<?php

namespace LoveThatFit\ProductIntakeBundle\Entity;
use Doctrine\ORM\EntityRepository;



class ProductSpecificationMappingRepository extends EntityRepository{
    public function allMappingArray() {
        $query = $this->getEntityManager()
                    ->createQuery('SELECT psm.id, psm.title, psm.brand, psm.description FROM LoveThatFitProductIntakeBundle:ProductSpecificationMapping psm');
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return "null";
        }
    }

    public function findOneByTitle($title) {
        $query = $this->getEntityManager()
            ->createQuery("SELECT psm FROM LoveThatFitProductIntakeBundle:ProductSpecificationMapping psm WHERE psm.title = :title")
            ->setParameters(array('title' => $title));
        try {
            return $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return "null";
        }
    }

}
