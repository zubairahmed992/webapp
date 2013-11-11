<?php

namespace LoveThatFit\RetailerAdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use LoveThatFit\AdminBundle\Entity\Retailer;
use LoveThatFit\AdminBundle\Form\Type\RetailerProductDetailType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ProductColor;
use LoveThatFit\AdminBundle\Entity\ProductSize;
use LoveThatFit\AdminBundle\Entity\ProductItem;
use LoveThatFit\AdminBundle\Entity\ProductSizeMeasurement;
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
use LoveThatFit\AdminBundle\Form\Type\ProductSizeMeasurementType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Exception\ParseException;
use LoveThatFit\AdminBundle\ImageHelper;
use ZipArchive;
use LoveThatFit\AdminBundle\Form\Type\ProductItemRawImageType;

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
        $productSpecification=$this->get('admin.helper.product.specification')->getProductSpecification();
        $productSpecificationHelper = $this->get('admin.helper.product.specification');
        $clothingTypes=$this->get('admin.helper.product.specification')->getWomenClothingType();
        $productForm = $this->createForm(new RetailerProductDetailType($productSpecificationHelper));     
        return $this->render('LoveThatFitRetailerAdminBundle:Product:new_product.html.twig', array(
                    'form' => $productForm->createView(),'productSpecification'=>$productSpecification,                    
        ));
    }
    
    public function retailerProductNewCreateAction(Request $request)
    {        
      // return new Response(json_encode($request->request->all()));
        
        $productSpecification=$this->get('admin.helper.product.specification')->getProductSpecification();
        $productSpecificationHelper = $this->get('admin.helper.product.specification');
        $em = $this->getDoctrine()->getManager();
        $entity = new Product();
        $form = $this->createForm(new RetailerProductDetailType($this->get('admin.helper.product.specification')), $entity);
       
        $form->bindRequest($request);
        $id = $this->get('security.context')->getToken()->getUser()->getId();                 
        $retailerentity = $this->get('admin.helper.retailer.user')->find($id);       
        $retailer = $this->getRetailer($retailerentity->getRetailer()->getId());
        if (!$retailer) {
            $this->get('session')->setFlash('warning', 'Unable to find Retailer.');
        }
       // return new Response(json_encode($request));
      //  if ($form->isValid()) {            
            
           
           $data=$request->request->all();
            if(isset($data['product']['styling_type'])){$entity->setStylingType($data['product']['styling_type']);}
            if(isset($data['product']['hem_length'])){$entity->setHemLength($data['product']['hem_length']);}
            if(isset($data['product']['neckline'])){$entity->setNeckLine($data['product']['neckline']);}
            if(isset($data['product']['sleeve_styling'])){$entity->setSleeveStyling($data['product']['sleeve_styling']);}
            if(isset($data['product']['rise'])){$entity->setRise($data['product']['rise']);}
            $entity->setCreatedAt(new \DateTime('now'));
            $entity->setUpdatedAt(new \DateTime('now'));
            $entity->setRetailer($retailer); 
            $retailer->addProduct($entity);
            $em->persist($retailer);
            $em->persist($entity);            
            $em->flush();
            $this->get('session')->setFlash('success', 'Retailer Product Detail has been Created.');
       return $this->redirect($this->generateUrl('retailer_admin_product_detail_show', array('id' => $entity->getId(),'product'=>$entity)));  
          
        
      
    }
    
    //------------------------------------------------------------------------------

    public function productDetailEditAction($id) {
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->findOneById($id);
$productSpecification=$this->get('admin.helper.product.specification')->getProductSpecification();
        $form = $this->createForm(new RetailerProductDetailType($this->get('admin.helper.product.specification')), $entity);
        $deleteForm = $this->getDeleteForm($id);

        return $this->render('LoveThatFitRetailerAdminBundle:Product:product_detail_edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity,
                    'productSpecification' =>$productSpecification  
                    ));
    }

    //------------------------------------------------------------------------------

    public function productDetailUpdateAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->find($id);
        if (!$entity) {
             
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }
        
        $form = $this->createForm(new RetailerProductDetailType($this->get('admin.helper.product.specification')), $entity);
        $form->bind($request);
        $gender = $entity->getGender();
        $clothing_type=$entity->getClothingType()->getTarget();
          if ($gender == 'M' and $clothing_type == 'Dress') {
            $form->get('gender')->addError(new FormError('Dresses can not be selected  for Male'));

            $this->get('session')->setFlash('warning', 'Dresses can not be selected for male.');
            return $this->render('LoveThatFitRetailerAdminBundle:Product:product_detail_new.html.twig', array(
                        'form' => $form->createView(),
            ));
        }
      
             $data=$request->request->all();
             if(isset($data['product']['styling_type'])){$entity->setStylingType($data['product']['styling_type']);}
            if(isset($data['product']['hem_length'])){$entity->setHemLength($data['product']['hem_length']);}
            if(isset($data['product']['neckline'])){$entity->setNeckLine($data['product']['neckline']);}
            if(isset($data['product']['sleeve_styling'])){$entity->setSleeveStyling($data['product']['sleeve_styling']);}
            if(isset($data['product']['rise'])){$entity->setRise($data['product']['rise']);}
            $entity->setUpdatedAt(new \DateTime('now'));
            $em->persist($entity);
            $em->flush();
            $this->get('session')->setFlash('success', 'Product Detail has been Update.');
            return $this->redirect($this->generateUrl('retailer_admin_product_detail_show', array('id' => $entity->getId(),'product'=>$entity)));
        
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
            return $this->redirect($this->generateUrl('retailer_product'));
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
        $product_limit =$this->get('admin.helper.product')->getRecordsCountWithCurrentProductLimit($id);
        $page_number=ceil($this->get('admin.helper.utility')->getPageNumber($product_limit[0]['id']));
        if($page_number==0){
            $page_number=1;
        }
       
        if (!$product) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }

        return $this->render('LoveThatFitRetailerAdminBundle:Product:product_detail_show.html.twig', array(
                    'product' => $product,
                    'page_number'=>$page_number,
        ));
    }
#-------------Clothing type base on Gender-----------------------------------#
public function retailerProductGenderBaseClothingTypeAction(Request $request){
    $target_array = $request->request->all();
    $gender=$target_array['gender'];
    return new response(json_encode($this->get('admin.helper.clothingtype')->findByGender($gender)));
    
}
    //------------------------------------------------------------------------------
    /*     * ************************* PRODUCT DETAIL COLOR ************************************************** */
//------------------------------------------------------------------------------



    public function productDetailColorAddNewAction($id) {
        $entity = $this->getProduct($id);
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }

        $colorform = $this->createForm(new ProductColorType());

        $imageUploadForm = $this->createForm(new ProductColorImageType());
        $patternUploadForm = $this->createForm(new ProductColorPatternType());
        return $this->render('LoveThatFitRetailerAdminBundle:Product:product_detail_show.html.twig', array(
                    'product' => $entity,
                    'colorform' => $colorform->createView(),
                    'imageUploadForm' => $imageUploadForm->createView(),
                    'patternUploadForm' => $patternUploadForm->createView(),
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

            $this->get('admin.helper.productcolor')->uploadSave($productColor);

            if ($productColor->displayProductColor or $product->displayProductColor == NULL) {
                $this->createDisplayDefaultColor($product, $productColor); //--add  product  default color 
            }
            $this->createSizeItem($product, $productColor, $colorform->getData()->getSizes()); //--creating sizes & item records
            $this->get('session')->setFlash('success', 'Product Detail color has been created.');
            return $this->redirect($this->generateUrl('retailer_admin_product_detail_show', array('id' => $id)));
        } else {
            $this->get('session')->setFlash('warning', 'Product Detail color cannot been created.');
        }
    }

    //--------------------------------------------------------------

    public function productDetailColorEditAction($id, $color_id, $temp_img_path = null) {

        $product = $this->getProduct($id);

        if (!$product) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }

        $productColor = $this->getProductColor($color_id);
        $sizeTitle = $productColor->getSizeTitleArray();

        $colorform = $this->createForm(new ProductColorType(), $productColor);
        $colorform->get('sizes')->setData($sizeTitle);

        $imageUploadForm = $this->createForm(new ProductColorImageType(), $productColor);
        $patternUploadForm = $this->createForm(new ProductColorPatternType(), $productColor);
        return $this->render('LoveThatFitRetailerAdminBundle:Product:product_detail_show.html.twig', array(
                    'product' => $product,
                    'colorform' => $colorform->createView(),
                    'color_id' => $color_id,
                    'imageUploadForm' => $imageUploadForm->createView(),
                    'patternUploadForm' => $patternUploadForm->createView(),
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

            $this->get('admin.helper.productcolor')->uploadSave($productColor);

            if ($productColor->displayProductColor or $product->displayProductColor == NULL) {
                $this->createDisplayDefaultColor($product, $productColor); //--add  product  default color 
            }

            $this->createSizeItem($product, $productColor, $colorForm->getData()->getSizes());

            $this->get('session')->setFlash(
                    'success', 'Product Color Detail has been updated!'
            );

            return $this->redirect($this->generateUrl('retailer_admin_product_detail_show', array('id' => $id)));
        } else {

            $this->get('session')->setFlash(
                    'warning', 'Unable to update Product Color Detail!'
            );

            $imageUploadForm = $this->createForm(new ProductColorImageType(), $productColor);
            return $this->render('LoveThatFitRetailerAdminBundle:Product:product_detail_show.html.twig', array(
                        'product' => $product,
                        'colorform' => $colorForm->createView(),
                        'color_id' => $color_id,
            ));
        }
    }

//----------------------------------------------------


    public function productColorTemporaryImageUploadAction(Request $request, $id) {

        $product = $this->getProduct($id);

        if (!$product) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }
        $productColor = new ProductColor();
        $productColor->setProduct($product);
        $colorImageForm = $this->createForm(new ProductColorImageType(), $productColor);
        $colorImageForm->bind($request);
        $temp = $productColor->uploadTemporaryImage();
        
        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . "/" . $productColor->getWebPath() . $temp['image_url'];
        $data = array('image_name' => $temp['image_name'],
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
            $defaultcolor = $this->getDefaultColorById($productColor);
            if (!$defaultcolor) {
                $em->remove($productColor);
                $em->flush();
                $this->get('session')->setFlash('success', 'Product Detail color has been Deleted.');
                return $this->redirect($this->generateUrl('retailer_admin_product_detail_show', array('id' => $id)));
            } else {
                $defaultcolor = null;
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
                return $this->redirect($this->generateUrl('retailer_admin_product_detail_show', array('id' => $id)));
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->setFlash(
                    'warning', 'This Product Color  cannot be deleted!'
            );
            // return $this->redirect($this->generateUrl('admin_products'));
            return $this->redirect($this->generateUrl('retailer_admin_product_detail_show', array('id' => $id)));
        }
    }

    /*     * *********************** PRODUCT DETAIL SIZE ************************************************** */

    //--------------------------------------------------------------

    public function productDetailSizeEditAction($id, $size_id) {
        $product = $this->getProduct($id);
        if (!$product) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }
        $product_size = $this->get('admin.helper.productsizes')->findMeasurementArray($size_id);
        //return new Response(var_dump($product_size));
        $clothingType = strtolower($product->getClothingType()->getName());
        $clothingTypeAttributes = $this->get('admin.helper.product.specification')->getAttributesFor($clothingType);
        $size_measurements = $this->get('admin.helper.productsizes')->checkAttributes($clothingTypeAttributes, $product_size);        
        $form = $this->createForm(new ProductSizeMeasurementType());        
        return $this->render('LoveThatFitRetailerAdminBundle:Product:retailer_product_detail_show.html.twig', array(
                    'product' => $product,
                    'size_measurements' => $size_measurements,
                     'size_id'=>$size_id,  
                     'form'=>$form->createView(),
                     'addform'=>$form->createView(),
                     'product_size' => $product_size,                      
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

        if ($product->getClothingType()->getTarget() == "Top" and $product->getGender() == 'M') {
            $sizeForm = $this->createForm(new ProductSizeManTopType(), $this->getProductSize($size_id));
        }
        if ($product->getClothingType()->getTarget() == "Top" and $product->getGender() == 'F') {
            $sizeForm = $this->createForm(new ProductSizeWomenTopType(), $this->getProductSize($size_id));
        }
        if ($product->getClothingType()->getTarget() == "Bottom" and $product->getGender() == 'M') {
            $sizeForm = $this->createForm(new ProductSizeManBottomType(), $this->getProductSize($size_id));
        }
        if ($product->getClothingType()->getTarget() == "Bottom" and $product->getGender() == 'F') {
            $sizeForm = $this->createForm(new ProductSizeWomenBottomType(), $this->getProductSize($size_id));
        }
        if ($product->getClothingType()->getTarget() == "Dress" or $product->getClothingType()->getTarget() == "dress" and $product->getGender() == 'F') {
            $sizeForm = $this->createForm(new ProductSizeWomenDressType(), $this->getProductSize($size_id));
        }


        //$sizeform = $this->createForm(new ProductSizeType(), $this->getProductSize($size_id));

        $sizeForm->bind($request);

        if ($sizeForm->isValid()) {
            $em->persist($entity_size);
            $em->flush();
            $this->get('session')->setFlash('success', 'Product Detail size has been update.');
            return $this->redirect($this->generateUrl('retailer_admin_product_detail_show', array('id' => $id)));
        } else {
            $this->get('session')->setFlash('warning', 'Please Try again');
            return $this->render('LoveThatFitRetailerAdminBundle:Product:product_detail_show.html.twig', array(
                        'product' => $product,
                        'sizeform' => $sizeForm->createView(),
                        'size_id' => $size_id,
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
        return $this->redirect($this->generateUrl('retailer_admin_product_detail_show', array('id' => $id)));
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
        $itemrawimageform = $this->createForm(new ProductItemRawImageType(), $this->getProductItem($item_id));
        return $this->render('LoveThatFitRetailerAdminBundle:Product:product_detail_show.html.twig', array(
                    'product' => $entity,
                    'itemform' => $itemform->createView(),
                    'item_id' => $item_id,
                    'itemrawimageform'=>$itemrawimageform->createView(),
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
            return $this->render('LoveThatFitRetailerAdminBundle:Product:product_detail_show.html.twig', array(
                        'product' => $entity,
                        'itemform' => $itemform->createView(),
            ));
        } else {

            $this->get('session')->setFlash('warning', 'Unable to Product Detail Item');

            return $this->render('LoveThatFitRetailerAdminBundle:Product:product_detail_show.html.twig', array(
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
        return $this->redirect($this->generateUrl('retailer_admin_product_detail_show', array('id' => $id)));
    }
 #----------------------Method for raw image edit -----------------------------#
  public function productDetailItemRawImageEditAction(Request $request, $id, $item_id){
      
     
      $entity = $this->getProduct($id);
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }

        $em = $this->getDoctrine()->getManager();
        $entity_item = $em->getRepository('LoveThatFitAdminBundle:ProductItem')->find($item_id);
        if (!$entity_item) {
            throw $this->createNotFoundException('Unable to find Product Item.');
        }
        
        $itemrawimageform = $this->createForm(new ProductItemRawImageType(),$entity_item);
        $itemrawimageform->bind($request);
        $entity_item->uploadRawImage(); //----- file upload method 
        $em->persist($entity_item);
         $em->flush();
       $this->get('session')->setFlash('success', 'Product item updated  Successfully');
      
        return $this->redirect($this->generateUrl('retailer_admin_product_detail_show', array('id' => $id))); 
  }
#-------------Product Raw Item Downloading ------------------------------------#
public function productDetailItemRawImageDownloadAction(Request $request, $id, $item_id){
    return new response($this->get('admin.helper.productitem')->rawImageDownload($item_id));
}
#--------------Raw image Deleteing --------------------------------------------#
public function productDetailItemRawImageDeleteAction(Request $request, $id, $item_id) {
        
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:ProductItem');
        $product = $repository->find($item_id);
        
         $old_image_path = $product->getRawImageWebPath();
         if (is_readable($old_image_path)) {
           @unlink($old_image_path);
       }
       
        $product->setRawImage('');
        $em->persist($product);
        $em->flush();
        $this->get('session')->setFlash('success', 'Raw Image Successfully Deleted');
        return $this->redirect($this->generateUrl('retailer_admin_product_detail_show', array('id' => $id)));
}


//------------------------------Product Size Mesuremnt-----------------------
public function createProductSizeMeasurementAction($id,$size_id)
{
    $product_size=$this->get('admin.helper.productsizes')->find($size_id);
        if(!$product_size)
        {
            throw $this->createNotFoundException('Unable to find Product Size.');
        }
        $entity = new ProductSizeMeasurement();
        $entity->setVerticalStretch($product_size->getProduct()->getVerticalStretch());
        $entity->setHorizontalStretch($product_size->getProduct()->getHorizontalStretch());
        $form = $this->createForm(new ProductSizeMeasurementType(),$entity);
        $deleteForm = $this->getDeleteForm($size_id);
        
        return $this->render('LoveThatFitRetailerAdminBundle:Product:productSizeMeasurementForm.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'product_size' => $product_size,                 
            )
                );
}



public function productSizeMeasurementCreateAction($id,$size_id,$title)
    {        
        $product_size=$this->get('admin.helper.productsizes')->find($size_id);
        if(!$product_size)
        {
            throw $this->createNotFoundException('Unable to find Product Size.');
        }
        $entity = new ProductSizeMeasurement();
        $entity->setVerticalStretch($product_size->getProduct()->getVerticalStretch());
        $entity->setHorizontalStretch($product_size->getProduct()->getHorizontalStretch());
        $form = $this->createForm(new ProductSizeMeasurementType(),$entity);
        $deleteForm = $this->getDeleteForm($size_id);        
        return $this->render('LoveThatFitRetailerAdminBundle:Product:productSizeMeasurement.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'product_size' => $product_size,
                    'title'=>$title,
                   )
                );
                  
         
    }
    
    
    public function productSizeMeasurementupdateAction(Request $request,$size_id,$title)
    {
        $product_size=$this->get('admin.helper.productsizes')->find($size_id);
        if(!$product_size)
        {
            throw $this->createNotFoundException('Unable to find Product Size.');
        }
        $em = $this->getDoctrine()->getManager();
        $entity = new ProductSizeMeasurement();         
        $form = $this->createForm(new ProductSizeMeasurementType(), $entity);
        if ($this->getRequest()->getMethod() == 'POST') {
        $form->bindRequest($request);
        $entity->setTitle($title);
        $entity->setVerticalStretch($product_size->getProduct()->getVerticalStretch());
        $entity->setHorizontalStretch($product_size->getProduct()->getHorizontalStretch());
        $entity->setProductSize($product_size); 
        $product_size->addProductSizeMeasurement($entity);
        $em->persist($product_size);
        $em->persist($entity);            
        $em->flush();  
        $this->get('session')->setFlash('success', 'Retailer Product Size Measurement Detail has been Created.');
        }    
       $id=$product_size->getProduct()->getId();
       $entity = $this->getProduct($id);             
        return $this->redirect($this->generateUrl('retailer_admin_product_detail_size_edit', array(
            'product'=>$entity,
            'id' => $id,
            'size_id'=>$size_id
         )));
        
        
        
        
        /*
        
        return $this->render('LoveThatFitRetailerAdminBundle:Product:productSizeMeasurement.html.twig', array(
                    'form' => $form->createView(),                    
                    'product_size' => $product_size,
                    'title'=>$title ));
          
         */
    }
    
    
    public function productSizeMeasurementEditAction($size_id,$measurement_id,$title)
    {
        $product_size=$this->get('admin.helper.productsizes')->find($size_id);
        if(!$product_size)
        {
            throw $this->createNotFoundException('Unable to find Product Size.');
        }
        $product_size_measurement=$this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ProductSizeMeasurement')
                ->find($measurement_id);
        $form = $this->createForm(new ProductSizeMeasurementType(),$product_size_measurement);
        $deleteForm = $this->getDeleteForm($size_id);        
        return $this->render('LoveThatFitRetailerAdminBundle:Product:productSizeMeasurement_edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'product_size' => $product_size,
                    'productSizeMeasurement'=>$product_size_measurement,
                    'title'=>$title,
                ));
        
    }
    
    public function productSizeMeasurementEditUpdateAction(Request $request,$size_id,$measurement_id,$title)
    {
        $product_size=$this->get('admin.helper.productsizes')->find($size_id);
        if(!$product_size)
        {
            throw $this->createNotFoundException('Unable to find Product Size.');
        }
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ProductSizeMeasurement')
                ->find($measurement_id);
        $form = $this->createForm(new ProductSizeMeasurementType(), $entity);
        $form->bindRequest($request); 
        if ($form->isValid()) {
        $em->persist($entity);            
        $em->flush();  
        $this->get('session')->setFlash('success', 'Retailer Product Size Measurement Detail has been Updated.');
        }        
        
      $id=$product_size->getProduct()->getId();
      $entity = $this->getProduct($id);             
        return $this->redirect($this->generateUrl('retailer_admin_product_detail_size_edit', array(
            'product'=>$entity,
            'id' => $id,
            'size_id'=>$size_id
         )));
        
        /*
        return $this->render('LoveThatFitRetailerAdminBundle:Product:productSizeMeasurement.html.twig', array(
                    'form' => $form->createView(),                    
                    'product_size' => $product_size,
                    'title'=>$title,
                ));
         
         */
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
    public function createDisplayDefaultColor($product, $productColor) {

        $em = $this->getDoctrine()->getManager();
        $product->setDisplayProductColor($productColor);
        $em->persist($product);
        $em->flush();
    }

    //----------------------Products Stats-----------------
    public function productStatsAction() {
        $productObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product');
        $products = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->findListAllProduct();
        $rec_count = count($productObj->countAllRecord());

        $entity = $this->getProductByBrand();
        return $this->render('LoveThatFitAdminBundle:Product:product_stats.html.twig', array(
                    'total_products' => $rec_count,
                    'femaleProduct' => $this->countProductsByGender('f'),
                    'maleProduct' => $this->countProductsByGender('m'),
                    'topProduct' => $this->countProductsByType('Top'),
                    'bottomProduct' => $this->countProductsByType('Bottom'),
                    'dressProduct' => $this->countProductsByType('Dress'),
                    'brandproduct' => $entity,
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

    private function countProductsByGender($gender) {
        $em = $this->getDoctrine()->getManager();
        $ProductTypeObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->findPrductByGender($gender);
        $rec_count = count($ProductTypeObj->findPrductByGender($gender));
        return $rec_count;
    }

    private function countProductsByType($target) {
        $em = $this->getDoctrine()->getManager();
        $ProductTypeObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->findPrductByType($target);
        $rec_count = count($ProductTypeObj->findPrductByType($target));
        return $rec_count;
    }

    private function getProductByBrand() {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product')
                ->findPrductByBrand();
        return $entity;
    }

    //---------------------------------------------------------------------


    private function getDefaultColorById($product_color) {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product')
                ->findDefaultProductByColorId($product_color);
        return $entity;
    }
//------------------------------------------------------------------------------------
    private function productSaveYaml() {
        $entity = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product')
                ->findAll();
        $array = array();
        $products = array();
        foreach ($entity as $product) {
            $brand_array = $this->findBrand($product->getBrand()->getId());
            $clothing_type_array = $this->findClothingType($product->getClothingType()->getId());
            $products['products'][$brand_array->getName()][$clothing_type_array->getName()][$product->getName()] = array('description' => $product->getDescription(), 'gender' => $product->getGender(), 'adjustment' => $product->getAdjustment());
            foreach ($product->getProductColors() as $productColor) {

                if ($product->getDisplayProductColor()->getId() == $productColor->getId()) {
                    $default = true;
                } else {
                    $default = false;
                }
                $products['products'][$brand_array->getName()][$clothing_type_array->getName()][$product->getName()] ['product_color'][$productColor->getTitle()] = array('title' => $productColor->getTitle(), 'image' => $productColor->getImage(), 'pattern' => $productColor->getPattern(), 'default' => $default);
                foreach ($product->getProductSizes() as $productSizes) {
                    $products['products'][$brand_array->getName()][$clothing_type_array->getName()][$product->getName()]['product_sizes'][$productSizes->getTitle()] = array('title' => $productSizes->getTitle(), 'inseam_min' => $productSizes->getInseamMin(), 'inseam_max' => $productSizes->getInseamMax(), 'hip_min' => $productSizes->getHipMin(), 'hip_max' => $productSizes->getHipMax(), 'waist_min' => $productSizes->getWaistMin(), 'waist_max' => $productSizes->getWaistMax(), 'bust_min' => $productSizes->getBustMin(), 'bust_max' => $productSizes->getBustMax(),);

                    $pi = $this->get('admin.helper.product_item')->findByColorSize($productColor->getId(), $productSizes->getId());
                    if ($pi) {
                        $products['products'][$brand_array->getName()][$clothing_type_array->getName()][$product->getName()]['product_item'][$productColor->getTitle()][$productSizes->getTitle()] = array('size_title' => $productSizes->getTitle(), 'product_color_title' => $productColor->getTitle(), 'image' => $pi->getImage());
                    }
                }
            }
        }
        //return $products;
        $yaml = Yaml::dump($products, 40);
        return @file_put_contents('../app/config/config_ltf_product.yml', $yaml);
    }

    #----------------------------------------------------------------------------# 

    public function findBrand($id) {
        return $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Brand')->find($id);
    }

    #---------------------------------------------------------------------------# 

    public function findClothingType($id) {
        return $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:ClothingType')->find($id);
    }

#---------------------Product Download-----------------------------------------#
    public function productDetailDownloadAction($id){
     return new Response($this->get('admin.helper.product')->zipDownload($id));
    }
#--------------------Multiple Iamge Download as Zip----------------------------#
    public function productDetailZipDownloadAction(Request $request)
    {
       $data = $request->request->all();
       return new Response($this->get('admin.helper.product')->zipMultipleDownload($data));
    }
    
  #-----------------------------------------------------------------------------#
  
#-------------------Searching Resulting----------------------------------------#
 public function productSeachResultAction(Request $request){
  $data = $request->request->all();
  
  $productResult=$this->get('admin.helper.product')->searchProduct($data);
 //return new response(json_encode($productResult));
  
 return $this->render('LoveThatFitAdminBundle:Product:searchResult.html.twig',$productResult);    
 }
#------------------------------------------------------------------------------#
 public function productSeachCategoryAction(Request $request){
     
    $target_array = $request->request->all();
    if($target_array){
    $result= $this->get('admin.helper.product')->searchCategory($target_array);
    return new response(json_encode($result));
    }else{
       return new response(json_encode("null"));
    }
     
 }
    
    
    private function getBrand($id) {
        $em = $this->getDoctrine()->getManager();
        $brand = $em->getRepository('LoveThatFitAdminBundle:Brand')->find($id);

        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Unable to find Brand.');
        }
        return $brand;
    }
    
    //---------------------------------------------------------------------
    private function getDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm();
    }
     private function getRetailer($id) {
        return $this->getDoctrine()
                        ->getRepository('LoveThatFitAdminBundle:Retailer')
                        ->find($id);
    }
}

