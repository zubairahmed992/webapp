<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ProductColor;
use LoveThatFit\AdminBundle\Entity\ProductSize;
use LoveThatFit\AdminBundle\Entity\ProductItem;
use LoveThatFit\AdminBundle\Entity\ProductItemPiece;
use LoveThatFit\AdminBundle\Entity\ProductSizeMeasurement;
use LoveThatFit\AdminBundle\Entity\ProductColorView;
use LoveThatFit\AdminBundle\Form\Type\DeleteType;
use LoveThatFit\AdminBundle\Form\Type\ProductColorViewType;
use Symfony\Component\HttpFoundation\Response;



class ProductColorViewController extends Controller {
    
    //------------------------View List Method------------------------------------
    public function listAction($product_color_id) {
        
        $product_color= $this->get('admin.helper.productcolor')->find($product_color_id);
        $entity=$this->get('admin.helper.product.color.view')->findProductColorViewByColor($product_color);        
         return $this->render('LoveThatFitAdminBundle:ProductColorView:_list.html.twig',array('color_view'=>$entity,'product_color_id'=>$product_color_id));
       
    }
    
    //------------------------Add New color view Method------------------------------------
    public function newAction($product_color_id) {
        $entity = $this->get('admin.helper.product.color.view')->createNew();
        $form = $this->createForm(new ProductColorViewType('add'), $entity);
        return $this->render('LoveThatFitAdminBundle:ProductColorView:_new.html.twig', array('form' => $form->createView(),'product_color_id'=>$product_color_id));
    }
    
    //------------------------Save in database New color view Method------------------------------------
    public function createAction($product_color_id,Request $request ) {
        $product_color= $this->get('admin.helper.productcolor')->find($product_color_id);
        $product=$product_color->getProduct();
        $entity = $this->get('admin.helper.product.color.view')->createNew();
        $form = $this->createForm(new ProductColorViewType('add'), $entity);
        $form->bind($request);   
        if ($form->isValid()) {
        $entity->setProductColor($product_color);
        $entity->setProduct($product);        
        $message_array=$this->get('admin.helper.product.color.view')->save($entity);        
        $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
        return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $product->getId())));
        //return $this->redirect($this->generateUrl('admin_product_color_view_list', array('product_color_id' => $product_color_id)));
        }
    }
    
    //------------------------Edit color view Method------------------------------------
    
    public function editAction($id) {
        $entity=$this->get('admin.helper.product.color.view')->find($id);
        $form = $this->createForm(new ProductColorViewType('edit'), $entity);
        return $this->render('LoveThatFitAdminBundle:ProductColorView:_edit.html.twig',array('form' => $form->createView(),'entity'=>$entity,'product_color_id'=>$entity->getProductColor()->getId()));
    }
    
    //------------------------Save updated color view Method in database------------------------------------
    
    public function updateAction($id,Request $request) {
       $entity=$this->get('admin.helper.product.color.view')->find($id);
       $form = $this->createForm(new ProductColorViewType('edit'), $entity);
       $form->bind($request);   
       if ($form->isValid()) {        
       $entity->setProductColor($entity->getProductColor());
       $entity->setProduct($entity->getProduct());
       $message_array=$this->get('admin.helper.product.color.view')->update($entity);      
       $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
      return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $entity->getProduct()->getId())));
       
       }
    }
    
    //------------------------Delete Color View Method------------------------------------
    
   public function deleteAction($id) {
   $entity=$this->get('admin.helper.product.color.view')->find($id);
       try {
            $message_array = $this->get('admin.helper.product.color.view')->delete($id);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
          
            return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $entity->getProduct()->getId())));            
           
        } catch (\Doctrine\DBAL\DBALException $e) {

            $this->get('session')->setFlash('warning', 'This Product Color view cannot be deleted!');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }  
    }
    
    
    

}
