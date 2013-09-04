<?php

namespace LoveThatFit\AdminBundle\Controller;

use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ProductColor;
use LoveThatFit\AdminBundle\Entity\ProductSize;
use LoveThatFit\AdminBundle\Entity\ProductItem;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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
use LoveThatFit\AdminBundle\Form\Type\ProductColorPatternType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Exception\ParseException;
use LoveThatFit\AdminBundle\ImageHelper;
use ZipArchive;


class ProductController extends Controller {

//---------------------------------------------------------------------
    
    public function indexAction($page_number, $sort = 'id') {
         $this->productSaveYaml();
        $product_with_pagination = $this->get('admin.helper.product')->getListWithPagination($page_number, $sort);
        return $this->render('LoveThatFitAdminBundle:Product:index.html.twig', $product_with_pagination);
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
        
        
        $data = $request->request->all();
        $ClothingType=$data['product']['ClothingType'];
       
        $entity = new Product();
      
        $form = $this->createForm(new ProductDetailType(), $entity);
        $form->bind($request);
        
       
       $gender=$entity->getGender();
       $clothing_type= $entity->getClothingType()->getTarget();
       if($gender=='M' and $clothing_type=='Dress')
       {
           $form->get('gender')->addError(new FormError('Dresses can not be selected  for Male'));
           
         $this->get('session')->setFlash('warning', 'Dresses can not be selected for male.');
            return $this->render('LoveThatFitAdminBundle:Product:product_detail_new.html.twig', array(
                    'form' => $form->createView(),
                ));      
       }    
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
                    'form' => $form->createView(),
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
        $gender=$entity->getGender();
        
        $clothing_type= $entity->getClothingType()->getTarget();
        if($clothing_type=='')
        {
             $this->get('session')->setFlash('warning', 'Select Clothing Type.');
            return $this->render('LoveThatFitAdminBundle:Product:product_detail_new.html.twig', array(
                    'form' => $form->createView(),
                ));      
        }
       
        if($gender=='M' and $clothing_type=='Dress' )
       {
         $this->get('session')->setFlash('warning', 'Dresses can not be selected for male.');
            return $this->render('LoveThatFitAdminBundle:Product:product_detail_new.html.twig', array(
                    'form' => $form->createView(),
                ));      
       }   
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
        $patternUploadForm=$this->createForm(new ProductColorPatternType());
        return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
                    'product' => $entity,
                    'colorform' => $colorform->createView(),
                    'imageUploadForm'=>$imageUploadForm->createView(),
                    'patternUploadForm'=>$patternUploadForm->createView(),
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
                       
           if($productColor->displayProductColor or $product->displayProductColor== NULL)
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
         $patternUploadForm=$this->createForm(new ProductColorPatternType(),$productColor);       
        return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
                    'product' => $product,
                    'colorform' => $colorform->createView(),
                    'color_id' => $color_id,
                    'imageUploadForm'=>$imageUploadForm->createView(),
                    'patternUploadForm'=>$patternUploadForm->createView(),
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
           
            if($productColor->displayProductColor or $product->displayProductColor== NULL)
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
        $temp = $productColor->uploadTemporaryImage();
        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath()."/".$productColor->getWebPath(). $temp['image_url'];
        $data=array('image_name' => $temp['image_name'],
                 'image_url' => $baseurl);
        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'text/html');          
          return $response;
      
    }
            

  
    

    //--------------------------------------------------------------
    public function productDetailColorDeleteAction($id, $color_id) {

        try {
            $em = $this->getDoctrine()->getManager();
            $productColor = $em->getRepository('LoveThatFitAdminBundle:ProductColor')->find($color_id);
            if (!$productColor) {
                $this->get('session')->setFlash('warning', 'Unable to find Product.');  
            }
            $defaultcolor=$this->getDefaultColorById($productColor);
            if(!$defaultcolor)
            {
             $em->remove($productColor);
             $em->flush();            
             $this->get('session')->setFlash('success', 'Product Detail color has been Deleted.');
             return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $id)));
            }else
            {            
               $defaultcolor=null; 
               $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->find($id);
               if (!$entity) {
               $this->get('session')->setFlash('warning', 'Unable to find Product.');
               }
               $entity->setDisplayProductColor($defaultcolor);
               $em->persist($entity);
               $em->flush();
               
               $em->remove($productColor);
             $em->flush();            
             $this->get('session')->setFlash('success', 'Product Detail color has been Deleted.');
               return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $id)));           
           }            
            
        } catch (\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->setFlash(
                    'warning', 'This Product Color  cannot be deleted!'
            );
           // return $this->redirect($this->generateUrl('admin_products'));
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
        if($product->getClothingType()->getTarget()=="Dress" and $product->getGender()=='F')
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
 
 
 //----------------------Products Stats-----------------
public function productStatsAction()
{
        $productObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product');
        $products = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->findListAllProduct();
        $rec_count = count($productObj->countAllRecord());       
    
        $entity=  $this->getProductByBrand();     
    return $this->render('LoveThatFitAdminBundle:Product:product_stats.html.twig',array(
                    'total_products'=>$rec_count,
                    'femaleProduct'=>  $this->countProductsByGender('f'),
                    'maleProduct'=>  $this->countProductsByGender('m'),
                    'topProduct'=>$this->countProductsByType('Top'),
                    'bottomProduct'=>$this->countProductsByType('Bottom'),
                    'dressProduct'=>$this->countProductsByType('Dress'),
                    'brandproduct'=>$entity,
        ));
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
    
    
    private function countProductsByGender($gender)
    {
        $em = $this->getDoctrine()->getManager();
        $ProductTypeObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                 ->findPrductByGender($gender);
		$rec_count = count($ProductTypeObj->findPrductByGender($gender));
        return $rec_count;
    }

    private function countProductsByType($target)
    {
        $em = $this->getDoctrine()->getManager();
        $ProductTypeObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                 ->findPrductByType($target);
		$rec_count = count($ProductTypeObj->findPrductByType($target));
        return $rec_count;
    }
    
    
    private function getProductByBrand()
    {
      $em = $this->getDoctrine()->getManager();     
      $entity = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product')
                 ->findPrductByBrand();		
        return $entity;
    }
    //---------------------------------------------------------------------
    
    
    private function getDefaultColorById($product_color)
    {
      $em = $this->getDoctrine()->getManager();     
      $entity = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product')
                 ->findDefaultProductByColorId($product_color);		
        return $entity; 
    }
    
     private function productSaveYaml() {
        $entity = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product')
                ->findAll();

        $array = array();
        foreach ($entity as $product) {
            $brand = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Brand')
                    ->findBrandByProduct($product->getBrand());
            $clothing_type = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:ClothingType')
                    ->findClothingTypeByProduct($product->getClothingType());
            foreach ($brand as $brands) {
                foreach ($clothing_type as $clothingType) {
                    array_push($array, array($brands->getName() => array($clothingType->getName() => array(
                                $product->getName() => array('description' => $product->getDescription(), 'gender' => $product->getGender(), 'adjustment' => $product->getAdjustment())))));

                    foreach ($product->getProductColors() as $productColor) {
                        $array[$product->getName()]['product_color'][$productColor->getTitle()] = array('title' => $productColor->getTitle(), 'image' => $productColor->getImage(), 'pattern' => $productColor->getPattern());
                        foreach ($product->getProductSizes() as $productSizes) {
                            $array[$product->getName()]['product_sizes'][$productSizes->getTitle()] = array('title' => $productSizes->getTitle(), 'inseam_min' => $productSizes->getInseamMin(), 'inseam_max' => $productSizes->getInseamMax(), 'hip_min' => $productSizes->getHipMin(), 'hip_max' => $productSizes->getHipMax(), 'waist_min' => $productSizes->getWaistMin(), 'waist_max' => $productSizes->getWaistMax(), 'bust_min' => $productSizes->getBustMin(), 'bust_max' => $productSizes->getBustMax(),);

                            foreach ($product->getProductItems() as $productItem) {

                                $array[$product->getName()]['product_item'][$productColor->getTitle()][$productItem->getLineNumber()] = array('size_title' => $productItem->getLineNumber(), 'product_color_title' => $productColor->getTitle(), 'image' => $productItem->getImage());
                            }
                        }
                    }
                }
            }
        }
        $yaml = Yaml::dump($array, 40);
        return @file_put_contents('../app/config/config_ltf_product.yml', $yaml);
    }
#---------------------Product Download-----------------------------------------#
  public function productDownloadsAction($id){
  return new Response($this->get('admin.helper.product')->zipDownload($id));
    }
  
    
}

