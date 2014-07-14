<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * RetailerSiteUserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RetailerSiteUserRepository extends EntityRepository
{
    public function findByReferenceId($user_reference_id){
        $query = $this->getEntityManager()
                    ->createQuery("SELECT rsu FROM LoveThatFitAdminBundle:RetailerSiteUser rsu
                                    WHERE rsu.user_reference_id =:user_reference_id")
            
                 ->setParameters(array('user_reference_id' => $user_reference_id));
                     try {
                     return $query->getSingleResult();
                } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
                }
            
       
        
    }
}
