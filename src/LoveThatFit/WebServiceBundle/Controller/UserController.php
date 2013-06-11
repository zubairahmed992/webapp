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
        $zipcode = $request_array['zipcode'];
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
 
  public function measurementCreateAction(Request $request)
  {
       if ($request->getMethod() == 'POST') {
           
           $request_array = $this->getRequest()->get('form');
           if(isset($request_array['weight']))
           { $weight = $request_array['weight'];  }    
           
           if(isset($request_array['height']))
           { $height = $request_array['height'];  }    
           
           if(isset($request_array['waist']))
           { $waist = $request_array['waist'];  }    
           
           if(isset($request_array['hip']))
           { $hip = $request_array['hip'];  }    
           
           if(isset($request_array['bust']))
           { $bust = $request_array['bust'];  }    
           
           if(isset($request_array['arm']))
           { $arm = $request_array['arm'];  }    
           
           if(isset($request_array['neck']))
           { $neck = $request_array['neck'];  }    
           
           if(isset($request_array['inseam']))
           { $inseam = $request_array['inseam'];  }    
           
           if(isset($request_array['back']))
           { $back = $request_array['back'];  }    
           
           if(isset($request_array['shoulder_height']))
           { $shoulder_height = $request_array['shoulder_height'];  }    
           
           if(isset($request_array['outseam']))
           { $outseam = $request_array['outseam'];  }    
           
           if(isset($request_array['chest']))
           { $chest = $request_array['chest'];  }    
           
           if(isset($request_array['sleeve']))
           { $sleeve = $request_array['sleeve'];  }    
           
           
          
           $entity = new Measurement();
          
            
            $entity->setUpdatedAt(new \DateTime('now'));  
            
             $entity->setWeight($weight);
             $entity->setHeight($height);
             $entity->setWaist($waist);
             $entity->setHip($hip);
             $entity->setBust($bust);
             $entity->setArm($arm);
             $entity->setNeck($neck);
             $entity->setInseam($inseam);
             $entity->setBack($back);
             $entity->setShoulderHeight($shoulder_height);
             $entity->setOutseam($outseam);
             $entity->setChest($chest);
             $entity->setSleeve($sleeve);
             
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return new Response(json_encode(array('msg' => 'success')));
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