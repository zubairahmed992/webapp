<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\AdminBundle\Event\CategoriesEvent;

class AppStoreLinkHelper {

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

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class) {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }

    public function createNew() {
        $class = $this->class;
        $categories = new $class();
        return $categories;
    }

    public function save($app_link) {
        $entity = $this->createNew();
        $entity->setAppLink($app_link);
        $entity->setUpdatedAt(new \DateTime('now'));
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function find($id) {
        return $this->repo->find($id);
    }

    public function update($id, $app_link) {
        $entity = $this->find($id);
        $entity->setAppLink($app_link);        
        $entity->setUpdatedAt(new \DateTime('now'));
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function findById($id){
        return $this->repo->findById($id);
    }
}