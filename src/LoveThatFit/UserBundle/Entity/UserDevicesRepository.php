<?php

namespace LoveThatFit\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UserDevicesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserDevicesRepository extends EntityRepository
{
    
 #---------Find user marking base on device id  and user i----------------------#

    public function findHeightPerInchRatio($deviceType,$userId) {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT ud.deviceUserPerInchPixelHeight FROM LoveThatFitUserBundle:UserDevices ud 
   
     WHERE ud.deviceType= :deviceType
     and ud.user =:userId"
                        )->setParameters(array('deviceType' => $deviceType, 'userId' => $userId));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
 #--------------- Find Device Type base on user id ---------------------------#   
    public function findDeviceTypeBaseOnUserId($userId){
            $query = $this->getEntityManager()
                        ->createQuery("
     SELECT ud.deviceType as deviceType FROM LoveThatFitUserBundle:UserDevices ud 
     WHERE  ud.user =:userId"
                        )->setParameters(array('userId' => $userId));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
	#--------------- Find All on user id ---------------------------#
	public function findAllDeviceTypeBaseOnUserId($userId){
	  $query = $this->getEntityManager()
		->createQuery("
	   SELECT ud FROM LoveThatFitUserBundle:UserDevices ud
	   WHERE  ud.user =:userId"
		)->setParameters(array('userId' => $userId));
	  try {
		return $query->getArrayResult();
	  } catch (\Doctrine\ORM\NoResultException $e) {
		return null;
	  }
	}

}
