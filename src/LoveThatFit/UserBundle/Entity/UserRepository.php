<?php

namespace LoveThatFit\UserBundle\Entity;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
     public function loadUserByUsername($username) {
        return $this->findOneBy(array('username' => $username));
    }
    
    public function loadUserByEmail($email) {
        return $this->findOneBy(array('email' => $email));
    }
    
    public function loadUserByAuthToken($auth_token) {
        return $this->findOneBy(array('authToken' => $auth_token));
    }
    
   public function isDuplicateEmail($id, $email) {
        try {

            $entityByEmail = $this->loadUserByEmail($email);
            
            
            if(!($id) && !($entityByEmail)){
                return false;
            }else{
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
    
    public function isUserNameExist($username) {
        try {
            
            $entity = $this->loadUserByUsername($username);
            return $entity ? true : false;
        
            
        } catch (\Exception $e) {
            return $e;
        }
    }
    
    
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
    
    
    
    public function countAllUserRecord() {
        $total_record = $this->getEntityManager()
                ->createQuery('SELECT us FROM LoveThatFitUserBundle:User us');
        try {
            return $total_record->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    
    
    
}