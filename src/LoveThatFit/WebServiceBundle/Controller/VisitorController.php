<?php

namespace LoveThatFit\WebServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\SiteBundle\Entity\Visitor;
use LoveThatFit\UserBundle\Form\Type\RegistrationType;

class VisitorController extends Controller {

    public function indexAction() {
        return $this->render('LoveThatFitWebServiceBundle:Visitor:email.html.twig');
    }

//--------------- Login Eror Set ------------------------------
    public function registrationAction() {

        $request = $this->getRequest();
        $security_context = $this->get('user.helper.user')->getRegistrationSecurityContext($this->getRequest());

        $referer = $request->headers->get('referer');
        $url_bits = explode('/', $referer);
        $security_context['referer'] = $url_bits[sizeof($url_bits) - 1];

        $routeName = $request->get('_route');


        if (array_key_exists('error', $security_context) and $security_context['error']) {
            $security_context['referer'] = "login";
        }
        if($routeName=='login'){
            $security_context['referer'] = "login";
        }

        $user = $this->get('user.helper.user')->createNewUser();
        $form = $this->createForm(new RegistrationType(), $user);

        $twitter_helper = $this->get('twitter_helper');

        $twitters = array();
        $twitters = $twitter_helper->twitter_latest();

        return $this->render('LoveThatFitWebServiceBundle:Visitor:register.html.twig', array(
            'form' => $form->createView(),
            'last_username' => $security_context['last_username'],
            'error' => $security_context['error'],
            'referer' => $security_context['referer'],
            'twitters' => $twitters,
        ));
    }
    #---------------------------------------------------

    public function registerAction() {
        return $this->render('LoveThatFitWebServiceBundle:Visitor:register.html.twig');
    }

    #---------------------------------------------------

    public function saveInfoAction() {
        $decoded = $this->getRequest()->request->all();
        $v = new Visitor();
        $v->setEmail($decoded['email']);
        $v->setName($decoded['name']);
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
#------------------------------------------------

    function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
#------------------------------------------------
    public function updateAction() {
        $decoded = $this->process_request();
        if (array_key_exists('email', $decoded) && strlen($decoded['email'])>0) {
            $v = $this->get('site.helper.visitor')->findOneByEmail($decoded['email']);
            if (!$v) {
                $v = new Visitor();
                $v->setEmail($decoded['email']);
            }
            $v->setBrowser($_SERVER['HTTP_USER_AGENT']);
            $v->setIpAddress($this->get_client_ip());
            $v->setCreatedAt(new \DateTime('now'));
            $v->setJsonData(json_encode($decoded));
            $em = $this->getDoctrine()->getManager();
            $em->persist($v);
            $em->flush();
            return new response(json_encode('save visitor info'));
        } else {
            return new response(json_encode('user email missing'));
        }
    }

#---------------------------------------------

    private function process_request() {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $decoded['base_path'] = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';
        return $decoded;
    }

#------------------------------------------------
    function _get_client_ip() {
        $ipaddress = '';
        if ($_SERVER['HTTP_CLIENT_IP'])
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if ($_SERVER['HTTP_X_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if ($_SERVER['HTTP_X_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if ($_SERVER['HTTP_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if ($_SERVER['HTTP_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if ($_SERVER['REMOTE_ADDR'])
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

}

