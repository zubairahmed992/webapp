<?php

namespace LoveThatFit\UserBundle\Entity;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function isDuplicateEmail($id, $email) {
        $query = $this->getEntityManager()
                        ->createQuery('
            SELECT u FROM LoveThatFitUserBundle:User u
            WHERE u.email = :email AND u.id <> :id' 
                        )->setParameters(array('id' => $id, 'email' => $email));
        try {
            return $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
}