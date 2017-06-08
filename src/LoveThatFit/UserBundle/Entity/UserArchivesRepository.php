<?php

namespace LoveThatFit\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UserAppAccessLogRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserArchivesRepository extends EntityRepository
{
  public function listAllPendingUsers($page_number = 0, $limit = 0, $sort = 'id') {
	if ($page_number <= 0 || $limit <= 0) {
	  $query = $this->getEntityManager()
		->createQuery('SELECT a.id,a.status FROM LoveThatFitUserBundle:UserArchives a where a.status=:status ORDER BY a.' . $sort . ' DESC')->setParameters(array('status' => '-1'));
	} else {
	  $query = $this->getEntityManager()
		->createQuery('SELECT a FROM LoveThatFitUserBundle:UserArchives a where a.status=:status ORDER BY a.' . $sort . ' DESC')->setParameters(array('status' => '-1'))
		->setFirstResult($limit * ($page_number - 1))
		->setMaxResults($limit);
	}
	try {
	  return $query->getResult();
	} catch (\Doctrine\ORM\NoResultException $e) {
	  return "null";
	}
  }

  public function countAllRecord() {
	$total_record = $this->getEntityManager()
	  ->createQuery('SELECT a FROM LoveThatFitUserBundle:UserArchives a  where a.status=-1');
	try {
	  return $total_record->getResult();
	} catch (\Doctrine\ORM\NoResultException $e) {
	  return null;
	}
  }

  public function getPendingArchive($user) {
	  $query = $this->getEntityManager()
		->createQuery(
		  "SELECT a
		 FROM LoveThatFitUserBundle:UserArchives a WHERE a.user=:user and a.status=:status
		 ")->setParameter('user',$user)
		 ->setParameter('status',"-1")
		->setMaxResults(1);
	try {
	  return $query->getSingleResult();
	} catch (\Doctrine\ORM\NoResultException $e) {
	  return "null";
	}
  }

  public function getAllArchive($user) {
	$query = $this->getEntityManager()
	  ->createQuery(
		"SELECT a
		 FROM LoveThatFitUserBundle:UserArchives a WHERE a.user=:user
		 ")->setParameter('user',$user);
	try {
	  return $query->getArrayResult();
	} catch (\Doctrine\ORM\NoResultException $e) {
	  return "null";
	}
  }
  public function getAllArchiveCount($user) {
	$query = $this->getEntityManager()
	  ->createQuery(
		"SELECT COUNT(a.id) as counter
		 FROM LoveThatFitUserBundle:UserArchives a WHERE a.user=:user
		 ")->setParameter('user',$user)
	  	   ->setMaxResults(1);
	try {
	  return $query->getSingleResult();
	} catch (\Doctrine\ORM\NoResultException $e) {
	  return "null";
	}
  }

  	public function search(
        $data,
        $page = 0,
        $max = NULL,
        $order,
        $user_id,
        $all,
        $getResult = true
    ) 
    {
	    $query  = $this->getEntityManager()->createQueryBuilder();
        $search = isset($data['query']) && $data['query']?$data['query']:null;
        $log_type = "calibration";
        $query 
            ->select('
                ua.id,
                u.email,
                ua.created_at,
                t.support_user_name,
                ua.version'
            )
            ->from('LoveThatFitUserBundle:UserArchives', 'ua')
            ->leftJoin(
            		"LoveThatFitUserBundle:User",
            		"u",
            		"WITH",
            		"u.id = ua.user"
            )
            ->leftJoin(
                    "LoveThatFitSupportBundle:SupportTaskLog",
                    "t",
                    "WITH",
                    $query->expr()->andX(
                        $query->expr()->eq('t.archive', 'ua.id'),
                        $query->expr()->eq('t.log_type',':log_type')
                    )
            )
            ->setParameter("log_type", "calibration")
            ->andWhere('ua.status = :pending');
        if ($all == 0) {
            $query 
                ->andWhere('t.support_admin_user =:user_id')
                ->setParameter('user_id', $user_id);
        }
        if ($search) {
            $query 
                ->andWhere('u.email like :search')
                ->orWhere('t.support_user_name like :search')
                ->setParameter('search', "%".$search."%");
        }
        $query->setParameter('pending', '-1');

        if (is_array($order)) {
            $orderByColumn    = $order[0]['column'];
            $orderByDirection = $order[0]['dir'];
            $query->OrderBy("ua.id", $orderByDirection);
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

    public function getVersion($user_id)
    {

        $query  = $this->getEntityManager()->createQueryBuilder();
        $query->select('ua.version')
            ->from('LoveThatFitUserBundle:UserArchives', 'ua')
            ->Where('ua.user =:user_id')
            ->setParameter('user_id', $user_id)
            ->OrderBy("ua.id", "desc")
            ->setMaxResults(1);

        try {
          return $query->getQuery()->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
          return "null";
        } 
        // return $query->getQuery()->getSingleResult();
    }
}
