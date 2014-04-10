<?php

namespace LoveThatFit\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

class UserRepository extends EntityRepository {

    public function findByEmail($email) {
        return $this->findOneBy(array('email' => $email));
    }

    #--------------------------------------------------------------

    public function loadUserByAuthToken($auth_token) {
        return $this->findOneBy(array('authToken' => $auth_token));
    }

    #--------------------------------------------------------------

    public function isDuplicateEmail($id, $email) {
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

    public function findAllUsers($page_number = 0, $limit = 0, $sort = 'id') {

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

    public function countAllUserRecord() {
        $total_record = $this->getEntityManager()
                ->createQuery('SELECT us FROM LoveThatFitUserBundle:User us');
        try {
            return $total_record->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    #--------------------------------------------------------------

    public function findUserSearchListByGender($gender) {
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

    public function findByName($firstname, $lastname) {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT us FROM LoveThatFitUserBundle:User us 
     WHERE us.firstName LIKE :firstName OR
        us.lastName LIKE :lastName"
                        )->setParameters(array('firstName' => '%' . $firstname . '%',
            'lastName' => '%' . $lastname . '%'));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    #--------------------------------------------------------------    

    public function findByGenderName($firstname, $lastname, $gender) {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT us FROM LoveThatFitUserBundle:User us 
     WHERE us.firstName LIKE :firstName OR
        us.lastName LIKE :lastName OR
        us.gender=:gender"
                        )->setParameters(
                array('firstName' => '%' . $firstname . '%',
                    'lastName' => '%' . $lastname . '%',
                    'gender' => $gender));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    #--------------------------------------------------------------

    public function findUserByAge($beginDate, $endDate) {
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

    public function findByNameGenderBirthDateRange($firstname, $lastname, $gender, $beginDate, $endDate) {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT us FROM LoveThatFitUserBundle:User us 
     WHERE us.firstName LIKE :firstName OR
        us.lastName LIKE :lastName OR
        us.gender=:gender  OR
        us.birthDate BETWEEN :startDate AND :endDate"
                        )->setParameters(
                array('firstName' => '%' . $firstname . '%',
                    'lastName' => '%' . $lastname . '%',
                    'gender' => $gender,
                    'startDate' => $beginDate,
                    'endDate' => $endDate));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

#--------------------------------------------------------------    

    public function findUserByGender($gender) {
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
    public function findOneByName($firstName) {
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

    public function findMaxUserId() {
        $query = $this->getEntityManager()
                ->createQuery('SELECT max(us.id) as id FROM LoveThatFitUserBundle:User us');
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    
    public function getRecordsCountWithCurrentUserLimit($user_id){
        
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
  public function getAllUserDeviceType(){
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
  public function getDeviceTypeBaseOnUser($user_id){
       return $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select('ud.device_name as deviceName')
                        ->from('LoveThatFitUserBundle:User', 'u')
                        ->innerJoin('u.user_devices', 'ud')
                        ->Where("ud.device_name!=''")
                        ->andWhere("u.id:=user_id ")
                        ->groupBy('ud.device_name')
                         ->setParameter('user_id',$user_id)
                        ->getQuery()
                        ->getResult(); 
  }
#--------------------- Get  User with Device Type ------------------------------#
 function getFirstLimtedUserWithDeviceType($limit=0,$user_id=0){
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
                        ->setParameter('user_id',$user_id)
                        ->getQuery()
                        ->getResult(); 
       
  }
   
}

