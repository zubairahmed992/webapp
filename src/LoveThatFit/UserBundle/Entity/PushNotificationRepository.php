<?php

namespace LoveThatFit\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * PushNotificationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PushNotificationRepository extends EntityRepository
{
    public function findById($id) {
        return $this->findOneBy(array('id' => $id));
    }
#-------------------- Find By User Id-----------------------------------------#
 public function findByUserId($userId) {
  $query = $this->getEntityManager()
             ->createQuery("SELECT pn FROM LoveThatFitUserBundle:PushNotification pn   
                           WHERE pn.userId = :userId")
             ->setParameters(array('userId' => $userId));
   try {
         return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
 }
 #-------------------- Find All active-----------------------------------------#
 public function findAllActive() {
  $query = $this->getEntityManager()
             ->createQuery("SELECT pn FROM LoveThatFitUserBundle:PushNotification pn   
                           WHERE pn.isActive = 1");
   try {
         return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
 }
 #--------------------- Get Push Notification  Base on Base  On Type----------#
 public function getPushNotificationBaseOnType($type){
    $query = $this->getEntityManager()
             ->createQuery("SELECT pn FROM LoveThatFitUserBundle:PushNotification pn   
                           WHERE pn.userId =0
                           AND pn.notification_type=:notification_type")
             ->setParameters(array('notification_type' => $type));
   try {
         return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
 }

}
