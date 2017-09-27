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
     public function countAllFNFGroupRecord($group_type = 1)
     {
         $total_record = $this->getEntityManager()
             ->createQuery('SELECT fnfg FROM LoveThatFitAdminBundle:FNFGroup fnfg where fnfg.isArchive = 0 and fnfg.group_type = :grp_typ')
             ->setParameter("grp_typ", $group_type);
         try {
             return $total_record->getResult();
         } catch (\Doctrine\ORM\NoResultException $e) {
             return null;
         }
     }

    public function countAllFNFGroupCountRecord($group_type = 1)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('COUNT(fnfg)')->from('LoveThatFitAdminBundle:FNFGroup', 'fnfg')
            ->andWhere('fnfg.isArchive = 0');
           // ->andWhere('fnfg.group_type = :group_type')
           // ->setParameter("group_type", $group_type);
        $count = $query->getQuery()->getSingleScalarResult();

        return $count;
    }

    public function checkFnfUserUpdate($userIds)
    {
         $sql = 'Update fnf_user set is_available = 1 
                    WHERE `user_id` IN ('.$userIds.')';
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
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
                fnfg.group_type,
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


     public function checkFnfUserToUniqueGroup( $userIds, $group_type )
    {

       
/*
        $query     = $this->getEntityManager()->createQueryBuilder();
            //u.id,fnfg.id as groupId,fnfg.groupTitle
         $query
            ->select('               
                DISTINCT u.id as user_id'
            )
            ->from('LoveThatFitAdminBundle:FNFUser', 'fnf')
            ->join('fnf.groups', 'fnfg')
            ->join('fnf.users', 'u')   
            ->andWhere('fnfg.isArchive = 0')         
            ->andWhere('u.id IN (:userId)')            
            ->setParameter('userId', $userIds);

        $preparedQuery = $query->getQuery();
        return $preparedQuery->getResult();
*/

       $sql = 'SELECT 
                  distinct fnf_user.user_id AS user_id,fnf_group.groupTitle
                FROM
                  `fnf_user` 
                  INNER JOIN `fnfusers_groups` 
                    ON (
                      fnf_user.`id` = fnfusers_groups.`fnfuser_id`
                    ) 
                  INNER JOIN `fnf_group` 
                    ON (
                      fnf_group.`id` = fnfusers_groups.`fnfgroup_id`
                    )
                    WHERE fnf_group.is_archive = 0 AND fnf_group.`group_type` = '.$group_type.'  AND fnf_user.`user_id` IN ('.$userIds.')';

                       // ->andWhere('v.workingHours IN (:workingHours)')
    //->setParameter('workingHours', $workingHours);

        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        //$stmt->bindValue('group_type', $group_type);
        //$stmt->bindValue('userIds', $userIds);
        $stmt->execute();


        return $stmt->fetchAll();
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
