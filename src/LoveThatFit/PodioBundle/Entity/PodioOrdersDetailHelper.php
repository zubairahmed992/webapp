<?php

namespace LoveThatFit\PodioBundle\Entity;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;

use LoveThatFit\PodioBundle\Entity\PodioOrdersDetail;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\UserBundle\Event\UserEvent;

use Symfony\Component\HttpFoundation\Request;

class PodioOrdersDetailHelper
{

    /**
     * Holds the Symfony2 event dispatcher service
     */
    protected $dispatcher;

    /**
     * Holds the Doctrine entity manager for database interaction
     * @var EntityManager
     */
    protected $em;

    /**
     * Entity-specific repo, useful for finding entities, for example
     * @var EntityRepository
     */
    protected $repo;

    /**
     * The Fully-Qualified Class Name for our entity
     * @var string
     */
    protected $class;
    private $container;

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, Container $container)
    {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }

    //---------------------------------------------------------------------   
    #!!!!!!! User Created at and Update At  ----------------!!!#

    public function createNew()
    {
        $class = $this->class;
        $podio = new $class();
        return $podio;
    }

//-------------------------------------------------------

    public function savePodioOrdersDetail($order_entity,$podio_id,$podio_item_id)
    {
        $entity = $this->createNew();
        $entity->setPodioId($podio_id);
        $entity->setCreatedAt(new \DateTime('now'));
        $entity->setUpdatedAt(new \DateTime('now'));
        $entity->setUserOrderDetail($order_entity);
        $entity->setPodioItemId($podio_item_id);

        $this->em->persist($entity);
        $this->em->flush();
    }
}