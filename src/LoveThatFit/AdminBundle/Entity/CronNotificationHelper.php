<?php

namespace LoveThatFit\AdminBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;

class CronNotificationHelper {

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

    public function __construct(
      EventDispatcherInterface $dispatcher,
      EntityManager $em,
      $class,
      Container $container
      ) {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }

    public function createNew()
    {
      $class = $this->class;
      $cronNotification = new $class();
      return $cronNotification;
    }

    public function save($entity)
    {
    }

    public function update($entity, $status)
    {
      $entity->setStatus($status);
      $this->em->persist($entity);
      $this->em->flush();

      return true;
    }

    public function find($id)
    {
      return $this->repo->find($id);
    }
   
    public function findAll()
    {
      return $this->repo->findAll();      
    }

    public function findByCronType($cronType)
    {
      return $this->repo->findByCronTypes($cronType);
    }
}