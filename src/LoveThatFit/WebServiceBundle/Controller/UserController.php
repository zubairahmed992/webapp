<?php

namespace LoveThatFit\WebServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use LoveThatFit\UserBundle\Entity\User;
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

    public function createRegistrationFormAction() {

        $gender=array(''=>'Select Gender','m'=>'Male','f'=>'Female');
        $form = $this->createFormBuilder()
                ->add('email', 'email')
                ->add('zipcode', 'text')
                ->add('password', 'repeated', array(
            'first_name' => 'password',
            'second_name' => 'confirm',
            'type' => 'password',
            'invalid_message' => 'The password fields must match.',
        ))
            
                        ->add('gender','choice', 
                        array('choices'=>$gender,
                       'multiple'  =>False,
                       'expanded'  => False, 
                       'required'  => false
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
        $zipcode = $request_array['gender'];
        if ($request->getMethod() == 'POST') {

            if ($this->isDuplicateEmail(Null, $email)) {
                return new Response(json_encode(array('error' => 'The Email already exists',)));
            }
            $entity = new User();

            $entity->setCreatedAt(new \DateTime('now'));
            $entity->setUpdatedAt(new \DateTime('now'));
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($entity);

            $password = $encoder->encodePassword($password, $entity->getSalt());

            $entity->setPassword($password);
            $entity->setEmail($email);
            $entity->setGender($gender);
            $entity->setZipcode($zipcode);

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return new Response(json_encode(array('msg' => 'success')));
        } else {
            return new Response(json_encode(array('error' => 'Try again')));
        }
            
  }
            
           
 
    #---------------------------Render Json--------------------------------------------------------------------#

    private function json_view($rec_count,$entity) {
         if ($rec_count > 0) {
            $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new
                        JsonEncoder()));
            return $serializer->serialize($entity, 'json');
        } else {
            return json_encode(array('msg'=>'Record Not Found'));
        }
    }

 #----------------------------------------------------------------------------------------------#
    
    private function isDuplicateEmail($id, $email) {
        return $this->getDoctrine()->getRepository('LoveThatFitUserBundle:User')->isDuplicateEmail($id, $email);
    }


}// End of Class