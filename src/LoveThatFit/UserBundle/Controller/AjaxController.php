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
        //$response = new Response(json_encode($sizes));
                   $response = new Response(json_encode($this->setSizeTitles($sizes)));
                $response->headers->set('Content-Type', 'text/html');          
                return $response;
    }
    
    public function setSizeTitles($sizes)
{ $new_sizes=array();
    $new_key='';
    $str="";
    foreach ($sizes as $value) {
        
        switch ($value['title']){
            case "0":
                $new_key="XXS : ".$value['title'];
                break;
            case "1":
                $new_key="XS : ".$value['title'];
                break;
            case "2":
                $new_key="XS : ".$value['title'];
                break;
            case "4":
                $new_key="S : ".$value['title'];
                break;
            case "6":
                $new_key="S : ".$value['title'];
                break;
            case "8":
                $new_key="M : ".$value['title'];
                break;
            case "10":
                $new_key="M : ".$value['title'];
                break;
            case "12":
                $new_key="L : ".$value['title'];
                break;
            case "14":
                $new_key="L : ".$value['title'];
                break;
            case "16":
                $new_key="XL : ".$value['title'];
                break;
            case "18":
                $new_key="XL : ".$value['title'];
                break;
            case "20":
                $new_key="XXL : ".$value['title'];
                break;
            case "22":
                $new_key="XXL : ".$value['title'];
                break;
            case "24":
                $new_key="XXL : ".$value['title'];
                break;
            case "26":
                $new_key="XXL : ".$value['title'];
                break;
            case "28":
                $new_key="XXL : ".$value['title'];
                break;
        }
        
        array_push($new_sizes, array("title"=>$new_key, "id"=>$value['id']));
        
    }
    return $new_sizes;
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
