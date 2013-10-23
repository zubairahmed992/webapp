<?php
namespace LoveThatFit\RetailerAdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Form\Type\RetailerPasswordReset;
use Symfony\Component\DependencyInjection\ContainerInterface;
use LoveThatFit\AdminBundle\Entity\RetailerUser;


class DefaultController extends Controller
{
    protected $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
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
    
    public function resetRetailerPasswordAction()
    {        
        $retailerid = $this->get('security.context')->getToken()->getUser()->getId();
        $entity = $this->get('admin.helper.retailer.user')->find($retailerid);        
        $passwordResetForm = $this->createForm(new RetailerPasswordReset(), $entity);
        return $this->render('LoveThatFitRetailerAdminBundle:Default:reset_password.html.twig', array(                   
                    'entity' => $entity,
                    'form_password_reset' => $passwordResetForm->createView()
                ));        
    }
    
    public function retailerPasswordUpdateAction(Request $request )
    {      
        $id = $this->get('security.context')->getToken()->getUser()->getId();        
        $em = $this->getDoctrine()->getManager();
        
        $entity = $this->get('admin.helper.retailer.user')->find($id);
        
        $retailer_old_password = $entity->getPassword();
        
        $salt_value_old = $entity->getSalt();
        
        $passwordResetForm = $this->createForm(new RetailerPasswordReset(), $entity);
        
        $passwordResetForm->bind($request);
        
        $data = $passwordResetForm->getData();
        
        $oldpassword = $data->getOldpassword();
        
        $factory = $this->get('security.encoder_factory');
        
        $encoder = $factory->getEncoder($entity);
        
        $password_old_enc = $encoder->encodePassword($oldpassword, $salt_value_old);        
        
        if ($retailer_old_password == $password_old_enc) {           
            
            if ($passwordResetForm->isValid()) {                                              
                $entity->setUpdatedAt(new \DateTime('now'));                
                $password= $this->encodePassword($entity);                 
                $entity->setPassword($password);
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
                $this->get('session')->setFlash('success', 'Password Updated Successfully');                
                
            } else {
                
                $this->get('session')->setFlash('warning', 'Confirm pass doesnt match');
            }
        } else {
            
            $this->get('session')->setFlash('warning', 'Please Enter Correct Password');        
            
        }
        
        $passwordResetForms = $this->createForm(new RetailerPasswordReset(), $entity);
        return $this->render('LoveThatFitRetailerAdminBundle:Default:reset_password.html.twig', array(                    
                    'entity' => $entity,
                    'form_password_reset' => $passwordResetForms->createView()
                ));
        
    }
    
    
     //------------- Password encoding ------------------------------------------
    public function encodePassword(RetailerUser $retailerUser) {
        return $this->encodeThisPassword($retailerUser, $retailerUser->getPassword());
    }

    //-------------------------------------------------------
    private function encodeThisPassword(RetailerUser $retailerUser, $password) {
        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($retailerUser);
        $password = $encoder->encodePassword($password, $retailerUser->getSalt());
        return $password;
    }

//------------------------------------------------------------------------------------------

    
    
    
}
