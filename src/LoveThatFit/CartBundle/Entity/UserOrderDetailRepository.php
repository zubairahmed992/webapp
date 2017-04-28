<?php

namespace LoveThatFit\CartBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UserOrderDetailRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserOrderDetailRepository extends EntityRepository
{
    public function findByOrderID($order_id)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('ud.id as order_detail_id, ud.qty, ud.amount, ud.item_description, ud.sku, i.id as item_id, i.image, IDENTITY(i.product) as product_id, b.name as brand_name')
            ->from('LoveThatFitCartBundle:UserOrderDetail', 'ud')
            ->leftJoin("LoveThatFitAdminBundle:ProductItem", "i", "WITH", "ud.product_item = i.id")
            ->leftJoin("LoveThatFitAdminBundle:Product", "p", "WITH", "i.product = p.id")
            ->leftJoin("LoveThatFitAdminBundle:Brand", "b", "WITH", "p.brand = b.id")
            ->where('ud.user_order=:order_id')->setParameter('order_id', $order_id)
            ->getQuery()
            ->getResult();
    }

    public function findByOrderIDExport($order_id)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('ud.qty, ud.amount, ud.item_description, p.control_number, b.name as brand_name')
            ->from('LoveThatFitCartBundle:UserOrderDetail', 'ud')
            ->leftJoin("LoveThatFitAdminBundle:ProductItem", "i", "WITH", "ud.product_item = i.id")
            ->leftJoin("LoveThatFitAdminBundle:Product", "p", "WITH", "i.product = p.id")
            ->leftJoin("LoveThatFitAdminBundle:Brand", "b", "WITH", "p.brand = b.id")
            ->where('ud.user_order=:order_id')->setParameter('order_id', $order_id)
            ->getQuery()
            ->getResult();
    }
}
