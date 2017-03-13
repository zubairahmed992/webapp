<?php

namespace LoveThatFit\CartBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CartRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class WishlistRepository extends EntityRepository
{
  public function findOneByUserItem($user,$product_item) {
	$record = $this->getEntityManager()
	  ->createQuery(
		"SELECT c.qty,c.id
		 FROM LoveThatFitCartBundle:Wishlist c WHERE c.user=:user and c.product_item=:product_item
		 ")->setParameter('user',$user)
	  	   ->setParameter('product_item', $product_item)
	  	   ->setMaxResults(1);

	try {
	  return $record->getSingleResult();
	} catch (\Doctrine\ORM\NoResultException $e) {
	  return null;
	}
  }

  public function removeWishlistByUser($user){
	$record = $this->getEntityManager()
	  ->createQuery("DELETE FROM LoveThatFitCartBundle:Wishlist c
                    WHERE c.user = :user")
	  ->setParameters(array('user' => $user));
	try {
	  return $record->getResult();
	} catch (\Doctrine\ORM\NoResultException $e) {
	  return null;
	}
  }

	public function removeWishlistByItem($user,$product_item){
	$record = $this->getEntityManager()
	  ->createQuery("DELETE FROM LoveThatFitCartBundle:Wishlist c
                    WHERE c.user = :user and c.product_item=:product_item")
	  ->setParameter('user' , $user)
      ->setParameter('product_item', $product_item);
	try {
	  return $record->getResult();
	} catch (\Doctrine\ORM\NoResultException $e) {
	  return null;
	}
  }


  public function countWishlistByUser($user){
	$record = $this->getEntityManager()
	  ->createQuery("SELECT COUNT(c.id) as counter FROM LoveThatFitCartBundle:Wishlist c
                    WHERE c.user = :user")
	  ->setParameters(array('user' => $user));
	try {
	  return $record->getSingleResult();
	} catch (\Doctrine\ORM\NoResultException $e) {
	  return null;
	}
  }

  public function countWishlistByUserQuantity($user){
	$record = $this->getEntityManager()
	  ->createQuery("SELECT COUNT(c.qty) as counter FROM LoveThatFitCartBundle:Wishlist c
                    WHERE c.user = :user")
	  ->setParameters(array('user' => $user));
	try {
	  return $record->getSingleResult();
	} catch (\Doctrine\ORM\NoResultException $e) {
	  return null;
	}
  }

}
