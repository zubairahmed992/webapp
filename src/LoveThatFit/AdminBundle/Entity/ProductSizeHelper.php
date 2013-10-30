<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\AdminBundle\Event\ProductSizeEvent;

class ProductSizeHelper {

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

    public function findSizeByProductTitle($title, $productid) {
        return $this->repo->findSizeByProductTitle($title, $productid);
    }
     public function find($id) {
        return $this->repo->find($id);
    }
    
    public function checkAttributes($attributes, $size_measurements) {
        $all_size_measurements = array();
        foreach ($attributes as $key => $value) {
            
            foreach ($size_measurements as $sm) {
            if ($sm->getTitle() == $key) {
                $all_size_measurements[$key] =  array('exists' => true, 'measurement' => $sm);
            }
        }
        $all_size_measurements[$key] = array('exists' => false, 'measurement' => null);
            
        }
        return $all_size_measurements;
    }

}