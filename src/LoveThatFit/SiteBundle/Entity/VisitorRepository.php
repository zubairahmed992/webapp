<?php

namespace LoveThatFit\SiteBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * VisitorRepository
 */
class VisitorRepository extends EntityRepository {



	 public function search(
        $data,
        $page = 0,
        $max = NULL,
        $order,
        $getResult = true
    ) 
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $search = isset($data['query']) && $data['query']?$data['query'] : null;
        $query 
            ->select('
                f.id, f.email,
                f.ip_address, f.created_at'
            )
            ->from('LoveThatFitSiteBundle:visitor', 'f');
            
        if ($search) {
            $query 
                ->andWhere('f.id like :search')
                ->orWhere('f.email like :search')
                ->orWhere('f.ip_address like :search')
                ->orWhere('f.created_at like :search')
                ->setParameter('search', "%".$search."%");
        }
      
        //$query->groupBy('f.product');
        if (is_array($order)) {
            $orderByColumn    = $order[0]['column'];
            $orderByDirection = $order[0]['dir'];
            if ($orderByColumn == 0) {
                $orderByColumn = "f.id";
            } elseif ($orderByColumn == 1) {
                $orderByColumn = "f.email";
            } elseif ($orderByColumn == 2) {
                $orderByColumn = "f.ip_address";
            } elseif ($orderByColumn == 3) {
                $orderByColumn = "f.created_at";
            }

            $query->OrderBy($orderByColumn, $orderByDirection);
        }
        if ($max) {
            $preparedQuery = $query->getQuery() 
                ->setMaxResults($max)
                ->setFirstResult(($page) * $max);
        } else {
            $preparedQuery = $query->getQuery(); 
        }

        return $getResult?$preparedQuery->getResult():$preparedQuery; 
    }


    public function findvisitorsList()
    {
        return  $this->getEntityManager()
            ->createQueryBuilder()
            ->select('
                f.id, f.email,
                f.ip_address, f.created_at'
            )
            ->from('LoveThatFitSiteBundle:visitor', 'f')          
            ->OrderBy("f.created_at", "desc")
            ->getQuery()
            ->getResult();
    }
  

}
