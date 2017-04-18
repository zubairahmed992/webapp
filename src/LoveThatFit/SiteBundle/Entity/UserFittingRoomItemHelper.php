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
#------------------------------------------------------------------
        public function viewUpdate($user_id, $item_id, $piece_id) {
        #~~~~~> find fitting room item    
        $fri = $this->findByUserItemId($user_id, $item_id);
        #return $this->toArray($fri);                                 
        if ($fri) {
            $product_item_piece = $piece_id == null ? null : $this->container->get('admin.helper.product.item.piece')->find($piece_id);
            if ($product_item_piece) {
                #return $product_item_piece->getProductColorView()->getTitle();
                if (!$fri->getProductItemPiece() || ($fri->getProductItemPiece() && $fri->getProductItemPiece()->getId() != $product_item_piece->getId())) {
                    $fri->setProductItemPiece($product_item_piece);
                    $this->save($fri);
                    return true;
                }
            } else {
                if ($fri->getProductItemPiece()) {
                    $fri->setProductItemPiece(null);
                    $this->save($fri);
                    return true;
                }
            }
        }
        return;
    }

    #====================================================
     public function _add($user, $item, $product_item_piece = null) {
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
                            #----------------------
                            if($product_item_piece){
                                $fri->setProductItemPiece($product_item_piece);
                            }else{
                                $fri->setProductItemPiece(null);
                            }
                            #----------------------
                            $this->save($fri);
                            $state_updated = true;
                            #update
                        }
                    } else {
                        if ($product_item_piece) {
                            if ($product_item_piece->getId() != $fri->getProductItemPiece()->getId()) {
                                $fri->setProductItemPiece($product_item_piece);
                                $this->save($fri);
                                $state_updated = true;
                            }
                        }
                        $already_existed = true;
                    }
                }
            }
        }

        if (!$state_updated && !$already_existed) {
            $this->createUserFittingRoomItem($user, $item, $product_item_piece);
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
    public function getItemIdsArrayByUser($user_id) {
        $fris = $this->findByUserId($user_id);
        $ar = array();
        
        foreach ($fris as $fri) {
            $piece_id = $fri->getProductItemPiece()?$fri->getProductItemPiece()->getId():null;
            array_push($ar, 
            array(  'product_id'=>$fri->getProductItem()->getProduct()->getId(),
                    'size_id'=>$fri->getProductItem()->getProductSize()->getId(),
                    'color_id'=>$fri->getProductItem()->getProductColor()->getId(),
                    'item_id'=>$fri->getProductItem()->getId(),
                    'item_piece_id'=>$piece_id,
                    )
                    );
        }
        return $ar;
    }

#------------------------------------------------------

    public function createUserFittingRoomItem($user, $productItem, $product_item_piece=null) {
        $userFittingRoomitem = new UserFittingRoomItem();
        $userFittingRoomitem->setCreatedAt(new \DateTime('now'));
        $userFittingRoomitem->setUpdatedAt(new \DateTime('now'));
        $userFittingRoomitem->setProductitem($productItem);
        $userFittingRoomitem->setProductItemPiece($product_item_piece);
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
    
#----------------------------------------------------
    public function toArray($fri){
        if ($fri){
        $piece_id = $fri->getProductItemPiece()?$fri->getProductItemPiece()->getId():null;
        return array(  'product_id'=>$fri->getProductItem()->getProduct()->getId(),
                    'size_id'=>$fri->getProductItem()->getProductSize()->getId(),
                    'color_id'=>$fri->getProductItem()->getProductColor()->getId(),
                    'item_id'=>$fri->getProductItem()->getId(),
                    'item_piece_id'=>$piece_id,
                    );
        }
    }


#------------------------------------------------------
    public function findByUserItemIdNew($user_id, $item_id) {
        return $this->repo->findByUserItemIdNew($user_id, $item_id);
    }

    #------------------------------------------------------
    public function getItemArrayByUser($user,$item_id) {
        $fris = $this->findByUserItemIdNew($user->getId(), $item_id);
        return $fris;
    }

    #------------------------------------------------------

    public function createUserFittingRoomItemWithProductId($user, $productItem, $product, $qty = 1) {
        $userFittingRoomitem = new UserFittingRoomItem();
        $userFittingRoomitem->setCreatedAt(new \DateTime('now'));
        $userFittingRoomitem->setUpdatedAt(new \DateTime('now'));
        $userFittingRoomitem->setProductitem($productItem);
        $userFittingRoomitem->setProductId($product);
        $userFittingRoomitem->setUser($user);
        $userFittingRoomitem->setQty($qty);

        return $this->save($userFittingRoomitem);
    }


    #------------------------------------------------------
    public function deleteByUserItemByProduct($user, $product_id, $product_item_id) {
        return $this->repo->deleteByUserItemByProduct($user->getId(), $product_id, $product_item_id);
    }


    #-------------------- Used in Get all Fitting Room service --------

    public function getAllFittingRoom($user) {
        $fris = $this->findByUserId($user->getId());

        $ar = array();
        $product_id = array();
        $getAllCategoriesByProductItem = $this->repo->getAllCategoriesByProductItem($user->getId());
        $a = 0;
        $previous_category = '';
        foreach($getAllCategoriesByProductItem as $key => $getAllCategoryByProductItem ){
            if($getAllCategoryByProductItem['product_id']!= null &&  $getAllCategoryByProductItem['product_item_id']) {

                if ($previous_category != '' && $previous_category != $getAllCategoryByProductItem['top_category_name']) {
                    $a++;
                }
                $ar['categories'][$a]['category_id'] = $getAllCategoryByProductItem['top_category_id'];
                $ar['categories'][$a]['name'] = $getAllCategoryByProductItem['top_category_name'];

                //Old Code
                //$ar['categories'][$a]['products'][] = $this->container->get('webservice.helper')->productDetailWithImagesForFitRoom($getAllCategoryByProductItem['product_id'], $getAllCategoryByProductItem['product_item_id'], $getAllCategoryByProductItem['qty'], $user);

                //New in Array
                $product_item_id_in_array = explode(',',$getAllCategoryByProductItem['product_item_id']);
                $ar['categories'][$a]['products'][] = $this->container->get('webservice.helper')->productDetailWithImagesForFitRoom($getAllCategoryByProductItem['product_id'], $product_item_id_in_array, $getAllCategoryByProductItem['qty'], $user);
                $previous_category = $ar['categories'][$a]['name'];
            }
        }
        return $ar;
    }

    #------------------------------------------------------
    public function findByUserItemByProduct($user_id, $product_id) {
        return $this->repo->findCountProductItemByUserid($user_id, $product_id);
    }


    #------------------------------------------------------
    public function findByUserItemByProductWithItemId($user_id, $product_id, $product_item_id) {
        return $this->repo->findCountProductItemByUseridWithProductItem($user_id, $product_id, $product_item_id);
    }
}

