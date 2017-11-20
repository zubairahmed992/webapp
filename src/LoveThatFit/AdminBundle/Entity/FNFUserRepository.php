<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * FNFUserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FNFUserRepository extends EntityRepository
{
    public function getApplicableUserForDiscount($user_id)
    {

        $sql = "SELECT f1_.id as group_id, f1_.min_amount as minAmount, f1_.discount as discount, l3_.auth_token as token, f0_.id AS id, f0_.is_available AS is_available1, f0_.is_archive AS is_archive2, f0_.user_id AS user_id3, f1_.group_type AS group_type
                    FROM fnf_user f0_ INNER JOIN fnfusers_groups f2_ ON f0_.id = f2_.fnfuser_id 
                    INNER JOIN fnf_group f1_ ON f1_.id = f2_.fnfgroup_id 
                    INNER JOIN ltf_users l3_ ON f0_.user_id = l3_.id 
                    LEFT JOIN user_orders u4_ ON l3_.id = u4_.user_id
                    WHERE l3_.id = :id and f1_.is_archive = 0 and (
                    case when f1_.group_type = 1
                    then ( f0_.is_available = 1 AND (u4_.discount_amount = 0 or u4_.discount_amount IS NULL) AND :current_date BETWEEN f1_.start_at AND f1_.end_at)
                    else 1
                    end
                    ) order by f1_.group_type desc limit 0,1";
        try {
            $date = new \DateTime("now");
            $conn = $this->getEntityManager()->getConnection();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue('id', $user_id);
            $stmt->bindValue('current_date', $date->format('Y-m-d H:i:s'));

            $stmt->execute();
            $returnArray = $stmt->fetchAll();
            if(!empty($returnArray))
            {
                return $returnArray[0];
            }
            else if(empty($returnArray))
            {
                $sql = "SELECT f1_.id as group_id, f1_.min_amount as minAmount, f1_.discount as discount, l3_.auth_token as token, f0_.id AS id, f0_.is_available AS is_available1, f0_.is_archive AS is_archive2, f0_.user_id AS user_id3, f1_.group_type AS group_type
                    FROM fnf_user f0_ INNER JOIN fnfusers_groups f2_ ON f0_.id = f2_.fnfuser_id 
                    INNER JOIN fnf_group f1_ ON f1_.id = f2_.fnfgroup_id 
                    INNER JOIN ltf_users l3_ ON f0_.user_id = l3_.id 
                    LEFT JOIN user_orders u4_ ON l3_.id = u4_.user_id
                    WHERE l3_.id = :id and f1_.is_archive = 0 and (
                    case when f1_.group_type = 1
                    then ( f0_.is_available = 1 AND :current_date BETWEEN f1_.start_at AND f1_.end_at)
                    else 1
                    end
                    ) order by f1_.group_type desc limit 0,1";

                $date = new \DateTime("now");
                $conn = $this->getEntityManager()->getConnection();
                $stmt = $conn->prepare($sql);
                $stmt->bindValue('id', $user_id);
                $stmt->bindValue('current_date', $date->format('Y-m-d H:i:s'));

                $stmt->execute();
                $returnArray = $stmt->fetchAll();
                if(!empty($returnArray))
                    return $returnArray[0];
            }

        } catch (\Doctrine\ORM\NoResultException $e) {

            $sql = "SELECT f1_.id as group_id, f1_.min_amount as minAmount, f1_.discount as discount, l3_.auth_token as token , f0_.id AS id, f0_.is_available AS is_available1, f0_.is_archive AS is_archive2, f0_.user_id AS user_id3, f1_.group_type AS group_type
                    FROM fnf_user f0_ INNER JOIN fnfusers_groups f2_ ON f0_.id = f2_.fnfuser_id 
                    INNER JOIN fnf_group f1_ ON f1_.id = f2_.fnfgroup_id 
                    INNER JOIN ltf_users l3_ ON f0_.user_id = l3_.id 
                    LEFT JOIN user_orders u4_ ON l3_.id = u4_.user_id
                    WHERE l3_.id = :id and f1_.is_archive = 0 and (
                    case when f1_.group_type = 1
                    then ( f0_.is_available = 1 AND :current_date BETWEEN f1_.start_at AND f1_.end_at)
                    else 1
                    end
                    ) order by f1_.group_type desc limit 0,1";
            try {
                $date = new \DateTime("now");
                $conn = $this->getEntityManager()->getConnection();
                $stmt = $conn->prepare($sql);
                $stmt->bindValue('id', $user_id);
                $stmt->bindValue('current_date', $date->format('Y-m-d H:i:s'));

                $stmt->execute();
                $returnArray = $stmt->fetchAll();
                if(!empty($returnArray))
                    return $returnArray[0];
            } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
            }

        }

    }

    // public function countAllFNFUserRecord()
    // {
    //     $total_record = $this->getEntityManager()
    //         ->createQuery('SELECT fnf, u FROM LoveThatFitAdminBundle:FNFUser fnf
    //                         JOIN fnf.users u 
    //                         join fnf.groups fg
    //                         WHERE fg.isArchive = 0');
    //     try {
    //         return $total_record->getResult();
    //     } catch (\Doctrine\ORM\NoResultException $e) {
    //         return null;
    //     }
    // }

    public function countAllFNFUserRecord()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('COUNT(fnf)')->from('LoveThatFitAdminBundle:FNFUser', 'fnf')
            ->join('fnf.groups', 'fnfg')
            ->andWhere('fnfg.isArchive = 0');
        $count = $query->getQuery()->getSingleScalarResult();

        return $count;
    }

    public function checkUserInGroup($groupId, $user_id)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query
            ->select('
                fnf.id as fnfid,
                fnfg.groupTitle as group_title,
                fnfg.discount,
                fnf.is_available,
                u.id,
                u.firstName,
                u.lastName,
                u.email,
                u.gender,
                u.createdAt,
                IDENTITY(u.original_user) as original_user_id'
            )
            ->from('LoveThatFitAdminBundle:FNFUser', 'fnf')
            ->join('fnf.groups', 'fnfg')
            ->join('fnf.users', 'u')
            ->andWhere('fnfg.id = :groupId')
            ->andWhere('fnf.users = :userId')
            ->setParameter("groupId", $groupId)
            ->setParameter('userId', $user_id);

        $preparedQuery = $query->getQuery();
        return $preparedQuery->getOneOrNullResult();
    }

    public function searchFNFUser($data, $page = 0, $max = NULL, $order, $getResult = true)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $search = isset($data['query']) && $data['query'] ? $data['query'] : null;

        $query
            ->select('
                fnf.id as fnfid,
                fnfg.groupTitle as group_title,
                fnfg.id as group_id,
                fnfg.discount,
                fnf.is_available,
                u.id,
                u.firstName,
                u.lastName,
                u.email,
                u.gender,
                u.createdAt,
                fnfg.group_type,
                IDENTITY(u.original_user) as original_user_id,
                o.id as orderId'
            )
            ->from('LoveThatFitAdminBundle:FNFUser', 'fnf')
            ->join('fnf.groups', 'fnfg')
            ->join('fnf.users', 'u')
            ->leftJoin('fnfg.user_order', 'o', 'with', 'o.user = u.id')
            ->andWhere('fnfg.isArchive = 0');
        if ($search) {
            $query
                ->andWhere('u.firstName like :search or u.lastName like :search or u.email like :search or fnfg.discount like :search or fnf.is_available like :search')
                /*->orWhere('u.lastName like :search')
                ->orWhere('u.email like :search')
                ->orWhere('fnfg.discount like :search')
                ->orWhere('fnf.is_available like :search')*/
                ->setParameter('search', "%" . $search . "%");
        }


        if (is_array($order)) {
            $orderByColumn = $order[0]['column'];
            $orderByDirection = $order[0]['dir'];
            if ($orderByColumn == 0) {
                //$orderByColumn = "u.id";
                $orderByColumn = "fnfg.id";
            } elseif ($orderByColumn == 1) {
                $orderByColumn = "group_title";
            } elseif ($orderByColumn == 2) {
                $orderByColumn = "u.firstName";
            } elseif ($orderByColumn == 3) {
                $orderByColumn = "u.email";
            } elseif ($orderByColumn == 5) {
                $orderByColumn = "orderId";
            }elseif ($orderByColumn == 6) {
                $orderByColumn = "fnf.is_available";
            }
            $query->OrderBy($orderByColumn, $orderByDirection);
        }
            /*echo $query->getQuery()->getSql(); die;
            return $query->getResult();*/
        if ($max) {
            $preparedQuery = $query->getQuery()
                ->setMaxResults($max)
                ->setFirstResult(($page) * $max);
        } else {
            $preparedQuery = $query->getQuery();
        }

        // echo $preparedQuery->getSQL(); die;
        return $getResult ? $preparedQuery->getResult() : $preparedQuery;
    }

    public function getUsersGroupData()
    {
        $query = $this->getEntityManager()->createQueryBuilder();

        $query
            ->select('
                fnf.id as fnfid,
                fnfg.groupTitle as group_title,
                fnfg.id as group_id,
                fnfg.discount,
                fnf.is_available,
                u.id,
                u.firstName,
                u.lastName,
                u.email,
                u.gender,
                u.createdAt,
                fnfg.group_type,
                IDENTITY(u.original_user) as original_user_id'
            )
            ->from('LoveThatFitAdminBundle:FNFUser', 'fnf')
            ->join('fnf.groups', 'fnfg')
            ->join('fnf.users', 'u')
            ->andWhere('fnfg.isArchive = 0')
            ->groupBy('u.id,fnfg.group_type')
            ->OrderBy('fnfg.id', 'desc');

        $preparedQuery = $query->getQuery();

        try {
            return $preparedQuery->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
}
