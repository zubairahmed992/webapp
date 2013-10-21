<?php
namespace LoveThatFit\RetailerAdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\UserBundle\Form\Type\UserPasswordReset;

class DefaultController extends Controller
{
    public function indexAction()
    {        
        $id = $this->get('security.context')->getToken()->getUser()->getId();        
        return $this->render('LoveThatFitRetailerAdminBundle:Default:index.html.twig',array('brands' => $this->get('admin.helper.retailer')->getRetailerBrand($id),));
    }
    
    public function retailerProductAction($id)
    {
      $retailer = $this->get('security.context')->getToken()->getUser()->getId();   
      $proudct = $this->get('admin.helper.retailer')->findProductByBrand($id);
      if (!$proudct) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
            return $this->render('LoveThatFitRetailerAdminBundle:Default:index.html.twig',array('brands' => $this->get('admin.helper.retailer')->getRetailerBrand($retailer),));
        }      
      else
      {
             return $this->render('LoveThatFitRetailerAdminBundle:Default:product.html.twig',array('product' =>$proudct,));
      }      
    }
    
    
    //----------------------------Retailer Login---------------------------------------
    public function retailerloginAction() {
        $security_context = $this->get('admin.helper.retailer.user')->getRegistrationSecurityContext($this->getRequest());
        return $this->render(
                        'LoveThatFitRetailerAdminBundle:Default:retailerLogin.html.twig', array(
                    'last_username' => $security_context['last_username'],
                    'error' => $security_context['error'],
                    
                        )
        );
    }
    
    
    
    
}
