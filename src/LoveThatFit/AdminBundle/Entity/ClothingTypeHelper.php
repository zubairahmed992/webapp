<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\AdminBundle\Event\ClothingTypeEvent;

class ClothingTypeHelper {

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

    public function createNew() {
        $class = $this->class;
        $clothing_types = new $class();
        return $clothing_types;
    }

    public function save($entity) {
        //$msg_array = null;        
        $name = $entity->getName();
        $msg_array = $this->validateForCreate($name);
        if ($msg_array == null and $name != null) {
            $entity->setCreatedAt(new \DateTime('now'));
            $entity->setUpdatedAt(new \DateTime('now'));
            $this->em->persist($entity);
            $this->em->flush();
            return array('message' => 'Clothing Type succesfully created.',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );
        } else {
            return $msg_array;
        }
    }

    public function find($id) {
        return $this->repo->find($id);
    }

    public function update($entity) {

        $msg_array = $this->validateForUpdate($entity);

        if ($msg_array == null) {
            $entity->setUpdatedAt(new \DateTime('now'));
            $this->em->persist($entity);
            $this->em->flush();

            return array('message' => 'ClothingType ' . $entity->getName() . ' succesfully updated!',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );
        } else {
            return $msg_array;
        }
    }

    //-------------------------------------------------------

    public function findWithSpecs($id) {
        $entity = $this->repo->find($id);

        if (!$entity) {
            $entity = $this->createNew();
            return array(
                'entity' => $entity,
                'message' => 'Clothing Type not found.',
                'message_type' => 'warning',
                'success' => false,
            );
        } else {
            return array(
                'entity' => $entity,
                'message' => 'Clothing Type found!',
                'message_type' => 'success',
                'success' => true,
            );
        }
    }

    public function delete($id) {
        $entity = $this->repo->find($id);
        $entity_name = $entity->getName();
        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();
            return array('clothing_types' => $entity,
                'message' => 'The Clothing Type ' . $entity_name . ' has been Deleted!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {

            return array('clothing_types' => $entity,
                'message' => 'clothing types not found!',
                'message_type' => 'warning',
                'success' => false,
            );
        }
    }

    public function getListWithPagination($page_number, $sort) {
        $yaml = new Parser();
        $pagination_constants = $yaml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
        $limit = $pagination_constants["constants"]["pagination"]["limit"];

        $entity = $this->repo->listAllClothingType($page_number, $limit, $sort);
        $rec_count = count($this->repo->countAllRecord());
        $cur_page = $page_number;

        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }
        return array('clothing_types' => $entity,
            'rec_count' => $rec_count,
            'no_of_pagination' => $no_of_paginations,
            'limit' => $cur_page,
            'per_page_limit' => $limit,
            'criteriaTop' => $this->countStatistics('Top'),
            'criteriaBottom' => $this->countStatistics('Bottom'),
            'criteriaDress' => $this->countStatistics('Dress'),
        );
    }

    //---------------------------------------------------------------------   
//-------------------------------------------------------
    public function findOneByName($name) {
        return $this->repo->findOneByName($name);
    }

    public function findClothingTypeByName($name) {
        return $this->repo->findClothingTypeByName($name);
    }
     
    #-----------------------------------------------
   public function findAll(){
  return $this->repo->findAll();      
    }

    //-------------------------------------------------------
    //Private Methods    
//----------------------------------------------------------
    private function validateForCreate($name) {
        if (count($this->findClothingTypeByName($name)) > 0) {
            return array('message' => 'clothing types Name already exists!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }
        return;
    }

//----------------------------------------------------------
    private function validateForUpdate($entity) {
        $clothing_types = $this->findClothingTypeByName($entity->getName());
        if ($clothing_types && $clothing_types->getId() != $entity->getId()) {
            return array('message' => 'Clothing Type Name already exists!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }
        return;
    }

    private function countStatistics($target) {

        return $rec_count = count($this->repo->findStatisticsBy($target));
    }

}