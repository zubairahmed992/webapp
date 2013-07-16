<?php

namespace LoveThatFit\AdminBundle;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Templating\EngineInterface;

class MailHelper {

    protected $mailer;
    protected $templating;
    var $conf;

    public function __construct() {
        
    }
    
    #------------------------Chek Token ------------------------#

    private function checkToken($email, $token) {

        if ($email) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LoveThatFitUserBundle:User')->findOneBy(array('email' => $email));

            if (count($entity) > 0) {

                $getAuthTokenWebService = $entity->getAuthTokenWebService();
                if ($getAuthTokenWebService == $token) {
                   return array('status'=>'True','msg'=>'Success');
            } else {
                return array('status'=>'False','msg'=>'Try Again');
            } 
            } else {
                return json_encode(array('Message' => 'User Not Found'));
            }
        } else {


            $getAuthTokenWebService = $entity->getAuthTokenWebService();
            if ($getAuthTokenWebService == $token) {
                return array('status'=>'True','msg'=>'Success');
            } else {
                return array('status'=>'False','msg'=>'Try Again');
            } 
        }
    }

    

}
