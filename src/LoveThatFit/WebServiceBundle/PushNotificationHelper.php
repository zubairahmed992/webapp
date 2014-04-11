<?php

namespace LoveThatFit\WebServiceBundle;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\AdminBundle\Event\ProductEvent;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\UserBundle\Entity\User;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\UserBundle\Entity\PushNotification;


class PushNotificationHelper{
   
     /**
     * Holds the Symfony2 event dispatcher service
     */
    protected $dispatcher;

    /**
     * Holds the Doctrine entity manager for database interaction
     * @var EntityManager 
     */
    protected $em;

    /**
     * Entity-specific repo, useful for finding entities, for example
     * @var EntityRepository
     */
    protected $repo;

    /**
     * The Fully-Qualified Class Name for our entity
     * @var string
     */
    protected $class;
    private $container;

    protected $conf;
    var $msgs = array();
    
     public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, Container $container) {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
        $conf_yml = new Parser();
        $this->conf = $conf_yml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
        
    }
 #-----------Delete Notification --------------------------------------#
     public function delete($id) {
        $entity = $this->repo->find($id);
        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();

            return array(
                'message_type' => 'success',
                'success' => true,
            );
        } else {

            return array(
                'message' => 'Product not found!',
                'success' => false,
            );
        }
    }
#---------------Find All Record---------------------------------------#
public function findAll(){
return $this->repo->findAll();
}
   //-------------------------------------------------------

    public function savePushNotification(PushNotification $PushNotification) {
      $PushNotification->setUpdatedAt(new \DateTime('now'));   
      $PushNotification->setCreatedAt(new \DateTime('now'));
       
       $this->em->persist($PushNotification);
       $this->em->flush();
    }
 
    public function sendPushNotification($deviceToken,$msg){
    $pass = '123456';
   // Get the parameters from http get or from command line

  $message=$msg;
   $badge = 1 ;
  $sound = 'default';
   // Construct the notification payload
  //foreach($msg as $messages=>$key){
    //  $message.=$key;
 // }
   $body = array();
   $body['aps'] = array('alert' => $message);

  if ($badge)
     $body['aps']['badge'] = $badge;
  if ($sound)
     $body['aps']['sound'] = $sound;
  
   /* End of Configurable Items */
   $server = 'developement';
  if($server=='production'){
   $cert= dirname(__FILE__).'/cerpro.pem';
   }else{
   $cert= dirname(__FILE__).'/cert.pem';
   }

   if($server=='production'){ 
      $appleServer='ssl://gateway.push.apple.com:2195';
      $certpem = $cert;
   }
   else{
      $appleServer='ssl://gateway.sandbox.push.apple.com:2195';
      $certpem = $cert;
    }
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $certpem);
        // assume the private key passphase was removed.
         stream_context_set_option($ctx, 'ssl', 'passphrase', $pass);
        $fp = stream_socket_client($appleServer, $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
        $payload = json_encode($body);
        foreach($deviceToken as $token=>$key){
         $msg = chr(0) . pack("n",32) . pack('H*', str_replace(' ', '', $key)) . pack("n",strlen($payload)).$payload;
        fwrite($fp, $msg);
        }
        fclose($fp);
        return "sending message :" . $payload;
    }
 
#-----------Fetch PushNotification type from YML ------------------------------#   
  public function getNotificationType($type){
    
       $allNotification= $this->conf["PushNotification"]["type"];
        foreach($allNotification as $singleNotification=>$key){
           if($singleNotification==$type and $key['status']=="true"){
            return array('status'=>$key['status'],'message'=>$key['message'],'type'=>$singleNotification);
           }
        }
       
  }
  #---------------Save the notification in DB---------------------------#
  public function setNotificationInDB($type, $msg){
        $PushNotification = new PushNotification();
        $PushNotification->setMessage($msg);
        $PushNotification->setNotificationType($type);
        $PushNotification->setUserId(0);
        $PushNotification->setNotificationLimit(0);
        $PushNotification->setIsActive(1);
        $this->savePushNotification($PushNotification);
        return array('msg'=>' Notifcation Saved Successfully');
  }
  
  #--------------Update Active and InActive Push Notification -----------------#
  public function updateStatus($target_array){
      
       $notificationData=$this->repo->findById($target_array['id']);
       $notificationData->setIsActive($target_array['isActive']);
        $this->savePushNotification($notificationData);
        return array('status'=>true,'msg'=>'Status Updated');
  }
  
  #----------Update Msg ----only ----------------------#
  public function updateMsg ($req){
       $notificationData=$this->repo->findById($req['id']);
       
        $notificationData->setMessage($req['msg']);
        $this->savePushNotification($notificationData);
        
        return array('status'=>true,'msg'=>'Message Send');
      
  }
#---------------Update Push Notification Table Base on User Id ----------------#
 public function updatePushNotification ($lastUserId,$notificationId){
    
    
        $notificationData=$this->repo->findById($notificationId);
       
      //  return $this->msgs[]=$lastUserId;
       if($notificationData->getUserId()<$lastUserId){
        $notificationData->setUserId($lastUserId);
        $this->savePushNotification($notificationData);
        return array('status'=>true,'msg'=>'Message Send');
     }else{
         return  array('status'=>false,'msg'=>'End Up','lastUserId'=>$lastUserId);
     }
     
 }
 #---------- Get Max User Id From User ----------------------------------------#
 public function getMaxUserId($userArray){
     $userId=array();
     if($userArray){
     foreach($userArray as $userIds){
          $userId[]=$userIds['UserId'];
     }
     return max($userId);}
     
 }
 
 #----------Find PushNotification Base On User Id------------------------------#
 public function findByUserId($userId){
  return $this->repo->findByUserId($userId);
 }
  #----------Find All active------------------------------#
 public function findAllActive(){
  return $this->repo->findAllActive();
 }
 
 ######################################################
 ####################################################3
#---------------Crone Job Call-----------------------#
 public function getCroneJob(){
  
    $data=$this->readNotification();
  
   if($data['status']==true){
       $data=$this->getCroneJob();
   }//else{
    //  $data= $this->inActiveNotification($this->msgs);
  // }
   return $data;
 }
 
 function readNotification(){      
     
     $notificationData=$this->findAllActive();
     $notifs=array();
     foreach($notificationData as $keyData){
         #$notifs[$keyData->getId()]=array(
         $notifs=array(
             'Id'=>$keyData->getId(),
             'msg'=>$keyData->getMessage(),
             'user_id'=>$keyData->getUserId(),
         );
         
        $data= $this->processNotification($notifs);
     }
     return $data;
    
    
    
 }
 function processNotification($notification){
     
    $userDeviceIdArray=$this->getUserDeviceIdArray(20, $notification['user_id']);
   //  return $userDeviceIdArray;
    #2 send notification
  //  $this->msgs[ $notification['user_id']]="Notification".$notification['user_id'];
    $this->msgs['last_user_id']=$notification['user_id'];
    $sendMsg=$this->sendPushNotification($userDeviceIdArray['deviceType'],$notification['msg']);
     #3 update notif DB
    #---------------------------------Get Max User Id -------------------------#
   #return array($userDeviceIdArray['lastUserId'],$notification['Id']);
    return $this->updatePushNotification($userDeviceIdArray['lastUserId'],$notification['Id']);
 
 }
 function getUserDeviceIdArray($limit, $lastUserId){
     $getUserList= $this->container->get('webservice.helper.user')->getFirstLimtedUserWithDeviceType($limit,$lastUserId);
     $lastUserId=$this->getMaxUserId($getUserList);
     $deviceType=$this->getDeviceNameArray($getUserList);
     return array ('lastUserId'=>$lastUserId,'deviceType'=>$deviceType);
 }
 private function getDeviceNameArray($userDeviceIdArray){
      $deviceType=array();
     foreach($userDeviceIdArray as $devices){
          $deviceType[]=$devices['deviceName'];
     }
     return $deviceType;
 }
 public function inActiveNotification($lastUserId=0){
    $msg=array();
    $notifcationData=$this->findByUserId($lastUserId['last_user_id']);
     foreach($notifcationData as $delNotif){
        $id=$delNotif->getId();
        $msg[]=$this->delete($id);
        
    }
    return $msg;
     
 }
 #-------------------------------------------
 
 
 
}
