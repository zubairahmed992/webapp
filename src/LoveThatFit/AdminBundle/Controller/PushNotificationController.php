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
        $get_all_notification = $this->get('push_notification_helper')->findAll();
        
        return $this->render('LoveThatFitAdminBundle:PushNotification:_index.html.twig',array('get_all_notification'=>$get_all_notification));
    }
#---------------------Send Push Notifcation -----------------------------------#
    public function sendPushNotifcationAction(Request $request){
         $target_array = $request->request->all();
       
         if(($target_array['msg']!=Null)){
         $res=$this->get('push_notification_helper')->updateMsg( $target_array );    
            if($res['status']==true){
                $this->get('push_notification_helper')->updateMsg( $target_array );    
            }
         }
         $this->get('push_notification_helper')->getCroneJob();
       return new response("Msg send successfully ..!");  
    }
#---- Actvie  and InActive the Notification ---------------------------------#
    public function statusUpdateAction(Request $request){
        $target_array=$request->request->all();
        $response=$this->get('push_notification_helper')->updateStatus($target_array);
       return new response(json_encode($response));
       
    }
#----------------------- Delete Notifcation --------------------------------#
    public function deletePushNotificationAction(Request $request){
        $target_array=$request->request->all();
        $response=$this->get('push_notification_helper')->delete($target_array['id']);
       return new response(json_encode($response));
        
        
    }

}