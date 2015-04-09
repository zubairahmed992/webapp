<?php
namespace LoveThatFit\AdminBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Yaml\Parser;
use LoveThatFit\AdminBundle\Event\ProductItemPieceEvent;
use Symfony\Component\HttpFoundation\Response;

class ProductItemPieceHelper {

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

    public function findOrCreateNew($item, $color_view){
        $piece = $this->findByItemColorView($item->getId(), $color_view->getId());
        if ($piece){
            return $piece;            
        }else{
            return $this->createNew($item, $color_view);
        }
            
    }
    //---------------------------------------------------------------------   
    public function createNew($item=null, $color_view=null) {
        $class = $this->class;
        $piece = new $class();
        if($item){
            $piece->setProductItem($item);
        }
        if($color_view){
            $piece->setProductColorView($color_view);
        }
        return $piece;
    }

    
//-------------------------------------------------------

    public function save($entity) {   
        $msg_array =null;
          if ($msg_array == null) {
            $entity->upload();
            $this->em->persist($entity);
            $this->em->flush();    
             return array('message' => 'Piece has been succesfully created.',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );
        } else {
            return $msg_array;
        }
    }
    //-------------------------------------------------------

    public function saveWithoutUpload($entity) {   
        $msg_array =null;
          if ($msg_array == null) {            
            $this->em->persist($entity);
            $this->em->flush();    
             return array('message' => 'Piece has been succesfully saved.',
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
            $entity->upload();
            $this->em->persist($entity);
            $this->em->flush();                  
    }

//-------------------------------------------------------

    public function delete($id) {

        $entity = $this->repo->find($id);
        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();
            return array('brands' => $entity,
                'message' => 'The Brand specification '. ' has been Deleted!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {

            return array('brands' => $entity,
                'message' => 'Brand specification not found!',
                'message_type' => 'warning',
                'success' => false,
            );
        }
    }

//-------------------------------------------------------

    public function findByItemColorView($item_id, $color_view_id) {        
        return $this->repo->findByItemColorView($item_id, $color_view_id);
    }
    public function find($id) {
        return $this->repo->find($id);
    }
   #-----------------------------------------------------
  public function findAll(){
  return $this->repo->findAll();      
    }
    
 



    
}