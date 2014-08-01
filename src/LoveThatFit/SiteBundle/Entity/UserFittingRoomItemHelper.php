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
#------------------------------------------------------

    
    public function add($user, $item_id) {
        $item = $this->container->get('admin.helper.productitem')->find($item_id);
        $fris = $this->findByUserId($user->getId());        
        $ar = array();        
        $already_existed=false;
        foreach ($fris as $fri) {
                $current_item = $fri->getProductItem();                
               if ($item_id != $current_item->getId()) {                   
                    if ($item->getProduct()->getclothingType()->getTarget() == $current_item->getProduct()->getclothingType()->getTarget()) {
                        #$ar[$fri->getId()] = 'product_id='.$fri->getProductItem()->getProduct()->getId() . '  item_id='.$fri->getProductItem()->getId() . ', target=' . $fri->getProductItem()->getProduct()->getclothingType()->getTarget().' ~~> matched Target Removed';
                        $this->delete($fri);
                    }else{
                        #$ar[$fri->getId()] = 'product_id='.$fri->getProductItem()->getProduct()->getId() . '  item_id='.$fri->getProductItem()->getId() . ', target=' . $fri->getProductItem()->getProduct()->getclothingType()->getTarget().' ~~> Unmatched Target';
                    }                   
               }else{
                   #$ar[$fri->getId()] = 'product_id='.$fri->getProductItem()->getProduct()->getId() . '  item_id='.$fri->getProductItem()->getId() . ', target=' . $fri->getProductItem()->getProduct()->getclothingType()->getTarget().'  ~> matched Item';
                   $already_existed=true;
               }            
        }
        if (!$already_existed){
            $this->createUserFittingRoomItem($user, $item);
        }
        return $ar;
    }
 #------------------------------------------------------   
    public function getArrayByUser($user) {
        $fris = $this->findByUserId($user->getId());
        $ar = array();
        foreach ($fris as $fri) {
#        $ar[$fri->getId()] = array('item_id'=>$fri->getProductItem()->getId(), 'target'=>$fri->getProductItem()->getProduct()->getclothingType()->getTarget());
                    $ar[$fri->getId()] = 'item_id='.$fri->getProductItem()->getId() . ', target=' . $fri->getProductItem()->getProduct()->getclothingType()->getTarget();
        }
        return $ar;
    }
    #------------------------------------------------------
    public function _add($user_id, $item_id) {
        $fris = $this->findByUserId($user_id);
        $ar = array();
        foreach ($fris as $fri) {
            #$item=$this->container->get('admin.helper.productitem')->find($item_id);
            #$ar[$fri->getId()]=$item->getProduct()->getclothingType()->getTarget().'=='.$fri->getProductItem()->getProduct()->getclothingType()->getTarget();    
            $current_item = $fri->getProductItem();
            
            # item exact match
            if ($item_id == $current_item->getId())
                $ar[$fri->getId()] = 'matching item';
            else {
                $item = $this->container->get('admin.helper.productitem')->find($item_id);

                if ($item) {
                    # item target match
                    if ($item->getProduct()->getclothingType()->getTarget() == $current_item->getProduct()->getclothingType()->getTarget()) {
                        $fri;
                        #Replace the Item
                        #$ar[$fri->getId()] = 'replaced';
                            #delete old
                            #create new
                        $this->createUserFittingRoomItem($user, $item);
                    } else {
                        #Add the item 
                        #$ar[$fri->getId()] = $fri;
                        #create new
                        
                        $this->createUserFittingRoomItem($user, $item);
                        # item Product match
                        #if ($item->getProduct()->getId() == $fri->getProductItem()->getProduct()->getId())
                        #   $ar[$fri->getId()] = 'matching product';
                    }
                } 
            }
        }
        return $ar;
    }

    

#------------------------------------------------------
public function createUserFittingRoomItem($user,$productItem){
            $userFittingRoomitem = new UserFittingRoomItem();           
            $userFittingRoomitem->setCreatedAt(new \DateTime('now'));
            $userFittingRoomitem->setUpdatedAt(new \DateTime('now'));
            $userFittingRoomitem->setProductitem($productItem);                   
            $userFittingRoomitem->setUser($user);            
          return  $this->save($userFittingRoomitem);      
      
}   
#------------------------------------------------------

public function findByUserItemId($user_id,$Item_id){
    return $this->repo->findByUserItemId($user_id,$Item_id);
}
#------------------------------------------------------
        
public function deleteByUserItem($user_id,$Item_id)
{
    $entity= $this->repo->findByUserItemId($user_id,$Item_id);
    return $this->delete($entity);    
}
#------------------------------------------------------
public function delete($entity)
{    
    if($entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
    }
    return true;    
}
#------------------------------------------------------

public function findByUserId($user_id){
    return $this->repo->findByUserId($user_id);
}


}