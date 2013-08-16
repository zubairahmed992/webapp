<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\AdminBundle\Event\productEvent;

class SurveyUserHelper {

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
        $surveyuser = new $class();
        return $surveyuser;
    }

#------------------------------------------------------

    public function find($id) {
        return $this->repo->find($id);
    }

public  function updateAnswerIfFound($question, $answers, $user) {
    
        $result = $this->repo->findby(array('question' => $question, 'user' => $user));
        $count_result = count($result);
        if ($count_result > 0) {

            return $this->updateSurveyUserAnswer($question, $answers, $user);
        } else {
            return $this->addSurveyUserAnswer($question, $answers, $user);
        }
    }
    
private function addSurveyUserAnswer($question, $answers, $user) {        
        $class = $this->class;
        $userSurvey = new $class();        
        $userSurvey->setQuestion($question);
        $userSurvey->setAnswer($answers);
        $userSurvey->setUser($user);
        $userSurvey->setSurvey('Question Answer Survey');
        $this->em->persist($userSurvey);
            $this->em->flush();
            return array('message' => 'Success! Answers Added Successfully.',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );        
    }

    private function updateSurveyUserAnswer($question, $answers, $user) {
        $surveyUser = $this->repo->findby(array('question' => $question, 'user' => $user));
        foreach ($surveyUser as $userSurvey) {
            $surveyId = $userSurvey->getId();
            $surveyUserId = $this->repo->find($surveyId);
        }
        $surveyUserId->setQuestion($question);
        $surveyUserId->setAnswer($answers);
        $surveyUserId->setUser($user);
        $surveyUserId->setSurvey('Question Answer Survey');
        $this->em->persist($surveyUserId);
            $this->em->flush();
        return array('message' => 'Success! Answers Updated Successfully.',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            ); 
        
    }      
    
    

}