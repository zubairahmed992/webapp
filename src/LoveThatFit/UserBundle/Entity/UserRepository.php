<?php

namespace LoveThatFit\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

class UserRepository extends EntityRepository
{

    public function findByEmail($email)
    {
        return $this->findOneBy(array('email' => $email));
    }

    #--------------------------------------------------------------

    public function loadUserByAuthToken($auth_token)
    {
        return $this->findOneBy(array('authToken' => $auth_token));
    }

    #--------------------------------------------------------------

    public function isDuplicateEmail($id, $email)
    {
        try {

            $entityByEmail = $this->findOneBy(array('email' => $email));

            if (!($id) && !($entityByEmail)) {
                return false;
            } else {
                $entityById = $this->find($id);

                if ($entityByEmail) {
                    return ($entityByEmail->getEmail() == $entityById->getEmail()) ? false : true;
                } else {
                    return false;
                }
            }
        } catch (\Exception $e) {
            return $e;
        }
    }

    #--------------------------------------------------------------

    public function findAllUsers($page_number = 0, $limit = 0, $sort = 'id')
    {

        if ($page_number <= 0 || $limit <= 0) {
            $query = $this->getEntityManager()
                ->createQuery('SELECT us FROM LoveThatFitUserBundle:User us ORDER BY us.' . $sort . ' ASC');
        } else {
            $query = $this->getEntityManager()
                ->createQuery('SELECT us FROM LoveThatFitUserBundle:User us ORDER BY us.' . $sort . ' ASC')
                ->setFirstResult($limit * ($page_number - 1))
                ->setMaxResults($limit);
        }
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    #--------------------------------------------------------------

    public function countAllUserRecord()
    {
        $total_record = $this->getEntityManager()
            ->createQuery('SELECT us FROM LoveThatFitUserBundle:User us');
        try {
            return $total_record->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    #--------------------------------------------------------------

    public function findUserSearchListByGender($gender)
    {
        $query = $this->getEntityManager()
            ->createQuery("
     SELECT us FROM LoveThatFitUserBundle:User us
     WHERE us.gender=:gender"
            )->setParameter('gender', $gender);
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    #--------------------------------------------------------------

    public function findByName($firstname, $lastname)
    {
        $query = $this->getEntityManager()
            ->createQuery("
     SELECT us FROM LoveThatFitUserBundle:User us
     WHERE us.firstName LIKE :firstName OR
        us.lastName LIKE :lastName"
            )->setParameters(array('firstName' => '%' . $firstname . '%',
            'lastName'                         => '%' . $lastname . '%'));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    #--------------------------------------------------------------

    public function findByGenderName($firstname, $lastname, $gender)
    {
        $query = $this->getEntityManager()
            ->createQuery("
     SELECT us FROM LoveThatFitUserBundle:User us
     WHERE us.firstName LIKE :firstName OR
        us.lastName LIKE :lastName OR
        us.gender=:gender"
            )->setParameters(
            array('firstName' => '%' . $firstname . '%',
                'lastName'        => '%' . $lastname . '%',
                'gender'          => $gender));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    #--------------------------------------------------------------

    public function findUserByAge($beginDate, $endDate)
    {
        $query = $this->getEntityManager()
            ->createQuery("
     SELECT u FROM LoveThatFitUserBundle:User u
     WHERE  u.birthDate BETWEEN :startDate AND :endDate"
            )->setParameter('startDate', $beginDate)
            ->setParameter('endDate', $endDate);
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    #--------------------------------------------------------------

    public function findByNameGenderBirthDateRange($firstname, $lastname, $gender, $beginDate, $endDate)
    {
        $query = $this->getEntityManager()
            ->createQuery("
     SELECT us FROM LoveThatFitUserBundle:User us
     WHERE us.firstName LIKE :firstName OR
        us.lastName LIKE :lastName OR
        us.gender=:gender  OR
        us.birthDate BETWEEN :startDate AND :endDate"
            )->setParameters(
            array('firstName' => '%' . $firstname . '%',
                'lastName'        => '%' . $lastname . '%',
                'gender'          => $gender,
                'startDate'       => $beginDate,
                'endDate'         => $endDate));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

#--------------------------------------------------------------

    public function findUserByGender($gender)
    {
        $query = $this->getEntityManager()
            ->createQuery("
     SELECT us FROM LoveThatFitUserBundle:User us
     WHERE us.gender=:gender"
            )->setParameter('gender', $gender);
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    #--------------------------------------------------------------
    public function findOneByName($firstName)
    {
        $record = $this->getEntityManager()
            ->createQuery("SELECT us FROM LoveThatFitUserBundle:User us
                                WHERE us.firstName = :firstName")
            ->setParameters(array('firstName' => $firstName));
        try {
            return $record->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    #--------------------------------------------------------------

    public function findMaxUserId()
    {
        $query = $this->getEntityManager()
            ->createQuery('SELECT max(us.id) as id FROM LoveThatFitUserBundle:User us');
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function getRecordsCountWithCurrentUserLimit($user_id)
    {

        $query = $this->getEntityManager()
            ->createQuery("SELECT count(us.id)as id  FROM LoveThatFitUserBundle:User us WHERE us.id<=:user_id")
            ->setParameters(array('user_id' => $user_id));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function findChildUser($user)
    {
        $record = $this->getEntityManager()
            ->createQuery("SELECT u,up FROM LoveThatFitUserBundle:User u
                                       JOIN u.userparentchildlink up
                                       WHERE up.parent=:child_id")
            ->setParameters(array('child_id' => $user));
        try {
            return $record->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

#--------------------------- Get User and Device Name -----------------------#
    public function getAllUserDeviceType()
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('u.id as UserId ,ud.device_name as deviceName')
            ->from('LoveThatFitUserBundle:User', 'u')
            ->innerJoin('u.user_devices', 'ud')
            ->Where("ud.device_name!=''")
            ->groupBy('ud.device_name')
            ->getQuery()
            ->getResult();
    }
#-------------------------------Get Device Type Base On User ------------------#
    public function getDeviceTypeBaseOnUser($user_id)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('ud.device_name as deviceName')
            ->from('LoveThatFitUserBundle:User', 'u')
            ->innerJoin('u.user_devices', 'ud')
            ->Where("ud.device_name!=''")
            ->andWhere("u.id:=user_id ")
            ->groupBy('ud.device_name')
            ->setParameter('user_id', $user_id)
            ->getQuery()
            ->getResult();
    }
#--------------------- Get  User with Device Type ------------------------------#
    public function getFirstLimtedUserWithDeviceType($limit = 0, $user_id = 0)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('u.id as UserId ,ud.device_name as deviceName')
            ->from('LoveThatFitUserBundle:User', 'u')
            ->innerJoin('u.user_devices', 'ud')
            ->Where("ud.device_name!=''")
            ->andWhere('u.id>:user_id')
            ->groupBy('ud.device_name')
            ->orderBy('u.id')
            ->setMaxResults($limit)
            ->setParameter('user_id', $user_id)
            ->getQuery()
            ->getResult();

    }

    #----------------------------------------------------------------

    public function findUserByOptions($options)
    {
        if (!array_key_exists('gender', $options) || $options['gender'] == null) {
            if (strlen($options['from_id']) > 0 && strlen($options['to_id']) > 0) {
                $record = $this->getEntityManager()
                    ->createQuery("SELECT u FROM LoveThatFitUserBundle:User u
                                                     WHERE u.id<=:to_id
                                                     AND u.id>=:from_id")
                    ->setParameters(array('to_id' => $options['to_id'], 'from_id' => $options['from_id']));
                return $record->getResult();
            } elseif (strlen($options['to_id']) > 0) {
                $record = $this->getEntityManager()
                    ->createQuery("SELECT u FROM LoveThatFitUserBundle:User u
                                                             WHERE u.id<=:to_id")
                    ->setParameters(array('to_id' => $options['to_id']));
                return $record->getResult();

            } elseif (strlen($options['from_id']) > 0) {
                $record = $this->getEntityManager()
                    ->createQuery("SELECT u FROM LoveThatFitUserBundle:User u
                                                     WHERE u.id>=:from_id")
                    ->setParameters(array('from_id' => $options['from_id']));
                return $record->getResult();
            } else {
                $record = $this->getEntityManager()
                    ->createQuery("SELECT u FROM LoveThatFitUserBundle:User u");
                return $record->getResult();
            }

        } else {
            #----1
            if (strlen($options['from_id']) > 0 && strlen($options['to_id']) > 0) {
                $record = $this->getEntityManager()
                    ->createQuery("SELECT u FROM LoveThatFitUserBundle:User u
                                                     WHERE u.gender=:gender
                                                     AND u.id<=:to_id
                                                     AND u.id>=:from_id")->setParameters(array('gender' => $options['gender'], 'to_id' => $options['to_id'], 'from_id' => $options['from_id']));
                return $record->getResult();

                #----2
            } elseif (strlen($options['to_id']) > 0) {
                $record = $this->getEntityManager()
                    ->createQuery("SELECT u FROM LoveThatFitUserBundle:User u
                                                             WHERE u.gender=:gender
                                                             AND u.id<=:to_id")
                    ->setParameters(array('gender' => $options['gender'], 'to_id' => $options['to_id']));
                return $record->getResult();
                #----3
            } elseif (strlen($options['from_id']) > 0) {
                $record = $this->getEntityManager()
                    ->createQuery("SELECT u FROM LoveThatFitUserBundle:User u
                                                     WHERE u.gender=:gender
                                                     AND u.id>=:from_id")
                    ->setParameters(array('gender' => $options['gender'], 'from_id' => $options['from_id']));
                return $record->getResult();

                #----4
            } else {
                $record = $this->getEntityManager()
                    ->createQuery("SELECT u FROM LoveThatFitUserBundle:User u
                                                     WHERE u.gender=:gender")
                    ->setParameters(array('gender' => $options['gender']));
                return $record->getResult();
            }

        }

    }
    #-------------------------------------------------------------------

    public function findWhereIdIn($options)
    {
        $record = $this->getEntityManager()
            ->createQuery("SELECT u FROM LoveThatFitUserBundle:User u
                                                     WHERE u.id IN (:options)")
            ->setParameters(array('options' => $options));
        return $record->getResult();
    }

    //autocomplete method
    #--------------------------------------------------------------

    public function getSearchUserData($term)
    {
        //echo $term;die;
        $query = $this->getEntityManager()
            ->createQuery("
     SELECT us.id,us.email FROM LoveThatFitUserBundle:User us
     WHERE us.email LIKE :term"
            )->setParameters(array('term' => $term . '%'));

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    //end of autocomplete method

    public function search($data, $page = 0, $max = null, $order, $getResult = true)
    {
        $query     = $this->getEntityManager()->createQueryBuilder();
        $search    = isset($data['query']) && $data['query'] ? $data['query'] : null;
        $gender    = isset($data['gender']) && $data['gender'] ? $data['gender'] : null;
        $startDate = isset($data['startDate']) && $data['startDate'] ? $data['startDate'] : null;
        $endDate   = isset($data['endDate']) && $data['endDate'] ? $data['endDate'] : null;

        $query
            ->select('
                u.id,
                u.firstName,
                u.lastName,
                u.email,
                u.gender,
                u.createdAt,
                IDENTITY(u.original_user) as original_user_id'
            )
            ->from('LoveThatFitUserBundle:User', 'u');
        if ($search) {
            $query
                ->andWhere('u.firstName like :search')
                ->orWhere('u.lastName like :search')
                ->orWhere('u.email like :search')
                ->setParameter('search', "%" . $search . "%");
        }
        if ($gender != "") {
            $query
                ->andWhere('u.gender=:gender')
                ->setParameter('gender', $gender);
        }
        if ($startDate != "" && $endDate != "") {
            $query
                ->andWhere('u.birthDate BETWEEN :startDate AND :endDate')
                ->setParameter('startDate', $startDate)
                ->setParameter('endDate', $endDate);
        }

        if (is_array($order)) {
            $orderByColumn    = $order[0]['column'];
            $orderByDirection = $order[0]['dir'];
            if ($orderByColumn == 0) {
                $orderByColumn = "u.id";
            } elseif ($orderByColumn == 1) {
                $orderByColumn = "u.firstName";
            } elseif ($orderByColumn == 2) {
                $orderByColumn = "u.email";
            } elseif ($orderByColumn == 4) {
                $orderByColumn = "u.createdAt";
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
        return $getResult ? $preparedQuery->getResult() : $preparedQuery;
    }

    public function findAllUsersAsc()
    {
        $query = $this->getEntityManager()
            ->createQuery('SELECT us FROM LoveThatFitUserBundle:User us ORDER BY us.email ASC');
        //echo $query->getSql();
        return $query->getResult();
    }

    public function findAllUsersAuthDeviceToken()
    {
        $query = $this->getEntityManager()->createQueryBuilder();

        return $query->select('u.authToken, u.device_tokens')
            ->from('LoveThatFitUserBundle:User', 'u')
            ->where('u.device_tokens != :null')->setParameter('null', 'N;')
            ->getQuery()
            ->getResult();
    }

    public function findByEventName($event_name)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('COUNT(u)')
            ->from('LoveThatFitUserBundle:User', 'u')
            ->where('u.event_name=:event_name')
            ->setParameter('event_name', $event_name)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findUserList($start_date, $end_date)
    {
        $start = $end = "";
        if ($start_date != "") {
            $start = date("Y-m-d", strtotime($start_date) );
        }
        if ($end_date != "") {
            $end = date("Y-m-d", strtotime($end_date) );
        }
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('u.id, u.firstName, u.lastName,
                              u.email, u.gender,u.zipcode,
                              u.createdAt
                              ')
            ->from('LoveThatFitUserBundle:User', 'u');
            if ($start != "" && $end != "") {
               $query->where('u.createdAt BETWEEN :start AND :end')
                    ->setParameter('start', $start)
                    ->setParameter('end', $end);
            }
            $query->OrderBy('u.id', 'DESC');
            
            $preparedQuery = $query->getQuery();
            return $preparedQuery->getResult();
    }
}