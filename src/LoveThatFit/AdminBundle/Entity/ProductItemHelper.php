<?php

namespace LoveThatFit\AdminBundle\Entity;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\AdminBundle\Event\productcolorEvent;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;


class ProductItemHelper {

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

     public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, Container $container)
    {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
        
    }

    public function find($id) {
        return $this->repo->find($id);
    }
   
    public function findByColorSize($product_color_id, $product_size_id){
       return $this->repo->findByColorSize($product_color_id, $product_size_id);
       
   }
   #-------------------------------------------------------------------------
    public function getProductItemById($id) {
        $product_item = $this->repo->find($id);
        return $product_item;
    }
   #--------------------------------------------------------------------------\
    public function getProductByItemId($productItem) {
        $entity = $this->repo->findProductByItemId($productItem);
        return $entity;
    }


}