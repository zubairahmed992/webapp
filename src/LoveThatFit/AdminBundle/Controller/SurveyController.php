<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Form\Type\SurveyQuestionType;
use LoveThatFit\AdminBundle\Form\Type\SurveyAnswerType;

class SurveyController extends Controller {

//---------------------------------------------------------------------
    public function indexAction($page_number, $sort = 'id') {
        $size_with_pagination = $this->get('admin.helper.surveyquestion')->getListWithPagination($page_number, $sort);
        return $this->render('LoveThatFitAdminBundle:Survey:index.html.twig', array(
                    'survey' => $size_with_pagination,
                    'operation' => null,
                    'id' => null,
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),));
    }

    public function addNewQuestionAction(Request $request) {
        $question = $this->get('admin.helper.surveyquestion')->createNewQuestion();
        $form = $this->createForm(new SurveyQuestionType('add'), $question);
        $form->bind($request);
        if ($form->isValid()) {
            $message_array = $this->get('admin.helper.surveyquestion')->saveQuestion($question);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            if ($message_array['success']) {
                return $this->redirect($this->generateUrl('admin_survey'));
            } else {
                $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
                return $this->redirect($this->generateUrl('admin_survey'));
            }
        }
    }

    public function addNewAnswerAction($question_id) {
        $size_with_pagination = $this->get('admin.helper.surveyquestion')->getListWithPagination($page_number = 1, $sort = 'id');
        $answer = $this->get('admin.helper.surveyanswer')->createNewAnswer();
        $form = $this->createForm(new SurveyAnswerType('add'), $answer);
        return $this->render('LoveThatFitAdminBundle:Survey:index.html.twig', array(
                    'answerForm' => $form->createView(),
                    'question_id' => $question_id,
                    'survey' => $size_with_pagination,
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation' => 'AddAnwser',
        ));
    }

    public function addAnswerAction(Request $request, $question_id) {
        $answer = $this->get('admin.helper.surveyanswer')->createNewAnswer();
        $form = $this->createForm(new SurveyAnswerType('add'), $answer);
        $question = $this->get('admin.helper.surveyquestion')->getquestionById($question_id);
        $answer->setQuestion($question);
        $form->bind($request);
        if ($form->isValid()) {
            $message_array = $this->get('admin.helper.surveyanswer')->saveAnswer($answer);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            if ($message_array['success']) {
                return $this->redirect($this->generateUrl('admin_survey'));
            } else {
                $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
                return $this->redirect($this->generateUrl('admin_survey'));
            }
        }
    }

    public function editQuestionAction($question_id) {
        $specs = $this->get('admin.helper.surveyquestion')->findWithSpecs($question_id);
        $entity = $specs['entity'];
        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
        }
        $form = $this->createForm(new SurveyQuestionType('edit'), $entity);
        return $this->render('LoveThatFitAdminBundle:Survey:index.html.twig', array(
                    'editForm' => $form->createView(),
                    'id' => $question_id,
                    'survey' => $this->get('admin.helper.surveyquestion')->getListWithPagination($page_number = 1, $sort = 'id'),
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation' => 'editQuestion',
        ));
    }

    public function editUpdatesQuestionsAction(Request $request, $question_id) {
        $specs = $this->get('admin.helper.surveyquestion')->findWithSpecs($question_id);
        $question = $specs['entity'];
        $form = $this->createForm(new SurveyQuestionType('edit'), $question);
        $form->bind($request);
        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
            return $this->redirect($this->generateUrl('admin_survey'));
        }
        if ($form->isValid()) {
            $message_array = $this->get('admin.helper.surveyquestion')->updateQuestion($question);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            if ($message_array['success']) {
                return $this->redirect($this->generateUrl('admin_survey'));
            } else {
                $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
                return $this->redirect($this->generateUrl('admin_survey'));
            }
        }
    }

    public function editAnswerAction($answer_id) {
        $specs = $this->get('admin.helper.surveyanswer')->findWithSpecs($answer_id);
        $entity = $specs['entity'];
        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
        }
        $form = $this->createForm(new SurveyAnswerType('edit'), $entity);
        return $this->render('LoveThatFitAdminBundle:Survey:index.html.twig', array(
                    'editAnswerForm' => $form->createView(),
                    'id' => $answer_id,
                    'survey' => $this->get('admin.helper.surveyquestion')->getListWithPagination($page_number = 1, $sort = 'id'),
                    'addNewForm' => $this->getAddNewQuestionForm()->createView(),
                    'operation' => 'editAnswer',
        ));
    }

    public function editUpdateAnswerAction(Request $request, $answer_id) {
        $specs = $this->get('admin.helper.surveyanswer')->findWithSpecs($answer_id);
        $entity = $specs['entity'];
        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
        }
        $form = $this->createForm(new SurveyAnswerType('edit'), $entity);
        $form->bind($request);
        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
            return $this->redirect($this->generateUrl('admin_survey'));
        }
        if ($form->isValid()) {
            $message_array = $this->get('admin.helper.surveyanswer')->updateAnswer($entity);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            if ($message_array['success']) {
                return $this->redirect($this->generateUrl('admin_survey'));
            } else {
                $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
                return $this->redirect($this->generateUrl('admin_survey'));
            }
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
        $question = $this->get('admin.helper.surveyquestion')->createNewQuestion();
        return $form = $this->createForm(new SurveyQuestionType('add'), $question);
    }

}

