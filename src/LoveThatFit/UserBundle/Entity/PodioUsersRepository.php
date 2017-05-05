<?php

namespace LoveThatFit\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

class PodioUsersRepository extends EntityRepository
{

    public function findByUserId($user_id)
    {
        return $this->findOneBy(array('user_id' => $user_id));
    }

    public function findUserByStatus($status)
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('u.id as member_id,u.email,u.gender,u.zipcode,u.birthDate,u.createdAt as member_created,pu.id,pu.status,pu.podio_id')
            ->from('LoveThatFitUserBundle:PodioUsers', 'pu')
            ->innerJoin('pu.user', 'u')
            ->where('pu.status IN (:status)')
            ->setParameter('status', $status)
            ->groupBy('u.id')
            ->getQuery();
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function findUserByStatus2($status,$user_id)
    {
        if($user_id) {
            $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('u.id as member_id,u.email,u.gender,u.zipcode,u.birthDate,u.createdAt as member_created,pu.id,pu.status,pu.podio_id')
                ->from('LoveThatFitUserBundle:PodioUsers', 'pu')
                ->innerJoin('pu.user', 'u')
                ->where('pu.status IN (:status)')
                ->andWhere('u.id =:user_id')
                ->setParameter('status', $status)
                ->setParameter('user_id', $user_id)
                ->groupBy('u.id')
                ->getQuery();
        } else {
            $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('u.id as member_id,u.email,u.gender,u.zipcode,u.birthDate,u.createdAt as member_created,pu.id,pu.status,pu.podio_id')
                ->from('LoveThatFitUserBundle:PodioUsers', 'pu')
                ->innerJoin('pu.user', 'u')
                ->where('pu.status IN (:status)')
                ->setParameter('status', $status)
                ->groupBy('u.id')
                ->getQuery();
        }
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function findPodioUserByUserId($user_id)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select(
            'u.id as member_id, pu.podio_id')
            ->from('LoveThatFitUserBundle:PodioUsers', 'pu')
            ->innerJoin('pu.user', 'u')
            ->where('u.id =:user_id')
            ->setParameter('user_id', $user_id)
            ->getQuery()
            ->getResult();
    }
    
}