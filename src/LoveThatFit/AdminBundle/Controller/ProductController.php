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
use LoveThatFit\AdminBundle\Form\Type\ProductColorImageType;
use LoveThatFit\AdminBundle\Form\Type\ProductSizeType;
use LoveThatFit\AdminBundle\Form\Type\ProductItemType;
use LoveThatFit\AdminBundle\Form\Type\ProductSizeManTopType;
use LoveThatFit\AdminBundle\Form\Type\ProductSizeWomenTopType;
use LoveThatFit\AdminBundle\Form\Type\ProductSizeManBottomType;
use LoveThatFit\AdminBundle\Form\Type\ProductSizeWomenBottomType;
use LoveThatFit\AdminBundle\Form\Type\ProductSizeWomenDressType;


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
            $this->get('session')->setFlash('warning', 'Unable to find Product.');  
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
            $this->get('session')->setFlash('success','The Product has been Created!');
            return $this->redirect($this->generateUrl('admin_product_show', array('id' => $entity->getId())));
        }else
        {
            $this->get('warning')->setFlash('warning','The Product cannot be Created!');
             return $this->render('LoveThatFitAdminBundle:Product:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                ));
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
            $this->get('session')->setFlash('warning', 'Unable to find Product.');  
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
            $this->get('session')->setFlash('success','The Product has been Update!');
            return $this->redirect($this->generateUrl('admin_product_show', array('id' => $entity->getId())));
        } else {
           
            $this->get('warning')->setFlash('warning','Unable to update Brand.');
            return $this->render('LoveThatFitAdminBundle:Product:edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity));           
            
        }
    }

//--------------------------------------------------------------------- 
    public function deleteAction($id) {

        try {
            $em = $this->getDoctrine()->getManager();

            $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->find($id);

            if (!$entity) {
                $this->get('session')->setFlash('warning','Unable to find Product.');                
            }

            $em->remove($entity);
            $em->flush();
            $this->get('success')->setFlash('success','This Product has been deleted!');
            return $this->redirect($this->generateUrl('admin_products'));            
        } catch (\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->setFlash('warning','This Product cannot be deleted!');
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
            $this->get('session')->setFlash('warning','Unable to find Brand.');            
        }
        return $brand;
    }

      
    /******************************************************************************
     ************************** PRODUCT DETAIL **********************************
     ****************************************************************************** */

    public function productDetailNewAction() {

        $productForm = $this->createForm(new ProductDetailType());
        return $this->render('LoveThatFitAdminBundle:Product:product_detail_new.html.twig', array(
                    'form' => $productForm->createView(),
                ));
    }

    //------------------------------------------------------------------------------

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
            $this->get('session')->setFlash('success', 'Product Detail has been created.');
            return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $entity->getId())));
        }else
        {
            $this->get('session')->setFlash('warning', 'Product Detail cannot be created.');
            return $this->render('LoveThatFitAdminBundle:Product:product_detail_new.html.twig', array(
                    'form' => $productForm->createView(),
                ));
        }
    }

    //------------------------------------------------------------------------------

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

    //------------------------------------------------------------------------------

    public function productDetailUpdateAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->find($id);

        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');            
        }

        $form = $this->createForm(new ProductDetailType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {

            $entity->setUpdatedAt(new \DateTime('now'));
            $em->persist($entity);
            $em->flush();
            $this->get('session')->setFlash('success', 'Product Detail has been Update.');
            return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $entity->getId())));
        } else {
            $this->get('session')->setFlash('warning', 'Unable to update Product Detail.');
            return $this->render('LoveThatFitAdminBundle:Product:product_detail_edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                   'entity' => $entity));
            
        }
    }

    //------------------------------------------------------------------------------

    public function productDetailDeleteAction($id) {
        try {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->find($id);

            if (!$entity) {
               $this->get('session')->setFlash('warning', 'Unable to find Product.');               
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->setFlash('success', 'Product Detail has been deleted.');
            return $this->redirect($this->generateUrl('admin_products'));
            
        } catch (\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->setFlash(
                    'warning', 'This Product cannot be deleted!'
            );            
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }

    //------------------------------------------------------------------------------

    public function productDetailShowAction($id) {
        $product = $this->getProduct($id);

        if (!$product) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');            
        }

        return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
                    'product' => $product
                ));
    }

 //------------------------------------------------------------------------------
/*************************** PRODUCT DETAIL COLOR ************************************************** */
//------------------------------------------------------------------------------
    
    

    public function productDetailColorAddNewAction($id) {
        $entity = $this->getProduct($id);
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');  
        }

        $colorform = $this->createForm(new ProductColorType());        
        $imageUploadForm=$this->createForm(new ProductColorImageType());
        return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
                    'product' => $entity,
                    'colorform' => $colorform->createView(),
                    'imageUploadForm'=>$imageUploadForm->createView(),        
                ));
    }

          //--------------------------------------------------------------   
    public function productDetailColorCreateAction(Request $request, $id) {

        $product = $this->getProduct($id);
        $productColor = new ProductColor();
        $productColor->setProduct($product);
        $colorform = $this->createForm(new ProductColorType(), $productColor);        
        $colorform->bind($request);
  
   
        if ($colorform->isValid()) {

            $em = $this->getDoctrine()->getManager();
            
            $productColor->savePattern(); //----- file upload method 
            $productColor->saveImage(); //----- file move from temp to permanent folder
                        
            $em->persist($productColor);
            $em->flush();
                       
            if($productColor->displayProductColor)
            {
               $this->createDisplayDefaultColor($product,$productColor); //--add  product  default color 
            }
            $this->createSizeItem($product, $productColor, $colorform->getData()->getSizes()); //--creating sizes & item records
            $this->get('session')->setFlash('success', 'Product Detail color has been created.');
            return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $id)));
        }else
        {
            $this->get('session')->setFlash('warning', 'Product Detail color cannot been created.');
        }
    }

    //--------------------------------------------------------------

    public function productDetailColorEditAction($id, $color_id, $temp_img_path=null) {
                
        $product = $this->getProduct($id);        
        
        if (!$product) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');  
        }
        
        $productColor = $this->getProductColor($color_id);
        $sizeTitle = $productColor->getSizeTitleArray();           
        
        $colorform = $this->createForm(new ProductColorType(), $productColor);        
        $colorform->get('sizes')->setData($sizeTitle);
        
        $imageUploadForm=$this->createForm(new ProductColorImageType() , $productColor);
                
        return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
                    'product' => $product,
                    'colorform' => $colorform->createView(),
                    'color_id' => $color_id,
                    'imageUploadForm'=>$imageUploadForm->createView(),
                ));
    }
    
    //--------------------------------------------------------------
    
    public function productDetailColorUpdateAction(Request $request, $id, $color_id) {
        
        $product = $this->getProduct($id);
        if (!$product) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');  
        }

        $productColor = $this->getProductColor($color_id);
        $colorForm = $this->createForm(new ProductColorType(), $productColor);
        $colorForm->bind($request);
                

        if ($colorForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
        
            $productColor->savePattern(); //----- file upload method 
            $productColor->saveImage(); //----- file move from temp to permanent folder
            //return new Response($productColor->savePattern() . "  -  " . $productColor->saveImage());
            $em->persist($productColor);
            $em->flush();
           
            if($productColor->displayProductColor)
            {
               $this->createDisplayDefaultColor($product,$productColor); //--add  product  default color 
            }
            
            $this->createSizeItem($product, $productColor, $colorForm->getData()->getSizes());
            
            $this->get('session')->setFlash(
                    'success', 'Product Color Detail has been updated!'
            );

            return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $id)));
        } else {

            $this->get('session')->setFlash(
                    'warning', 'Unable to update Product Color Detail!'
            );
            
            $imageUploadForm=$this->createForm(new ProductColorImageType() , $productColor);
            return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
                        'product' => $product,
                        'colorform' => $colorForm->createView(),
                        'color_id' => $color_id,
                        'imageUploadForm'=>$imageUploadForm->createView(),
                    ));
        }
    }


//----------------------------------------------------

    
    public function productColorTemporaryImageUploadAction(Request $request, $id){
       
        $product = $this->getProduct($id);        
        
        if (!$product) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');  
        }
        $productColor = new ProductColor();
        $productColor->setProduct($product);
        $colorImageForm = $this->createForm(new ProductColorImageType(), $productColor);
        $colorImageForm->bind($request);
        
        $temp=$productColor->uploadTemporaryImage();
        
        $response = new Response(json_encode(array(
                                'entity' => $productColor,
                                'imageurl' => $productColor->getWebPath(),
                                'file_name' => $temp,
                            )));
        $response->headers->set('Content-Type', 'application/json');          
            
       $this->get('session')->setFlash('success','Image uploaded '.$response);
       return $this->redirect($this->getRequest()->headers->get('referer'));
      
    }
            

  
    

    //--------------------------------------------------------------
    public function productDetailColorDeleteAction($id, $color_id) {

        try {
            $em = $this->getDoctrine()->getManager();
            $productColor = $em->getRepository('LoveThatFitAdminBundle:ProductColor')->find($color_id);
            if (!$productColor) {
                $this->get('session')->setFlash('warning', 'Unable to find Product.');  
            }
            
            $em->remove($productColor);
            $em->flush();            
            $this->get('session')->setFlash('success', 'Product Detail color has been Deleted.');
            return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $id)));
        } catch (\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->setFlash(
                    'warning', 'This Product Color  cannot be deleted!'
            );
            return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $id)));
        }
    }

    /************************* PRODUCT DETAIL SIZE ************************************************** */
    //--------------------------------------------------------------

    public function productDetailSizeEditAction($id,$size_id) {
    
        $product = $this->getProduct($id);

        if (!$product) {
           $this->get('session')->setFlash('warning', 'Unable to find Product.');  
        }
       
        
        if($product->getClothingType()->getTarget()=="Top" and $product->getGender()=='M')
        {
        $sizeForm = $this->createForm(new ProductSizeManTopType(),$this->getProductSize($size_id));   
        }
        if($product->getClothingType()->getTarget()=="Top" and $product->getGender()=='F')
        {
        $sizeForm = $this->createForm(new ProductSizeWomenTopType(),$this->getProductSize($size_id));   
        }
        if($product->getClothingType()->getTarget()=="Bottom" and $product->getGender()=='M')
        {
        $sizeForm = $this->createForm(new ProductSizeManBottomType(),$this->getProductSize($size_id));   
        }
        if($product->getClothingType()->getTarget()=="Bottom" and $product->getGender()=='F')
        {
        $sizeForm = $this->createForm(new ProductSizeWomenBottomType(),$this->getProductSize($size_id));   
        }
        if($product->getClothingType()->getTarget()=="Dress" and $product->getGender()=='F')
        {
        $sizeForm = $this->createForm(new ProductSizeWomenDressType(),$this->getProductSize($size_id));       
        }

        // $sizeForm = $this->createForm(new ProductSizeType(), $this->getProductSize($size_id));
         return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
                    'product' => $product,
                    'sizeform' => $sizeForm->createView(),
                    'size_id' =>$size_id,
                ));
        
    }

//----------------------------------------------------------------
    public function productDetailSizeUpdateAction(Request $request, $id, $size_id) {


        $product = $this->getProduct($id);

        if (!$product) {
            $this->get('session')->setFlash('warning', 'Please Insert valid value');
        }
        $em = $this->getDoctrine()->getManager();
        $entity_size = $em->getRepository('LoveThatFitAdminBundle:ProductSize')->find($size_id);
       
        if($product->getClothingType()->getTarget()=="Top" and $product->getGender()=='M')
        {
        $sizeForm = $this->createForm(new ProductSizeManTopType(),$this->getProductSize($size_id));   
        }
        if($product->getClothingType()->getTarget()=="Top" and $product->getGender()=='F')
        {
        $sizeForm = $this->createForm(new ProductSizeWomenTopType(),$this->getProductSize($size_id));   
        }
        if($product->getClothingType()->getTarget()=="Bottom" and $product->getGender()=='M')
        {
        $sizeForm = $this->createForm(new ProductSizeManBottomType(),$this->getProductSize($size_id));   
        }
        if($product->getClothingType()->getTarget()=="Bottom" and $product->getGender()=='F')
        {
        $sizeForm = $this->createForm(new ProductSizeWomenBottomType(),$this->getProductSize($size_id));   
        }
        if($product->getClothingType()->getTarget()=="Dress" and $product->getGender()=='M')
        {
        $sizeForm = $this->createForm(new ProductSizeWomenDressType(),$this->getProductSize($size_id));       
        }
        
        
        //$sizeform = $this->createForm(new ProductSizeType(), $this->getProductSize($size_id));
      
        $sizeForm->bind($request);

        if ($sizeForm->isValid()) {
            $em->persist($entity_size);
            $em->flush();
            $this->get('session')->setFlash('success', 'Product Detail size has been update.');
           return $this->redirect($this->generateUrl('admin_product_detail_show',  array('id' => $id)));
        } else {
            $this->get('session')->setFlash('warning', 'Please Try again');
             return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
                    'product' => $product,
                    'sizeform' => $sizeForm->createView(),
                    'size_id' =>$size_id,
                ));
        }
        
    }

//-----------------------------------------------------------------------
    public function productDetailSizeDeleteAction(Request $request, $id, $size_id) {
        $em = $this->getDoctrine()->getManager();

        $repository = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:ProductSize');
        $product = $repository->find($size_id);

        $em->remove($product);
        $em->flush();
       $this->get('session')->setFlash('success', 'Successfully Deleted');
        return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $id)));
    }

//
    /*     * ************************* PRODUCT DETAIL ITEM ************************************************** */
    //--------------------------------------------------------------

    public function productDetailItemEditAction($id, $item_id) {
        
        $entity = $this->getProduct($id);
        
        if (!$entity) {
           $this->get('session')->setFlash('warning', 'Unable to find Product.');  
        }

        $itemform = $this->createForm(new ProductItemType(), $this->getProductItem($item_id));

        return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
                    'product' => $entity,
                    'itemform' => $itemform->createView(),
                    'item_id' => $item_id,
                ));

    }
//----------------------------------------------------------------
    public function productDetailItemUpdateAction(Request $request, $id, $item_id) {


        $entity = $this->getProduct($id);
        if (!$entity) {
          $this->get('session')->setFlash('warning', 'Unable to find Product.');  
        }

        $em = $this->getDoctrine()->getManager();
        $entity_item = $em->getRepository('LoveThatFitAdminBundle:ProductItem')->find($item_id);
        if (!$entity_item) {
            throw $this->createNotFoundException('Unable to find Product Item.');
        }

        $itemform = $this->createForm(new ProductItemType(), $entity_item);
        $itemform->bind($request);

        //condition for image ??????????????????????????????????????
           /* if (!$entity_item->getImage())
            {
                $form->get('image')->addError(new FormError('Please upload image'));
            }
*/
        
        if ($itemform->isValid()) {
            $entity_item->upload(); //----- file upload method 

            $em->persist($entity_item);
            $em->flush();
           $this->get('session')->setFlash('success', 'Product item updated  Successfully');
           return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
                    'product' => $entity,
                    'itemform' => $itemform->createView(),
                  
                ));
            
        } else {
            
            $this->get('session')->setFlash('warning', 'Unable to Product Detail Item');           
             
            return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
                    'product' => $entity,
                    'itemform' => $itemform->createView(),
                    'item_id' => $item_id,
                ));
        }
    }

    //-----------------------------------------------------------------------
    public function productDetailItemDeleteAction(Request $request, $id, $item_id) {
        $em = $this->getDoctrine()->getManager();

        $repository = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:ProductItem');
        $product = $repository->find($item_id);

        $em->remove($product);
        $em->flush();
        $this->get('session')->setFlash('success', 'Successfully Deleted');
        return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $id)));
    }

//        
    //------------------------- Private methods ------------------------- 

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

    //------------------------------------------------------------------------
    public function getProductColor($id) {
        return $this->getDoctrine()
                        ->getRepository('LoveThatFitAdminBundle:ProductColor')
                        ->find($id);
    }
 //------------------------------------------------------------------
 public function createDisplayDefaultColor ($product,$productColor){
             
                $em = $this->getDoctrine()->getManager();
                  $product->setDisplayProductColor($productColor);
                 $em->persist($product);
                 $em->flush();
 }
//------------------------------------------------------------------


    private function createSizeItem($product, $p_color, $sizes) {
        $em = $this->getDoctrine()->getManager();
        foreach ($sizes as $s) {
            
            //--------------check if size already there before inserting new size------------
            $p_size = $product->getSizeByTitle($s);

            if (!$p_size) {
                //--------------inseart size------------
                $p_size = new ProductSize();
                $p_size->setProduct($product);
                $p_size->setTitle($s);
                $em->persist($p_size);
                $em->flush();
                $this->addItem($product, $p_color, $p_size);
            } else {
                //--------------check if item already there before inserting new item------------
                $p_item = $product->getThisItem($p_color, $p_size);

                if (!$p_item) {
                    $this->addItem($product, $p_color, $p_size);
                }
            }
        }
    }

//---------------------------------------------------------------------

    private function addItem($product, $p_color, $p_size) {
        $em = $this->getDoctrine()->getManager();
        $p_item = new ProductItem();
        $p_item->setProduct($product);
        $p_item->setProductSize($p_size);
        $p_item->setProductColor($p_color);
        $em->persist($p_item);
        $em->flush();
    }

    //---------------------------------------------------------------------
    
}

