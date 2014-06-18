<?php

namespace LoveThatFit\ShopifyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class FittingRoomController extends Controller
{
    #   temp-code = '8d9e6d19e48e3e002af09b19ea016081' 
    #   $api_key = '1464612a653803d72f4d6d998e59d785';
    #    $shared_secret = '497d438714a4fc95f688850fc476caa5';
    #    $shop_domain = 'lovethatfit-2.myshopify.com';    
    #    $access_token = '019860835bfd8ac1c5259702c5b953a9';
#Non embedded app    
#----------------------------------------------------------
    #    $api_key = '584e96437b334b01028908e63a602204';
    #    $shared_secret = '0d694e4a176838a97fa10d3dd81491dd';
    #    $shop_domain = 'lovethatfit-2.myshopify.com';    
    #    $access_token = '8b14eb6efcf7c5fa7b0c76e9b329d06e';
    
    public function showAction()
    {
        return new Response('Hooaa!');
       return $this->render('LoveThatFitShopifyBundle:FittingRoom:fitting_room.html.twig');
    }
    
}
