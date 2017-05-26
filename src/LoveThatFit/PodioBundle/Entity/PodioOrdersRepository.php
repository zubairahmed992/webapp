<?php

namespace LoveThatFit\PodioBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

class PodioOrdersRepository extends EntityRepository
{

    public function findByOrderId($order_id)
    {
        return $this->findOneBy(array('order_id' => $order_id));
    }

    public function findOrdersByStatus($status)
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('o.id as order_id, o.order_date, o.billing_first_name, o.billing_last_name, 
                      o.billing_address1, o.billing_address2, o.billing_city, o.billing_postcode, 
                      o.billing_country, o.billing_state, o.shipping_first_name, o.shipping_last_name,
                      o.shipping_address1, o.shipping_address2, o.shipping_city, o.shipping_postcode,
                      o.shipping_country, o.shipping_state, o.order_status, o.order_amount,
                      o.transaction_status, o.transaction_id, o.payment_method, o.payment_json, o.billing_phone,
                      o.shipping_phone, o.order_number, o.shipping_amount, o.discount_amount, o.total_amount,
                      o.user_order_date, po.id, po.status
                    ')
            ->from('LoveThatFitPodioBundle:PodioOrders', 'po')
            ->innerJoin('po.user_podio_order', 'o')
            ->where('po.status IN (:status)')
            ->setParameter('status', $status)
            ->groupBy('o.id')
            ->getQuery();
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function findPodioOrderByOrderId($order_id)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select(
            'o.id as order_id')
            ->from('LoveThatFitPodioBundle:PodioOrders', 'po')
            ->innerJoin('po.user_podio_order', 'o')
            ->where('o.id =:order_id')
            ->setParameter('order_id', $order_id)
            ->getQuery()
            ->getResult();
    }
    
}