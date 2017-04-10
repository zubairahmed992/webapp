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

class UserItemFavHistoryHelper{

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

    function testFunction(){
        return "test function in user item fav history helper";
    }
    
//-------------------------------------------------------
    public function createNew() {
        $class = $this->class;
        $userItemTryHistory = new $class();
        return $userItemTryHistory;
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
    public function createUserItemFavHistory($user,$p , $items, $status, $page){


        $useritemtryhistory = new UserItemFavHistory();
        $useritemtryhistory->setUser($user);
        $useritemtryhistory->setProduct($p);
        $useritemtryhistory->setProductitem($items);
        $useritemtryhistory->setStatus($status);
        if($page != null){
            $useritemtryhistory->setPage($page);
        }
        $this->save($useritemtryhistory);
        return true;
    }
    
public function countUserItemFavHistory($user,$product,$productItem)
   {
        $entity = $this->repo->findUserItemAllFavHistory($user,$product,$productItem);
        $rec_count = count($this->repo->findUserItemAllFavHistory($user,$product,$productItem));
        return $rec_count;
   } 


}