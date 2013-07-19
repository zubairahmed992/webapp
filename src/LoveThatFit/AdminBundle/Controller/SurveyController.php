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
                    'data' =>  $this->getQuestionsList(),
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation'=>null,
                    'id'=>null,
                    'count_question'=>count($this->getQuestionsList()),
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
                    'data' =>  $this->getQuestionsList(),
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation'=>null,
                    'id'=>null,
                    'count_question'=>count($this->getQuestionsList()),
        ));
       }
       }else
       {
         $this->get('session')->setFlash('warning','Please Enter Values Correctly!');
       return $this->render('LoveThatFitAdminBundle:Survey:index.html.twig', array(
                    'data' =>  $this->getQuestionsList(),
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation'=>null,
                    'id'=>null,
                    'count_question'=>count($this->getQuestionsList()),
        ));
       }
    }
    
public function addNewAnswerAction($question_id) {
        $answer = new SurveyAnswer() ;
        $form = $this->getAnswerForm($answer);
        return $this->render('LoveThatFitAdminBundle:Survey:index.html.twig', array(
                    'answerForm' => $form->createView(),
                    'question_id' => $question_id,
                    'data' => $this->getQuestionsList(),                    
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation'=>'AddAnwser',
                    'count_question'=>count($this->getQuestionsList()),
        ));
    }
    public function addAnswerAction(Request $request, $question_id) {
        $em = $this->getDoctrine()->getManager();        
        $answer = new SurveyAnswer();
        $question = $this->getquestionById($question_id);
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
                    'data' => $this->getQuestionsList(),                    
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation'=>'AddAnwser',
                    'count_question'=>count($this->getQuestionsList()),
        ));
        }
        }
        else
        {
           $this->get('session')->setFlash('warning','Answer cannot be creatd');
            return $this->render('LoveThatFitAdminBundle:Survey:index.html.twig', array(
                    'answerForm' => $form->createView(),
                    'question_id' => $question_id,
                    'data' => $this->getQuestionsList(),                    
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation'=>'AddAnwser',
                    'count_question'=>count($this->getQuestionsList()),
        ));
        }
    }

    public function editQuestionAction($question_id) {
        $form = $this->getQuestionFormById($question_id);
        return $this->render('LoveThatFitAdminBundle:Survey:index.html.twig', array(
                    'editForm' => $form->createView(),
                    'id' => $question_id,                   
                    'data' => $this->getQuestionsList(),
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation'=>'editQuestion',
                    'count_question'=>count($this->getQuestionsList()),
        ));
    }

    public function editUpdatesQuestionsAction(Request $request, $question_id) {
        $em = $this->getDoctrine()->getManager();
        $question = $this->getquestionById($question_id);
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
                    'data' => $this->getQuestionsList(),
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation'=>'editQuestion',
                    'count_question'=>count($this->getQuestionsList()),
        ));
        }
       }
       else
        {   
            $this->get('session')->setFlash('warning','Survey Question cannot be update');
            return $this->render('LoveThatFitAdminBundle:Survey:index.html.twig', array(
                    'editForm' => $form->createView(),
                    'id' => $question_id,                   
                    'data' => $this->getQuestionsList(),
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation'=>'editQuestion',
                    'count_question'=>count($this->getQuestionsList()),
        ));
        }       
    }

    public function editAnswerAction($answer_id) {       
        $answer = $this->getAnswerById($answer_id);
        $form = $this->createFormBuilder($answer)
                ->add('answer', 'text')
                ->getForm();
        return $this->render('LoveThatFitAdminBundle:Survey:index.html.twig', array(
                    'editAnswerForm' => $form->createView(),
                    'id' => $answer_id,                    
                    'data' => $this->getQuestionsList(),
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation'=>'editAnswer',
                    'count_question'=>count($this->getQuestionsList()),            
        ));
    }

    public function editUpdateAnswerAction(Request $request, $answer_id) {
        $em = $this->getDoctrine()->getManager();
        $answer = $this->getAnswerById($answer_id);
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
                    'data' => $this->getQuestionsList(),
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation'=>'editAnswer',
                    'count_question'=>count($this->getQuestionsList()),            
        ));
        }
        }else
        {
            $this->get('session')->setFlash('warning','Survey Answer cannot be update');
            return $this->render('LoveThatFitAdminBundle:Survey:index.html.twig', array(
                    'editAnswerForm' => $form->createView(),
                    'id' => $answer_id,                    
                    'data' => $this->getQuestionsList(),
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation'=>'editAnswer',
                    'count_question'=>count($this->getQuestionsList()),            
        ));
        }
        
    }

    public function deleteQuestionAction(Request $request, $question_id) {
        $em = $this->getDoctrine()->getManager();
        $question = $this->getquestionById($question_id);
        if (!$question) {
                throw $this->createNotFoundException('Unable to find Survey Question.');
            }
        $em->remove($question);
        $em->flush();
        $this->get('session')->setFlash('success','Survey Question has been deleted');
        return $this->redirect($this->generateUrl('admin_survey'));
    }

    public function deleteAnswersAction(Request $request, $answer_id) {
        $em = $this->getDoctrine()->getManager();
        $answer = $this->getAnswerById($answer_id);
        if (!$answer) {
                throw $this->createNotFoundException('Unable to find Answer.');
            }
        $em->remove($answer);
        $em->flush();
        $this->get('session')->setFlash('success','Survey Answer has been deleted');
        return $this->redirect($this->generateUrl('admin_survey'));
    }
//---------------------------------------------------------------------------------    
    private function getQuestionsList() {
       $question = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:SurveyQuestion')->findAll();
       $rec_count = count($question);       
       return $question;       
    }

    private function getquestionById($id) {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:SurveyQuestion');
        $question = $repository->find($id);
        return $question;
    }

    private function getAnswerById($id) {
        $repository = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:SurveyAnswer');
        $answer = $repository->find($id);
        return $answer;
    }

    private function getAddNewQuestionForm() {
        $question = new SurveyQuestion();
        return $this->getQuestionForm($question);
    }

    private function getQuestionFormById($question_id) {
        $question = $this->getquestionById($question_id);
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

