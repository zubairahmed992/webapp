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
  
    public function sizeChartSizesByBrandJSONAction($brand_id, $gender, $target) {
        
        $sizes = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:SizeChart')
                ->findByBrandGenderTarget($brand_id, $gender, $target);
        
        return new Response(json_encode($sizes));
    }

}
