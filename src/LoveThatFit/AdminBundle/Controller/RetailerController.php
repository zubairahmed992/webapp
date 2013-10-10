<?php

namespace LoveThatFit\AdminBundle\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Form\Type\DeleteType;
use LoveThatFit\AdminBundle\Form\Type\RetailerType;
use LoveThatFit\AdminBundle\Form\Type\RetailerUserType;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;
use LoveThatFit\AdminBundle\Entity\Retailer;
use LoveThatFit\AdminBundle\Entity\RetailerUser;
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
                    'retaileruser'=>$this->getRetailerUserByRetailer($entity)
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
                    'entity' => $entity,
                    'brands'=>  $this->getBrandList(),
                    'retailerUserForm'=>$this->createRetailerUser($specs)
                )
                );
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
            
            $retailer=$this->get('admin.helper.retailer')->find($id);
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
                    'entity' => $entity,
                    'retailerUserForm'=>$this->createRetailerUser($retailer)
                ));
    }
    
    public function newRetailerUserAction($id)
    {
        $entity = $this->getRetailer($id);
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Unable to find Retailer.');
        }
        $retailerUser = $this->get('admin.helper.retailer.user')->createNew();
        $form = $this->createForm(new RetailerUserType('add'),$retailerUser);
        $deleteForm = $this->createForm(new DeleteType(), $entity);
        return $this->render('LoveThatFitAdminBundle:Retailer:retailer_user.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity,                    
                )
                );
    }
    
   
    
    public function createRetailerUserAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $retailer = $this->getRetailer($id);
        $retailerUser = new RetailerUser(); 
        $form = $this->createForm(new RetailerUserType('add'),$retailerUser);
        $form->bind($request);
        if ($form->isValid()) {
            $retailerUser->setRetailer($retailer);            
            $retailerUser->setCreatedAt(new \DateTime('now'));
            $retailerUser->setUpdatedAt(new \DateTime('now'));
            $em->persist($retailerUser);
            $em->flush();
            $this->get('session')->setFlash('success', 'Retailer User has been created.');
            return $this->redirect($this->generateUrl('admin_retailers'));
        } else {
           return $this->render('LoveThatFitAdminBundle:Retailer:retailer_user.html.twig', array(
                    'form' => $form->createView(),                    
                    'entity' => $retailer,                    
                )
                );
        }
    }
    
    
     public function editRetailerUserAction($id)
    {
        $specs = $this->get('admin.helper.retailer.user')->findWithSpecs($id);
        $entity = $specs['entity'];
        $form = $this->createForm(new RetailerUserType('edit'), $entity);      
        return $this->render('LoveThatFitAdminBundle:Retailer:retaile_user_edit.html.twig', array(
                    'form' => $form->createView(),                   
                    'entity' => $entity,
                )
                );
    }
    
    public function updateRetailerUserAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:RetailerUser')->find($id);
        $form = $this->createForm(new RetailerUserType('edit'), $entity);
        $form->bind($request);
        if ($form->isValid()) {
            $entity->setCreatedAt(new \DateTime('now'));
            $entity->setUpdatedAt(new \DateTime('now'));
            $em->persist($entity);
            $em->flush();
            $this->get('session')->setFlash('success', 'Product Detail has been Update.');
            return $this->redirect($this->generateUrl('admin_retailers'));
        }
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

  

    //---------------------------Brand List-------------------
    private function getBrandList()
    {
        $brand=$this->get('admin.helper.brand')->getBrnadList();
        return $brand;
        
    }
    
    private function getRetailer($id)
    {
        return $this->getDoctrine()
                        ->getRepository('LoveThatFitAdminBundle:Retailer')
                        ->find($id);
    }
    
    private function addRetailerBrand($retailer, $brand) {        
        $retailerBrand = new Retailer();        
        $retailerBrand->setQuestion($question);
        $userSurvey->setAnswer($answers);
        $userSurvey->setUser($user);
        $userSurvey->setSurvey('Question Answer Survey');
        $this->em->persist($userSurvey);
            $this->em->flush();
            return array('message' => 'Success! Answers Added Successfully.',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );        
    }
    private function getRetailerUserByRetailer($retailer)
    {
        $retaielerUser=$this->get('admin.helper.retailer.user')->getRetaielerUserByRetailer($retailer);
        return $retaielerUser;
    }
    
//-------------------------------------------------------
    private function encodeThisPassword(Retailer $retailer, $password) {
        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($retailer);
        $password = $encoder->encodePassword($password, $retailer->getSalt());
        return $password;
    }
  
}
