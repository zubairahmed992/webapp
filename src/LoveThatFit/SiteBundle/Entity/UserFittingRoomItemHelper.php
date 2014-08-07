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

class UserFittingRoomItemHelper {

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

    public function add($user, $item) {
        $fris = $this->findByUserId($user->getId());
        $state_updated = false;
        $already_existed = false;
        if ($item) {
            foreach ($fris as $fri) {
                $current_item = $fri->getProductItem();
                if ($current_item) {
                    if ($item->getId() != $current_item->getId()) {
                        if ($item->getProduct()->getclothingType()->getTarget() == $current_item->getProduct()->getclothingType()->getTarget()) {
                            #$this->delete($fri);
                            $fri->setProductitem($item);
                            $this->save($fri);
                            $state_updated = true;
                            #update
                        }
                    } else {
                        $already_existed = true;
                    }
                }
            }
        }

        if (!$state_updated && !$already_existed) {
            $this->createUserFittingRoomItem($user, $item);
            $state_updated = true;
        }
        return $state_updated;
        #$this->getArrayByUser($user);
    }

    #------------------------------------------------------   

    public function getArrayByUser($user) {
        $fris = $this->findByUserId($user->getId());
        $ar = array();
        foreach ($fris as $fri) {
#        $ar[$fri->getId()] = array('item_id'=>$fri->getProductItem()->getId(), 'target'=>$fri->getProductItem()->getProduct()->getclothingType()->getTarget());
            $ar[$fri->getId()] = 'item_id=' . $fri->getProductItem()->getId() . ', target=' . $fri->getProductItem()->getProduct()->getclothingType()->getTarget();
        }
        return $ar;
    }
#------------------------------------------------------
    public function getItemIdsArrayByUser($user) {
        $fris = $this->findByUserId($user->getId());
        $ar = array();
        foreach ($fris as $fri) {
            array_push($ar, $fri->getProductItem()->getId());
        }
        return $ar;
    }

#------------------------------------------------------

    public function createUserFittingRoomItem($user, $productItem) {
        $userFittingRoomitem = new UserFittingRoomItem();
        $userFittingRoomitem->setCreatedAt(new \DateTime('now'));
        $userFittingRoomitem->setUpdatedAt(new \DateTime('now'));
        $userFittingRoomitem->setProductitem($productItem);
        $userFittingRoomitem->setUser($user);
        return $this->save($userFittingRoomitem);
    }

#------------------------------------------------------

    public function findByUserItemId($user_id, $Item_id) {
        return $this->repo->findByUserItemId($user_id, $Item_id);
    }

#------------------------------------------------------

    public function deleteByUserItem($user_id, $Item_id) {
        $entity = $this->repo->findByUserItemId($user_id, $Item_id);
        return $this->delete($entity);
    }

#------------------------------------------------------

    public function delete($entity) {
        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();
        }
        return true;
    }

#------------------------------------------------------

    public function findByUserId($user_id) {
        return $this->repo->findByUserId($user_id);
    }

}

