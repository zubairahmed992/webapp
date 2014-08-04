<?php

namespace LoveThatFit\UserBundle\Controller;

use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\UserBundle\Entity\UserMarker;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use \DateTime;

class MaskedMarkerController extends Controller {

    public function userMarkerAction()
    {
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $this->get('user.helper.user')->find($id);
        $maskMarker=$this->get('user.marker.helper')->findMarkerByUser($user);
        if(count($maskMarker)>0){
        return new Response(json_encode($maskMarker->getMarkerJson()));
        }else
        {
            return new response('not exists');
        }
    }
    
    
    //--------------------------Save User Marker in database if exists then update if not then add-------------------------------
    public function saveUserMarkerAction(Request $request)
    {
        $usermaker=$request->request->all();
        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $this->get('user.helper.user')->find($id);
        $maskMarker=$this->get('user.marker.helper')->findMarkerByUser($user);
        if(count($maskMarker)>0)
        {
            $this->get('user.marker.helper')->updateUserMarker($user,$maskMarker);
            return new response('updated');
        }else
        {             
            return $this->get('user.marker.helper')->saveUserMarker($user,$usermaker);
            return new response('added');
        }
        
    }
    
    
    
    
    
    

}

?>