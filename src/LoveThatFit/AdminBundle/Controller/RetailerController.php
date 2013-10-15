<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Component\Form\FormError;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\AdminBundle\Form\Type\DeleteType;
use LoveThatFit\AdminBundle\Form\Type\RetailerType;
use LoveThatFit\AdminBundle\Form\Type\RetailerUserType;
use LoveThatFit\AdminBundle\Entity\Retailer;
use LoveThatFit\AdminBundle\Entity\RetailerUser;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;

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
        $brand_limit = $this->get('admin.helper.retailer')->getRecordsCountWithCurrentRetailerLimit($id);
        $page_number = ceil($this->get('admin.helper.utility')->getPageNumber($brand_limit[0]['id']));
        if ($page_number == 0) {
            $page_number = 1;
        }
        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
        }
        return $this->render('LoveThatFitAdminBundle:Retailer:show.html.twig', array(
                    'retailer' => $entity,
                    'page_number' => $page_number,
                    'retaileruser' => $this->getRetailerUserByRetailer($entity),
                    'brands' => $this->get('admin.helper.retailer')->getBrandByRetailer($entity->getId()),
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
                    'brands' => $this->getBrandList(),
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

            $retailer = $this->get('admin.helper.retailer')->find($id);
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
                    'retailerUserForm' => $this->createRetailerUser($retailer)
        ));
    }

//------------------------------------------------------------------------------------------

    public function newRetailerUserAction($id) {
        $entity = $this->getRetailer($id);
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Unable to find Retailer.');
        }
        $retailerUser = $this->get('admin.helper.retailer.user')->createNew();
        $form = $this->createForm(new RetailerUserType('add'), $retailerUser);
        $deleteForm = $this->createForm(new DeleteType(), $entity);
        return $this->render('LoveThatFitAdminBundle:Retailer:retailer_user.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity,
                        )
        );
    }

//------------------------------------------------------------------------------------------


    public function createRetailerUserAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $retailer = $this->getRetailer($id);
        $retailerUser = new RetailerUser();
        $form = $this->createForm(new RetailerUserType('add'), $retailerUser);
        $form->bind($request);
        if ($form->isValid()) {
            $password = $this->encodePassword($retailerUser);
            $retailerUser->setPassword($password);
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

//------------------------------------------------------------------------------------------

    public function editRetailerUserAction($id) {
        $entity = $this->get('admin.helper.retailer.user')->find($id);
        $form = $this->createForm(new RetailerUserType('edit'), $entity);
        return $this->render('LoveThatFitAdminBundle:Retailer:retaile_user_edit.html.twig', array(
                    'form' => $form->createView(),
                    'entity' => $entity,
                        )
        );
    }

//------------------------------------------------------------------------------------------

    public function updateRetailerUserAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:RetailerUser')->find($id);
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Unable to find Retailer User.');
        }
        $form = $this->createForm(new RetailerUserType('edit'), $entity);
        $form->bind($request);
        if ($form->isValid()) {
            $entity->setUpdatedAt(new \DateTime('now'));
            $password = $this->encodePassword($entity);
            $entity->setPassword($password);
            $em->persist($entity);
            $em->flush();
        }
        $this->get('session')->setFlash('success', 'Retailer Detail has been Update.');
        return $this->redirect($this->generateUrl('admin_retailers'));
    }

//------------------------------------------------------------------------------------------

    public function retailerBrandManageAction($id) {
        $entity = $this->getRetailer($id);
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Unable to find Retailer.');
        }        
        $brand_form = $this->addRetailerBrandForm();
        $brand_form->get('brands')->setData($entity->getBrandArray());
        return $this->render('LoveThatFitAdminBundle:Retailer:retailer_manage_brands.html.twig', array(
                    'retailerid' => $entity,
                    'form' => $brand_form->createView(),
                    'brand' => $this->getBrandList(),
                    'retailerBrand' => $this->getRetailerUserByRetailer($entity->getId()),
        ));
    }

//------------------------------------------------------------------------------------------

    public function createRetailerBrandAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $retailer = $this->getRetailer($id);
        if (!$retailer) {
            $this->get('session')->setFlash('warning', 'Unable to find Retailer.');
        }
        foreach($retailer->getBrands() as $brand)
        {
           $brand->removeRetailer($retailer);
           $retailer->removeBrand($brand);
           $em->persist($brand);
           $em->persist($retailer);
           $em->flush();            
        }
       $data = $request->request->all();
       if($data['form']['brands'])
       {
       $brands = $data['form']['brands'];     
        foreach ($brands as $key=>$value){
            $brand = $this->get('admin.helper.brand')->find($value);
            $brand->addRetailer($retailer);
            $retailer->addBrand($brand);
            $em->persist($brand);
            $em->persist($retailer);
            $em->flush();
        }
        $this->get('session')->setFlash('success', 'Retailer Brand has been created.');
       }
        return $this->redirect($this->generateUrl('admin_retailer_show', array('id' => $retailer->getId())));
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

    //------------------Delete Retaielr User---------------------------------------------------

    public function deleteRetailerUserAction($id) {
        try {
            $message_array = $this->get('admin.helper.retailer.user')->delete($id);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            return $this->redirect($this->generateUrl('admin_retailers'));
        } catch (\Doctrine\DBAL\DBALException $e) {

            $this->get('session')->setFlash('warning', 'This retailer cannot be deleted!');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }

    //------------- Password encoding ------------------------------------------
    public function encodePassword(RetailerUser $retailerUser) {
        return $this->encodeThisPassword($retailerUser, $retailerUser->getPassword());
    }

    //---------------------------Brand List-------------------
    private function getBrandList() {
        $brand = $this->get('admin.helper.brand')->getBrnadList();
        return $brand;
    }

//------------------------------------------------------------------------------------------

    private function getRetailer($id) {
        return $this->getDoctrine()
                        ->getRepository('LoveThatFitAdminBundle:Retailer')
                        ->find($id);
    }

//------------------------------------------------------------------------------------------

    private function getRetailerUserByRetailer($retailer) {
        $retaielerUser = $this->get('admin.helper.retailer.user')->getRetaielerUserByRetailer($retailer);
        return $retaielerUser;
    }

//-------------------------------------------------------
    private function encodeThisPassword(RetailerUser $retailerUser, $password) {
        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($retailerUser);
        $password = $encoder->encodePassword($password, $retailerUser->getSalt());
        return $password;
    }

//------------------------------------------------------------------------------------------

    private function addRetailerBrandForm() {        
        $builder = $this->createFormBuilder();
        //$brands= $this->get('admin.helper.brand')->getBrnadArray();
                    $builder->add(
                'brands', 'choice', 
                array('choices'=>$this->get('admin.helper.brand')->getBrandArray(),
                       'multiple'  => true,
                       'expanded'  => true, 
                ));        
        return $builder->getForm();
    }    
//------------------------------------------------------------------------------------------


}
