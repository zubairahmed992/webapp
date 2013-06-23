<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\AdminBundle\Event\BrandEvent;

class BrandHelper {

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
        $brand = new $class();
        return $brand;
    }

//-------------------------------------------------------

    public function save($entity) {
        
        $entity->setCreatedAt(new \DateTime('now'));
        $entity->setUpdatedAt(new \DateTime('now'));    
            
        $entity->upload();
        $this->em->persist($entity);
        $this->em->flush();
        
        return array('brands' => $entity,
            'message' => 'The Brand has been Created!',
            'message_type' => 'success',
            
        );
    }

//-------------------------------------------------------

    public function find($id) {
        return $this->repo->find($id);
    }

//-------------------------------------------------------
    public function findByName($name) {
        return $this->repo->findByName($name);
    }

    //-------------------------------------------------------
    public function getListWithPagination($page_number, $sort) {
        $yaml = new Parser();
        $pagination_constants = $yaml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
        $limit = $pagination_constants["constants"]["pagination"]["limit"];

        $entity = $this->repo->listAllBrand($page_number, $limit, $sort);
        $rec_count = count($this->repo->countAllRecord());
        $cur_page = $page_number;

        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }
        return array('brands' => $entity,
            'rec_count' => $rec_count,
            'no_of_pagination' => $no_of_paginations,
            'limit' => $cur_page,
            'per_page_limit' => $limit,
        );
    }

    //-------------------------------------------------------
    public function isValid($entity) {
        
        if ($entity->getName() == '') {
            return array('message' => 'Please enter the Brand Name!',
                'field' => 'name',
                'valid' => false,
            );
        }elseif (count($this->findByName($entity->getName())) > 0) {
            return array('message' => 'Brand Name already exists!',
                'field' => 'name',
                'valid' => false,
            );
        }
        
        if ($entity->file == '') {
            return array('message' => 'Please add the Brand Logo image!',
                'field' => 'file',
                'valid' => false,
            );
        }
        
        return array('message' => 'Brand succesfully created.',
            'field' => 'all',
            'valid' => true,
        );
    }

}