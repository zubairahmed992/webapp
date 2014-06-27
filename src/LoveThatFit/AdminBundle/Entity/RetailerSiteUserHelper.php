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
    private $container;

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class) {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }

    //---------------------------------------------------------------------   

    public function createNew() {
        $class = $this->class;
        $brand = new $class();
        return $brand;
    }

//-------------------------------------------------------

 public function find($id) {
        return $this->repo->find($id);
    }
//-------------------------------------------------------
public function findSiteUserByUserId($user_id){
    return $this->repo->findSiteUserByUserId($user_id);
}    
 

}