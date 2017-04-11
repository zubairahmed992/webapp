<?php
namespace LoveThatFit\AdminBundle\Entity;

use LoveThatFit\AdminBundle\Entity\Product;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use LoveThatFit\AdminBundle\Entity\ShopLookProduct;

class ShopLookProductHelper
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
        $shopLookProduct = new $class();
        return $shopLookProduct;
    }

    public function save($entity, $shoplook_entity, $decoded) {

        /* Added Records on Shop Look Product*/
        foreach($decoded['products'] as $key => $val){
            $entity = self::createNew();
            $sort_number = $key + 1;
            $entity->setShoplook($shoplook_entity);
            $entity->setProductId($val);
            $entity->setSorting($sort_number);
            $entity->setCreatedAt(new \DateTime('now'));
            $entity->setUpdatedAt(new \DateTime('now'));
            $this->em->persist($entity);
        }
        $this->em->flush();
        $this->em->clear();
    }


    #-----------------------------------------------
    public function getShopLookProductsById($shoplook_id){
        return $this->repo->getShopLookProductsById($shoplook_id);
    }

    public function removeId($product_id){
        return $this->repo->removeId($product_id);
    }

}