<?php

namespace LoveThatFit\AdminBundle\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Form\Type\DeleteType;
use LoveThatFit\AdminBundle\Form\Type\RetailerType;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;
use LoveThatFit\AdminBundle\Entity\Retailer;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RetailerController extends Controller {

    protected $container;

    /**
     * {@inheritDoc}
     */ 
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
    //------------------------------------------------------------------------------------------
    public function indexAction($page_number, $sort = 'id') {
        $brands_with_pagination = $this->get('admin.helper.retailer')->getListWithPagination($page_number, $sort);
        return $this->render('LoveThatFitAdminBundle:Retailer:index.html.twig', $brands_with_pagination);
    }

//------------------------------------------------------------------------------------------

    public function showAction($id) {

        $specs = $this->get('admin.helper.retailer')->findWithSpecs($id);
        $entity = $specs['entity'];
        $brand_limit =$this->get('admin.helper.retailer')->getRecordsCountWithCurrentRetailerLimit($id);
        $page_number=ceil($this->get('admin.helper.utility')->getPageNumber($brand_limit[0]['id']));
        if($page_number==0){
       $page_number=1;
     }
        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
        }
        return $this->render('LoveThatFitAdminBundle:Retailer:show.html.twig', array(
                    'retailer' => $entity,
                    'page_number'=>$page_number,
        ));
    }

//------------------------------------------------------------------------------------------
    public function newAction() {

        $entity = $this->get('admin.helper.retailer')->createNew();
        $form = $this->createForm(new RetailerType('add'), $entity);

        return $this->render('LoveThatFitAdminBundle:Retailer:new.html.twig', array(
                    'form' => $form->createView()));
    }

    //------------------------------------------------------------------------------------------
    public function createAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->get('admin.helper.retailer')->createNew();
        $form = $this->createForm(new RetailerType('add'), $entity);
        $form->bind($request);
       if ($this->get('admin.helper.retailer')->isDuplicateEmail(Null, $entity->getEmail())) {
               
           $this->get('session')->setFlash('warning', 'This email address has already been taken!');  
           //$form->get('email')->addError(new FormError('This email address has already been taken.'));
            }  else {
             if ($form->isValid()) {            
              $message_array = $this->get('admin.helper.retailer')->save($entity);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);

            if ($message_array['success']) {
                return $this->redirect($this->generateUrl('admin_retailers'));
            }

            if ($message_array['success']) {
                return $this->redirect($this->generateUrl('admin_retailers'));
            }
        } else {
            $this->get('session')->setFlash('warning', 'The Retailer can not be Created!');
        }
            }
        

        return $this->render('LoveThatFitAdminBundle:Retailer:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

//------------------------------------------------------------------------------------------
    public function editAction($id) {

        $specs = $this->get('admin.helper.retailer')->findWithSpecs($id);
        $entity = $specs['entity'];

        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
        }

        $form = $this->createForm(new RetailerType('edit'), $entity);

        $deleteForm = $this->createForm(new DeleteType(), $entity);
        return $this->render('LoveThatFitAdminBundle:Retailer:edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity));
    }

    //------------------------------------------------------------------------------------------

    public function updateAction(Request $request, $id) {

        $specs = $this->get('admin.helper.retailer')->findWithSpecs($id);
        $entity = $specs['entity'];

        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
            return $this->redirect($this->generateUrl('admin_retailers'));
        }

        $form = $this->createForm(new RetailerType('edit'), $entity);
        $form->bind($request);

        if ($form->isValid()) {

            $message_array = $this->get('admin.helper.retailer')->update($entity);

            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);

            if ($message_array['success'] == true) {
                return $this->redirect($this->generateUrl('admin_retailers'));
            }
        } else {
            $this->get('session')->setFlash('warning', 'Unable to update retailer!');
        }
        $deleteForm = $this->createForm(new DeleteType(), $entity);
        return $this->render('LoveThatFitAdminBundle:Retailer:edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity));
    }

//------------------------------------------------------------------------------------------

    public function deleteAction($id) {
        try {

            $message_array = $this->get('admin.helper.retailer')->delete($id);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);

            return $this->redirect($this->generateUrl('admin_retailers'));
        } catch (\Doctrine\DBAL\DBALException $e) {

            $this->get('session')->setFlash('warning', 'This retailer cannot be deleted!');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }
    
   //------------- Password encoding ------------------------------------------
    public function encodePassword(Retailer $retailer) {
        return $this->encodeThisPassword($retailer, $retailer->getPassword());
    }

//-------------------------------------------------------
    private function encodeThisPassword(Retailer $retailer, $password) {
        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($retailer);
        $password = $encoder->encodePassword($password, $retailer->getSalt());
        return $password;
    }
  
}
