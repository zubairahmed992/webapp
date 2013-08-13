<?php

namespace LoveThatFit\AdminBundle\Controller;
use LoveThatFit\AdminBundle\Entity\SurveyQuestion;
use LoveThatFit\AdminBundle\Entity\SurveyAnswer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\UserBundle\Entity\UserSurvey;
use LoveThatFit\UserBundle\Entity\User;

class SurveyController extends Controller {
//---------------------------------------------------------------------
    public function indexAction() {
        return $this->render('LoveThatFitAdminBundle:Survey:index.html.twig', array(
                    'data' =>$this->get('admin.helper.surveyquestion')->getQuestionsList(),
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation'=>null,
                    'id'=>null,
                    'count_question'=>count($this->get('admin.helper.surveyquestion')->getQuestionsList()),
        ));
    }    
    public function addNewQuestionAction(Request $request) {
        $question = new SurveyQuestion();
        $form = $this->getQuestionForm($question);        
        $form->bind($request);
        $title=$question->getQuestion();
       if($title!=null)
       {
        if ($form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($question);
        $em->flush();
        $this->get('session')->setFlash('success','Survey Question has been created');
        return $this->redirect($this->generateUrl('admin_survey'));
       }else
       {
         $this->get('session')->setFlash('warning','Survey Question cantnot be created!');
       return $this->render('LoveThatFitAdminBundle:Survey:index.html.twig', array(
                    'data' =>  $this->get('admin.helper.surveyquestion')->getQuestionsList(),
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation'=>null,
                    'id'=>null,
                    'count_question'=>count($this->get('admin.helper.surveyquestion')->getQuestionsList()),
        ));
       }
       }else
       {
         $this->get('session')->setFlash('warning','Please Enter Values Correctly!');
       return $this->render('LoveThatFitAdminBundle:Survey:index.html.twig', array(
                    'data' =>  $this->get('admin.helper.surveyquestion')->getQuestionsList(),
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation'=>null,
                    'id'=>null,
                    'count_question'=>count($this->get('admin.helper.surveyquestion')->getQuestionsList()),
        ));
       }
    }
    
public function addNewAnswerAction($question_id) {
        $answer = new SurveyAnswer() ;
        $form = $this->getAnswerForm($answer);
        return $this->render('LoveThatFitAdminBundle:Survey:index.html.twig', array(
                    'answerForm' => $form->createView(),
                    'question_id' => $question_id,
                    'data' => $this->get('admin.helper.surveyquestion')->getQuestionsList(),                    
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation'=>'AddAnwser',
                    'count_question'=>count($this->get('admin.helper.surveyquestion')->getQuestionsList()),
        ));
    }
    public function addAnswerAction(Request $request, $question_id) {
        $em = $this->getDoctrine()->getManager();        
        $answer = new SurveyAnswer();
        $question = $this->get('admin.helper.surveyquestion')->getquestionById($question_id);
        $answers = $answer->setQuestion($question);
        $form = $this->createFormBuilder($answers)
                ->add('answer', 'text')
                ->getForm();
        $form->bind($request);
        $title=$answer->getAnswer();
        if($title!=null)
        {
        if ($form->isValid()) {
        $em->persist($answer);
        $em->flush();
        $this->get('session')->setFlash('success','Answer has been created');
        return $this->redirect($this->generateUrl('admin_survey'));
        }else
        {
           $this->get('session')->setFlash('warning','Answer cannot be creatd');
            return $this->render('LoveThatFitAdminBundle:Survey:index.html.twig', array(
                    'answerForm' => $form->createView(),
                    'question_id' => $question_id,
                    'data' => $this->get('admin.helper.surveyquestion')->getQuestionsList(),                    
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation'=>'AddAnwser',
                    'count_question'=>count($this->get('admin.helper.surveyquestion')->getQuestionsList()),
        ));
        }
        }
        else
        {
           $this->get('session')->setFlash('warning','Answer cannot be creatd');
            return $this->render('LoveThatFitAdminBundle:Survey:index.html.twig', array(
                    'answerForm' => $form->createView(),
                    'question_id' => $question_id,
                    'data' => $this->get('admin.helper.surveyquestion')->getQuestionsList(),                    
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation'=>'AddAnwser',
                    'count_question'=>count($this->get('admin.helper.surveyquestion')->getQuestionsList()),
        ));
        }
    }

    public function editQuestionAction($question_id) {
        $form = $this->getQuestionFormById($question_id);
        return $this->render('LoveThatFitAdminBundle:Survey:index.html.twig', array(
                    'editForm' => $form->createView(),
                    'id' => $question_id,                   
                    'data' => $this->get('admin.helper.surveyquestion')->getQuestionsList(),
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation'=>'editQuestion',
                    'count_question'=>count($this->get('admin.helper.surveyquestion')->getQuestionsList()),
        ));
    }

    public function editUpdatesQuestionsAction(Request $request, $question_id) {
        $em = $this->getDoctrine()->getManager();
        $question =$this->get('admin.helper.surveyquestion')->getquestionById($question_id);
        $form = $this->getQuestionForm($question);
        $form->bind($request);
         $title=$question->getQuestion();
       if($title!=null)
       {
        if($form->isValid())
        {
        $em->persist($question);
        $em->flush();
        $this->get('session')->setFlash('success','Survey Question has been updated');
        return $this->redirect($this->generateUrl('admin_survey'));
        }else
        {   
            $this->get('session')->setFlash('warning','Survey Question cannot be update');
            return $this->render('LoveThatFitAdminBundle:Survey:index.html.twig', array(
                    'editForm' => $form->createView(),
                    'id' => $question_id,                   
                    'data' => $this->get('admin.helper.surveyquestion')->getQuestionsList(),
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation'=>'editQuestion',
                    'count_question'=>count($this->get('admin.helper.surveyquestion')->getQuestionsList()),
        ));
        }
       }
       else
        {   
            $this->get('session')->setFlash('warning','Survey Question cannot be update');
            return $this->render('LoveThatFitAdminBundle:Survey:index.html.twig', array(
                    'editForm' => $form->createView(),
                    'id' => $question_id,                   
                    'data' => $this->get('admin.helper.surveyquestion')->getQuestionsList(),
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation'=>'editQuestion',
                    'count_question'=>count($this->get('admin.helper.surveyquestion')->getQuestionsList()),
        ));
        }       
    }
    public function editAnswerAction($answer_id) {       
        $answer = $this->get('admin.helper.surveyquestionanswer')->getAnswerById($answer_id);
        $form = $this->createFormBuilder($answer)
                ->add('answer', 'text')
                ->getForm();
        return $this->render('LoveThatFitAdminBundle:Survey:index.html.twig', array(
                    'editAnswerForm' => $form->createView(),
                    'id' => $answer_id,                    
                    'data' => $this->get('admin.helper.surveyquestion')->getQuestionsList(),
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation'=>'editAnswer',
                    'count_question'=>count($this->get('admin.helper.surveyquestion')->getQuestionsList()),            
        ));
    }

    public function editUpdateAnswerAction(Request $request, $answer_id) {
        $em = $this->getDoctrine()->getManager();
        $answer = $this->get('admin.helper.surveyquestionanswer')->getAnswerById($answer_id);
        $form = $this->createFormBuilder($answer)
                ->add('answer', 'text',array('label'=>' '))
                ->getForm();
        $form->bind($request);
       $title=$answer->getAnswer();
        if($title!=null)
        {
        if($form->isValid())
        {
        $em->persist($answer);
        $em->flush();
        $this->get('session')->setFlash('success','Survey Answer has been updated');
        return $this->redirect($this->generateUrl('admin_survey'));
        }else
        {
            $this->get('session')->setFlash('warning','Survey Answer cannot be update');
            return $this->render('LoveThatFitAdminBundle:Survey:index.html.twig', array(
                    'editAnswerForm' => $form->createView(),
                    'id' => $answer_id,                    
                    'data' => $this->get('admin.helper.surveyquestion')->getQuestionsList(),
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation'=>'editAnswer',
                    'count_question'=>count($this->get('admin.helper.surveyquestion')->getQuestionsList()),            
        ));
        }
        }else
        {
            $this->get('session')->setFlash('warning','Survey Answer cannot be update');
            return $this->render('LoveThatFitAdminBundle:Survey:index.html.twig', array(
                    'editAnswerForm' => $form->createView(),
                    'id' => $answer_id,                    
                    'data' => $this->get('admin.helper.surveyquestion')->getQuestionsList(),
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation'=>'editAnswer',
                    'count_question'=>count($this->get('admin.helper.surveyquestion')->getQuestionsList()),            
        ));
        }
        
    }

    public function deleteQuestionAction(Request $request, $question_id) {
        try {

            $message_array = $this->get('admin.helper.surveyquestion')->delete($question_id);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);

            return $this->redirect($this->generateUrl('admin_survey'));
        } catch (\Doctrine\DBAL\DBALException $e) {

            $this->get('session')->setFlash('warning', 'Survey Question has been deleted!');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }

    public function deleteAnswersAction(Request $request, $answer_id) {
        try {

            $message_array = $this->get('admin.helper.surveyanswer')->delete($answer_id);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);

            return $this->redirect($this->generateUrl('admin_survey'));
        } catch (\Doctrine\DBAL\DBALException $e) {

            $this->get('session')->setFlash('warning', 'Survey Answer has been deleted!');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }
//---------------------------------------------------------------------------------    
   private function getAddNewQuestionForm() {
        $question = new SurveyQuestion();
        return $this->getQuestionForm($question);
    }

    private function getQuestionFormById($question_id) {
        $question = $this->get('admin.helper.surveyquestion')->getquestionById($question_id);
        return $this->geteditQuestionForm($question);
    }

    private function getQuestionForm($question) {
        return $this->createFormBuilder($question)
                        ->add('question', 'text', array('label' =>' '))
                        ->add('questionstatus', 'hidden', array('data' => '1',))
                        ->getForm();
    }
    
    private function geteditQuestionForm($question)
    {
        return $this->createFormBuilder($question)
                        ->add('question', 'text', array('label' =>' '))
                        ->add('questionstatus', 'checkbox',array('label' =>' ','required'  => false))
                        ->getForm();
    }
    
    private function getAnswerForm($answer) {
        $answer = new SurveyAnswer();
        return $this->createFormBuilder($answer)
                        ->add('answer', 'text',array('label'=>' '))
                        ->getForm();
    }
    
    
    

}

