<?php

namespace LoveThatFit\SiteBundle\Entity;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class VisitorHelper {

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

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, Container $container) {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }

//-------------------------------------------------------
    public function createNew() {
        $class = $this->class;
        $visitor = new $class();
        return $visitor;
    }

    //-------------------------------------------------------

    public function save($entity) {
        $this->em->persist($entity);
        $this->em->flush();
    }

#------------------------------------------------------

    public function find($id) {
        return $this->repo->find($id);
    }
#------------------------------------------------------

    public function findByEmail($email) {
        return $this->repo->findByEmail($email);
    }

}

