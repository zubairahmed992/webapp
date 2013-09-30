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
                    ->createQuery('SELECT us FROM LoveThatFitUserBundle:User us')
                    ->setMaxResults($user_id);
                    return $query->getResult();
        } 

}

