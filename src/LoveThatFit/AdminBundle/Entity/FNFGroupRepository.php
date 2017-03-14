<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * FNFGroupRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FNFGroupRepository extends EntityRepository
{
    public function countAllFNFGroupRecord()
    {
        $total_record = $this->getEntityManager()
            ->createQuery('SELECT fnfg FROM LoveThatFitAdminBundle:FNFGroup fnfg where fnfg.isArchive = 0');
        try {
            return $total_record->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function getGroupDataById( $groupId )
    {
        $query     = $this->getEntityManager()->createQueryBuilder();

        $query
            ->select('
                fnf.id as fnfid,
                fnfg.discount,
                fnfg.min_amount,
                fnf.is_available,
                fnfg.startAt,
                fnfg.endAt,
                u.id,
                u.email,
                IDENTITY(u.original_user) as original_user_id'
            )
            ->from('LoveThatFitAdminBundle:FNFUser', 'fnf')
            ->join('fnf.groups', 'fnfg')
            ->join('fnf.users', 'u')
            ->andWhere('fnfg.isArchive = 0')
            ->andWhere('fnfg.id = :groupId')
            ->setParameter("groupId", $groupId);

        $preparedQuery = $query->getQuery();
        return $preparedQuery->getResult();
    }

    public function searchFNFGroups( $data, $page = 0, $max = NULL, $order, $getResult = true )
    {
        $query     = $this->getEntityManager()->createQueryBuilder();
        $search    = isset($data['query']) && $data['query'] ? $data['query'] : null;

        $query
            ->select('
                fnfg.id,
                fnfg.groupTitle,
                fnfg.discount,
                fnfg.min_amount
            ')
            ->from('LoveThatFitAdminBundle:FNFGroup', 'fnfg')
            ->andWhere('fnfg.isArchive = 0');

        if ($search) {
            $query
                ->andWhere('fnfg.groupTitle like :search')
                ->setParameter('search', "%".$search."%");
        }

        if (is_array($order)) {
            $orderByColumn    = $order[0]['column'];
            $orderByDirection = $order[0]['dir'];
            if ($orderByColumn == 0) {
                $orderByColumn = "fnfg.id";
            } elseif ($orderByColumn == 1) {
                $orderByColumn = "fnfg.groupTitle";
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
}