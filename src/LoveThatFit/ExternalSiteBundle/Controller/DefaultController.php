<?php

namespace LoveThatFit\ExternalSiteBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('LoveThatFitExternalSiteBundle:Default:index.html.twig', array('name' => $name));
    }
    public function proxyFittingRoomAction($access_token,$user_id,$sku)
    {
        
        # get retailer by token
        $retailer=$this->getRetailerBaseToken($access_token);
        if($retailer)
        {
           return new response($this->userCheck($user_id,$sku,$retailer));
            
        }else
        {
            return new response("Retailer not found..");
        }
        
    }
    
    #------------Get Retailer base on  Access Token------------------------------#
    public function getRetailerBaseToken($access_token=null){
       if($this->get('admin.helper.retailer')->getRetailerBaseToken($access_token)){
           return $this->get('admin.helper.retailer')->getRetailerBaseToken($access_token);
       }else{
           return false;
       }
      
    }
    
    public function userCheck($user_id, $sku,$retailer=null) {
       
        if ($user_id == null) {
            return $this->redirect($this->generateUrl('external_login'), 301);
        }
        $site_user = $this->get('admin.helper.retailer.site.user')->findByReferenceId($user_id,$retailer->getId());
        
        if (is_object($site_user)) {            
             $this->get('user.helper.user')->getLoggedInById($site_user->getUser());
            return $this->redirect($this->generateUrl('inner_shopify_index', array('sku' => $sku, 'user_id' => $site_user->getId())), 301);
        } else {
            
            //$retailer = $this->get('admin.helper.retailer')->find(1);
            $this->setNewUserSession($user_id, $sku,$retailer->getId());
            return $this->redirect($this->generateUrl('external_login'), 301);
        }
    }

    //-----------------------------------------
    public function setNewUserSession($site_user_id, $sku,$retailer_id) {
        $session = $this->get("session");
        $session->set('shopify_user', array('site_user_id' => $site_user_id,
            'sku' => $sku,
            'retailer_id'=>$retailer_id
            ));
    }
    
}
