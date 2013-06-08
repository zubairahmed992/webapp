<?php

namespace LoveThatFit\UserBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use LoveThatFit\UserBundle\Event\UserEvent;


class UserHelper{

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

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class)
    {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
        
    }
 //---------------------------------------------------------------------   
    
    public function createNewUser()
{
    $class = $this->class;
    $user = new $class();

    return $user;
}
//-------------------------------------------------------

public function saveUser(User $user)
{
    $this->em->persist($user);
    $this->em->flush();
    $this->dispatcher->dispatch('foo_bundle.post.comment_added', new CommentEvent($post, $comment));
}
//-------------------------------------------------------

public function find($id)
{
    return $this->repo->find($id);
}
    
}