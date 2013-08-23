<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\AdminBundle\Event\surveyquestionEvent;

class SurveyQuestionHelper {

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

    public function createNewQuestion() {
        $class = $this->class;
        $question = new $class();
        return $question;
    }

    public function saveQuestion($entity) {   
        $question=$entity->getQuestion();
        $msg_array = $this->validateForCreate($question);
        if ($msg_array == null) {           
            $this->em->persist($entity);
            $this->em->flush();
            return array('message' => 'Question succesfully created.',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );
        } else {
            return $msg_array;
        }
        
    }

    public function updateQuestion($entity) {
        $msg_array = $this->validateForUpdate($entity);
        if ($msg_array == null) {
            $this->em->persist($entity);
            $this->em->flush();
            return array('message' => 'Question ' . $entity->getQuestion() . ' succesfully updated!',
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
        $entity_name = $entity->getQuestion();
        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();
            return array('question' => $entity,
                'message' => 'The Question ' . $entity_name . ' has been Deleted!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {
            return array('question' => $entity,
                'message' => 'question not found!',
                'message_type' => 'warning',
                'success' => false,
            );
        }
    }

    public function createNewAnswer() {
        $class = $this->class;
        $surveyanswer = new $class();
        return $surveyanswer;
    }

#------------------------------------------------------

    public function find($id) {
        return $this->repo->find($id);
    }

    public function findOneByName($title) {
        return $this->repo->findOneByName($title);
    }
    
    public function findWithSpecs($id) {
        $entity = $this->repo->find($id);
        if (!$entity) {
            $entity = $this->createNewQuestion();
            return array(
                'entity' => $entity,
                'message' => 'Question not found.',
                'message_type' => 'warning',
                'success' => false,
            );
        } else {
            return array(
                'entity' => $entity,
                'message' => 'Question found!',
                'message_type' => 'success',
                'success' => true,
            );
        }
    }

    public function getListWithPagination($page_number, $sort) {
        $yaml = new Parser();
        $pagination_constants = $yaml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
        $limit = $pagination_constants["constants"]["pagination"]["limit"];

        $entity = $this->repo->findlistAllQuestion($page_number, $limit, $sort);
        $rec_count = count($this->repo->countAllRecord());
        $cur_page = $page_number;

        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }
        return array(
            'data' => $entity,            
            'count_question' => $rec_count,
            'rec_count' => $rec_count,
            'no_of_pagination' => $no_of_paginations,
            'limit' => $cur_page,
            'per_page_limit' => $limit,
        );
    }

    private function validateForCreate($question) {
        if($question==null){            
            return array('message' => 'Enter values correctly!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }else
        {
        if (count($this->findOneByName($question)) > 0) {
            return array('message' => 'Question already exists!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }        
        }
        return;
    }

//----------------------------------------------------------
    private function validateForUpdate($entity) {
        $question=$entity->getQuestion();
        $questions = $this->findOneByName($entity->getQuestion());
        if($question==null)
        {
          return array('message' => 'Enter values correctly!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );  
        }else
        {
          if ($questions and $questions->getId()!= $entity->getId()) {
            return array('message' => 'Question already exists!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }    
        }        
        return;
    }

    public function getQuestionsList() {
        $question = $this->repo->findAll();
        return $question;
    }

     public function getquestionById($id) {
        
        $question=$this->repo->find($id);        
        return $question;
    }    
    

}