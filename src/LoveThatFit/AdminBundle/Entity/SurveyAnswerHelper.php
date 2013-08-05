<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\AdminBundle\Event\surveyAnswerEvent;

class SurveyAnswerHelper {

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

    public function createNewAnswer() {
        $class = $this->class;
        $answer = new $class();
        return $answer;
    }
    
    public function saveAnswer($entity) {
        //$msg_array =null;
        //$msg_array = ;

        $answer = $entity->getAnswer();
        $msg_array = $this->validateForCreate($answer);
        if ($msg_array == null) {            
            $entity->upload();
            $this->em->persist($entity);
            $this->em->flush();
            return array('message' => 'Answer succesfully created.',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );
        } else {
            return $msg_array;
        }
    }
    
    
    public function updateAnswer($entity) {

        $msg_array = $this->validateForUpdate($entity);

        if ($msg_array == null) {            
            $this->em->persist($entity);
            $this->em->flush();
            return array('message' => 'Question ' . $entity->getAnswer() . ' succesfully updated!',
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
        $entity_name = $entity->getAnswer();
        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();
            return array('answer' => $entity,
                'message' => 'The Question ' . $entity_name . ' has been Deleted!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {
            return array('answer' => $entity,
                'message' => 'question not found!',
                'message_type' => 'warning',
                'success' => false,
            );
        }
    }

    

   
    
#------------------------------------------------------

   public function find($id)
{
    return $this->repo->find($id);
}

public function findOneByName($answer) {
        return $this->repo->findOneByName($answer);
    }


public function findWithSpecs($id) {
        $entity = $this->repo->find($id);

        if (!$entity) {
            $entity = $this->createNew();
            return array(
                'entity' => $entity,
                'message' => 'Answer not found.',
                'message_type' => 'warning',
                'success' => false,
            );
        } else {
            return array(
                'entity' => $entity,
                'message' => 'Answer found!',
                'message_type' => 'success',
                'success' => true,
            );
        }
    }

    
    
    private function validateForCreate($answer) {
        if (count($this->findOneByName($answer)) > 0) {
            return array('message' => 'Answer Name already exists!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }
        return;
    }

//----------------------------------------------------------
    private function validateForUpdate($entity) {
        $question = $this->findOneByName($entity->getName());

        if ($question && $question->getId() != $entity->getId()) {
            return array('message' => 'Answer Name already exists!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }
        return;
    }
    
   public  function getAnswerById($id) {
        $answer=$this->repo->find($id);
        return $answer;
    }

}