<?php

namespace LoveThatFit\UserBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use LoveThatFit\UserBundle\Form\Type\MeasurementStepFourType;
use LoveThatFit\UserBundle\Form\Type\RegistrationStepFourType;
use LoveThatFit\UserBundle\Form\Type\MeasurementVerticalPositionFormType;
use LoveThatFit\UserBundle\Form\Type\MeasurementHorizantalPositionFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceController extends Controller {

     public function loginAction() {
        
        $security_context  = $this->get('user.helper.user')->getRegistrationSecurityContext($this->getRequest());
       
        return $this->render(
                        'LoveThatFitUserBundle:Device:login.html.twig', array(
                        'last_username' => $security_context['last_username'],
                    'error' => $security_context['error'],
                    
                        )
        );
    }
    
      #--------------------------Registration Step Four For Ipad
   public function _editImageAction( $edit_type = null) {
       $securityContext = $this->container->get('security.context');
       
        if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            $id = $securityContext->getToken()->getUser()->getId();
        } else{
            return $this->redirect($this->generateUrl('device_browser_login'));
        }
       
        $user = $this->get('user.helper.user')->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Unable to find User.');
        }
        $measurement = $user->getMeasurement();
        
        $measurement_vertical_form = $this->createForm(new MeasurementVerticalPositionFormType(), $measurement);
        $measurement_horizontal_form = $this->createForm(new MeasurementHorizantalPositionFormType(), $measurement);
        $form = $this->createForm(new RegistrationStepFourType(), $user);
        $measurement_form = $this->createForm(new MeasurementStepFourType(), $measurement);
        $marker = $this->get('user.marker.helper')->getByUser($user);
        #return new response($marker->getRectHeight());   
        $edit_type=$edit_type==null?'registration':'fitting_room';
        
        return $this->render('LoveThatFitUserBundle:Device:ipad_stepfour.html.twig', array(
                    'form' => $form->createView(),                    
                    'measurement_form' => $measurement_form->createView(),                   
                    'measurement_vertical_form' => $measurement_vertical_form->createView(),
                    'measurement_horizontal_form' => $measurement_horizontal_form->createView(),
                    'entity' => $user,
                    'measurement' => $measurement,
                    'edit_type' => $edit_type,
                    'marker' => $marker,
            ));
   }
 
   
    #---------------------------------------------------------------------------
    
   public function editImageAction($edit_type=null) {
       $securityContext = $this->container->get('security.context');
       
       if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $securityContext->getToken()->getUser();
        } else{
            return $this->redirect($this->generateUrl('device_browser_login'));
        }
        $measurement = $user->getMeasurement();
         $users=$this->get('user.helper.parent.child')->findByUser($user);
        if($users)
        {
        if($users->getIsApproved()=='0')
        {        // Rendering step four
            $form = $this->createForm(new UserParentChildLinkType());
            return $this->render('LoveThatFitUserBundle:Registration:user_parent_child.html.twig', array(
                    'form' => $form->createView(),                    
                    'entity' => $user,      
                    'edit_type' => 'disaprove',
                    'isapproved'=>0,
                ));
        }
        if($users->getIsApproved()==NULL or $users->getIsApproved()==null or $users->getIsApproved()=='' )
        {        // Rendering step four
            $form = $this->createForm(new UserParentChildLinkType());
            return $this->render('LoveThatFitUserBundle:Registration:user_parent_child.html.twig', array(
                    'form' => $form->createView(),                    
                    'entity' => $user,      
                    'edit_type' => 'fitting_room',
                    'isapproved'=>$users->getIsApproved(),
                ));
        }
        
        
        if($users->getIsApproved()==1)
        {        // Rendering step four
          $measurement_vertical_form = $this->createForm(new MeasurementVerticalPositionFormType(), $measurement);
        $measurement_horizontal_form = $this->createForm(new MeasurementHorizantalPositionFormType(), $measurement);
        $form = $this->createForm(new RegistrationStepFourType(), $user);
        $measurement_form = $this->createForm(new MeasurementStepFourType(), $measurement);
        $marker = $this->get('user.marker.helper')->getByUser($user);
        $default_marker = $this->get('user.marker.helper')->getDefaultValuesBaseOnBodyType($user);
       // return new response(json_encode($default_marker));
                
        $edit_type=$edit_type==null?'registration':$edit_type;
        
        return $this->render('LoveThatFitUserBundle:Device:device_view.html.twig', array(
                    'form' => $form->createView(),
                    'form' => $form->createView(),
                        'measurement_form' => $measurement_form->createView(),                   
                    'measurement_vertical_form' => $measurement_vertical_form->createView(),
                    'measurement_horizontal_form' => $measurement_horizontal_form->createView(),
                    'entity' => $user,
                    'measurement' => $measurement,
                    'edit_type' => $edit_type,
                    'marker' => $marker,
                    'default_marker' => $default_marker,
            ));
        }        
        }elseif(!$users  and $user->getAge()<15 and $user->isApproved==NULL){        
            $form = $this->createForm(new UserParentChildLinkType());
            return $this->render('LoveThatFitUserBundle:Registration:user_parent_child.html.twig', array(
                    'form' => $form->createView(),                    
                    'entity' => $user,      
                    'edit_type' => 'registration',
                    'isapproved'=>$user->isApproved,
                ));
        }else
        {        
        $measurement_vertical_form = $this->createForm(new MeasurementVerticalPositionFormType(), $measurement);
        $measurement_horizontal_form = $this->createForm(new MeasurementHorizantalPositionFormType(), $measurement);
        $form = $this->createForm(new RegistrationStepFourType(), $user);
        $measurement_form = $this->createForm(new MeasurementStepFourType(), $measurement);
        $marker = $this->get('user.marker.helper')->getByUser($user);
        $default_marker = $this->get('user.marker.helper')->getDefaultValuesBaseOnBodyType($user);
       // return new response(json_encode($default_marker));
                
        $edit_type=$edit_type==null?'registration':$edit_type;
        
        return $this->render('LoveThatFitUserBundle:Device:device_view.html.twig', array(
                    'form' => $form->createView(),               
                    'measurement_form' => $measurement_form->createView(),                   
                    'measurement_vertical_form' => $measurement_vertical_form->createView(),
                    'measurement_horizontal_form' => $measurement_horizontal_form->createView(),
                    'entity' => $user,
                    'measurement' => $measurement,
                    'edit_type' => $edit_type,
                    'marker' => $marker,
                    'default_marker' => $default_marker,
            ));
    }
}
#----------------------------------------------------------------------------
   public function editImageAuthAction() {
        $handle = fopen('php://input', 'r');
        $jsonInput = fgets($handle);
        $request_array = json_decode($jsonInput, true);
        $token = $request_array['authTokenWebService'];

        $user = $this->get('user.helper.user')->findByAuthToken($token);
        if ($token && $user) {
            $this->get('user.helper.user')->getLoggedIn($user);
            return $this->redirect($this->generateUrl('device_browser_image_edit_web'));
        } else {
            return new Response('Unable to Authenticate.. ');
        }
    }
}

?>