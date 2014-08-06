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

class UserItemTryHistoryHelper{

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
public function createUserItemTryHistory($user,$product_id,$productItem, $json_feedback, $fits,$fit_index){
    $product_helper = $this->container->get('admin.helper.product');
    
      $product=  $product_helper->find($product_id);
      $rec_count = $this->countUserItemTryHistory($user,$product,$productItem);      
        if ($rec_count>0) {
        //$em = $this->getDoctrine()->getEntityManager();
        $userItemTry = $this->repo->findby(array('product'=>$product,'productitem' => $productItem, 'user' => $user));
        foreach ($userItemTry as $userTryItem) {
            $usertryItemId = $userTryItem->getId();
            $counts= $userTryItem->getCount();            
            $userItemTryId = $this->repo->find($usertryItemId);
        }       
        $count=$counts+1;
        $userItemTryId->setCount($count);
        $userItemTryId->setFeedback($json_feedback);
        $userItemTryId->setFit($fits);
        $userItemTryId->setUpdatedAt(new \DateTime('now'));
        $this->save($userItemTryId);
        //$em->persist($userItemTryId);
        //$em->flush();
        } else {            
            $useritemtryhistory = new UserItemTryHistory();
            $useritemtryhistory->setCount(1);
            $useritemtryhistory->setFit($fits);
            $useritemtryhistory->setCreatedAt(new \DateTime('now'));
            $useritemtryhistory->setUpdatedAt(new \DateTime('now'));
            $useritemtryhistory->setProductitem($productItem);
            $useritemtryhistory->setProduct($product);            
            $useritemtryhistory->setUser($user);
            $useritemtryhistory->setFeedback($json_feedback);
            $useritemtryhistory->setFitIndex($fit_index);
            $this->save($useritemtryhistory);
           // $em = $this->getDoctrine()->getManager();
           // $em->persist($useritemtryhistory);
           // $em->flush();
        }      
        return true;
}   
public function countUserItemTryHistory($user,$product,$productItem)
   {
        $entity = $this->repo->findUserItemAllTryHistory($user,$product,$productItem);
        $rec_count = count($this->repo->findUserItemAllTryHistory($user,$product,$productItem));
        return $rec_count;
   } 

}