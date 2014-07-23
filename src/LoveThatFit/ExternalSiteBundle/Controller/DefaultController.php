<?php

namespace LoveThatFit\ExternalSiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('LoveThatFitExternalSiteBundle:Default:index.html.twig', array('name' => $name));
    }
    public function proxyFittingRoomAction($token, $user_id, $sku)
    {
        # get retailer by token
        # check user against retailer & Reference User Id
        # check product available against sku + retailer
    }
    
    public function userCheck($user_id, $sku) {
       
        if ($user_id == null) {
            return $this->redirect($this->generateUrl('external_login'), 301);
        }
        
        $site_user = $this->get('admin.helper.retailer.site.user')->findByReferenceId($user_id);
        
        if (is_object($site_user)) {            
          /*
           * Dont need to chek this here, deal with it in fitting room::::
           * 
            $itemBySku = $this->get('admin.helper.productitem')->findItemBySku($sku);           
            if ($itemBySku == null || empty($itemBySku)|| !isset($itemBySku)){
             return new response("Product not found"); 
            }
            */
             $this->get('user.helper.user')->getLoggedInById($site_user->getUser());
            return $this->redirect($this->generateUrl('inner_shopify_index', array('sku' => $sku, 'user_id' => $site_user->getId())), 301);
        } else {
            
            //$retailer = $this->get('admin.helper.retailer')->find(1);
            $this->setNewUserSession($user_id, $sku);
            return $this->redirect($this->generateUrl('external_login'), 301);
        }
    }

    //-----------------------------------------
    public function setNewUserSession($site_user_id, $sku) {
        $session = $this->get("session");
        $session->set('shopify_user', array('site_user_id' => $site_user_id,
            'sku' => $sku));
    }
    
}
