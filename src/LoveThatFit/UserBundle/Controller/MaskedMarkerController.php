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
        
    }
    
    
    //--------------------------Save User Marker in database if exists then update if not then add-------------------------------
    public function saveUserMarker(Request $request)
    {
        
        
    }
    
    //-------------------------Add New User Marker in database----------------------------------------------
    
    public function addUserMarker()
    {
        
    }
    
    //-------------------------update user Marker----------------------------------------------
    public function updateUserMarker()
    {
        
    }
    
    
    
    
    

}

?>