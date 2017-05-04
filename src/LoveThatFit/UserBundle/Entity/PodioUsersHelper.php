<?php

namespace LoveThatFit\UserBundle\Entity;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;

use LoveThatFit\UserBundle\Entity\PodioUsers;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\UserBundle\Event\UserEvent;

use Symfony\Component\HttpFoundation\Request;

class PodioUsersHelper
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

    public function savePodioUsers($user_entity)
    {
        $entity = $this->createNew();
        $entity->setStatus(0);
        $entity->setPodioId(0);
        $entity->setCreatedAt(new \DateTime('now'));
        $entity->setUpdatedAt(new \DateTime('now'));
        $entity->setUser($user_entity);

        $this->em->persist($entity);
        $this->em->flush();
    }

//-------------------------------------------------------

    public function updatePodioUsers($id, $podio_id)
    {
        $entity = $this->find($id);
        $entity->setStatus(1);
        $entity->setPodioId($podio_id);
        $entity->setUpdatedAt(new \DateTime('now'));

        $this->em->persist($entity);
        $this->em->flush();
    }

    #----------------------------All Find Method -------------------------------------------------------------#    

    public function findAll()
    {
        return $this->repo->findAll();
    }

    #-----------------------------------------------------------
    public function find($id)
    {
        return $this->repo->findOneBy(array('id' => $id));
    }

    #-----------------------------------------------------------
    public function findWhereIdIn($ids)
    {
        return $this->repo->findWhereIdIn($ids);
    }

//---------------------------------------------------------
    //-------------------------------------------------------
    public function findOneByPodioId($podio_id)
    {
        return $this->repo->findOneByName($podio_id);
    }

    //-------------------------------------------------------
    public function findUserByStatus($status)
    {
        return $this->repo->findUserByStatus($status);
    }

    public function findPodioUserByUserId($user_id)
    {
        return $this->repo->findPodioUserByUserId($user_id);   
    }

}