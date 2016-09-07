<?php

namespace LoveThatFit\SupportBundle\Entity;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityRepository;

/**
 * SupportTaskLogRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SupportTaskLogRepository extends EntityRepository{
    
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
                u.id,
                u.user_name,
                s.log_type,
                MAX(s.duration) AS fast,
                MIN(s.duration) AS slow,
                AVG(s.duration) AS avrg,
                COUNT(s.id) AS total'
            )
            ->from('LoveThatFitSupportBundle:SupportTaskLog', 's')
            ->leftJoin(
            	"LoveThatFitAdminBundle:SupportAdminUser",
            	"u",
            	"WITH",
            	"s.support_admin_user = u.id"
            );
        if ($search) {
            $query 
                ->andWhere('u.user_name like :search')
                ->setParameter('search', "%".$search."%");
        }
        $query->groupBy('s.support_admin_user');

        if (is_array($order)) {
            $orderByColumn    = $order[0]['column'];
            $orderByDirection = $order[0]['dir'];
            $query->OrderBy("u.id", $orderByDirection);
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

    public function findSupprtUser($id)
    {
        return  $this->getEntityManager()
                ->createQueryBuilder()
                ->select('
                    u.id,
                    u.user_name,
                    MAX(s.duration) AS fast,
                    MIN(s.duration) AS slow,
                    AVG(s.duration) AS avrg,
                    COUNT(s.id) as total'
                )
                ->from('LoveThatFitSupportBundle:SupportTaskLog', 's')
                ->leftJoin(
                    "LoveThatFitAdminBundle:SupportAdminUser",
                    "u",
                    "WITH",
                    "s.support_admin_user = u.id"
                )
                ->where('s.support_admin_user=:id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getResult();
    }

    public function findAboveAverage($id, $avgVal, $maxVal)
    {
        return $this->getEntityManager()
                ->createQueryBuilder()
                ->select('COUNT(s.id) as above_avg')
                ->from('LoveThatFitSupportBundle:SupportTaskLog', 's')
                ->leftJoin(
                    "LoveThatFitAdminBundle:SupportAdminUser",
                    "u",
                    "WITH",
                    "s.support_admin_user = u.id"
                )
                ->where('s.support_admin_user=:id')
                ->setParameter('id', $id)
                ->andwhere('s.duration>:avgVal')
                ->setParameter('avgVal', $avgVal)
                ->andwhere('s.duration<:maxVal')
                ->setParameter('maxVal', $maxVal)
                ->getQuery()
                ->getResult();
    }

    public function findBelowAverage($id, $avgVal, $minVal)
    {
        return $this->getEntityManager()
                ->createQueryBuilder()
                ->select('COUNT(s.id) as below_avg')
                ->from('LoveThatFitSupportBundle:SupportTaskLog', 's')
                ->leftJoin(
                    "LoveThatFitAdminBundle:SupportAdminUser",
                    "u",
                    "WITH",
                    "s.support_admin_user = u.id"
                )
                ->where('s.support_admin_user=:id')
                ->setParameter('id', $id)
                ->andwhere('s.duration<:avgVal')
                ->setParameter('avgVal', $avgVal)
                ->andwhere('s.duration>:minVal')
                ->setParameter('minVal', $minVal)
                ->getQuery()
                ->getResult();
    }

    public function showSearch(
        $data,
        $page = 0,
        $max = NULL,
        $order,
        $userid,
        $getResult = true
    ) 
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $search = isset($data['query']) && $data['query']?$data['query']:null;
        
        $query 
            ->select('
                s.log_type,
                s.member_email,
                s.start_time,
                s.end_time,
                s.duration'
            )
            ->from('LoveThatFitSupportBundle:SupportTaskLog', 's')
            ->leftJoin(
                    "LoveThatFitUserBundle:UserArchives",
                    "ua",
                    "WITH",
                    "s.archive = ua.id"
                )
            ->where('s.support_admin_user=:userid')
            ->setParameter('userid', $userid);
        
        if ($search) {
            $query 
                ->andWhere('s.member_email like :search')
                ->setParameter('search', "%".$search."%");
        }

        if (is_array($order)) {
            $orderByColumn    = $order[0]['column'];
            $orderByDirection = $order[0]['dir'];
            $query->OrderBy("s.id", $orderByDirection);
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
