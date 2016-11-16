<?php

namespace LoveThatFit\AdminBundle\Entity;
use Doctrine\ORM\EntityRepository;



class ProductSpecificationMappingRepository extends EntityRepository{
    public function allMappingArray() {
        $query = $this->getEntityManager()
                    ->createQuery('SELECT psm.id, psm.title, psm.brand, psm.description FROM LoveThatFitAdminBundle:ProductSpecificationMapping psm');
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return "null";
        }
    }

}
