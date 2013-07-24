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
    
    public function findUserSearchListByGender($gender)
    {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT us FROM LoveThatFitUserBundle:User us 
     WHERE
        us.gender=:gender"
            )->setParameter('gender', $gender); 
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
        
    }
    
    public function findUserSearchListByName($firstname,$lastname)
    {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT us FROM LoveThatFitUserBundle:User us 
     WHERE
        us.firstName LIKE :firstName
        or
        us.lastName LIKE :lastName         
        "
        )->setParameters(array('firstName'=>'%'.$firstname.'%','lastName'=>'%'.$lastname.'%')); 
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
        
    }
    
    
    public function findUserSearchListBy($firstname,$lastname,$gender)
    {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT us FROM LoveThatFitUserBundle:User us 
     WHERE
        us.firstName LIKE :firstName
        or
        us.lastName LIKE :lastName
        or
        us.gender=:gender        
        "
        )->setParameters(array('firstName'=>'%'.$firstname.'%','lastName'=>'%'.$lastname.'%','gender'=>$gender)); 
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
        
    }
    
    public function findUserByAge($beginDate,$endDate)
    {
      $query = $this->getEntityManager()
                        ->createQuery("
     SELECT u FROM LoveThatFitUserBundle:User u 
     where
        u.birthDate BETWEEN :startDate
        AND 
        :endDate
        "
        )->setParameter('startDate',$beginDate)
         ->setParameter('endDate',$endDate);
        try {                     
            return $query->getResult();
            
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
        
    }
    
    public function findUserSearchListsBy($firstname,$lastname,$gender,$beginDate,$endDate)
    {
      $query = $this->getEntityManager()
                        ->createQuery("
     SELECT us FROM LoveThatFitUserBundle:User us 
     where
        us.firstName LIKE :firstName
        or
        us.lastName LIKE :lastName
        or
        us.gender=:gender 
        or
        us.birthDate BETWEEN :startDate
        AND 
        :endDate
        "
        )->setParameters(array('firstName'=>'%'.$firstname.'%','lastName'=>'%'.$lastname.'%','gender'=>$gender,'startDate'=>$beginDate,'endDate'=>$endDate));
        try {                     
            return $query->getResult();
            
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
        
    }
    
    
    public function findUserByGender($gender)
    {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT us FROM LoveThatFitUserBundle:User us 
     where
        us.gender=:gender"
        )->setParameter('gender',$gender);
        try {                     
            return $query->getResult();
            
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    
  public function findUserByMonth()
  {//SELECT DATE_FORMAT(us.created_at, '%M') as month,COUNT(id) as total FROM LoveThatFitUserBundle:User us  GROUP BY DATE_FORMAT(us.created_at, '%Y%M') 
      $query = $this->getEntityManager()
                        ->createQuery("
     SELECT COUNT(u.id) as total FROM LoveThatFitUserBundle:User u"
        );
        try {                     
            return $query->getArrayResult();
            
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        } 
  }
    
  public function findUserAge()
  {
      $query = $this->getEntityManager()
                        ->createQuery("
     SELECT us.birthDate, CURDATE(),TIMESTAMPDIFF(YEAR,us.birthDate,CURDATE()) AS age FROM LoveThatFitUserBundle:User us 
     "
        );
        try {                     
            return $query->getResult();
            
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
  }
  
  public function findUserByAgeGroup($startage,$endage)
  {
      $query = $this->getEntityManager()
                        ->createQuery("
     SELECT us FROM LoveThatFitUserBundle:User us 
     where 
     TIMESTAMPDIFF(YEAR,us.birth_date,CURRENT_DATE()) BETWEEN
     :startage
     AND 
     :endage"
            )->setParameters(array('startage'=>$startage,'endage'=>$endage));
        try {                     
            return $query->getResult();
            
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
  }
  
  public function findOneByName($firstName) {
        $record = $this->getEntityManager()
                        ->createQuery("SELECT us FROM LoveThatFitUserBundle:User us   
                                WHERE us.firstName = :firstName")
                        ->setParameters(array('firstName' =>$firstName));
        try {
            return $record->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
  
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
    
    
}



