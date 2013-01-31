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
    
    public function isDuplicateEmail($id, $email) {
        try {

            $entityByEmail = $this->loadUserByEmail($email);
            $entityById = $this->find($id);

            if ($entityByEmail) {
                return ($entityByEmail->getEmail() == $entityById->getEmail()) ? false : true;
            } else {
                return false;
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
}