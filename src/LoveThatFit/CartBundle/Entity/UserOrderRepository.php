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

	public function search(
		$data,
		$page = 0,
		$max = NULL,
		$order,
		$getResult = true
	) 
	{
		$query = $this->getEntityManager()->createQueryBuilder();
		$search = isset($data['query']) && $data['query']?$data['query']:null;
		$query 
		    ->select('
		    	o.id,
		        o.order_number,
				o.billing_first_name,
				o.billing_last_name,
				o.order_date,
				o.order_amount,
				o.payment_json'
		    )
		    ->from('LoveThatFitCartBundle:UserOrder', 'o');
		if ($search) {
		    $query 
		        ->andWhere('o.billing_first_name like :search')
                ->orWhere('o.billing_last_name like :search')
                ->setParameter('search', "%".$search."%");
		}
		if (is_array($order)) {
            $orderByColumn    = $order[0]['column'];
            $orderByDirection = $order[0]['dir'];
            if ($orderByColumn == 0) {
                $orderByColumn = "o.order_number";
            } elseif ($orderByColumn == 1) {
                $orderByColumn = "o.billing_first_name";
            } elseif ($orderByColumn == 2) {
                $orderByColumn = "o.order_date";
            } elseif ($orderByColumn == 3) {
                $orderByColumn = "o.order_amount";
            }
            $query->OrderBy($orderByColumn, $orderByDirection);
        }

		if ($max) {
		    $preparedQuery = $query->getQuery() 
		        ->setMaxResults($max)
		        ->setFirstResult(($page) * $max);
		} else {
		    $preparedQuery = $query->getQuery(); 
		}
		return $getResult?$preparedQuery->getResult():$preparedQuery; 
	}

	public function findOrderListByUserID($user_id)
	{
		$query = $this->getEntityManager()->createQueryBuilder();
		return $query->select('o.id, o.order_date, o.billing_first_name, o.billing_last_name, 
		o.billing_address1, o.billing_address2, o.billing_city, o.billing_postcode, 
		o.billing_country, o.billing_state, o.shipping_first_name, o.shipping_last_name,
		o.shipping_address1, o.shipping_address2, o.shipping_city, o.shipping_postcode,
		o.shipping_country, o.shipping_state, o.order_status, o.order_amount,
		o.transaction_status, o.transaction_id, o.payment_method, o.billing_phone,
		o.shipping_phone, o.order_number, o.shipping_amount')
		->from('LoveThatFitCartBundle:UserOrder', 'o')
		->where('o.user =:user_id')->setParameter('user_id', $user_id)
		->getQuery()
		->getResult();
	}

	public function findOrderList()
	{
		$query = $this->getEntityManager()->createQueryBuilder();
      	return $query->select('o.id, o.order_number, o.billing_first_name, 
      						  o.billing_last_name, o.order_date, o.order_amount, 
      						  o.payment_json, o.shipping_address1, o.shipping_city, 
							  o.shipping_state, o.shipping_country, o.shipping_postcode
							')
      	  	->from('LoveThatFitCartBundle:UserOrder', 'o')
      	  	->OrderBy('o.order_number', 'DESC')
          	->getQuery()
          	->getResult();
	}
}
