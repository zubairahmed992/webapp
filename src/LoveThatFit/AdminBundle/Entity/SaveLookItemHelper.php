<?php
/**
 * Created by PhpStorm.
 * User: haris.khalique
 * Date: 1/16/2017
 * Time: 7:16 PM
 */

namespace LoveThatFit\AdminBundle\Entity;

use LoveThatFit\AdminBundle\Entity\ProductItem;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use LoveThatFit\AdminBundle\Entity\SaveLook;

class SaveLookItemHelper
{
    protected $dispatcher;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repo;

    /**
     * @var string
     */
    protected $class;

    private $container;

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, Container $container) {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
        $this->container = $container;
    }

    public function createNew() {
        $class = $this->class;
        $saveLook = new $class();
        return $saveLook;
    }

    public function getItemById($itemId = 0)
    {
        $product_item_helper = $this->container->get('admin.helper.productitem');
        $product_items = $product_item_helper->getProductItemById( $itemId );

        return $product_items;
    }

    public function save($entity) {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function addProductItem(SaveLook $saveLook, ProductItem $productItem) {
        $saveLookObj = new SaveLookItem();
        $saveLookObj->setItems( $productItem);
        $saveLookObj->setSavelook( $saveLook);
        $this->save($saveLookObj);

        return $saveLookObj;
    }
}