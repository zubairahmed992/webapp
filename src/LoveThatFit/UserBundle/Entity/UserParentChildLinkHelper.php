<?php

namespace LoveThatFit\UserBundle\Entity;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;
use LoveThatFit\UserBundle\Entity\UserParentChildLink;
use LoveThatFit\UserBundle\Entity\User;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\UserBundle\Event\UserEvent;
use Symfony\Component\HttpFoundation\Request;

class UserParentChildLinkHelper {

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
    
     public function createNew()
     {
       $class = $this->class;
       $userparentchildlink = new $class();
       return $userparentchildlink;
     }

    public function saveUserParentLink(UserParentChildLink $userparentchildlink) {                
        $this->em->persist($userparentchildlink);
        $this->em->flush();
    }  
    
    public function update($userparentchildlink)
    {
        $this->em->persist($userparentchildlink);
        $this->em->flush();
    }
    
    public function findByUser($user)
    {
     return $this->repo->findByUser($user);
    }
    
    
    public function find($id) {
        return $this->repo->find($id);
    } 
}
    
?>