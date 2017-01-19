<?php

namespace LoveThatFit\ProductIntakeBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;

class ProductSpecificationMappingHelper {

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

     private $container;

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class,Container $container) {
         $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }
    //-------------------------Create New Brand--------------------------------------------   

    public function createNew() {
        $class = $this->class;
        $c = new $class();
        $c->setCreatedAt(new \DateTime('now'));
        $c->setDisabled(false);
        return  $c;        
    }

//--------------------------Save Brand----------------------------------------------------------------

    public function save($entity) {       
        $this->em->persist($entity);
        $this->em->flush();        
    }

    
//------------------------------------------------------

    public function delete($id) {

        $entity = $this->repo->find($id);
        $title = $entity->getTitle();
        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();
            return array(
                'message' => 'The Brand ' . $title . ' has been Deleted!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {

            return array(
                'message' => 'Mapping not found!',
                'message_type' => 'warning',
                'success' => false,
            );
        }
    }

//----------------------Find Mappings By ID----------------------------------------------------------------

    public function find($id) {
        return $this->repo->find($id);
    }
   #--------------------Find All Mappings---------------------------------------------------------------------------------
  public function findAll(){
  return $this->repo->findAll();      
    }
//----------------------Find By title----------------------------------------------------------------
    public function findOneByTitle($title) {
        return $this->repo->findOneByTitle($title);
    }
    #--------------------------------------------
    
    public function getAllMappingArray() {
        return $this->repo->allMappingArray();
    }
    
    #--------------------------------------------
  public function toArray(){
      return array(
          'title' =>  $this->getTitle(),
          'brand' =>  $this->getBrand(),
          'gender' =>  $this->getGender(),
          'clothing_type' =>  $this->getClothingType(),
          'description' =>  $this->getDescription(),
          'mapping_json' =>  $this->getMappingJson(),
          'created_at' =>  $this->getCreatedAt(),
          'disabled' =>  $this->getDisabled(),
      );
  }
}
