<?php

namespace LoveThatFit\ProductIntakeBundle\Entity;
use Doctrine\ORM\EntityRepository;



class ProductSpecificationRepository extends EntityRepository{

    public function findOneByTitle($title) {
        $query = $this->getEntityManager()
            ->createQuery("SELECT ps FROM LoveThatFitProductIntakeBundle:ProductSpecification ps WHERE ps.title = :title")
            ->setParameters(array('title' => $title));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return "null";
        }
    }

}
