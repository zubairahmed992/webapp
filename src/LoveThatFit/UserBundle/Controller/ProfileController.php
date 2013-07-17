<?php

namespace LoveThatFit\UserBundle\Controller;

use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\UserBundle\Entity\Measurement;
use LoveThatFit\UserBundle\Form\Type\ProfileMeasurementType;
use LoveThatFit\UserBundle\Form\Type\ProfileMeasurementMaleType;
use LoveThatFit\UserBundle\Form\Type\ProfileMeasurementFemaleType;
use LoveThatFit\UserBundle\Form\Type\ProfileSettingsType;
use LoveThatFit\UserBundle\Form\Type\SizeChartMeasurementType;
use LoveThatFit\UserBundle\Form\Type\UserPasswordReset;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use LoveThatFit\AdminBundle\Entity\SurveyQuestion;
use LoveThatFit\AdminBundle\Entity\SurveyAnswer;
use LoveThatFit\AdminBundle\Entity\SurveyUser;
use LoveThatFit\AdminBundle\Entity\Brand;
use LoveThatFit\AdminBundle\Entity\SizeChart;

class ProfileController extends Controller {

    public function aboutMeAction() {

        $id = $this->get('security.context')->getToken()->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        $measurement = $entity->getMeasurement();



        if ($entity->getGender() == 'm') {
            $measurementForm = $this->createForm(new ProfileMeasurementMaleType(), $measurement);
        } else {
            $measurementForm = $this->createForm(new ProfileMeasurementFemaleType(), $measurement);
        }

        $brandSizeChartForm = $this->createForm(new SizeChartMeasurementType($this->getBrandArray('Top'), $this->getBrandArray('Bottom'), $this->getBrandArray('Dress')), $measurement);

        return $this->render('LoveThatFitUserBundle:Profile:aboutMe.html.twig', array(
                    'form' => $measurementForm->createView(),
                    'validation_groups' => array('profile_measurement'),
                    'measurement' => $measurement,
                    'entity' => $entity,
                    'sizechartsizeform' => $brandSizeChartForm->createView(),
                ));
    }

    //-------------------------------------------------------------
    public function aboutMeUpdateAction() {

        $id = $this->get('security.context')->getToken()->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        $measurement = $entity->getMeasurement();

        // $measurementForm = $this->createForm(new ProfileMeasurementType(), $measurement);
        if ($entity->getGender() == 'm') {
            $measurementForm = $this->createForm(new ProfileMeasurementMaleType(), $measurement);
        }
        if ($entity->getGender() == 'f') {
            $measurementForm = $this->createForm(new ProfileMeasurementFemaleType(), $measurement);
        }

        $measurementForm->bind($this->getRequest());
        $measurement->setUpdatedAt(new \DateTime('now'));
        $em->persist($measurement);
        $em->flush();
        $this->get('session')->setFlash('success', 'Your measurement information has been saved.');
      $brandSizeChartForm = $this->createForm(new SizeChartMeasurementType($this->getBrandArray('Top'), $this->getBrandArray('Bottom'), $this->getBrandArray('Dress')), $measurement);

        return $this->render('LoveThatFitUserBundle:Profile:aboutMe.html.twig', array(
                    'form' => $measurementForm->createView(),
                    'measurement' => $measurement,
                    'entity' => $entity,
                              'sizechartsizeform' => $brandSizeChartForm->createView(),
  
                ));
    }

    //-------------------------------------------------------------

    public function accountSettingsAction() {

        $id = $this->get('security.context')->getToken()->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        $userForm = $this->createForm(new ProfileSettingsType(), $entity);
        $passwordResetForm = $this->createForm(new UserPasswordReset(), $entity);

        return $this->render('LoveThatFitUserBundle:Profile:profileSettings.html.twig', array(
                    'form' => $userForm->createView(),
                    'entity' => $entity,
                    'form_password_reset' => $passwordResetForm->createView()
                ));
    }

    //-------------------------------------------------------------

    public function accountSettingsUpdateAction() {

        $id = $this->get('security.context')->getToken()->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        $userForm = $this->createForm(new ProfileSettingsType(), $entity);
        $userForm->bind($this->getRequest());

        if ($userForm->isValid()) {
            $getAvatar = $entity->getAvatar();
            $entity->uploadAvatar();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->setFlash('Success', 'Profile has been updated.');
        }
        $passwordResetForm = $this->createForm(new UserPasswordReset(), $entity);
        return $this->render('LoveThatFitUserBundle:Profile:profileSettings.html.twig', array(
                    'form' => $userForm->createView(),
                    'entity' => $entity,
                    'form_password_reset' => $passwordResetForm->createView()
                ));
    }

//-------------------------------------------------------------------------


    public function passwordResetUpdateAction(Request $request) {

        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        $user_old_password = $entity->getPassword();
        $salt_value_old = $entity->getSalt();

        $userForm = $this->createForm(new UserPasswordReset(), $entity);
        $userForm->bind($request);
        $data = $userForm->getData();

        $oldpassword = $data->getOldpassword();

        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($entity);
        $password_old_enc = $encoder->encodePassword($oldpassword, $salt_value_old);

        if ($user_old_password == $password_old_enc) {

            $em->persist($entity);
            $em->flush();

            if ($userForm->isValid()) {

                $data = $userForm->getData();
                $password = $data->getPassword();
                $salt_value = $entity->getSalt();
                $entity->setUpdatedAt(new \DateTime('now'));
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($entity);
                $password = $encoder->encodePassword($password, $salt_value);
                $entity->setPassword($password);
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
                $this->get('session')->setFlash('Success', 'Password Updated Successfully');
            } else {
                $this->get('session')->setFlash('Warning', 'Confirm pass doesnt match');
            }
        } else {
            $this->get('session')->setFlash('Warning', 'Please Enter Correct Password');
        }
        $userForms = $this->createForm(new ProfileSettingsType(), $entity);
        $passwordResetForm = $this->createForm(new UserPasswordReset(), $entity);

        return $this->render('LoveThatFitUserBundle:Profile:profileSettings.html.twig', array(
                    'form' => $userForms->createView(),
                    'entity' => $entity,
                    'form_password_reset' => $passwordResetForm->createView()
                ));
    }

    //--------------------------- What I like --------------------------
    public function whatILikeAction() {
        return $this->render('LoveThatFitUserBundle:Profile:whatILike.html.twig', array(
                    'data' => $this->getQuestionsList(),
                    'form' => $this->addUserSurveyForm()->createView(),
                    'userid' => $this->get('security.context')->getToken()->getUser(),
                    'count_question' => count($this->getQuestionsList()),
                ));
    }

    //----------------------------------------------------------------------------
    public function submitUserSurveyFormAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $data = $request->request->all();
        $str = '';
        foreach ($data as $key => $value) {
            $answer = $em->getRepository('LoveThatFitAdminBundle:SurveyAnswer')->find($value);
            $str = $str . ',' . $answer->getQuestion()->getId();
            $strs = explode(',', $str);
            foreach ($strs as $questionId) {
                $userSurvey = new SurveyUser();
                $question = $this->getquestionById($questionId);
                $answers = $this->getAnswerById($value);
            }
            $addanswer = $this->updateAnswerIfFound($question, $answers, $user);
        }
        return $this->redirect($this->generateUrl('user_profile_what_i_like'));
    }

    //----------------------------------------------------------------------------

    private function updateAnswerIfFound($question, $answers, $user) {
        $result = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:SurveyUser')->findby(array('question' => $question, 'user' => $user));
        $count_result = count($result);
        if ($count_result > 0) {

            return $this->updateSurveyUserAnswer($question, $answers, $user);
        } else {
            return $this->addSurveyUserAnswer($question, $answers, $user);
        }
    }

    private function addSurveyUserAnswer($question, $answers, $user) {
        $userSurvey = new SurveyUser();
        $userSurvey->setQuestion($question);
        $userSurvey->setAnswer($answers);
        $userSurvey->setUser($user);
        $userSurvey->setSurvey('Question Answer Survey');
        $em = $this->getDoctrine()->getManager();
        $em->persist($userSurvey);
        $em->flush();
        $this->get('session')->setFlash('success', 'Success! Answers Updated Successfully');
    }

    private function updateSurveyUserAnswer($question, $answers, $user) {
        $em = $this->getDoctrine()->getEntityManager();
        $surveyUser = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:SurveyUser')->findby(array('question' => $question, 'user' => $user));
        foreach ($surveyUser as $userSurvey) {
            $surveyId = $userSurvey->getId();
            $surveyUserId = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:SurveyUser')->find($surveyId);
        }
        $surveyUserId->setQuestion($question);
        $surveyUserId->setAnswer($answers);
        $surveyUserId->setUser($user);
        $surveyUserId->setSurvey('Question Answer Survey');
        $em->persist($surveyUserId);
        $em->flush();
        $this->get('session')->setFlash('success', 'Success! Answers Updated Successfully');
    }
    
    
    //----------------------Profile Size Chart Sizes----------------Azeem----------
    
    public function profileSizeChartSizesAction()
    {
        $id = $this->get('security.context')->getToken()->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);
        
        $measurement = $entity->getMeasurement(); 
       
        if ($entity->getGender() == 'm') {
            $brandSizeChartForm = $this->createForm(new SizeChartMeasurementType($this->getBrandArray('Top'), $this->getBrandArray('Bottom')), $measurement);
        } else {
            $brandSizeChartForm = $this->createForm(new SizeChartMeasurementType($this->getBrandArray('Top'), $this->getBrandArray('Bottom'), $this->getBrandArray('Dress')), $measurement);
        }
        $brandSizeChartForm->bind($this->getRequest());
        $request_array = $this->getRequest()->get('brand_size_chart');
        if ($entity->getGender() == 'm') {

            if (array_key_exists('top_size', $request_array)) {
                 $measurement->top_size = $request_array['top_size'];                                
            }
            if (array_key_exists('bottom_size', $request_array)) {
                $measurement->bottom_size = $request_array['bottom_size'];
            }
        } else {
            if (array_key_exists('top_size', $request_array)) {
                $measurement->top_size = $request_array['top_size'];
            }
            if (array_key_exists('bottom_size', $request_array)) {
                $measurement->bottom_size = $request_array['bottom_size'];
            }
            if (array_key_exists('dress_size', $request_array)) {
                $measurement->dress_size = $request_array['dress_size'];
            }
        }
        
        return new Response(json_encode($measurement));
    }

    
    public function userTryProductsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findTryPropductHistory();
        return $this->render('LoveThatFitUserBundle:Profile:user_product_history.html.twig',array('product'=>$entity));
    }






    private function getQuestionsList() {
        $question = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:SurveyQuestion')->findAll();
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

    private function addUserSurveyForm() {
        $builder = $this->createFormBuilder();
        return $builder->getForm();
    }

    //------------------------------------------------------------------------
    //methods will be moved somewhere on refactoring ------------------------------
    //------------------------------------------------------------------------

    private function getBrandArray($target) {

        $brands = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:SizeChart')
                ->getBrandsByTarget($target);

        $brands_array = array();
        foreach ($brands as $i) {
            $brands_array[$i['id']] = $i['name'];
        }
        return $brands_array;
    }

}

?>