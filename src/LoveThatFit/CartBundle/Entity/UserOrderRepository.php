<?php

namespace LoveThatFit\CartBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UserOrderRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserOrderRepository extends EntityRepository
{
  public function findOneByUserAddress($user) {
	$record = $this->getEntityManager()
	  ->createQuery(
		"SELECT uo.id,uo.shipping_first_name,uo.shipping_last_name,
		 uo.shipping_address1,uo.shipping_city,uo.shipping_country,uo.shipping_postcode,uo.shipping_state
		 FROM LoveThatFitCartBundle:UserOrder uo where  uo.user=:user
		 ")->setParameter('user',$user)->setMaxResults(1);
	//echo $record->getSQL();die;
	//print_r($record);
	//die;
	try {
	  return $record->getSingleResult();
	} catch (\Doctrine\ORM\NoResultException $e) {
	  return null;
	}
  }

  public function countAllRecord() {
	$total_record = $this->getEntityManager()
	  ->createQuery('SELECT o FROM LoveThatFitCartBundle:UserOrder o');
	try {
	  return $total_record->getResult();
	} catch (\Doctrine\ORM\NoResultException $e) {
	  return null;
	}
  }
  public function listAllOrders($page_number = 0, $limit = 0, $sort = 'id') {
	if ($page_number <= 0 || $limit <= 0) {
	  $query = $this->getEntityManager()
		->createQuery('SELECT o FROM LoveThatFitCartBundle:UserOrder o ORDER BY o.' . $sort . ' DESC');
	} else {
	  $query = $this->getEntityManager()
		->createQuery('SELECT o FROM LoveThatFitCartBundle:UserOrder o ORDER BY o.' . $sort . ' DESC')
		->setFirstResult($limit * ($page_number - 1))
		->setMaxResults($limit);
	}
	try {
	  return $query->getResult();
	} catch (\Doctrine\ORM\NoResultException $e) {
	  return "null";
	}
  }

  public function getRecordsCountWithCurrentOrderLimit($order_id){

	$query = $this->getEntityManager()
	  ->createQuery("SELECT count(o.id) as id FROM LoveThatFitCartBundle:UserOrder o WHERE o.id <=:order_id")
	  ->setParameters(array('order_id' => $order_id));
	try {
	  return $query->getResult();
	} catch (\Doctrine\ORM\NoResultException $e) {
	  return null;
	}
  }
  public function countAllRecordByUser($user) {
	$total_record = $this->getEntityManager()
	  ->createQuery('SELECT o.id as id FROM LoveThatFitCartBundle:UserOrder o where o.user=:user')->setParameters(array('user' => $user));
	try {
	  return $total_record->getResult();
	} catch (\Doctrine\ORM\NoResultException $e) {
	  return null;
	}
  }
  public function listAllOrdersByUser($page_number = 0, $limit = 0,$user, $sort = 'id') {
	if ($page_number <= 0 || $limit <= 0) {
	  $query = $this->getEntityManager()
		->createQuery('SELECT o FROM LoveThatFitCartBundle:UserOrder o where o.user=:user ORDER BY o.' . $sort . ' DESC')->setParameters(array('user' => $user));
	} else {
	  $query = $this->getEntityManager()
		->createQuery('SELECT o FROM LoveThatFitCartBundle:UserOrder o where o.user=:user ORDER BY o.' . $sort . ' DESC')->setParameters(array('user' => $user))
		->setFirstResult($limit * ($page_number - 1))
		->setMaxResults($limit);

	}
	try {
	  return $query->getResult();
	} catch (\Doctrine\ORM\NoResultException $e) {
	  return "null";
	}
  }

  public function findOneByUser($order_id,$user) {
	$record = $this->getEntityManager()
	  ->createQuery(
		"SELECT count(o.id) as counter
		 FROM LoveThatFitCartBundle:UserOrder o WHERE o.user=:user and o.id=:order_id
		 ")->setParameter('order_id',$order_id)
	  ->setParameter('user', $user)
	  ->setMaxResults(1);

	try {
	  return $record->getSingleResult();
	} catch (\Doctrine\ORM\NoResultException $e) {
	  return null;
	}
  }
}
