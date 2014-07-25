<?php

namespace LoveThatFit\SiteBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\UserBundle\Entity\User;
use Symfony\Component\Yaml\Parser;

class UserFittingRoomItemHelper{

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
    
//-------------------------------------------------------
    public function createNew() {
        $class = $this->class;
        $userFittingRoomItem = new $class();
        return $userFittingRoomItem;
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
         
#--------------------Site Bundle Refactoring--------------------/
public function createUserFittingRoomItem($user,$productItem){
            $userFittingRoomitem = new UserFittingRoomItem();           
            $userFittingRoomitem->setCreatedAt(new \DateTime('now'));
            $userFittingRoomitem->setUpdatedAt(new \DateTime('now'));
            $userFittingRoomitem->setProductitem($productItem);                   
            $userFittingRoomitem->setUser($user);            
          return  $this->save($userFittingRoomitem);      
      
}   

public function deleteFittingRoomItem($user,$productItem)
{
    $entity= $this->repo->findUserFittingRommItem($user,$productItem);
    if($entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
    }
    return true;    
}



}