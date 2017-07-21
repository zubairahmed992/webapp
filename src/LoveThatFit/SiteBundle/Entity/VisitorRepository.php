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
                 f.name,f.email, f.created_at'
            )
            ->from('LoveThatFitSiteBundle:visitor', 'f')
            ->where('f.email is not null')
            ->andWhere('f.email != :identifier')
      		->setParameter('identifier', '');
            
            
            
        if ($search) {
            $query 
                ->andWhere('f.name like :search')
                ->orWhere('f.email like :search')
                ->orWhere('f.created_at like :search')               
                ->setParameter('search', "%".$search."%");
        }
      
       	
        if (is_array($order)) {
            $orderByColumn    = $order[0]['column'];
            $orderByDirection = $order[0]['dir'];
            if ($orderByColumn == 0) {
                $orderByColumn = "f.name";
            } elseif ($orderByColumn == 1) {
                $orderByColumn = "f.email";
            } elseif ($orderByColumn == 2) {
                $orderByColumn = "f.created_at";
            }

            $query->OrderBy($orderByColumn, $orderByDirection);
        }
        $query->groupBy('f.email');
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
                f.email,f.name,f.created_at'
            )
            ->from('LoveThatFitSiteBundle:visitor', 'f')  
             ->where('f.email is not null')
            ->andWhere('f.email != :identifier')
      		->setParameter('identifier', '') 
            ->OrderBy("f.created_at", "desc")
            ->groupBy('f.email')
            ->getQuery()
            ->getResult();
    }
  

}
