<?php

namespace LoveThatFit\WebServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\UserBundle\Entity\Measurement;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use LoveThatFit\AdminBundle\Entity\Brand;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ClothingType;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use LoveThatFit\UserBundle\Form\Type\RegistrationType;

class UserController extends Controller {
#--------------------------------Login ----------------------------------------------------------------#
    #---------------------Login Service---------------------------------------------------------#
    
     public function createFormAction(Request $request) {

        $defaultData = array('message' => 'Enter your email address');
        $form = $this->createFormBuilder($defaultData)
                ->add('email', 'text')
                ->add('password', 'password')
                ->getForm();
        return $this->render('LoveThatFitWebServiceBundle::loginForm.html.twig', array(
                    'form' => $form->createView()));
    }

    public function loginAction() {
          $request = $this->get('request');
           
        if ($request->getMethod() == 'POST'){
           // $form->bindRequest($request);
             $email = $request->request->get('email');
            $password=$request->request->get('password');
            //$data = $request->getData();
           // $email = $data['email'];
            //$password = $data['password'];
            
            $em = $this->getDoctrine()->getManager();
            $entity =$em->getRepository('LoveThatFitUserBundle:User')->findOneBy(array('email'=>$email));
           
            if (count($entity) >0) {

                $user_db_password = $entity->getPassword();
                $salt_value_db = $entity->getSalt();

                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($entity);
                $password_old_enc = $encoder->encodePassword($password, $salt_value_db);
                if ($user_db_password == $password_old_enc) {
                    $user_id=$entity->getId();
                    $first_name=$entity->getFirstName();
                    $last_name=$entity->getLastName();
                    $gender=$entity->getGender();
                    $zipcode=$entity->getZipcode();
                    $birth_date=$entity->getBirthDate();
                    $image=$entity->getImage();
                    $avatar=$entity->getAvatar();
                   $userinfo=array();
                   $userinfo['id']=$user_id;
                   $userinfo['email']=$email;
                   $userinfo['first_name']=$first_name;
                   $userinfo['last_name']=$last_name;
                   $userinfo['zipcode']=$zipcode;
                   $userinfo['gender']=$gender;
                   if(isset($birth_date)){
                   $userinfo['birth_date']= $birth_date->format('Y-m-d');
                   }
                   $userinfo['image']=$image;
                   $userinfo['avatar']=$avatar;
                   $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath().'/uploads/ltf/users/'.$user_id."/";
                   $userinfo['path']=$baseurl;
                 
                    return new Response(json_encode($userinfo));
                } else {
                     return new Response(json_encode(Null));
                }
            }
           else {
               return new Response(json_encode(Null));
           }  
       }
    }

    
    #------------------------------------------Registration---------------------------------------------#
    public function createRegistrationFormAction() {

        $gender = array('' => 'Select Gender', 'm' => 'Male', 'f' => 'Female');
        $form = $this->createFormBuilder()
                ->add('email', 'email')
                ->add('zipcode', 'text')
                ->add('password', 'repeated', array(
                    'first_name' => 'password',
                    'second_name' => 'confirm',
                    'type' => 'password',
                    'invalid_message' => 'The password fields must match.',
                ))
                ->add('gender', 'choice', array('choices' => $gender,
                    'multiple' => False,
                    'expanded' => False,
                    'required' => false
                ))
                ->getForm();


        return $this->render('LoveThatFitWebServiceBundle::registrationForm.html.twig', array(
                    'form' => $form->createView()));
    }

    public function registrationCreateAction(Request $request) {

        $request_array = $this->getRequest()->get('form');
        $email = $request_array['email'];
        $password = implode($request_array['password']);
        $gender = $request_array['gender'];
        $zipcode = $request_array['zipcode'];
        
        #-------------------Measurement data---------------------#
         if (isset($request_array['weight'])) {
                $weight = $request_array['weight'];
            }

            if (isset($request_array['height'])) {
                $height = $request_array['height'];
            }

            if (isset($request_array['waist'])) {
                $waist = $request_array['waist'];
            }

            if (isset($request_array['hip'])) {
                $hip = $request_array['hip'];
            }

            if (isset($request_array['bust'])) {
                $bust = $request_array['bust'];
            }

            if (isset($request_array['arm'])) {
                $arm = $request_array['arm'];
            }

            if (isset($request_array['neck'])) {
                $neck = $request_array['neck'];
            }

            if (isset($request_array['inseam'])) {
                $inseam = $request_array['inseam'];
            }

            if (isset($request_array['back'])) {
                $back = $request_array['back'];
            }

            if (isset($request_array['shoulder_height'])) {
                $shoulder_height = $request_array['shoulder_height'];
            }

            if (isset($request_array['outseam'])) {
                $outseam = $request_array['outseam'];
            }

            if (isset($request_array['chest'])) {
                $chest = $request_array['chest'];
            }

            if (isset($request_array['sleeve'])) {
                $sleeve = $request_array['sleeve'];
            }


           #-----------------End of Measuremnt data-----------------------# 
        if ($request->getMethod() == 'POST') {

            if ($this->isDuplicateEmail(Null, $email)) {
                return new Response(json_encode(array('error' => 'The Email already exists',)));
            }
            $user = new User();

            $user->setCreatedAt(new \DateTime('now'));
            $user->setUpdatedAt(new \DateTime('now'));
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);

            $password = $encoder->encodePassword($password, $user->getSalt());

            $user->setPassword($password);
            $user->setEmail($email);
            $user->setGender($gender);
            $user->setZipcode($zipcode);
             

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush(); 
       #---------------Set Data of Measuremnt -------------------#
            $measurment = new Measurement();


            $measurment->setUpdatedAt(new \DateTime('now'));

            if (isset($request_array['weight'])) {
                $measurment->setWeight($weight);
            }
            if (isset($request_array['height'])) {
                $measurment->setHeight($height);
            }
            if (isset($request_array['waist'])) {
                $measurment->setWaist($waist);
            }
            if (isset($request_array['hip'])) {
                $measurment->setHip($hip);
            }
            if (isset($request_array['bust'])) {
                $measurment->setBust($bust);
            }
            if (isset($request_array['arm'])) {
                $measurment->setArm($arm);
            }
            if (isset($request_array['neck'])) {
                $measurment->setNeck($neck);
            }
            if (isset($request_array['inseam'])) {
                $measurment->setInseam($inseam);
            }
            if (isset($request_array['back'])) {
                $measurment->setBack($back);
            }
            if (isset($request_array['shoulder_height'])) {
                $measurment->setShoulderHeight($shoulder_height);
            }
            if (isset($request_array['outseam'])) {
                $measurment->setOutseam($outseam);
            }
            if (isset($request_array['chest'])) {
                $measurment->setChest($chest);
            }
            if (isset($request_array['sleeve'])) {
                $measurment->setSleeve($sleeve);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($measurment);
            $em->flush();
  #------------------------End of Seting measuremt----------#          
            
          

            return new Response(json_encode(array('msg' => 'success')));
        } else {
            return new Response(json_encode(array('error' => 'Try again')));
        }
    }

    #-------------------------Measurement-----------------------------------------------------------------------------#       

    public function createMeasurementFormAction() {


        $form = $this->createFormBuilder()
                ->add('neck', 'text')
                ->add('chest', 'text')
                ->add('waist', 'text')
                ->add('inseam', 'text')
                ->getForm();


        return $this->render('LoveThatFitWebServiceBundle::measurementForm.html.twig', array(
                    'form' => $form->createView()));
    }

    public function measurementCreateAction(Request $request) {
        if ($request->getMethod() == 'POST') {

            $request_array = $this->getRequest()->get('form');
            if (isset($request_array['weight'])) {
                $weight = $request_array['weight'];
            }

            if (isset($request_array['height'])) {
                $height = $request_array['height'];
            }

            if (isset($request_array['waist'])) {
                $waist = $request_array['waist'];
            }

            if (isset($request_array['hip'])) {
                $hip = $request_array['hip'];
            }

            if (isset($request_array['bust'])) {
                $bust = $request_array['bust'];
            }

            if (isset($request_array['arm'])) {
                $arm = $request_array['arm'];
            }

            if (isset($request_array['neck'])) {
                $neck = $request_array['neck'];
            }

            if (isset($request_array['inseam'])) {
                $inseam = $request_array['inseam'];
            }

            if (isset($request_array['back'])) {
                $back = $request_array['back'];
            }

            if (isset($request_array['shoulder_height'])) {
                $shoulder_height = $request_array['shoulder_height'];
            }

            if (isset($request_array['outseam'])) {
                $outseam = $request_array['outseam'];
            }

            if (isset($request_array['chest'])) {
                $chest = $request_array['chest'];
            }

            if (isset($request_array['sleeve'])) {
                $sleeve = $request_array['sleeve'];
            }



            $entity = new Measurement();


            $entity->setUpdatedAt(new \DateTime('now'));

            if (isset($request_array['weight'])) {
                $entity->setWeight($weight);
            }
            if (isset($request_array['height'])) {
                $entity->setHeight($height);
            }
            if (isset($request_array['waist'])) {
                $entity->setWaist($waist);
            }
            if (isset($request_array['hip'])) {
                $entity->setHip($hip);
            }
            if (isset($request_array['bust'])) {
                $entity->setBust($bust);
            }
            if (isset($request_array['arm'])) {
                $entity->setArm($arm);
            }
            if (isset($request_array['neck'])) {
                $entity->setNeck($neck);
            }
            if (isset($request_array['inseam'])) {
                $entity->setInseam($inseam);
            }
            if (isset($request_array['back'])) {
                $entity->setBack($back);
            }
            if (isset($request_array['shoulder_height'])) {
                $entity->setShoulderHeight($shoulder_height);
            }
            if (isset($request_array['outseam'])) {
                $entity->setOutseam($outseam);
            }
            if (isset($request_array['chest'])) {
                $entity->setChest($chest);
            }
            if (isset($request_array['sleeve'])) {
                $entity->setSleeve($sleeve);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return new Response(json_encode(array('msg' => 'success')));
        }
    }

    #--------------Change Password--------------------------------------------------------#

    public function changePasswordFormAction() {
        $form = $this->createFormBuilder()
                ->add('email', 'email')
                ->add('old_password', 'password')
                ->add('password', 'repeated', array(
                    'first_name' => 'password', 'label' => 'New Password',
                    'second_name' => 'confirm',
                    'type' => 'password',
                    'invalid_message' => 'The password fields must match.',
                ))
                ->getForm();

        return $this->render('LoveThatFitWebServiceBundle::changePasswordForm.html.twig', array(
                    'form' => $form->createView()));
    }

    #-------------------------------------------------------------------------------------------------------#

    public function changePasswordAction(Request $request) {
        $request_array = $this->getRequest()->get('form');


        if (isset($request_array['email'])) {
            $email = $request_array['email'];
        }
        if (isset($request_array['password'])) {
            $new_password = $request_array['password']['password'];
        }
        if (isset($request_array['old_password'])) {
            $old_password = $request_array['old_password'];
        }


        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitUserBundle:User')->findOneBy(array('email' => $email));

        if (count($entity) > 0) {

            $user_db_password = $entity->getPassword();
            $salt_value_old = $entity->getSalt();

            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($entity);
            $password_old_enc = $encoder->encodePassword($old_password, $salt_value_old);

            if ($password_old_enc == $user_db_password) {

                $entity->setUpdatedAt(new \DateTime('now'));
                $password = $encoder->encodePassword($new_password, $salt_value_old);
                $entity->setPassword($password);
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
                return new response(json_encode(array('Message' => 'Paasword has been updated')));
            } else {
                return new response(json_encode(array('Message' => 'Invalid Password')));
            }
        } else {

            return new response(json_encode(array('Message' => 'Invalid Email')));
        }
    }

    #---------------------------Render Json--------------------------------------------------------------------#

    private function json_view($rec_count, $entity) {
        if ($rec_count > 0) {
            $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
                        JsonEncoder()));
            return $serializer->serialize($entity, 'json');
        } else {
            return json_encode(array('msg' => 'Record Not Found'));
        }
    }

    #----------------------------------------------------------------------------------------------#

    private function isDuplicateEmail($id, $email) {
        return $this->getDoctrine()->getRepository('LoveThatFitUserBundle:User')->isDuplicateEmail($id, $email);
    }

}

// End of Class