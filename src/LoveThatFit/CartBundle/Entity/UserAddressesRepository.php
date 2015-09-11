<?php

namespace LoveThatFit\CartBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UserAddressesRepository
 *
 * This class was generated byfindAddressByUserId the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserAddressesRepository extends EntityRepository
{
  public function findAddressByUserId($user,$bill) {
	$str="";
	$parameters['user'] = $user;
	if($bill == 1){
	  $str=" and a.billing_default=1";
	}else{
	  $str=" and a.shipping_default=1";
	}
	$record = $this->getEntityManager()
	  ->createQuery(
		"SELECT a.id,a.address1
		 FROM LoveThatFitCartBundle:UserAddresses a
		 where a.user=:user $str
		 ")->setParameters($parameters);
	try {
	  return $record->getArrayResult();
	} catch (\Doctrine\ORM\NoResultException $e) {
	  return null;
	}
  }
  public function findAllAddressByUserId($user) {

	$record = $this->getEntityManager()
	  ->createQuery(
		"SELECT a.id,a.address1
		 FROM LoveThatFitCartBundle:UserAddresses a
		 where a.user=:user
		 ")->setParameter('user',$user);
	try {
	  return $record->getArrayResult();
	} catch (\Doctrine\ORM\NoResultException $e) {
	  return null;
	}
  }
  public function findDefaultBillingAddressByUserId($user) {
	$record = $this->getEntityManager()
	  ->createQuery(
		"SELECT a.id,a.first_name,a.last_name,a.phone,a.address1,a.address2,a.city,a.state,
		a.country,a.postcode,a.billing_default
		 FROM LoveThatFitCartBundle:UserAddresses a
		 where a.user=:user and a.billing_default=1
		 ")->setParameter('user',$user)->setMaxResults(1);

	try {
	  return $record->getSingleResult();
	} catch (\Doctrine\ORM\NoResultException $e) {
	  return null;
	}
  }
  public function findDefaultShippingAddressByUserId($user) {
	$record = $this->getEntityManager()
	  ->createQuery(
		"SELECT a.id,a.first_name,a.last_name,a.phone,a.address1,a.address2,a.city,a.state,
		a.country,a.postcode,a.shipping_default
		 FROM LoveThatFitCartBundle:UserAddresses a
		 where a.user=:user and a.shipping_default=1
		 ")->setParameter('user',$user)->setMaxResults(1);

	try {
	  return $record->getSingleResult();
	} catch (\Doctrine\ORM\NoResultException $e) {
	  return null;
	}
  }
  public function findAddressCountByUserId($user) {
	$record = $this->getEntityManager()
	  ->createQuery(
		"SELECT count(a.id) as counter
		 FROM LoveThatFitCartBundle:UserAddresses a
		 where a.user=:user
		 ")->setParameter('user',$user);

	try {
	  return $record->getSingleResult();
	} catch (\Doctrine\ORM\NoResultException $e) {
	  return null;
	}
  }
  public function findUserAddressValue($user,$val_type) {
	$qryFilter='';
	if($val_type == 1){
	  $qryFilter='set a.billing_default=0';
	}
	if($val_type == 2){
	  $qryFilter='set a.shipping_default=0';
	}

	$record = $this->getEntityManager()
	  ->createQuery(
		"UPDATE LoveThatFitCartBundle:UserAddresses a
		 $qryFilter
		 where a.user=:user
		 ")->setParameter('user',$user);

	try {
	  return $record->getSingleResult();
	} catch (\Doctrine\ORM\NoResultException $e) {
	  return null;
	}
  }
}
