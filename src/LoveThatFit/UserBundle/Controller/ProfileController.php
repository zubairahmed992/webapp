<?php

namespace LoveThatFit\UserBundle\Controller;

use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\UserBundle\Entity\Measurement;
use LoveThatFit\UserBundle\Form\Type\ProfileMeasurementType;
use LoveThatFit\UserBundle\Form\Type\ProfileMeasurementMaleType;
use LoveThatFit\UserBundle\Form\Type\ProfileMeasurementFemaleType;
use LoveThatFit\UserBundle\Form\Type\ProfileSettingsType;
use LoveThatFit\UserBundle\Form\Type\UserPasswordReset;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
          }else{
          $measurementForm = $this->createForm(new ProfileMeasurementFemaleType(), $measurement);
          }
       
        return $this->render('LoveThatFitUserBundle:Profile:aboutMe.html.twig', array(
                    'form' => $measurementForm->createView(),
                    'validation_groups' => array('profile_measurement'),
                    'measurement' => $measurement,
                    'entity'=>$entity,
                    
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
          }else{
          $measurementForm = $this->createForm(new ProfileMeasurementFemaleType(), $measurement);
          }
        
        $measurementForm->bind($this->getRequest());       
        if($measurementForm->isValid())
        {        
        $measurement->setUpdatedAt(new \DateTime('now')); 
        $em->persist($measurement);
        $em->flush();
        $this->get('session')->setFlash('success', 'Your measurement information has been saved.');
        }       
         return $this->render('LoveThatFitUserBundle:Profile:aboutMe.html.twig', array(
                    'form' => $measurementForm->createView(),
                    'measurement' => $measurement,   
                     'entity'=>$entity,
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
        
        if ($userForm->isValid())
        {
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
                } 
                else
                {
                $this->get('session')->setFlash('Warning', 'Confirm pass doesnt match');
                }
           }
           else 
              {
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
    
    
    public function _whatILikeAction()
    {       
      $em = $this->getDoctrine()->getManager();
      $user=$this->get('security.context')->getToken()->getUser();
      $question = $em->getRepository('LoveThatFitAdminBundle:SurveyQuestion')->find(1);
      $answer = $em->getRepository('LoveThatFitAdminBundle:SurveyAnswer')->find(1);
      //$userServey = $em->getRepository('LoveThatFitAdminBundle:SurveyUser')->findBy(array('question'=>$question));
      

        $query = $em->createQuery(
            'SELECT us, q FROM LoveThatFitAdminBundle:SurveyQuestion q 
                JOIN q.SurveyUser us
                WHERE q.id > :qid'                
        )->setParameter('qid', 1);

        $userServey  = $query->getResult();      
      
            
      return new Response($userServey->getSurvey());
    }
    
    
    public function whatILikeAction()
    {       
        return $this->render('LoveThatFitUserBundle:Profile:whatILike.html.twig', array(
                    'data' =>  $this->getQuestionsList(), 
                    'form'=>$this->addUserSurveyForm()->createView(),                    
                    'userid'=>$this->get('security.context')->getToken()->getUser(),
                    'count_question'=>count($this->getQuestionsList()),
                    'answer'=>  $this->getAnswersList(),
                    
        ));
    }
    public function _submitUserSurveyFormAction(Request $request)
    {
      $em = $this->getDoctrine()->getManager();
      $user=$this->get('security.context')->getToken()->getUser();
      $data = $request->request->all();
      $str='';
      //return new Response(json_encode($data));
      foreach ($data as $key => $value) {
        $question = $em->getRepository('LoveThatFitAdminBundle:SurveyQuestion')->find($key);
        $answer = $em->getRepository('LoveThatFitAdminBundle:SurveyAnswer')->find($value);
        $userServey = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:SurveyUser')->findby(array('question'=>$question));
        return new Response('hey');
        return new Response($userServey->getSurvey());
        if($userServey){
        $userSurvey->setAnswer($answer);
        $em = $this->getDoctrine()->getManager();
        $em->persist($userSurvey);
        $em->flush();
        }else{              
        $userSurvey = new SurveyUser();        
        $userSurvey->setQuestion($question);
        $userSurvey->setAnswer($answer);
        $userSurvey->setUser($user);
        $userSurvey->setSurvey('Question Answer Survey');
        $em = $this->getDoctrine()->getManager();
        $em->persist($userSurvey);
        $em->flush();              
          }
//$userSurvey = new SurveyUser(); 
                    
          
       }
       
       return new Response('yuppee');
       //return $this->redirect($this->generateUrl('user_profile_what_i_like'));
    } 
    
    public function submitUserSurveyFormAction(Request $request)
    {
      $em = $this->getDoctrine()->getManager();
      $user=$this->get('security.context')->getToken()->getUser();
      $data = $request->request->all();
      $str='';
      foreach ($data as $key => $value) {
         $answer = $em->getRepository('LoveThatFitAdminBundle:SurveyAnswer')->find($value);
         $str = $str.','.$answer->getQuestion()->getId();
         $strs=explode(',',$str);
         foreach($strs as $questionId)
         {   
          $userSurvey = new SurveyUser(); 
          $question = $this->getquestionById($questionId);          
          $answers=  $this->getAnswerById($value);
         }
          $addanswer =$this->updateAnswerIfFound($question,$answers,$user);
       }
       return $this->redirect($this->generateUrl('user_profile_what_i_like'));
    }
    
    
    
    
    //SizeChart Form Azeem
    
    public function SizeChartUserRegistrationFormAction()
    {        
        
        $top_fitting_size_chart_id=  $this->getBrandByTop($target='Top');        
        $bottom_fitting_size_chart_id=$this->getBrandByBottom($target='Bottom');
        $dress_fitting_size_chart_id =$this->getBrandByDresses($target='dress');
        $top_fittings_size_chart_id=  $this->getSizeByTop($target='Top');
        $bottom_fittings_size_chart_id=$this->getSizeByBottom($target='Bottom');
        $dress_fittings_size_chart_id =$this->getSizeByDresses($target='dress');
        
        $form = $this->createFormBuilder()
                ->add(
                'Brand', 'choice', 
                array('choices'=>$top_fitting_size_chart_id,
                       'multiple'  =>False,
                       'expanded'  => False, 
                ))
                ->add(
                'Brand1', 'choice', 
                array('choices'=>$bottom_fitting_size_chart_id,
                       'multiple'  =>False,
                       'expanded'  => False, 
                ))
                ->add(
                'Brand2', 'choice', 
                array('choices'=>$dress_fitting_size_chart_id,
                       'multiple'  =>False,
                       'expanded'  => False, 
                ))                
                ->add(
                'sizetop', 'choice', 
                array('choices'=>$top_fittings_size_chart_id,
                       'multiple'  =>False,
                       'expanded'  => False, 
                ))
                ->add(
                'sizebottom', 'choice', 
                array('choices'=>$bottom_fittings_size_chart_id,
                       'multiple'  =>False,
                       'expanded'  => False, 
                ))
                ->add(
                'sizedress', 'choice', 
                array('choices'=>$dress_fittings_size_chart_id,
                       'multiple'  =>False,
                       'expanded'  => False, 
                ))
                ->getForm();       
        return $this->render('LoveThatFitUserBundle:Profile:sizechart.html.twig', array(
                    'form' => $form->createView()));
    }
    
    
    
    private function getBrandByTop($target='Top')
    {
        $em = $this->getDoctrine()->getManager();        
        $brand = $em->getRepository('LoveThatFitAdminBundle:SizeChart')->findBrandByTop($target);        
        return $brand;
    }
    
    private function getBrandByBottom($target='Bottom')
    {
        $em = $this->getDoctrine()->getManager();        
        $brand = $em->getRepository('LoveThatFitAdminBundle:SizeChart')->findBrandByBottom($target);
        
        return $brand;
    }
    
    private function getBrandByDresses($target='dress')
    {
        $em = $this->getDoctrine()->getManager();        
        $brand = $em->getRepository('LoveThatFitAdminBundle:SizeChart')->findBrandByBottom($target);
        return $brand;
    }
    
    
    private function getSizeByTop($target='Top')
    {
        $em = $this->getDoctrine()->getManager();        
        $size = $em->getRepository('LoveThatFitAdminBundle:SizeChart')->findSizeByTop($target);
        return $size;
    }
    
    private function getSizeByBottom($target='Bottom')
    {
        $em = $this->getDoctrine()->getManager();        
        $size = $em->getRepository('LoveThatFitAdminBundle:SizeChart')->findSizeByBottom($target);
        return $size;
    }
    
    private function getSizeByDresses($target='dress')
    {
        $em = $this->getDoctrine()->getManager();        
        $size = $em->getRepository('LoveThatFitAdminBundle:SizeChart')->findSizeByBottom($target);
        return $size;
    }
    
    
    
    
    
    private function updateAnswerIfFound($question,$answers,$user) {
        $result = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:SurveyUser')->findby(array('question'=>$question,'user'=>$user));
        $count_result=count($result);      
       
        if($count_result>0)
        {
            return $this->updateSurveyUserAnswer($question,$answers,$user);         
        }
        else
        {
            return $this->addSurveyUserAnswer($question,$answers,$user);
        }       
    }    
    private function addSurveyUserAnswer($question,$answers,$user)
    {
        $userSurvey = new SurveyUser();        
        $userSurvey->setQuestion($question);
        $userSurvey->setAnswer($answers);
        $userSurvey->setUser($user);
        $userSurvey->setSurvey('Question Answer Survey');
        $em = $this->getDoctrine()->getManager();
        $em->persist($userSurvey);
        $em->flush();
        $this->get('session')->setFlash('success', 'Answer Added Successfuly');
    }    
    private function updateSurveyUserAnswer()
    {
        $this->get('session')->setFlash('success', 'Answer Update Successfuly');
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
    
    private function getAnswersList() {
        $question = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:SurveyAnswer')->findAll();
        return $question;
    }
    
    private function addUserSurveyForm()
    {
        $builder = $this->createFormBuilder();    
        return $builder->getForm();       
    }
    private function getUserSurveyList()
    {
        $repository = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:SurveyUser');
        return $repository->findAll();
    }
    
    
    
    

}

?>