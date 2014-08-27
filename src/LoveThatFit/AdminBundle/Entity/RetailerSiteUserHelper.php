<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use LoveThatFit\AdminBundle\Entity\RetailerSiteUser;

class RetailerSiteUserHelper {

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

    //---------------------------------------------------------------------   

    public function createNew() {
        $class = $this->class;
        return  new $class();
    }

//-------------------------------------------------------

 public function find($id) {
        return $this->repo->find($id);
    }

//-------------------------------------------------------
public function findByReferenceId($user_reference_id=null,$retailer_id=null){
    return $this->repo->findByReferenceId($user_reference_id,$retailer_id);
}    

//-------------------------------------------------------
public function findUserByReferenceId($user_reference_id){
    return $this->repo->findUserByReferenceId($user_reference_id);
} 

//--------------------------------------------------------
public function findByUserIdRetId($user_id,$ret_id){
    return $this->repo->findByUserIdRetId($user_id,$ret_id);
} 
//-------------------------------------------------------

    public function addNew($user, $user_reference_id, $retailer=null,$customer_order=null) {
        
        $site_user_object=$this->findByUserIdRetId($user->getId(),$retailer->getId());
        if($site_user_object){
            //update
            $this->update($site_user_object,$retailer,$user,$user_reference_id,$customer_order);
        }else{
        $entity = $this->createNew();    
        $entity->setRetailer($retailer);
        $entity->setUser($user);
        $entity->setUserReferenceId($user_reference_id);
        $entity->setCustomerOrder($customer_order);
        $entity->setCreatedAt(new \DateTime('now'));
        $this->save($entity);        
        return $entity;
        }
    }

  public function update($entity,$retailer, $user, $user_reference_id,$customer_order=null)
  {
        $entity->setRetailer($retailer);
        $entity->setUser($user);
        $entity->setUserReferenceId($user_reference_id); 
        $entity->setCustomerOrder($customer_order);
        $entity->setUpdatedAt(new \DateTime('now'));
        $this->em->persist($entity);
        $this->em->flush();
        return array('message' => 'User Id for retailer succesfully update.',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );
  }
    
    //-------------------------------------------------------

    public function save($entity) {
            $entity->setUpdatedAt(new \DateTime('now'));
            $this->em->persist($entity);
            $this->em->flush();

            return array('message' => 'User Id for retailer succesfully created.',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );
    }

    
   //-------------------------------------------------------
    
    public function delete($id) {
        $entity = $this->repo->find($id);        
        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();
            return array('retailer' => $entity,
                'message' => 'The Retailer has been Deleted!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {

            return array('retailers' => $entity,
                'message' => 'Retailer not found!',
                'message_type' => 'warning',
                'success' => false,
            );
        }
  } 
}