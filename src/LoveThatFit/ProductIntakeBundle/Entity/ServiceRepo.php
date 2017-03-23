<?php

namespace LoveThatFit\ProductIntakeBundle\Entity;


use Doctrine\ORM\EntityManager;

class ServiceRepo
{

    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    #-------------------------------------------------------------------
    public function getProductSpecification($decoded)
    {
            $style_id_number = $decoded['result']['style_id_number'];   
            $style_name = $decoded['result']['style_name'];             
            $title = $decoded['result']['title'];  
            $query = $this->em
                ->createQueryBuilder()
                ->select('ps.specs_json')
                ->from('LoveThatFitProductIntakeBundle:ProductSpecification', 'ps')
                ->where("ps.style_id_number='$style_id_number'  OR  ps.style_name = '$style_name' OR  ps.title = '$title'")               
                ->getQuery();               
            try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

}