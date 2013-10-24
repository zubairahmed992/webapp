<?php

namespace LoveThatFit\RetailerAdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\Retailer;
use LoveThatFit\AdminBundle\Form\Type\RetailerProductDetailType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;


class RetailerProductController extends Controller {

//---------------------------------------------------------------------
protected $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function indexAction($page_number, $sort = 'id') {
        $id = $this->get('security.context')->getToken()->getUser()->getId();        
        $retailer=$this->get('admin.helper.retailer')->find($id);
        $product_with_pagination = $this->get('admin.helper.product')->getListWithPagination($page_number, $sort);
        return $this->render('LoveThatFitRetailerAdminBundle:Product:index.html.twig',array('products'=>$product_with_pagination,'retailer' => $this->get('admin.helper.retailer.user')->getRetailerNameByRetailerUser($retailer)));
    }

    public function retailerProductNewAction()
    {
        /*
        $id = $this->get('security.context')->getToken()->getUser()->getId();                 
        $retailerentity = $this->get('admin.helper.retailer.user')->find($id);
        //return new response(json_encode($retailerentity->getRetailer()->getId()));
        $entity = $this->getRetailer($retailerentity->getRetailer()->getId());
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Unable to find Retailer.');
        }        
        $brand_form = $this->addRetailerBrandForm();
        $brand_form->get('brands')->setData($entity->getBrandArray());        
        */
        $productForm = $this->createForm(new RetailerProductDetailType());        
        return $this->render('LoveThatFitRetailerAdminBundle:Product:new_product.html.twig', array(
                    'form' => $productForm->createView(),                    
        ));
    }
    
    public function retailerProductNewCreateAction(Request $request)
    {        
        
        //return new Response(json_encode($request->request->all()));
        
        
        $em = $this->getDoctrine()->getManager();
        $entity = new Product();
        $form = $this->createForm(new RetailerProductDetailType(), $entity);
        if ($this->getRequest()->getMethod() == 'POST') {
        $form->bindRequest($request); 
        $id = $this->get('security.context')->getToken()->getUser()->getId();                 
        $retailerentity = $this->get('admin.helper.retailer.user')->find($id);       
        $retailer = $this->getRetailer($retailerentity->getRetailer()->getId());
        if (!$retailer) {
            $this->get('session')->setFlash('warning', 'Unable to find Retailer.');
        }
        //return new Response(json_encode($request));
        if ($form->isValid()) {            
           $entity->setCreatedAt(new \DateTime('now'));
            $entity->setUpdatedAt(new \DateTime('now'));
            $entity->setRetailer($retailer); 
            $retailer->addProduct($entity);
            $em->persist($retailer);
            $em->persist($entity);            
            $em->flush();
            $this->get('session')->setFlash('success', 'Retailer Product Detail has been Created.');
       }   
        }else
        {
          $this->get('session')->setFlash('warning', 'The Retailer Product can not be Created!');
       }    
        
       return $this->render('LoveThatFitRetailerAdminBundle:Product:new_product.html.twig', array(
                    'form' => $form->createView(),                    
        ));
    }
    
    
    
    
     private function getRetailer($id) {
        return $this->getDoctrine()
                        ->getRepository('LoveThatFitAdminBundle:Retailer')
                        ->find($id);
    }
}

