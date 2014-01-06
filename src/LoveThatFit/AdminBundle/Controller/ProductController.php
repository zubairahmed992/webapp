<?php

namespace LoveThatFit\AdminBundle\Controller;

use LoveThatFit\AdminBundle\Entity\ProductCSVHelper;
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
use LoveThatFit\AdminBundle\Entity\ProductSizeMeasurement;
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
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use LoveThatFit\AdminBundle\ImageHelper;
use ZipArchive;
use LoveThatFit\AdminBundle\Form\Type\ProductItemRawImageType;

class ProductController extends Controller {

//---------------------------------------------------------------------

    public function indexAction($page_number, $sort = 'id') {
        //$this->productSaveYaml();

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
            $this->get('session')->setFlash('warning', 'Unable to find Brand.');
        }
        return $brand;
    }

    /*     * *************************************************************************
     * ************************* PRODUCT DETAIL ********************************
     * *********************************************************************** */

    public function productDetailNewAction() {


        $productSpecificationHelper = $this->get('admin.helper.product.specification');
        $clothingTypes = $this->get('admin.helper.product.specification')->getWomenClothingType();
        $productForm = $this->createForm(new ProductDetailType($productSpecificationHelper));
        return $this->render('LoveThatFitAdminBundle:Product:product_detail_new.html.twig', array(
                    'form' => $productForm->createView(),
                    'productSpecification' => $this->get('admin.helper.product.specification')->getProductSpecification()
                ));
    }

#-----------------------------Product Detail ----------------------------------#

    public function productDetailCreateAction(Request $request) {

        $data = $request->request->all();
        $productSpecificationHelper = $this->get('admin.helper.product.specification');
        $entity = new Product();
        $form = $this->createForm(new ProductDetailType($this->get('admin.helper.product.specification')), $entity);
        $form->bindRequest($request);
        $productArray = $this->get('admin.helper.product')->productDetailArray($data, $entity);
        $this->get('session')->setFlash($productArray['message_type'], $productArray['message']);
        return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $entity->getId())));
    }

    #--------------------Method for Edit Product Detail----------------------------#

    public function productDetailEditAction($id) {
        $entity = $this->get('admin.helper.product')->find($id);
        $productSpecification = $this->get('admin.helper.product.specification')->getProductSpecification();
        $form = $this->createForm(new ProductDetailType($this->get('admin.helper.product.specification')), $entity);
        $deleteForm = $this->getDeleteForm($id);

        return $this->render('LoveThatFitAdminBundle:Product:product_detail_edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity,
                    'productSpecification' => $productSpecification,
                    'fit_priority' => $entity->getFitPriority(),
                    'fabric_content' => $entity->getFabricContent(),
                    'garment_detail' => $entity->getGarmentDetail(),
                ));
    }

#------------------Product Update Method---------------------------------------#

    public function productDetailUpdateAction(Request $request, $id) {
        $entity = $this->get('admin.helper.product')->find($id);
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }
        $form = $this->createForm(new ProductDetailType($this->get('admin.helper.product.specification')), $entity);
        $form->bind($request);
        $data = $request->request->all();
        $productArray = $this->get('admin.helper.product')->productDetailArray($data, $entity);
        $this->get('session')->setFlash($productArray['message_type'], $productArray['message']);
        return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $entity->getId(), 'product' => $entity, 'fit_priority' => $entity->getFitPriority())));
    }

#---------------Clothing type base on Gender-----------------------------------#

    public function productGenderBaseClothingTypeAction(Request $request) {
        $target_array = $request->request->all();
        $gender = $target_array['gender'];
        return new response(json_encode($this->get('admin.helper.clothingtype')->findByGender($gender)));
    }

#------------Clothing type attribute base on clothing type --------------------#

    public function productClothingTypeAttributeAction(Request $request) {
        $target_array = $request->request->all();
        $clothingTypeAttributes = $this->get('admin.helper.product')->productClothingTypeAttribute($target_array);
        return new response(json_encode($clothingTypeAttributes));
    }

#-------------------------------Product Delete --------------------------------#

    public function productDetailDeleteAction($id) {
        $productArray = $this->get('admin.helper.product')->productDelete($id);
        $this->get('session')->setFlash($productArray['message_type'], $productArray['message']);
        return $this->redirect($this->generateUrl('admin_products'));
    }

#----------------------Proudct Detail------------------------------------------#

    public function productDetailShowAction($id) {
        $product = $this->getProduct($id);
        $product_limit = $this->get('admin.helper.product')->getRecordsCountWithCurrentProductLimit($id);
        $page_number = ceil($this->get('admin.helper.utility')->getPageNumber($product_limit[0]['id']));
        if ($page_number == 0) {
            $page_number = 1;
        }
        if (!$product) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }
        return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
                    'product' => $product,
                    'page_number' => $page_number,
                ));
    }

#------------------------ PRODUCT DETAIL COLOR --------------------------------#

    public function productDetailColorAddNewAction($id) {
        $entity = $this->getProduct($id);
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }
        $sizes = $this->get('admin.helper.product')->productDetailColorAdd($entity);
        // return new response(json_encode($sizes));
        $colorform = $this->createForm(new ProductColorType($sizes['petite'], $sizes['regular'], $sizes['tall'], $sizes['women_waist']));
        $imageUploadForm = $this->createForm(new ProductColorImageType());
        $patternUploadForm = $this->createForm(new ProductColorPatternType());
        return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
                    'product' => $entity,
                    'colorform' => $colorform->createView(),
                    'imageUploadForm' => $imageUploadForm->createView(),
                    'patternUploadForm' => $patternUploadForm->createView(),
                ));
    }

#--------------------PRODUCT COLOR CREATE--------------------------------------#

    public function productDetailColorCreateAction(Request $request, $id) {

        $product = $this->getProduct($id);
        $productColor = new ProductColor();
        $productColor->setProduct($product);
        $sizes = $this->get('admin.helper.product')->productDetailColorAdd($product);
        //return new response(json_encode($sizes));
        $colorform = $this->createForm(new ProductColorType($sizes['petite'], $sizes['regular'], $sizes['tall'], $sizes['women_waist']), $productColor);
        $colorform->bind($request);
        if ($colorform->isValid()) {

            $this->get('admin.helper.productcolor')->uploadSave($productColor);

            if ($productColor->displayProductColor or $product->displayProductColor == NULL) {
                $this->createDisplayDefaultColor($product, $productColor); //--add  product  default color 
            }
            $this->createSizeItemForBodyTypes($product, $productColor, $colorform->getData()); //--creating sizes & item records
            $this->get('session')->setFlash('success', 'Product Detail color has been created.');
            return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $id)));
        } else {
            $this->get('session')->setFlash('warning', 'Product Detail color cannot been created.');
        }
    }

#----------------------------Product Color Edit--------------------------------#

    public function productDetailColorEditAction($id, $color_id, $temp_img_path = null) {
        $product = $this->getProduct($id);
        if (!$product) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }
        $productColor = $this->getProductColor($color_id);
        $sizeTitle = $this->get('admin.helper.productsizes')->getSizeArrayBaseOnProduct($id);
        // return new response(json_encode($sizeTitle));
        $sizes = $this->get('admin.helper.product')->productDetailColorAdd($product);
        $colorform = $this->createForm(new ProductColorType($sizes['petite'], $sizes['regular'], $sizes['tall'], $sizes['women_waist']), $productColor);
        if (isset($sizeTitle['Petite'])) {
            $colorform->get('petiteSizes')->setData($sizeTitle['Petite']);
        }
        if (isset($sizeTitle['Regular'])) {
            $colorform->get('regularSizes')->setData($sizeTitle['Regular']);
        }
        if (isset($sizeTitle['Tall'])) {
            $colorform->get('tallSizes')->setData($sizeTitle['Tall']);
        }
        if (isset($sizeTitle['Waist'])) {
            $colorform->get('womenWaistSizes')->setData($sizeTitle['Waist']);
        }
        $imageUploadForm = $this->createForm(new ProductColorImageType(), $productColor);
        $patternUploadForm = $this->createForm(new ProductColorPatternType(), $productColor);
        return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
                    'product' => $product,
                    'colorform' => $colorform->createView(),
                    'color_id' => $color_id,
                    'imageUploadForm' => $imageUploadForm->createView(),
                    'patternUploadForm' => $patternUploadForm->createView(),
                ));
    }

#-------------------------Product color Update---------------------------------#

    public function productDetailColorUpdateAction(Request $request, $id, $color_id) {
        $product = $this->getProduct($id);
        if (!$product) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }

        $productColor = $this->getProductColor($color_id);
        $sizes = $this->get('admin.helper.product')->productDetailColorAdd($product);
        $colorform = $this->createForm(new ProductColorType($sizes['petite'], $sizes['regular'], $sizes['tall'], $sizes['women_waist']), $productColor);
        $colorform->bind($request);
        if ($colorform->isValid()) {
            $this->get('admin.helper.productcolor')->uploadSave($productColor);
            if ($productColor->displayProductColor or $product->displayProductColor == NULL) {
                $this->createDisplayDefaultColor($product, $productColor); //--add  product  default color 
            }
            $this->createSizeItemForBodyTypes($product, $productColor, $colorform->getData());
            $this->get('session')->setFlash(
                    'success', 'Product Color Detail has been updated!'
            );

            return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $id)));
        } else {

            $this->get('session')->setFlash(
                    'warning', 'Unable to update Product Color Detail!'
            );

            $imageUploadForm = $this->createForm(new ProductColorImageType(), $productColor);
            return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
                        'product' => $product,
                        'colorform' => $colorForm->createView(),
                        'color_id' => $color_id,
                    ));
        }
    }

#---------------------Product Color Temporary ---------------------------------#

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

#--------------------------Product Color Delete--------------------------------#

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
                return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $id)));
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
                return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $id)));
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->setFlash(
                    'warning', 'This Product Color  cannot be deleted!'
            );
            return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $id)));
        }
    }

#--------------------Product Detail Size----------------------------------------#

    public function productDetailSizeEditAction($id, $size_id) {
        $product = $this->getProduct($id);
        if (!$product) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }
        $product_size = $this->get('admin.helper.productsizes')->findMeasurementArray($size_id);
        $productsize = $this->get('admin.helper.productsizes')->find($size_id);
        $clothingType = strtolower($product->getClothingType()->getName());
        $clothingTypeAttributes = $this->get('admin.helper.product.specification')->getAttributesFor($clothingType);
        $size_measurements = $this->get('admin.helper.productsizes')->checkAttributes($clothingTypeAttributes, $product_size);
        $form = $this->createForm(new ProductSizeMeasurementType());
        return $this->render('LoveThatFitAdminBundle:Product:product_size_detail_show.html.twig', array(
                    'product' => $product,
                    'size_measurements' => $size_measurements,
                    'size_id' => $size_id,
                    'form' => $form->createView(),
                    'addform' => $form->createView(),
                    'product_size' => $product_size,
                    'sizetitle' => $productsize->getTitle(),
                ));
    }

#-------------------Product Detail Size Update---------------------------------#

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

        $sizeForm->bind($request);
        if ($sizeForm->isValid()) {
            $em->persist($entity_size);
            $em->flush();
            $this->get('session')->setFlash('success', 'Product Detail size has been update.');
            return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $id)));
        } else {
            $this->get('session')->setFlash('warning', 'Please Try again');
            return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
                        'product' => $product,
                        'sizeform' => $sizeForm->createView(),
                        'size_id' => $size_id,
                    ));
        }
    }

#-----------------Product Detail Delete ---------------------------------------#

    public function productDetailSizeDeleteAction(Request $request, $id, $size_id) {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:ProductSize');
        $product = $repository->find($size_id);
        $em->remove($product);
        $em->flush();
        $this->get('session')->setFlash('success', 'Successfully Deleted');
        return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $id)));
    }

#----------------------Product Item Edit ---------------------------------------#

    public function productDetailItemEditAction($id, $item_id) {
        $entity = $this->getProduct($id);
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }
        $itemform = $this->createForm(new ProductItemType(), $this->getProductItem($item_id));
        $itemrawimageform = $this->createForm(new ProductItemRawImageType(), $this->getProductItem($item_id));
        return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
                    'product' => $entity,
                    'itemform' => $itemform->createView(),
                    'item_id' => $item_id,
                    'itemrawimageform' => $itemrawimageform->createView(),
                ));
    }

#-----------------------Product Detail Item Update-----------------------------#

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

#--------------------Product Detail Item Delete---------------------------------#

    public function productDetailItemDeleteAction(Request $request, $id, $item_id) {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:ProductItem');
        $product = $repository->find($item_id);
        $em->remove($product);
        $em->flush();
        $this->get('session')->setFlash('success', 'Successfully Deleted');
        return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $id)));
    }

#----------------------Method for raw image edit -----------------------------#

    public function productDetailItemRawImageEditAction(Request $request, $id, $item_id) {
        $entity = $this->getProduct($id);
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }

        $em = $this->getDoctrine()->getManager();
        $entity_item = $em->getRepository('LoveThatFitAdminBundle:ProductItem')->find($item_id);
        if (!$entity_item) {
            throw $this->createNotFoundException('Unable to find Product Item.');
        }

        $itemrawimageform = $this->createForm(new ProductItemRawImageType(), $entity_item);
        $itemrawimageform->bind($request);
        $entity_item->uploadRawImage(); //----- file upload method 
        $em->persist($entity_item);
        $em->flush();
        $this->get('session')->setFlash('success', 'Product item updated  Successfully');
        return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $id)));
    }

#-------------Product Raw Item Downloading ------------------------------------#

    public function productDetailItemRawImageDownloadAction(Request $request, $id, $item_id) {
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
        return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $id)));
    }

    //------------------------- Private methods ------------------------- 
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

#--------------------------------Products Stats--------------------------------#

    public function productStatsAction() {
        $productObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product');
        $products = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->findListAllProduct();
        $rec_count = count($productObj->countAllRecord());

        $entity = $this->getProductByBrand();
        return $this->render('LoveThatFitAdminBundle:Product:product_stats.html.twig', array(
                    'total_products' => $rec_count,
                    'femaleProduct' => $this->get('admin.helper.product')->countProductsByGender('f'),
                    'maleProduct' => $this->get('admin.helper.product')->countProductsByGender('m'),
                    'topProduct' => $this->get('admin.helper.product')->countProductsByType('Top'),
                    'bottomProduct' => $this->get('admin.helper.product')->countProductsByType('Bottom'),
                    'dressProduct' => $this->get('admin.helper.product')->countProductsByType('Dress'),
                    'brandproduct' => $entity,
                ));
    }

#---------------START OF CREATE SIZE ITEM -------------------------------------#

    private function createSizeItemForBodyTypes($product, $p_color, $all_sizes) {
        $this->get('admin.helper.productsizes')->createSizeItemForBodyTypes($product, $p_color, $all_sizes);
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

    public function productDetailDownloadAction($id) {
        $product = $this->get('admin.helper.product')->zipDownload($id);
        if ($product['status'] == '1') {

            $this->get('session')->setFlash('warning', 'Images not found');
            return $this->redirect($this->generateUrl('admin_products'));
        }
    }

#--------------------Multiple Iamge Download as Zip----------------------------#

    public function productDetailZipDownloadAction(Request $request) {
        $data = $request->request->all();
        return new Response($this->get('admin.helper.product')->zipMultipleDownload($data));
    }

    #-----------------------------------------------------------------------------#
#-------------------Searching Resulting----------------------------------------#

    public function productSeachResultAction(Request $request) {
        $data = $request->request->all();
        $productResult = $this->get('admin.helper.product')->searchProduct($data);
        return $this->render('LoveThatFitAdminBundle:Product:searchResult.html.twig', $productResult);
    }

#------------------------------------------------------------------------------#

    public function productSeachCategoryAction(Request $request) {

        $target_array = $request->request->all();
        if ($target_array) {
            $result = $this->get('admin.helper.product')->searchCategory($target_array);
            return new response(json_encode($result));
        } else {
            return new response(json_encode("null"));
        }
    }

    #-----------------------------------------------------------------------

    public function createProductSizeMeasurementAction($id, $size_id) {
        $product_size = $this->get('admin.helper.productsizes')->find($size_id);
        if (!$product_size) {
            throw $this->createNotFoundException('Unable to find Product Size.');
        }
        $entity = new ProductSizeMeasurement();
        $entity->setVerticalStretch($product_size->getProduct()->getVerticalStretch());
        $entity->setHorizontalStretch($product_size->getProduct()->getHorizontalStretch());
        $form = $this->createForm(new ProductSizeMeasurementType(), $entity);
        $deleteForm = $this->getDeleteForm($size_id);

        return $this->render('LoveThatFitAdminBundle:Product:productSizeMeasurementForm.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'product_size' => $product_size,
                        )
        );
    }

#-----------------------------------------------------------------------

    public function sizeMeasurementNewAction($id, $size_id, $title) {
        $product_size = $this->get('admin.helper.productsizes')->find($size_id);
        if (!$product_size) {
            throw $this->createNotFoundException('Unable to find Product Size.');
        }
        $id = $product_size->getProduct()->getId();
        $product = $this->getProduct($id);
        //$product=$this->get('admin.helper.product')->find($id);
        $entity = new ProductSizeMeasurement();
        $entity->setVerticalStretch($product_size->getProduct()->getVerticalStretch());
        $entity->setHorizontalStretch($product_size->getProduct()->getHorizontalStretch());
        $form = $this->createForm(new ProductSizeMeasurementType(), $entity);

        return $this->render('LoveThatFitAdminBundle:Product:_productSizeMeasurement.html.twig', array(
                    'form' => $form->createView(),
                    'product_size' => $product_size,
                    'title' => $title,
                    'id' => $id,
                    'productname' => $product->getName(),
                    'sizetitle' => $product_size->getTitle(),
                        )
        );
    }

#-----------------------------------------------------------------------

    public function productSizeMeasurementCreateAction($id, $size_id, $title) {
        $product_size = $this->get('admin.helper.productsizes')->find($size_id);
        if (!$product_size) {
            throw $this->createNotFoundException('Unable to find Product Size.');
        }
        $id = $product_size->getProduct()->getId();
        $product = $this->getProduct($id);
        //$product=$this->get('admin.helper.product')->find($id);
        $entity = new ProductSizeMeasurement();
        $entity->setVerticalStretch($product_size->getProduct()->getVerticalStretch());
        $entity->setHorizontalStretch($product_size->getProduct()->getHorizontalStretch());
        $form = $this->createForm(new ProductSizeMeasurementType(), $entity);
        $deleteForm = $this->getDeleteForm($size_id);
        return $this->render('LoveThatFitAdminBundle:Product:productSizeMeasurement.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'product_size' => $product_size,
                    'title' => $title,
                    'id' => $id,
                    'productname' => $product->getName(),
                    'sizetitle' => $product_size->getTitle(),
                        )
        );
    }

    public function productSizeMeasurementupdateAction(Request $request, $id, $size_id, $title) {
        $product_size = $this->get('admin.helper.productsizes')->find($size_id);
        if (!$product_size) {
            throw $this->createNotFoundException('Unable to find Product Size.');
        }
        $em = $this->getDoctrine()->getManager();
        $entity = new ProductSizeMeasurement();
        $entity->setVerticalStretch($product_size->getProduct()->getVerticalStretch());
        $entity->setHorizontalStretch($product_size->getProduct()->getHorizontalStretch());
        $form = $this->createForm(new ProductSizeMeasurementType(), $entity);
        $deleteForm = $this->getDeleteForm($size_id);
        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($request);
            $entity->setTitle($title);
            $entity->setProductSize($product_size);
            $product_size->addProductSizeMeasurement($entity);
            $em->persist($product_size);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->setFlash('success', 'Product Size Measurement Detail has been Created.');
            $id = $product_size->getProduct()->getId();
            $product = $this->getProduct($id);
            return $this->redirect($this->generateUrl('admin_product_detail_size_edit', array(
                                'product' => $product,
                                'id' => $id,
                                'size_id' => $size_id,
                                'productname' => $product->getName(),
                                'sizetitle' => $product_size->getTitle(),
                            )));
        }


        /*

          return $this->render('LoveThatFitRetailerAdminBundle:Product:productSizeMeasurement.html.twig', array(
          'form' => $form->createView(),
          'product_size' => $product_size,
          'title'=>$title ));

         */
    }

    public function productSizeMeasurementEditAction($id, $size_id, $measurement_id, $title) {
        $product_size = $this->get('admin.helper.productsizes')->find($size_id);
        if (!$product_size) {
            throw $this->createNotFoundException('Unable to find Product Size.');
        }

        $product = $this->get('admin.helper.product')->find($id);
        $product_size_measurement = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ProductSizeMeasurement')
                ->find($measurement_id);
        $form = $this->createForm(new ProductSizeMeasurementType(), $product_size_measurement);
        $deleteForm = $this->getDeleteForm($size_id);
        return $this->render('LoveThatFitAdminBundle:Product:productSizeMeasurement_edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'product_size' => $product_size,
                    'productSizeMeasurement' => $product_size_measurement,
                    'title' => $title,
                    'id' => $id,
                    'sizetitle' => $product_size->getTitle(),
                    'productname' => $product->getName(),
                ));
    }

    public function productSizeMeasurementEditUpdateAction(Request $request, $id, $size_id, $measurement_id, $title) {
        $product_size = $this->get('admin.helper.productsizes')->find($size_id);
        if (!$product_size) {
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
            $this->get('session')->setFlash('success', 'Product Size Measurement Detail has been Updated.');
        }

        $id = $product_size->getProduct()->getId();
        $entity = $this->getProduct($id);
        return $this->redirect($this->generateUrl('admin_product_detail_size_edit', array(
                            'product' => $entity,
                            'id' => $id,
                            'size_id' => $size_id
                        )));
    }

    public function productSizeMeasurementdeleteAction($id, $size_id, $measurement_id, $title) {
        $product_size = $this->get('admin.helper.productsizes')->find($size_id);
        if (!$product_size) {
            throw $this->createNotFoundException('Unable to find Product Size.');
        }
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ProductSizeMeasurement')
                ->find($measurement_id);
        $em->remove($entity);
        $em->flush();
        $this->get('session')->setFlash('success', 'Successfully Deleted');
        $id = $product_size->getProduct()->getId();
        $product = $this->getProduct($id);
        return $this->redirect($this->generateUrl('admin_product_detail_size_edit', array(
                            'product' => $product,
                            'id' => $id,
                            'size_id' => $size_id
                        )));
    }

#---------------Get Brand Base on the Retailer---------------------#

    public function findBrandBaseOnRetailerAction(Request $request) {
        $target_array = $request->request->all();
        return new response(json_encode($this->get('admin.helper.retailer')->findBrandBaseOnRetailer($target_array['retailer_id'])));
    }

#-----------------------Form Form Upload CSV File------------------#

    public function addCsvProductFormAction() {
        $form = $this->createFormBuilder()
                ->add('csvfile', 'file')
                ->getForm();
        return $this->render('LoveThatFitAdminBundle:Product:import_csv.html.twig', array('form' => $form->createView(),)
        );
    }

#------------Upload CSV Product------------------------------------------------#

    public function uploadProductCsvAction(Request $request) {
        $form = $this->createFormBuilder()
                ->add('csvfile', 'file')
                ->getForm();
        $form->bindRequest($request);
        $file = $form->get('csvfile');
        $filename = $file->getData();
        $pcsv = new ProductCSVHelper($filename);
        $data=$pcsv->read();
        
        #$this->savecsvdata($pcsv, $data);        
        $str = $this->kazaimSizes($data);
        return  new Response($str);
        //return  new Response(json_encode($data));
    }

    //------------------------------------------------------
    private function savecsvdata($pcsv, $data){
        
        $retailer=$this->get('admin.helper.retailer')->findOneByName($data['retailer_name']);        
        $clothingType=$this->get('admin.helper.clothingtype')->findOneByName(strtolower($data['clothing_type']));
        $brand=$this->get('admin.helper.brand')->findOneByName($data['retailer_name']);
        $em = $this->getDoctrine()->getManager();
        $product=$pcsv->fillProduct($data);
        
        $product->setBrand($brand);
        $product->setClothingType($clothingType);
        $product->setRetailer($retailer);
        $em->persist($product);
        $em->flush();
        $this->addProductSizes($product, $data);
        $this->addProductColors($product, $data);
        $this->addProductItems($product);
        return;    
    }
    #------------------------------------------------------------

    public function addProductColors($product, $data) {        
        $em = $this->getDoctrine()->getManager();
        
        foreach($data['product_color'] as $c){
            $pc = new ProductColor;
            $pc->setTitle(strtolower($c));
            $pc->setProduct($product);
            $em->persist($pc);
            $em->flush();            
        }
        return;
    }
    #------------------------------------------------------------
    public function addProductSizes($product, $data) {        
        $em = $this->getDoctrine()->getManager();
        foreach($data['sizes'] as $key=>$value){
            $ps = new ProductSize;
            $ps->setTitle($key);
            $ps->setProduct($product);
            $ps->setBodyType('Regular');
            $em->persist($ps);
            $em->flush();            
            addProductSizeMeasurement($ps, $value);
            
        }
        return $product;
    }
    #------------------------------------------------------
      public function addProductSizeMeasurement($size, $data) {        
        $em = $this->getDoctrine()->getManager();
        foreach($data as $key=>$value){
            $ps = new ProductSize;
            $ps->setTitle($key);
            $ps->setProduct($product);
            $ps->setBodyType('Regular');
            $em->persist($ps);
            $em->flush();            
        }
        return $product;
    }
    
    #------------------------------------------------------------
      #------------------------------------------------------------
    public function kazaimSizes($data) {                
        $foo=array();
        foreach($data['sizes'] as $key=>$value){
            $foo[$key]    =  $this->kazaim($value);            
        }
        return json_encode($foo);
    }
    #-------
      public function kazaim($data) {        
        $str=array();
        foreach($data as $key=>$value ){           
            if ($key!='key'){
            $str[$key]=$value['ideal_body_size_high'].', '. $value['ideal_body_size_low'].', '. $value['maximum_body_measurement'];
            }
            
        
        }
        return $str;
    }
    
#------------------------------------------------------
    public function readProductCsvAction() {
        $pcsv = new ProductCSVHelper("../app/config/LaceBlouse.csv");
        return  new Response(json_encode($pcsv->read()));
    }
    
    
    

}


/*
 * //------------------------------------------------------

    public function __readProductCsvAction() {
        $row = 0;
        $previous_row = '';
        if (($handle = fopen("../app/config/LaceBlouse.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($row >= 5 && $row <= 22) {
                    #garment_measurement_flat	stretch_type_percentage	garment_measurement_stretch_fit	maximum_body_measurement ideal_body_size_high | ideal_body_size_low			
                    echo "00  |" . $data[23] . ":" . $data[25] . ":" . $data[26] . ":" . $data[27] . ":" . $data[28] . ":" . $data[29] . ":" . $data[30] . "<br>";
                    echo "0   |" . $data[31] . ":" . $data[32] . ":" . $data[33] . ":" . $data[34] . ":" . $data[35] . ":" . $data[36] . ":" . $data[37] . "<br>";
                    echo "2   |" . $data[39] . ":" . $data[33] . ":" . $data[34] . ":" . $data[35] . ":" . $data[36] . ":" . $data[37] . ":" . $data[38] . "<br>";
                    echo "4   |" . $data[47] . ":" . $data[48] . ":" . $data[49] . ":" . $data[50] . ":" . $data[51] . ":" . $data[52] . ":" . $data[53] . "<br>";
                    echo "6   |" . $data[55] . ":" . $data[56] . ":" . $data[57] . ":" . $data[58] . ":" . $data[59] . ":" . $data[60] . ":" . $data[61] . "<br>";
                    echo "8   |" . $data[63] . ":" . $data[64] . ":" . $data[65] . ":" . $data[66] . ":" . $data[67] . ":" . $data[68] . ":" . $data[69] . "<br>";
                    echo "10  |" . $data[71] . ":" . $data[72] . ":" . $data[73] . ":" . $data[74] . ":" . $data[75] . ":" . $data[76] . ":" . $data[77] . "<br>";
                    echo "12  |" . $data[79] . ":" . $data[80] . ":" . $data[81] . ":" . $data[82] . ":" . $data[83] . ":" . $data[84] . ":" . $data[85] . "<br>";
                    echo "14  |" . $data[87] . ":" . $data[88] . ":" . $data[89] . ":" . $data[90] . ":" . $data[91] . ":" . $data[92] . ":" . $data[93] . "<br>";
                    echo "16  |" . $data[95] . ":" . $data[96] . ":" . $data[97] . ":" . $data[98] . ":" . $data[99] . ":" . $data[100] . ":" . $data[101] . "<br>";
                }
                echo "<br>";
                $previous_row = $data;
                $row++;
            }
            fclose($handle);
            return new Response('true');
        }
    }
 */