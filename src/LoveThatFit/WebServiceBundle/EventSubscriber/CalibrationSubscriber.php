<?php
namespace LoveThatFit\WebServiceBundle\EventSubscriber;
use Symfony\Component\Yaml\Parser;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use LoveThatFit\WebServiceBundle\Event\CalibrationEvent;

class CalibrationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            CalibrationEvent::NAME => 'onCalibration',
        );
    }

    public function onCalibration(CalibrationEvent $calibrationEvent)
    {
       //Posting data to NODE-JS server
    //Code start 
    $yaml = new Parser();
    //Get API URL & Secret code from param file
    $conf = $yaml->parse(file_get_contents('../app/config/parameters.yml'));
   //Load API URL
   $calibrationPortalAPIURL = isset($conf['parameters']['calibration_portal_api_url'])?trim($conf['parameters']['calibration_portal_api_url']):false;
   //Load API secret
   $calibrationPortalSecret = isset($conf['parameters']['calibration_portal_secret'])?trim($conf['parameters']['calibration_portal_secret']):false;
   
           //Verify both values are set in param file
           //It should be like this
           //calibration_portal_api_url: http://localhost:5000/api/work-flow/add
           //calibration_portal_secret: ulE:8#RpTC&lV^[1OT_4j8sKQL}*U4

        //Validate API URL & Secret
        if($calibrationPortalAPIURL && $calibrationPortalSecret){
          //Getting ready request infromation
           $data = array('api_key' => $calibrationPortalSecret, 'user_id' => $calibrationEvent->getUserID(),'email'=>$calibrationEvent->getEmail(),'status'=> $calibrationEvent->getStatus());  
           //Convert into url request                                                       
            $data_string = http_build_query($data);                                                                                   
            //starting CURL
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL,$calibrationPortalAPIURL);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,
                        $data_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));


            // receive server response ...
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $server_output = curl_exec ($ch);

            curl_close ($ch);
            //End CURL execution
        }

    }
}