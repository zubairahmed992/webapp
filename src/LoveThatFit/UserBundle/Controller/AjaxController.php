<?php

namespace LoveThatFit\UserBundle\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use LoveThatFit\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AjaxController extends Controller {
  
    public function sizeChartSizesByBrandJSONAction($brand_id, $gender, $target,$body_type) {
        
      
        $sizes = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:SizeChart')
                ->findByBrandGenderTargetBodyType($brand_id, $gender, $target,$body_type);
                   $response = new Response(json_encode($sizes));
                $response->headers->set('Content-Type', 'text/html');          
                return $response;
    }
    #--!!!!!This should removed when profile setting update !!!!---------#
    public function brandSizeChartByJSONAction($brand_id, $gender, $target) {
        
      
        $brandsizechart = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:SizeChart')
                ->findByBrandGenderTarget($brand_id, $gender, $target);
                $response = new Response(json_encode($brandsizechart));
                $response->headers->set('Content-Type', 'text/html');          
                return $response;
    }
    

}
