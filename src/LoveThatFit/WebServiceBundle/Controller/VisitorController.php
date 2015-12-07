<?php
namespace LoveThatFit\WebServiceBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\SiteBundle\Entity\Visitor;

class VisitorController extends Controller {


    public function indexAction() {
        return $this->render('LoveThatFitWebServiceBundle:Visitor:email.html.twig');
        $v = new Visitor();
        $v->setEmail('sfdsfdsfd');
        $v->setBrowser($_SERVER['HTTP_USER_AGENT']);
        $v->setDevice('sfdsfdsfd');
        $v->setIpAddress('sfdsfdsfd');
        $v->setCountry('sfdsfdsfd');
        $v->setCreatedAt(new \DateTime('now'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($v);
        $em->flush();
        #return new response(json_encode(get_browser(null, true)));
        return new response(json_encode($_SERVER['HTTP_USER_AGENT']));      
    }
    #---------------------------------------------------
  public function saveInfoAction() {
      
        $decoded = $this->getRequest()->request->all();
        $v = new Visitor();
        $v->setEmail($decoded['email']);
        $v->setBrowser($_SERVER['HTTP_USER_AGENT']);
        #$v->setDevice('sfdsfdsfd');
        $v->setIpAddress($this->get_client_ip());
        #$v->setCountry('sfdsfdsfd');
        $v->setCreatedAt(new \DateTime('now'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($v);
        $em->flush();
        return new response(json_encode('save visitor info'));      
    }    
    
    function _get_client_ip() {
    $ipaddress = '';
    if ($_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if($_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if($_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if($_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if($_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

// Function to get the client IP address
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
    
}

