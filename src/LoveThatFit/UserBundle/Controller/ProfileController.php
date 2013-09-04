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
use Symfony\Component\Form\FormError;
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
        
        $entity = $this->get('user.helper.user')->find($id);
        $measurement = $entity->getMeasurement();

        if ($entity->getGender() == 'm') {
            $measurementForm = $this->createForm(new ProfileMeasurementMaleType(), $measurement);
        } else {
            $measurementForm = $this->createForm(new ProfileMeasurementFemaleType(), $measurement);
        }
        
        return $this->render('LoveThatFitUserBundle:Profile:aboutMe.html.twig', array(
                    'form' => $measurementForm->createView(),
                    'validation_groups' => array('profile_measurement'),
                    'measurement' => $measurement,
                    'entity' => $entity,
                ));
    }

    //-------------------------------------------------------------
    public function aboutMeUpdateAction() {

        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $entity = $this->get('user.helper.user')->find($id);
        $measurement = $entity->getMeasurement();

        if ($entity->getGender() == 'm') {
            $measurementForm = $this->createForm(new ProfileMeasurementMaleType(), $measurement);
        }
        if ($entity->getGender() == 'f') {
            $measurementForm = $this->createForm(new ProfileMeasurementFemaleType(), $measurement);
        }

        $measurementForm->bind($this->getRequest());
        $measurement->setUpdatedAt(new \DateTime('now'));

        $this->get('user.helper.measurement')->saveMeasurement($measurement);
        $this->get('session')->setFlash('success', 'Your measurement information has been saved.');
        return $this->render('LoveThatFitUserBundle:Profile:aboutMe.html.twig', array(
                    'form' => $measurementForm->createView(),
                    'measurement' => $measurement,
                    'entity' => $entity,
                ));
    }

    //-------------------------------------------------------------

    public function accountSettingsAction() {

        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $entity = $this->get('user.helper.user')->find($id);
        
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
        $entity=$this->get('user.helper.user')->find($id);
        
        $userForm = $this->createForm(new ProfileSettingsType(), $entity);
        $userForm->bind($this->getRequest());

        if ($userForm->isValid()) {
            $this->get('user.helper.user')->updateProfile($entity);
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
        $user_helper = $this->get('user.helper.user');
         
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
                //$factory = $this->get('security.encoder_factory');
                //$encoder = $factory->getEncoder($entity);
                //$password = $encoder->encodePassword($password, $salt_value);
                $password= $user_helper->encodePassword($entity);
                 
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

    
    public function _passwordResetUpdateAction(Request $request) {

        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $user=$this->get('user.helper.user')->find($id);
        $userForm = $this->createForm(new UserPasswordReset(), $user);
        $userForm->bind($request);
        $data = $userForm->getData();
        $oldPassword = $data->getOldpassword();
        $newPassword = $data->getPassword();
        
        $response_array=$this->get('user.helper.user')->resetPassword($user, $data);
        $user=$response_array['entity'];
        return new Response(var_dump($response_array['header'].':  '.$response_array['message']));
        $this->get('session')->setFlash($response_array['header'], $response_array['message']);
        //$this->get('session')->setFlash('Success', 'Profile has been updated.');
        $userForms = $this->createForm(new ProfileSettingsType(), $user);
        $passwordResetForm = $this->createForm(new UserPasswordReset(), $user);

        return $this->render('LoveThatFitUserBundle:Profile:profileSettings.html.twig', array(
                    'form' => $userForms->createView(),
                    'entity' => $user,
                    'form_password_reset' => $passwordResetForm->createView()
                ));
    }


    public function userTryProductsAction($page_number = 0, $limit = 0)
    {
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();        
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findTryProductHistory($user_id, $page_number, $limit);
        return $this->render('LoveThatFitUserBundle:Profile:user_product_history.html.twig',array('productItem'=>$entity));
    }

    
    //--------------------------- What I like --------------------------
    public function whatILikeAction() {
        return $this->render('LoveThatFitUserBundle:Profile:whatILike.html.twig', array(
                    'data' =>$this->get('admin.helper.surveyquestion')->getQuestionsList(),
                    'form' => $this->addUserSurveyForm()->createView(),
                    'userid' => $this->get('security.context')->getToken()->getUser(),
                    'count_question' => count($this->get('admin.helper.surveyquestion')->getQuestionsList()),
                ));
    }

    //----------------------------------------------------------------------------
    public function submitUserSurveyFormAction(Request $request) {        
        $user = $this->get('security.context')->getToken()->getUser();
        $data = $request->request->all();
        $str = '';
        foreach ($data as $key => $value) {            
            $answer = $this->get('admin.helper.surveyanswer')->find($value);
            $str = $str . ',' . $answer->getQuestion()->getId();
            $strs = explode(',', $str);
            foreach ($strs as $questionId) {                
                $question = $this->get('admin.helper.surveyquestion')->getquestionById($questionId);
                $answers = $this->get('admin.helper.surveyanswer')->getAnswerById($value);
            }
           $message_array= $this->get('admin.helper.surveyuser')->updateAnswerIfFound($question, $answers, $user);
        }   
        $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
        return $this->redirect($this->generateUrl('user_profile_what_i_like'));
    }

    //----------------------------------------------------------------------------

    
    private function addUserSurveyForm() {
        $builder = $this->createFormBuilder();
        return $builder->getForm();
    }

}

?>