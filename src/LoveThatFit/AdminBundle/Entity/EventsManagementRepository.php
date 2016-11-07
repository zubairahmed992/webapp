<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ClothingTypeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EventsManagementRepository extends EntityRepository
{
	
/*-----------------------------------------------------------------
Written:Suresh
Description: Find all product with limit and sort 
param:limit, page_number,limit,sort	 
------------------------------------------------------------------*/
	
  #-------------Find All Clothing type for Web Service -----#
  public function findAllRecord()
  {
    $query = $this->getEntityManager()
      ->createQuery('SELECT e.id, e.event_name 
        FROM LoveThatFitAdminBundle:EventsManagement e 
        where e.disabled = 0
        order by e.id Desc
    ');
    try {
      return $query->getResult();
    } catch (\Doctrine\ORM\NoResultException $e) {
      return null;
    }
  }
    
  public function findByEventName($event_name)
  {
    $query = $this->getEntityManager()->createQueryBuilder();
    $query 
        ->select('e.id, e.event_name')
        ->from('LoveThatFitAdminBundle:EventsManagement', 'e')
        ->where('e.event_name=:event_name')
        ->setParameter('event_name', $event_name);

    return $query->getQuery()->getResult();
  }

  #--------------Find Clothing Type By ID---------------------------------#
  public function findById($id){
       $query = $this->getEntityManager()
                        ->createQuery("SELECT e FROM LoveThatFitAdminBundle:EventsManagement e WHERE e.id=:id"  )->setParameters(array('id' => $id)) ;
        try {
            return $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
  }

  public function search(
    $data,
    $page = 0,
    $max = NULL,
    $order,
    $getResult = true
  ) 
  {
    $query = $this->getEntityManager()->createQueryBuilder();
    $search = isset($data['query']) && $data['query']?$data['query']:null;
    $query 
        ->select('
            e.id,
            e.event_name,
            e.disabled,
            e.created_at'
        )
        ->from('LoveThatFitAdminBundle:EventsManagement', 'e')
        ->where('e.disabled=:status')
        ->setParameter('status', 0);
    if ($search) {
        $query 
            ->andWhere('e.event_name like :search')
            ->setParameter('search', "%".$search."%");
    }
    if (is_array($order)) {
        $orderByColumn    = $order[0]['column'];
        $orderByDirection = $order[0]['dir'];
        $query->OrderBy("e.id", $orderByDirection);
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
}
