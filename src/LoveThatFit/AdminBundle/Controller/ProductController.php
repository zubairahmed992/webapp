<?php

namespace LoveThatFit\AdminBundle\Controller;

use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ProductColor;
use LoveThatFit\AdminBundle\Entity\ProductSize;
use LoveThatFit\AdminBundle\Entity\ProductItem;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Form\Type\ProductType;
use LoveThatFit\AdminBundle\Form\Type\ProductDetailType;
use LoveThatFit\AdminBundle\Form\Type\ProductColorType;
use LoveThatFit\AdminBundle\Form\Type\ProductSizeType;
use LoveThatFit\AdminBundle\Form\Type\ProductItemType;

class ProductController extends Controller {

//---------------------------------------------------------------------
    public function indexAction($page_number = 1, $sort = 'id') {
        $limit = 5;

        $productObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product');

        $products = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->findAllProduct($page_number, $limit, $sort);

        $rec_count = count($productObj->countAllRecord());
        $cur_page = $page_number;

        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }

        return $this->render('LoveThatFitAdminBundle:Product:index.html.twig', array(
                    'products' => $products,
                    'rec_count' => $rec_count,
                    'no_of_pagination' => $no_of_paginations,
                    'limit' => $cur_page,
                    'per_page_limit' => $limit,
                ));
    }

//--------------------------------------------------------------------- 
    public function showAction($id) {
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product.');
        }

        return $this->render('LoveThatFitAdminBundle:Product:show.html.twig', array(
                    'product' => $entity
                ));
    }

//--------------------------------------------------------------------- 
    public function newAction() {

        $entity = new Product();

        $form = $this->getEditForm($entity);


        return $this->render('LoveThatFitAdminBundle:Product:new.html.twig', array(
                    'form' => $form->createView()));
    }

//--------------------------------------------------------------------- 
    public function createAction(Request $request) {
        $entity = new Product();

        $form = $this->getEditForm($entity);

        $form->bind($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $entity->setCreatedAt(new \DateTime('now'));
            $entity->setUpdatedAt(new \DateTime('now'));


            $entity->upload(); //----- file upload method 

            $entity->uploadFittingRoomImage();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_product_show', array('id' => $entity->getId())));
        }

        return $this->render('LoveThatFitAdminBundle:Product:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                ));
    }

//--------------------------------------------------------------------- 

    public function editAction($id) {
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->findOneById($id);

        $form = $this->getEditForm($entity);
        $deleteForm = $this->getDeleteForm($id);

        return $this->render('LoveThatFitAdminBundle:Product:edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity));
    }

//--------------------------------------------------------------------- 
    public function updateAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product.');
        }

        $form = $this->getEditForm($entity);
        $form->bind($request);

        $deleteForm = $this->getDeleteForm($id);

        if ($form->isValid()) {


            $entity->setUpdatedAt(new \DateTime('now'));

            $entity->upload(); //----- file upload method 
            $entity->uploadFittingRoomImage();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('admin_product_show', array('id' => $entity->getId())));
        } else {
            throw $this->createNotFoundException('Unable to update Brand.');
        }
    }

//--------------------------------------------------------------------- 
    public function deleteAction($id) {

        try {
            $em = $this->getDoctrine()->getManager();

            $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Product.');
            }

            $em->remove($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_products'));
        } catch (\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->setFlash(
                    'warning', 'This Product cannot be deleted!'
            );
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }

//--------------------------------------------------------------------- 

    private function getEditForm($entity) {
        return $this->createForm(new ProductType(), $entity);
    }

//---------------------------------------------------------------------
    private function getDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm();
    }

//--------------------------------------------------------------------- 
    private function getBrand($id) {
        $em = $this->getDoctrine()->getManager();
        $brand = $em->getRepository('LoveThatFitAdminBundle:Brand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product.');
        }
        return $brand;
    }

    /*     * ****************************************************************************
     * ************************* PRODUCT DETAIL **********************************
     * ***************************************************************************** */

    public function productDetailNewAction() {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product');

        $productForm = $this->createForm(new ProductDetailType());

        return $this->render('LoveThatFitAdminBundle:Product:product_detail_new.html.twig', array(
                    'form' => $productForm->createView(),
                ));
    }

    /*     * **************************************************************************** */

    public function productDetailCreateAction(Request $request) {

        $entity = new Product();

        $form = $this->createForm(new ProductDetailType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $entity->setCreatedAt(new \DateTime('now'));
            $entity->setUpdatedAt(new \DateTime('now'));

            $em->persist($entity);
            $em->flush();

    return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $entity->getId())));
        }
    }

    /*     * **************************************************************************** */

    public function productDetailEditAction($id) {
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->findOneById($id);

        $form = $this->createForm(new ProductDetailType(), $entity);
        $deleteForm = $this->getDeleteForm($id);

        return $this->render('LoveThatFitAdminBundle:Product:product_detail_edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity));
    }

    /*     * *************************************************************************** */

    public function productDetailUpdateAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product.');
        }

        $form = $this->createForm(new ProductDetailType(), $entity);
        $form->bind($request);

        $deleteForm = $this->getDeleteForm($id);

        if ($form->isValid()) {

            $entity->setUpdatedAt(new \DateTime('now'));

            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $entity->getId())));
        } else {
            throw $this->createNotFoundException('Unable to update Product Detail.');
        }
    }

    /*     * *************************************************************************** */

    public function productDetailDeleteAction($id) {
        try {
            $em = $this->getDoctrine()->getManager();

            $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Product.');
            }

            $em->remove($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_products'));
        } catch (\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->setFlash(
                    'warning', 'This Product cannot be deleted!'
            );
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }

    /*     * *************************************************************************** */

    public function productDetailShowAction($id) {
            $entity = $this->getProduct($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product.');
        }
        
        return $this->renderProductDetail($entity);
    }

    /*     * *************************************************************************** */

    public function productDetailColorCreateAction(Request $request, $id) {

        $product = $this->getProduct($id);
        
        $entity = new ProductColor();
        $entity->setProduct($product);
        $colorform = $this->createForm(new ProductColorType(), $entity);

        $colorform->bind($request);

        if ($colorform->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $entity->upload(); //----- file upload method 

            $em->persist($entity);
            $em->flush();
            
            $this->createSizeItem($product, $entity, $colorform->getData()->getSizes()); //--creating sizes & item records
            
            return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $id)));
        }
    }
    //--------------------------------------------------------------
    
    public function productDetailSizeEditAction($id, $size_id) {
        $entity = $this->getProduct($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product.');
        }
        
        return $this->renderProductDetail($entity, $size_id);
    }
//----------------------------------------------------------------
    public function productDetailSizeUpdateAction(Request $request, $id, $size_id) {
        
        
        $entity = $this->getProduct($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product.');
        }
        
        return $this->renderProductDetail($entity, $size_id);
    }

   
    
//-----------------------------------------------------------------------
 //--------------------------------------------------------------
    
    public function productDetailItemEditAction($id, $item_id) {
        $entity = $this->getProduct($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product.');
        }
        
        
        
        $colorform = $this->createForm(new ProductColorType());
        $itemform = $this->createForm(new ProductItemType($this->getProductItem($item_id)));
    
    
    return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
                    'product' => $entity, 
                    'colorform' => $colorform->createView(),
                     'itemform' => $itemform->createView(),
                    'item_id' => $item_id,
         
        
            
                ));
        
        
       // return $this->renderProductDetail($entity, $item_id);
    }
     public function productDetailItemUpdateAction(Request $request, $id,$item_id) {
       
        
        $entity = $this->getProduct($id);
        $colorform = $this->createForm(new ProductColorType());
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product.');
        }
        
         $em = $this->getDoctrine()->getManager();
         $entity_item = $em->getRepository('LoveThatFitAdminBundle:ProductItem')->find($item_id);

        if (!$entity_item) {
            throw $this->createNotFoundException('Unable to find Product Item.');
        }

        $itemform = $this->createForm(new ProductItemType(), $entity_item);
        $itemform->bind($request);

         if ($itemform->isValid()) {

          $entity_item->upload(); //----- file upload method 
           
          $em->persist($entity_item);
          $em->flush();
           
          return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
                                'product' => $entity, 
                                'colorform' => $colorform->createView(),
                                'itemform' => $itemform->createView(),
                                'item_id' => 0,
                               

                        ));
        } else {
            throw $this->createNotFoundException('Unable to update Product Detail Item');
        }
    }
//        
    //------------------------- Private methods -------------------------
    
    
    private function renderProductDetail($product, $size_id=null,$item_id=null)
{
       
    $colorform = $this->createForm(new ProductColorType());
    
    $sizeform = $this->createForm(new ProductSizeType($this->getProductSize($size_id)));
    $itemform = $this->createForm(new ProductItemType($this->getProductItem($item_id)));
    
    
    return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
                    'product' => $product, 
                    'colorform' => $colorform->createView(),
                    'sizeform' => $sizeform->createView(),
                    'itemform' => $itemform->createView(),
                    'size_id' => $size_id,
                    'item_id' => $item_id,
            
                ));
}
    
//------------------------------------------------------------------------
    public function getProduct($id) {
        return $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->find($id);
    }

//------------------------------------------------------------------------
    public function getProductSize($id) {
        return $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ProductSize')
                ->find($id);
    }
//------------------------------------------------------------------------
    public function getProductItem($id) {
        return $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ProductItem')
                ->find($id);
    }    
    

//------------------------------------------------------------------

    private function createSizeItem($product, $color, $sizes) {

        foreach ($sizes as $s) {

            //--------------inseart size------------
            $p_size = new ProductSize();
            $p_size->setProduct($product);
            $p_size->setTitle($s);
            $em = $this->getDoctrine()->getManager();
            $em->persist($p_size);
            $em->flush();
            //-------------------- insert item ----------------
            $p_item = new ProductItem();
            $p_item->setProduct($product);
            $p_item->setProductSize($p_size);
            $p_item->setProductColor($color);
            $em->persist($p_item);
            $em->flush();
        }
    }
    
}

