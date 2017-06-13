<?php

namespace LoveThatFit\AdminBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
class ProductSizeMeasurementHelper {

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
    protected $conf;
   
    
    
    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, Container $container){
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);        
    
    }
    
    public function find($id) {
        return $this->repo->find($id);
    }
    
    
    public function update($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
        return array('message' => 'Size Measurement ' . $entity->getTitle() . ' succesfully updated!',
            'field' => 'all',
            'message_type' => 'success',
            'success' => true,
        );
    }
   
    public function delete($id)
    {
        $entity = $this->repo->find($id);
        

        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();

            
            return array('product' => $entity,
                'message' => 'The measurements has been Deleted!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {

            return array(
                'message' => 'measurement not found!',
                'message_type' => 'warning',
                'success' => false,
            );
        }
    }
 
}