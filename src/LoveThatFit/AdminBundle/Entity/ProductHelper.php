<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\AdminBundle\Event\productEvent;

class ProductHelper {

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
//-------------------------------------------------------

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class) {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }
    
//-------------------------------------------------------
    public function createNew() {
        $class = $this->class;
        $product = new $class();
        return $product;
    }
   
    //-------------------------------------------------------

    public function save($entity) {
        //$msg_array =null;
        //$msg_array = ;

        $productName = $entity->getName();
        $msg_array = $this->validateForCreate($productName);
        if ($msg_array == null) {
            $entity->setCreatedAt(new \DateTime('now'));
            $entity->setUpdatedAt(new \DateTime('now'));

            $entity->upload();
            $this->em->persist($entity);
            $this->em->flush();

            return array('message' => 'Product succesfully created.',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );
        } else {
            return $msg_array;
        }
    }

    //-------------------------------------------------------

    public function update($entity) {

        $msg_array = $this->validateForUpdate($entity);

        if ($msg_array == null) {
            $entity->setUpdatedAt(new \DateTime('now'));

            $entity->upload();
            $this->em->persist($entity);
            $this->em->flush();

            return array('message' => 'Product ' . $entity->getName() . ' succesfully updated!',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );
        } else {
            return $msg_array;
        }
    }

//-------------------------------------------------------

    public function delete($id) {
        $entity = $this->repo->find($id);
        $entity_name = $entity->getName();

        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();

            return array('product' => $entity,
                'message' => 'The Product ' . $entity_name . ' has been Deleted!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {

            return array('product' => $entity,
                'message' => 'Product not found!',
                'message_type' => 'warning',
                'success' => false,
            );
        }
    }
//-------------------------------------------------------
    
    public function findWithSpecs($id) {
        $entity = $this->repo->find($id);

        if (!$entity) {
            $entity = $this->createNew();
            return array(
                'entity' => $entity,
                'message' => 'Product not found.',
                'message_type' => 'warning',
                'success' => false,
            );
        } else {
            return array(
                'entity' => $entity,
                'message' => 'Product found!',
                'message_type' => 'success',
                'success' => true,
            );
        }
    }
    
    public function removeBrand() {
        return $this->repo->removeBrand();
    }

    //-------------------------------------------------------

    public function getListWithPagination($page_number, $sort) {
        $yaml = new Parser();
        $pagination_constants = $yaml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
        $limit = $pagination_constants["constants"]["pagination"]["limit"];

        $entity = $this->repo->listAllProduct($page_number, $limit, $sort);
        $rec_count = count($this->repo->countAllRecord());
        $cur_page = $page_number;

        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }
        return array('products' => $entity,
            'rec_count' => $rec_count,
            'no_of_pagination' => $no_of_paginations,
            'limit' => $cur_page,
            'per_page_limit' => $limit,
            'femaleProduct'=>  $this->countProductsByGender('f'),
            'maleProduct'=>  $this->countProductsByGender('m'),
            'topProduct'=>$this->countProductsByType('Top'),
            'bottomProduct'=>$this->countProductsByType('Bottom'),
            'dressProduct'=>$this->countProductsByType('Dress')
        );
    }

//Private Methods    
//----------------------------------------------------------
    private function validateForCreate($name) {
        if (count($this->findOneByName($name)) > 0) {
            return array('message' => 'Product Name already exists!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }
        return;
    }

//----------------------------------------------------------
    private function validateForUpdate($entity) {
        $brand = $this->findOneByName($entity->getName());

        if ($brand && $brand->getId() != $entity->getId()) {
            return array('message' => 'Product Name already exists!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }
        return;
    }
    
#------------------------------------------------------
public function find($id) {
        return $this->repo->find($id);
    }


#---------------------------------------------------
    public function findProductByTitle($name) {
        return $this->repo->findProductByTitle($name);
    }
    
#---------------------------------------------------    
    private function countProductsByGender($gender)
    {
        
        return count($this->repo->findPrductByGender($gender));        
        
    }
    
    #---------------------------------------------------
    
    private function countProductsByType($target)
    {
        
        return count($this->repo->findPrductByType($target));           
    }
    
    #---------------------------------------------------
    //               Methods Product listing on index page
    #---------------------------------------------------
    
    public function listByType($options) {
        
            switch ($options['list_type'])
        {
        case "latest":        
        return $this->findByGenderLatest($options['gender']);
        break;
    
        case "most_tried_on":        
        return $this->findMostTriedOnByGender($options['gender']);
        break;
    
        case "recently_tried_on":        
        return $this->findRecentlyTriedOnByUser($options['user_id']);
        break;
    
        case "most_faviourite":        
        return $this->findMostLikedByGender($options['gender']);
        break ;
        
        case "ltf_recommendation":        
        return $this->findLTFRecomendedByGender($options['gender']);
        break ;
            
        default:
        return $this->findByGenderLatest($options['gender']);
        break ;
        }
        
   }
    #---------------------------------------------------
    
    
    public function findByGenderLatest($gender, $page_number=0, $limit=0) {
        return $this->repo->findByGenderLatest($gender, $page_number, $limit);
   }
    #---------------------------------------------------
    
    public function findRecentlyTriedOnByUser($user_id, $page_number=0, $limit=0) {
        return $this->repo->findRecentlyTriedOnByUser($user_id, $page_number, $limit);        
   }
   #---------------------------------------------------
    
   public function findMostTriedOnByGender($gender, $page_number=0, $limit=0) {
        return $this->repo->findMostTriedOnByGender($gender, $page_number, $limit);        
   }
    
   #---------------------------------------------------
    
   public function findLTFRecomendedByGender($gender, $page_number=0, $limit=0) {
        return $this->repo->findMostTriedOnByGender($gender, $page_number, $limit);        
   }
   #---------------------------------------------------
    
   public function findMostLikedByGender($gender, $page_number=0, $limit=0) {
        return $this->repo->findByGenderMostLiked($gender, $page_number, $limit);        
   } 
    
    #---------------------------------------------------
    #---------WROK WILL BE DONE I FUTURE!!!!!!!!!!!!!!!!!!!!!!!!!!!----#
    public function getDefaultFittingAlerts($product_id)
    {       
        // find product
        $product = $this->repo->find($product_id);        
      if($product){
        $product_color = $product->getDisplayProductColor();
        $product_color_id = $product_color->getId();        
         //get color size array, sizes that are available in this color         
        $color_sizes_array = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ProductColor')
                ->getSizeArray($product_color_id);
        $size_id = null;        
         // find size id is not in param gets the first size id for this color      
            $psize = array_shift($color_sizes_array);
            $size_id = $psize['id'];
       $product_size_id = $size_id;
        $product_size = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ProductSize')
                ->find($product_size_id);
        //2) color & size can get an item
        if ($product_size && $product_color) {
            $product_item = $this->getDoctrine()
                    ->getRepository('LoveThatFitAdminBundle:ProductItem')
                    ->findByColorSize($product_color->getId(), $product_size->getId());
            
          return  $item_id=$product_item->getId();
        }     
        }//End of If   
    }         
 

}