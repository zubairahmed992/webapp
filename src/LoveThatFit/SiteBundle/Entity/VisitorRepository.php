<?php

namespace LoveThatFit\SiteBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * VisitorRepository
 */
class VisitorRepository extends EntityRepository {
    
    public function findByEmail($email) {
        $total_record = $this->getEntityManager()
                        ->createQuery("SELECT v FROM LoveThatFitSiteBundle:Visitor v     
        WHERE
        v.email = :email"
                        )->setParameters(array('email' => $email));
        try {
            return $total_record->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

}
