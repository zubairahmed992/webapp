<?php

namespace LoveThatFit\AdminBundle\Controller;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ProductColor;
use LoveThatFit\AdminBundle\Entity\ProductSize;
use LoveThatFit\AdminBundle\Entity\ProductItem;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;

class PushNotificationController extends Controller {

//---------------------------------------------------------------------

    public function indexAction() {
        $get_all_notification = $this->get('push_notification_helper')->findAllActive();
        
        return $this->render('LoveThatFitAdminBundle:PushNotification:_index.html.twig',array('get_all_notification'=>$get_all_notification));
    }
#---------------------Send Push Notifcation -----------------------------------#
    public function sendPushNotifcationAction(Request $request){
         $target_array = $request->request->all();
         if($target_array['msg']){
         $res=$this->get('push_notification_helper')->updateMsg( $target_array );    
            if($res['status']==true){
                $this->get('push_notification_helper')->updateMsg( $target_array );    
            }
         }
         
        
    }

}