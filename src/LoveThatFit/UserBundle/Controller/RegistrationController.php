<?php

namespace LoveThatFit\UserBundle\Controller;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\AdminBundle\Entity\SizeChart;
use LoveThatFit\UserBundle\Form\Type\SizeChartType;
use LoveThatFit\UserBundle\Entity\Measurement;
use LoveThatFit\UserBundle\Form\Type\RegistrationType;
use LoveThatFit\UserBundle\Form\Type\UserType;
use LoveThatFit\UserBundle\Form\Type\MeasurementStepFourType;
use LoveThatFit\UserBundle\Form\Type\RegistrationStepTwoType;
use LoveThatFit\UserBundle\Form\Type\RegistrationStepThreeType;
use LoveThatFit\UserBundle\Form\Type\RegistrationStepFourType;
use LoveThatFit\UserBundle\Form\Type\RegistrationMeasurementType;
use LoveThatFit\UserBundle\Form\Type\RegistrationMeasurementMaleType;
use LoveThatFit\UserBundle\Form\Type\RegistrationMeasurementFemaleType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RegistrationController extends Controller {

    //-------------------------------------------------------------------------

    public function testAction() {

        $entity = new User();
        $form = $this->createForm(new RegistrationType(), $entity);
        return $this->render('LoveThatFitUserBundle:Registration:test.html.twig', array(
                    'form' => $form->createView()));
    }

    //-------------------------------------------------------------------------------

    public function sizeChartAction() {

//        $form = $this->getSizeChartForm();
        $size_chart_form = $this->createForm(new SizeChartType($this->getBrandArray('Top'),$this->getBrandArray('Bottom'),$this->getBrandArray('Dress'), $this->getDoctrine()));
        return $this->render('LoveThatFitUserBundle:Registration:_size_chart.html.twig', array(
                    'size_chart_form' => $size_chart_form->createView(),    
            ));
    }

    private function getSizeChartForm() {
        $top_brands_array = $this->getBrandArray('Top');
        $bottom_brands_array = $this->getBrandArray('Bottom');
        $dress_brands_array = $this->getBrandArray('Dress');

        $defaultData = array('message' => 'Type your message here');
        $form = $this->createFormBuilder($defaultData)
                ->add('Top', 'choice', array('choices' => $top_brands_array, 'required' => false))
                ->add('bottom', 'choice', array('choices' => $bottom_brands_array, 'required' => false))
                ->add('dress', 'choice', array('choices' => $dress_brands_array, 'required' => false,))
                ->getForm();
        return $form;
    }

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

    //-------------------------------------------------------------------------------
    public function stepOneAction() {
        $entity = new User();
        $form = $this->createForm(new UserType(), $entity);
        return $this->render('LoveThatFitUserBundle:Registration:stepone.html.twig', array(
                    'form' => $form->createView()));
    }

    //------------------------- Login after registration------------------------------

    public function getLoggedIn($userEntity) {
        $token = new UsernamePasswordToken($userEntity, null, 'secured_area', array('ROLE_USER'));
        $this->get('security.context')->setToken($token);
    }

    //------------------------- methods will likely to be moved somewhere on refactoring make thin controllers------------------------------
    private function isDuplicateUserName($username) {
        return $this->getDoctrine()->getRepository('LoveThatFitUserBundle:User')->isUserNameExist($username);
    }

    private function isDuplicateEmail($id, $email) {
        return $this->getDoctrine()->getRepository('LoveThatFitUserBundle:User')->isDuplicateEmail($id, $email);
    }

//--------------------------STEP-1-----------------------------------------------


    public function stepOneCreateAction() {
        try {
            $entity = new User();
            $form = $this->createForm(new UserType(), $entity);
            $form->bind($this->getRequest());


            if ($form->isValid()) {
                //Duplicate user name check
                if ($this->isDuplicateUserName($entity->getUsername())) {
                    $form->get('username')->addError(new FormError('User name already taken'));

                    return $this->render('LoveThatFitUserBundle:Registration:stepone.html.twig', array(
                                'form' => $form->createView(),
                                'entity' => $entity));
                } else {

                    $entity->setCreatedAt(new \DateTime('now'));
                    $entity->setUpdatedAt(new \DateTime('now'));

                    $factory = $this->get('security.encoder_factory');
                    $encoder = $factory->getEncoder($entity);
                    $password = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
                    $entity->setPassword($password);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($entity);
                    $em->flush();

                    // This is because the two fields (height & weight) added/moved from measurement to this step
                    $measurement = new Measurement();
                    $measurement->setUser($entity);

                    //Login after registration, the rest of the steps are secured for logged In users access only
                    $this->getLoggedIn($entity);

                    $form = $this->createForm(new RegistrationStepTwoType(), $entity);
                    return $this->render('LoveThatFitUserBundle:Registration:steptwo.html.twig', array(
                                'form' => $form->createView(),
                                'entity' => $entity));
                }
            } else {

                return $this->render('LoveThatFitUserBundle:Registration:stepone.html.twig', array(
                            'form' => $form->createView(),
                            'entity' => $entity));
            }
        } catch (\Doctrine\DBAL\DBALException $e) {

            $form->addError(new FormError('Something went wrong.'));
            return $this->render('LoveThatFitUserBundle:Registration:stepone.html.twig', array(
                        'form' => $form->createView(),
                        'entity' => $entity));
        }
    }

    //--------------------------STEP-2-----------------------------------------------

    public function stepTwoCreateAction(Request $request, $id) {

        // ID should be taken from the current user, not by passing in params !!!!
        // !!!!!!!!!!!!!!! AS
        //$id = $this->get('security.context')->getToken()->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User.');
        }

        // check for duplicate email ------------------------
        $postData = $request->request->get('user');

        $exists = $this->isDuplicateEmail($id, $postData['email']);

        if ($exists) {

            $form = $this->createForm(new RegistrationStepTwoType(), $entity);
            $form->bind($request);
            $form->get('email')->addError(new FormError('Email already exists'));
            return $this->render('LoveThatFitUserBundle:Registration:steptwo.html.twig', array(
                        'form' => $form->createView(),
                        'entity' => $entity));
        }
        //////////////////////////////////////////

        $measurement = $entity->getMeasurement();
        if (!$measurement) {
            $measurement = new Measurement();
            $measurement->setUser($entity);
        }

        $measurement->setWeight($postData['measurement']['weight']);
        $measurement->setHeight($postData['measurement']['height']);
        $em->persist($measurement);
        $em->flush();

        $form = $this->createForm(new RegistrationStepTwoType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            //send registration email ....            
            $this->get('mail_helper')->sendRegistrationEmail($entity);

            $form = $this->createForm(new RegistrationStepFourType(), $entity);
            $measurement_form = $this->createForm(new MeasurementStepFourType(), $measurement);
            return $this->render('LoveThatFitUserBundle:Registration:stepfour.html.twig', array(
                        'form' => $form->createView(),
                        'entity' => $entity,
                        'measurement_form' => $measurement_form->createView(),
                        'measurement' => $measurement,
                    ));
        } else {
            return $this->render('LoveThatFitUserBundle:Registration:steptwo.html.twig', array(
                        'form' => $form->createView(),
                        'entity' => $entity));
        }
    }

    //--------------------------STEP-3-----------------------------------------------

    public function stepThreeCreateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        $measurement = $user->getMeasurement();

        if (!$measurement) {
            $measurement = new Measurement();
            $measurement->setUser($user);
        }

        $form = $this->createForm(new RegistrationStepThreeType(), $measurement);
        $form->bind($request);
        if ($form->isValid()) {

            $measurement->setCreatedAt(new \DateTime('now'));
            $measurement->setUpdatedAt(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($measurement);
            $em->flush();
            //!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Change For Demo
            //-------------------------------------------------------------
            //$form = $this->createForm(new RegistrationStepFourType(), $user);
            //return $this->render('LoveThatFitUserBundle:Registration:stepfour.html.twig', array(
            //          'form' => $form->createView(),
            //        'entity' => $user));
            return $this->redirect($this->generateUrl('inner_site_index'));
        } else {
            return $this->render('LoveThatFitUserBundle:Registration:stepthree.html.twig', array(
                        'form' => $form->createView(),
                        'entity' => $user));
        }
    }

//--------------------------STEP-4-----------------------------------------------
    public function stepFourCreateAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User.');
        }

        $form = $this->createForm(new RegistrationStepFourType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {

            $entity->upload();

            $em->persist($entity);
            $em->flush();

            $response = new Response(json_encode(array(
                                'entity' => $entity,
                                'imageurl' => $entity->getWebPath()
                            )));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            $response = "Invalid image data";
            return new Response($response);
        }
    }

    //---------------------------------Step-4 Update (image Cropping & resizing)
    public function stepFourImageUpdateAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $id = $_POST['id'];
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User.');
        }

        $data = substr($_POST['imageData'], strpos($_POST['imageData'], ",") + 1);
        $decodedData = base64_decode($data);
        $fp = fopen($entity->getAbsolutePath(), 'wb');
        fwrite($fp, $decodedData);
        fclose($fp);
        $response = "true";
        return new Response($response);
    }

    //------------------------Step-2 Edit ----------------
    public function stepTwoEditAction() {
        $id = $this->get('security.context')->getToken()->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        $form = $this->createForm(new UserType(), $entity);
        $form = $this->createForm(new RegistrationStepTwoType(), $entity);

        return $this->render('LoveThatFitUserBundle:Registration:steptwo.html.twig', array(
                    'form' => $form->createView(),
                    'entity' => $entity));
    }

//------------------------Step-3 Edit ----------------
    public function stepThreeEditAction() {

        $id = $this->get('security.context')->getToken()->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        $measurement = $user->getMeasurement();

        if (!$measurement) {
            $measurement = new Measurement();
            $measurement->setUser($user);

            $em->persist($measurement);
            $em->flush();
            $measurement = $user->getMeasurement();
        }


        $form = $this->createForm(new RegistrationStepThreeType(), $measurement);
        return $this->render('LoveThatFitUserBundle:Registration:stepthree.html.twig', array(
                    'form' => $form->createView(),
                    'entity' => $user));
    }

    //------------------------Step-4 Edit ----------------

    public function stepFourEditAction() {

        $id = $this->get('security.context')->getToken()->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User.');
        }

        $measurement = $this->getMeasurement($entity);

        $form = $this->createForm(new RegistrationStepFourType(), $entity);
        $measurement_form = $this->createForm(new MeasurementStepFourType(), $measurement);

        return $this->render('LoveThatFitUserBundle:Registration:stepfour.html.twig', array(
                    'form' => $form->createView(),
                    'form' => $form->createView(),
                    'measurement_form' => $measurement_form->createView(),
                    'entity' => $entity,
                    'measurement' => $measurement,
                ));
    }

    //------------------------Step-4 Measurement Update----------------

    public function stepFourMeasurementUpdateAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        $measurement = $this->getMeasurement($user);

        $form = $this->createForm(new MeasurementStepFourType(), $measurement);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($measurement);
            $em->flush();
            return new Response('Measurement Updated');
        } else {
            return new Response('Something went wrong..');
        }
    }

    //------------------- Reset Password ----------------------------

    public function passwordResetFormAction() {

        $id = $this->get('security.context')->getToken()->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        $defaultData = array('message' => 'Enter your email address');
        $form = $this->createFormBuilder($defaultData)
                ->add('password', 'repeated', array(
                    'first_name' => 'password',
                    'second_name' => 'confirm',
                    'type' => 'password',
                    'invalid_message' => 'The password fields must match.',
                ))
                ->getForm();
        return $this->render('LoveThatFitUserBundle:Registration:passwordResetForm.html.twig', array(
                    'form' => $form->createView(), 'entity' => $entity));
    }

    //---------------------------------------------------------------------------------
    public function passwordUpdateAction(Request $request, $id) {

        $defaultData = array('message' => 'Update password');
        $form = $this->createFormBuilder($defaultData)
                ->add('password', 'repeated', array(
                    'first_name' => 'password',
                    'second_name' => 'confirm',
                    'type' => 'password',
                    'invalid_message' => 'The password fields must match.',
                ))
                ->getForm();

        try {

            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Authentication expired or link not found.');
            }
            $form->bind($request);

            if ($form->isValid()) {
                $data = $form->getData();

                $entity->setUpdatedAt(new \DateTime('now'));

                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($entity);

                $password = $encoder->encodePassword($data['password'], $entity->getSalt());

                $entity->setPassword($password);

                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->render('LoveThatFitUserBundle:Registration:message.html.twig', array(
                            'message' => 'You password has been changed.'));
            } else {

                return $this->render('LoveThatFitUserBundle:Registration:passwordResetForm.html.twig', array(
                            'form' => $form->createView()));
            }
        } catch (\Doctrine\DBAL\DBALException $e) {

            $form->addError(new FormError('Something went wrong.'));
            return $this->render('LoveThatFitUserBundle:Registration:passwordResetForm.html.twig', array(
                        'form' => $form->createView()));
        }
    }

    //------------------------------ Get Measurement Entity -----------------------

    private function getMeasurement($user) {
        $measurement = $user->getMeasurement();

        if (!$measurement) {
            $measurement = new Measurement();
            $measurement->setUser($user);
        }
        return $measurement;
    }

//-------------------------------------------------------------------------
    //------------------------- New Registration Process ------------------------------------------------
    //-------------------------------------------------------------------------


    public function registrationAction() {
        $entity = new User();
        $form = $this->createForm(new RegistrationType(), $entity);
        return $this->render('LoveThatFitUserBundle:Registration:registration.html.twig', array(
                    'form' => $form->createView()));
    }

    //-------------------------------------------------------------------------

    public function registrationCreateAction() {
        try {
            $entity = new User();
            $form = $this->createForm(new RegistrationType(), $entity);
            $form->bind($this->getRequest());

            if ($this->isDuplicateEmail(Null, $entity->getEmail())) {
                $form->get('email')->addError(new FormError('This email has already taken.'));
            }


            if ($form->isValid()) {

                $entity->setCreatedAt(new \DateTime('now'));
                $entity->setUpdatedAt(new \DateTime('now'));

                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($entity);
                $password = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
                $entity->setPassword($password);

                $em = $this->getDoctrine()->getManager();

                $em->persist($entity);
                $em->flush();

                // Adding Measurement record for the user for the later usage
                $measurement = new Measurement();
                $measurement->setUser($entity);
                $em->persist($measurement);
                $em->flush();

                //Login after registration, the rest of the steps are secured for logged In users access only
                $this->getLoggedIn($entity);

                //send registration email ....            
                $this->get('mail_helper')->sendRegistrationEmail($entity);

                if ($entity->getGender() == 'm') {
                    $registrationMeasurementform = $this->createForm(new RegistrationMeasurementMaleType(), $measurement);
                } else {
                    $registrationMeasurementform = $this->createForm(new RegistrationMeasurementFemaleType(), $measurement);
                }


                return $this->render('LoveThatFitUserBundle:Registration:_measurement.html.twig', array(
                            'form' => $registrationMeasurementform->createView(),
                            'measurement' => $measurement,
                            'entity' => $entity));
            } else {

                return $this->render('LoveThatFitUserBundle:Registration:registration.html.twig', array(
                            'form' => $form->createView(),
                            'entity' => $entity));
            }
        } catch (\Doctrine\DBAL\DBALException $e) {

            $form->addError(new FormError('Some thing went wrong'));

            return $this->render('LoveThatFitUserBundle:Registration:registration.html.twig', array(
                        'form' => $form->createView(),
                        'entity' => $entity));
        }
    }

    public function measurementCreateAction() {

        $id = $this->get('security.context')->getToken()->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);

        $measurement = $entity->getMeasurement();

        // $registrationMeasurementform = $this->createForm(new RegistrationMeasurementType(), $measurement);

        if ($entity->getGender() == 'm') {
            $registrationMeasurementform = $this->createForm(new RegistrationMeasurementMaleType($this->getBrandArray('Top'),$this->getBrandArray('Bottom'),$this->getBrandArray('Dress')), $measurement);
        } else {
            $registrationMeasurementform = $this->createForm(new RegistrationMeasurementFemaleType(), $measurement);
        }
        $registrationMeasurementform->bind($this->getRequest());

        $em->persist($measurement);
        $em->flush();

        // Rendering step four
        $form = $this->createForm(new RegistrationStepFourType(), $entity);
        $measurement_form = $this->createForm(new MeasurementStepFourType(), $measurement);

        return $this->render('LoveThatFitUserBundle:Registration:stepfour.html.twig', array(
                    'form' => $form->createView(),
                    'measurement_form' => $measurement_form->createView(),
                    'entity' => $entity,
                    'measurement' => $measurement,
                ));
    }

    public function renderThisAction() {

        $id = $this->get('security.context')->getToken()->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->find($id);
        $measurement = $entity->getMeasurement();
//return new Response(var_dump($this->getBrandArray('Top')));        
        if ($entity->getGender() == 'm') {
            $registrationMeasurementform = $this->createForm(new RegistrationMeasurementMaleType($this->getBrandArray('Top'),$this->getBrandArray('Bottom'),$this->getBrandArray('Dress')), $measurement);
        } else {
            $registrationMeasurementform = $this->createForm(new RegistrationMeasurementFemaleType(), $measurement);
        }


        return $this->render('LoveThatFitUserBundle:Registration:_measurement.html.twig', array(
                    'form' => $registrationMeasurementform->createView(),
                    'measurement' => $measurement,
                    'entity' => $entity));
    }

}

?>