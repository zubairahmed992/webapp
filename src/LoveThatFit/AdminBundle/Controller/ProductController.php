<?php

namespace LoveThatFit\AdminBundle\Controller;

use LoveThatFit\AdminBundle\Entity\ProductCSVHelper;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ProductColor;
use LoveThatFit\AdminBundle\Entity\ProductSize;
use LoveThatFit\AdminBundle\Entity\ProductItem;
use LoveThatFit\AdminBundle\Entity\ProductItemPiece;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Form\Type\ProductDetailType;
use LoveThatFit\AdminBundle\Form\Type\ProductRawType;
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
use LoveThatFit\AdminBundle\Form\Type\ProductItemPieceType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Constraints\Null;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use LoveThatFit\AdminBundle\ImageHelper;
use ZipArchive;
use LoveThatFit\AdminBundle\Form\Type\ProductItemRawImageType;
use LoveThatFit\AdminBundle\Form\Type\ProductDescriptionType;

class ProductController extends Controller {

    public $error_string;
//---------------------------------------------------------------------

    public function __indexAction($page_number, $sort = 'id') {
        $product_with_pagination = $this->get('admin.helper.product')->getListWithPagination($page_number, $sort);

        //return new response(json_encode(($product_with_pagination)));
        return $this->render('LoveThatFitAdminBundle:Product:index.html.twig', $product_with_pagination);
    }

    public function indexAction()
    {
        return $this->render('LoveThatFitAdminBundle:Product:index_with_grid.html.twig',
                array(
                    'femaleProduct' =>  $this->get('admin.helper.product')->countProductsByGender('f'),
                    'maleProduct' => $this->get('admin.helper.product')->countProductsByGender('m'),
                    'rec_count' => $this->get('admin.helper.product')->getTotalProductCount(),
                    'brandList' => $this->container->get('admin.helper.brand')->findAll(),
                    'topProduct' =>  $this->get('admin.helper.product')->countProductsByType('Top'),
                    'bottomProduct' =>  $this->get('admin.helper.product')->countProductsByType('Bottom'),
                    'dressProduct' =>  $this->get('admin.helper.product')->countProductsByType('Dress'),
                    'category' => $this->container->get('admin.helper.clothing_type')->getArray(),
                    'size_specs' => $this->container->get('admin.helper.size')->getDefaultArray(),
                )
            );
    }

    public function paginateAction(Request $request)
    {
        $requestData = $this->get('request')->request->all();
        $output      = $this->get('admin.helper.product')->searchAllProduct($requestData);

        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
    }

    public function searchProductsAction(Request $request)
    {
        $requestData = $this->get('request')->request->all();
        $output      = $this->get('admin.helper.product')->searchProductByCriteria($requestData);

        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
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
        if (!$brand) {
            $this->get('session')->setFlash('warning', 'Unable to find Brand.');
        }
        return $brand;
    }

    /*     * *************************************************************************
     * ************************* PRODUCT DETAIL ********************************
     * *********************************************************************** */

    public function productDetailNewAction() {
        $productSpecificationHelper = $this->get('admin.helper.product.specification');
        $brandObj = json_encode($this->get('admin.helper.brand')->getBrandNameId());
        $productForm = $this->createForm(new ProductDetailType($productSpecificationHelper,$this->get('admin.helper.size')->getAllSizeTitleType()));
        return $this->render('LoveThatFitAdminBundle:Product:product_detail_new.html.twig', array(
            'form' => $productForm->createView(),
            'productSpecification' => $this->get('admin.helper.product.specification')->getProductSpecification(),
            'brandObj'=>$brandObj,
        ));
    }

#-----------------------------Product Detail ----------------------------------#

    public function productDetailCreateAction(Request $request) {
        $data = $request->request->all();
        $entity = new Product();
        $form = $this->createForm(new ProductDetailType($this->get('admin.helper.product.specification'),$this->get('admin.helper.size')->getAllSizeTitleType()), $entity);
        $form->bindRequest($request);
        $productArray = $this->get('admin.helper.product')->productDetailArray($data, $entity);
        $this->get('session')->setFlash($productArray['message_type'], $productArray['message']);
        return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $entity->getId())));
    }

    #--------------------Method for Edit Product Detail----------------------------#

    public function productDetailEditAction($id) {
        $entity = $this->get('admin.helper.product')->find($id);
        $entity->setGender(strtolower($entity->getGender()));
        $productSpecification = $this->get('admin.helper.product.specification')->getProductSpecification();
        #---------------- PRODUCT STATUS UPDATE -----------------#
        $status = $this->get('admin.helper.product')->getProductIntakeStatus($id);
        $disabled = $this->get('admin.helper.product')->getProductStatus($id);
        $form = $this->createForm(new ProductDetailType($this->get('admin.helper.product.specification'),$this->get('admin.helper.size')->getAllSizeTitleType(),$status,$disabled), $entity);
        $deleteForm = $this->getDeleteForm($id);

        $brandObj = json_encode($this->get('admin.helper.brand')->getBrandNameId());

        return $this->render('LoveThatFitAdminBundle:Product:product_detail_edit.html.twig', array(
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
            'entity' => $entity,
            'productSpecification' => $productSpecification,
            'fit_priority' => $entity->getFitPriority(),
            'fabric_content' => $entity->getFabricContent(),
            'garment_detail' => $entity->getGarmentDetail(),
            'brandObj'=>$brandObj,
        ));
    }

    #--------------------Method for Edit Product Raw as needed----------------------------#

    public function productRawEditAction($id) {
        $entity = $this->get('admin.helper.product')->find($id);
        $form = $this->createForm(new ProductRawType(), $entity);
        return $this->render('LoveThatFitAdminBundle:Product:_product_raw_edit.html.twig', array(
            'form' => $form->createView(),
            'entity' => $entity,
        ));
    }
    public function productRawUpdateAction(Request $request, $id) {
        $entity = $this->get('admin.helper.product')->find($id);
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }
        $raw_form = $this->createForm(new ProductRawType(), $entity);
        $raw_form->bind($request);
        $productArray = $this->get('admin.helper.product')->save($entity);
        return new Response(json_encode($productArray));
    }

#------------------Product Update Method---------------------------------------#

    public function productDetailUpdateAction(Request $request, $id) {
        $entity = $this->get('admin.helper.product')->find($id);
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }
        $status = $this->get('admin.helper.product')->getProductIntakeStatus($id);
        $disabled = $this->get('admin.helper.product')->getProductStatus($id);
        $form = $this->createForm(new ProductDetailType($this->get('admin.helper.product.specification'),$this->get('admin.helper.size')->getAllSizeTitleType(),$status,$disabled), $entity);
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
#------------Clothing type  base on clothing type --------------------#

    public function productClothingTypeAction(Request $request) {
        $target_array = $request->request->all();
        $clothingType = $this->get('admin.helper.product')->productClothingType($target_array);
        return new response(json_encode($clothingType));
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
        $productItems=$this->get('admin.helper.productitem')->getAllItemBaseProduct($id);
        $product_limit = $this->get('admin.helper.product')->getRecordsCountWithCurrentProductLimit($id);
        $page_number = ceil($this->get('admin.helper.utility')->getPageNumber($product_limit[0]['id']));
        if ($page_number == 0) {
            $page_number = 1;
        }
        if (!$product) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }

        #---------------- PRODUCT STATUS UPDATE -----------------#
        $status = $this->get('admin.helper.product')->getProductIntakeStatus($id);
        $disabled = $this->get('admin.helper.product')->getProductStatus($id);
        $productSpecificationHelper = $this->get('admin.helper.product.specification');
        $productForm = $this->createForm(new ProductDetailType($productSpecificationHelper,$this->get('admin.helper.size')->getAllSizeTitleType(),$status,$disabled));

        return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
            'form' => $productForm->createView(),
            'product' => $product,
            'page_number' => $page_number,
            'productItems'=>$productItems,
        ));
    }

#------------------Product Status Update Method---------------------------------------#
    public function productStatusUpdateAction(Request $request) {
        $target_array = $request->request->all();
        $productStatus = $this->get('admin.helper.product')->setProductIntakeStatus($target_array['status'],$target_array['id']);
        return new response(json_encode($productStatus));
    }

#------------------------ PRODUCT DETAIL COLOR --------------------------------#

    public function productDetailColorAddNewAction($id) {

        $product = $this->get('admin.helper.product')->find($id);
        if (!$product) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }
        $sizes=$this->get('admin.helper.product')->getSizeArray($product);
        $productItems = $this->get('admin.helper.productitem')->getAllItemBaseProduct($id);
        $colorform = $this->createForm(new ProductColorType($sizes));
        $imageUploadForm = $this->createForm(new ProductColorImageType());
        $patternUploadForm = $this->createForm(new ProductColorPatternType());

        #---------------- PRODUCT STATUS UPDATE -----------------#
        $status = $this->get('admin.helper.product')->getProductIntakeStatus($id);
        $disabled = $this->get('admin.helper.product')->getProductStatus($id);
        $productSpecificationHelper = $this->get('admin.helper.product.specification');
        $productForm = $this->createForm(new ProductDetailType($productSpecificationHelper,$this->get('admin.helper.size')->getAllSizeTitleType(),$status,$disabled));

        return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
            'form' => $productForm->createView(),
            'product' => $product,
            'colorform' => $colorform->createView(),
            'imageUploadForm' => $imageUploadForm->createView(),
            'patternUploadForm' => $patternUploadForm->createView(),
            'productItems'=>$productItems,
            'allSizes'=>$sizes,
        ));
    }

#--------------------PRODUCT COLOR CREATE--------------------------------------#

    public function productDetailColorCreateAction(Request $request, $id) {

        $product = $this->get('admin.helper.product')->find($id);
        $productColor = new ProductColor($product);
        $sizes = $this->get('admin.helper.product')->getSizeArray($product);

        $colorform = $this->createForm(new ProductColorType($sizes), $productColor);
        $colorform->bind($request);
        if ($colorform->isValid()) {
            $this->get('admin.helper.productcolor')->uploadSave($productColor);
            if ($productColor->displayProductColor or $product->displayProductColor == NULL) {
                $this->get('admin.helper.product')->updateDisplayColor($product, $productColor); //--add  product  default color 
            }else{
                $this->get('admin.helper.product')->updatedAt($product);
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
        $product = $this->get('admin.helper.product')->find($id);
        if (!$product) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }

        $productItems=$this->get('admin.helper.productitem')->getAllItemBaseProduct($id);
        $productColor = $this->getProductColor($color_id);
        $sizeTitle = $this->get('admin.helper.productsizes')->getSizeArrayBaseOnProduct($id);
        //return new response(json_encode($sizeTitle));
        $sizes=$this->get('admin.helper.product')->getBrandSpecifications($product);
        //$sizes = $this->get('admin.helper.product')->productDetailColorAdd($product);
        // return new response(json_encode($sizes));
        $colorform = $this->createForm(new ProductColorType($sizes),$productColor);

        if (isset($sizeTitle['Petite'])) {
            $colorform->get('petite')->setData($sizeTitle['Petite']);
        }
        if (isset($sizeTitle['Regular'])) {
            $colorform->get('regular')->setData($sizeTitle['Regular']);
        }
        if (isset($sizeTitle['Tall'])) {
            $colorform->get('tall')->setData($sizeTitle['Tall']);
        }
        if (isset($sizeTitle['Plus'])) {
            $colorform->get('plus')->setData($sizeTitle['Plus']);
        }
        if (isset($sizeTitle['Athletic'])) {
            $colorform->get('athletic')->setData($sizeTitle['Athletic']);
        }
        if (isset($sizeTitle['Portley'])) {
            $colorform->get('portley')->setData($sizeTitle['Portley']);
        }
        if (isset($sizeTitle['Big'])) {
            $colorform->get('big')->setData($sizeTitle['Big']);

        }

        //   return new response(json_encode(var_dump($colorform)));

        #---------------- PRODUCT STATUS UPDATE -----------------#
        $status = $this->get('admin.helper.product')->getProductIntakeStatus($id);
        $disabled = $this->get('admin.helper.product')->getProductStatus($id);
        $productSpecificationHelper = $this->get('admin.helper.product.specification');
        $productForm = $this->createForm(new ProductDetailType($productSpecificationHelper,$this->get('admin.helper.size')->getAllSizeTitleType(),$status,$disabled));

        $imageUploadForm = $this->createForm(new ProductColorImageType(), $productColor);
        $patternUploadForm = $this->createForm(new ProductColorPatternType(), $productColor);
        return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
            'form' => $productForm->createView(),
            'product' => $product,
            'colorform' => $colorform->createView(),
            'color_id' => $color_id,
            'imageUploadForm' => $imageUploadForm->createView(),
            'patternUploadForm' => $patternUploadForm->createView(),
            'productItems'=>$productItems,
        ));
    }

#-------------------------Product color Update---------------------------------#

    public function productDetailColorUpdateAction(Request $request, $id, $color_id) {
        $product = $this->getProduct($id);
        if (!$product)
            $this->get('session')->setFlash('warning', 'Unable to find Product.');

        $productColor = $this->getProductColor($color_id);
        $sizes = $this->get('admin.helper.product')->getBrandSpecifications($product);
        $colorform = $this->createForm(new ProductColorType($sizes), $productColor);
        $colorform->bind($request);
        if ($colorform->isValid()) {
            $this->get('admin.helper.color')->save(strtolower($productColor->getTitle())); //Save new color names
            $this->get('admin.helper.productcolor')->uploadSave($productColor);
            if ($productColor->displayProductColor or $product->displayProductColor == NULL) {
                $this->get('admin.helper.product')->updateDisplayColor($product, $productColor); //--add  product  default color 
            } else {
                $this->get('admin.helper.product')->updatedAt($product);
            }
            $this->createSizeItemForBodyTypes($product, $productColor, $colorform->getData()); #Create Items against color & selected sizes            
            $this->get('session')->setFlash('success', 'Product Color Detail has been updated!');
            return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $id)));
        } else {

            $this->get('session')->setFlash('warning', 'Unable to update Product Color Detail!');
            $productItems=$this->get('admin.helper.productitem')->getAllItemBaseProduct($id);
            $imageUploadForm = $this->createForm(new ProductColorImageType(), $productColor);
            $patternUploadForm = $this->createForm(new ProductColorPatternType(), $productColor);
            return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
                'product' => $product,
                'colorform' => $colorform->createView(),
                'color_id' => $color_id,
                'imageUploadForm' => $imageUploadForm->createView(),
                'patternUploadForm' => $patternUploadForm->createView(),
                'productItems'=>$productItems,
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

    public function productDetailSizeEditAction($id, $size_id)
    {
        $product = $this->getProduct($id);
        if (!$product) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }

        $product_size = $this->get('admin.helper.productsizes')->findMeasurementArray($size_id);

        $productsize = $this->get('admin.helper.productsizes')->find($size_id);
        $clothingType = strtolower($product->getClothingType()->getName());
        $clothingTypeAttributes = $this->get('admin.helper.product.specification')->getAttributesFor($clothingType);



        $size_measurements = $this->get('admin.helper.productsizes')->checkAttributes($clothingTypeAttributes, $product_size);
        $form = $this->createForm(new ProductSizeMeasurementType('edit'));
        return $this->render('LoveThatFitAdminBundle:Product:product_size_detail_show.html.twig', array(
            'product' => $product,
            'size_measurements' => $size_measurements,
            'size_id' => $size_id,
            'form' => $form->createView(),
            'addform' => $form->createView(),
            'product_size' => $product_size,
            'sizetitle' => $productsize->getTitle(),
            'sizeStatus' => $productsize->getDisabled(),
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
            $this->get('admin.helper.product')->updatedAt($product);
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

//-----------------------------Product Item two piece add new-----------------------------
    public function productDetailItemPieceAddNewAction($id,$item_id)
    {
        $item=$this->getProductItem($item_id);
        $piece=new ProductItemPiece();
        $piece->setProductItem($item);
        $pieceitemform = $this->createForm(new ProductItemPieceType($item->getProductColor()->getProductColorViews()),$piece);
        return $this->render('LoveThatFitAdminBundle:Product:product_detail_item_two_piece_new.html.twig', array(
            'form' => $pieceitemform->createView(),
            'item_id' => $item_id,
            'entity'=>$this->getProduct($id),
            'product_item_piece'=>$piece,
        ));

    }

    public function productDetailItemPieceCreateAction(Request $request,$id,$item_id)
    {
        $item=$this->getProductItem($item_id);
        $piece=new ProductItemPiece();
        $form = $this->createForm(new ProductItemPieceType($item->getProductColor()->getProductColorViews()),$piece);
        $form->bind($request);
        $em = $this->getDoctrine()->getManager();
        $piece->setProductitem($item);
        $piece->upload();
        $em->persist($piece);
        $em->flush();
        $this->get('session')->setFlash('success', 'Product Detail size has been update.');
        return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $id)));
    }

    public function productDetailItemPieceDeleteAction($id,$piece_id)
    {
        try {
            $message_array = $this->get('admin.helper.product.item.piece')->delete($piece_id);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $id)));

        } catch (\Doctrine\DBAL\DBALException $e) {

            $this->get('session')->setFlash('warning', 'This Piece cannot be deleted!');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

    }

    public function productDetailItemPieceEditAction($id,$item_id,$piece_id)
    {
        $item=$this->getProductItem($item_id);
        $entity=$this->get('admin.helper.product.item.piece')->find($piece_id);
        $form = $this->createForm(new ProductItemPieceType($item->getProductColor()->getProductColorViews()), $entity);
        return $this->render('LoveThatFitAdminBundle:Product:product_detail_item_two_piece_edit.html.twig', array(
            'form' => $form->createView(),
            'item_id' => $item_id,
            'entity'=>$this->getProduct($id),
            'piece_id'=>$piece_id,
            'piece'=>$entity,
        ));
    }

    public function productDetailItemPieceUpdateAction(Request $request,$id,$item_id,$piece_id)
    {
        $item=$this->getProductItem($item_id);
        $entity=$this->get('admin.helper.product.item.piece')->find($piece_id);
        $form = $this->createForm(new ProductItemPieceType($item->getProductColor()->getProductColorViews()), $entity);
        $form->bind($request);
        $em = $this->getDoctrine()->getManager();
        $entity->setProductitem($item);
        $entity->upload();
        $em->persist($entity);
        $em->flush();
        $this->get('session')->setFlash('success', 'Product Detail size has been update.');
        return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $id)));
    }

#----------------------Product Item Edit ---------------------------------------#

    public function productDetailItemEditAction($id, $item_id) {

        $entity = $this->getProduct($id);
        $productItems=$this->get('admin.helper.productitem')->getAllItemBaseProduct($id);
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }
        $itemform = $this->createForm(new ProductItemType(), $this->getProductItem($item_id));
        $itemrawimageform = $this->createForm(new ProductItemRawImageType(), $this->getProductItem($item_id));

        #---------------- PRODUCT STATUS UPDATE -----------------#
        $status = $this->get('admin.helper.product')->getProductIntakeStatus($id);
        $disabled = $this->get('admin.helper.product')->getProductStatus($id);
        $productSpecificationHelper = $this->get('admin.helper.product.specification');
        $productForm = $this->createForm(new ProductDetailType($productSpecificationHelper,$this->get('admin.helper.size')->getAllSizeTitleType(),$status,$disabled));

        return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
            'form' => $productForm->createView(),
            'product' => $entity,
            'itemform' => $itemform->createView(),
            'item_id' => $item_id,
            'itemrawimageform' => $itemrawimageform->createView(),
            'productItems'=>$productItems,
        ));
    }

#-----------------------Product Detail Item Update-----------------------------#

    public function productDetailItemUpdateAction(Request $request, $id, $item_id) {
        $entity = $this->getProduct($id);
        $productItems=$this->get('admin.helper.productitem')->getAllItemBaseProduct($id);
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

        #---------------- PRODUCT STATUS UPDATE -----------------#
        $status = $this->get('admin.helper.product')->getProductIntakeStatus($id);
        $disabled = $this->get('admin.helper.product')->getProductStatus($id);
        $productSpecificationHelper = $this->get('admin.helper.product.specification');
        $productForm = $this->createForm(new ProductDetailType($productSpecificationHelper,$this->get('admin.helper.size')->getAllSizeTitleType(),$status,$disabled));

        if ($itemform->isValid()) {
            $this->get('admin.helper.product')->updatedAt($entity);
            $entity_item->upload(); //----- file upload method 
            $em->persist($entity_item);
            $em->flush();
            $this->get('session')->setFlash('success', 'Product item updated  Successfully');
            return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
                'form' => $productForm->createView(),
                'product' => $entity,
                'itemform' => $itemform->createView(),
                'productItems'=>$productItems,
            ));
        } else {

            $this->get('session')->setFlash('warning', 'Unable to Product Detail Item');

            return $this->render('LoveThatFitAdminBundle:Product:product_detail_show.html.twig', array(
                'form' => $productForm->createView(),
                'product' => $entity,
                'itemform' => $itemform->createView(),
                'item_id' => $item_id,
                'productItems'=>$productItems,
            ));
        }
    }
#---------------------------------------------------------------------------
    public function itemPriceUpdateAction() {
        $request_array =$this->getRequest()->request->all();
        $this->get('admin.helper.product')->updatePrice($request_array['product_id'],$request_array['price']);
        return new response($request_array['product_id'].'  <> '.$request_array['price']);
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
        return  $this->get('admin.helper.productsizes')->createSizeItemForBodyTypes($product, $p_color, $all_sizes);
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
        $form = $this->createForm(new ProductSizeMeasurementType('add'), $entity);
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
        $form = $this->createForm(new ProductSizeMeasurementType('add'), $entity);

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
        $form = $this->createForm(new ProductSizeMeasurementType('add'), $entity);
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
#-----------------------------------------------------------------------

    public function productSizeMeasurementupdateAction(Request $request, $id, $size_id, $title) {
        $product_size = $this->get('admin.helper.productsizes')->find($size_id);
        if (!$product_size) {
            throw $this->createNotFoundException('Unable to find Product Size.');
        }
        $em = $this->getDoctrine()->getManager();
        $entity = new ProductSizeMeasurement();
        $entity->setVerticalStretch($product_size->getProduct()->getVerticalStretch());
        $entity->setHorizontalStretch($product_size->getProduct()->getHorizontalStretch());
        $form = $this->createForm(new ProductSizeMeasurementType('edit'), $entity);
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
#-----------------------------------------------------------------------

    public function productSizeMeasurementEditAction($id, $size_id, $measurement_id, $title) {
        $product_size = $this->get('admin.helper.productsizes')->find($size_id);
        if (!$product_size) {
            throw $this->createNotFoundException('Unable to find Product Size.');
        }

        $product = $this->get('admin.helper.product')->find($id);
        $product_size_measurement = $this->getDoctrine()
            ->getRepository('LoveThatFitAdminBundle:ProductSizeMeasurement')
            ->find($measurement_id);
        $form = $this->createForm(new ProductSizeMeasurementType('edit'), $product_size_measurement);
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
#-----------------------------------------------------------------------

    public function productSizeMeasurementEditUpdateAction(Request $request, $id, $size_id, $measurement_id, $title) {



        $product_size = $this->get('admin.helper.productsizes')->find($size_id);
        if (!$product_size) {
            throw $this->createNotFoundException('Unable to find Product Size.');
        }
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()
            ->getRepository('LoveThatFitAdminBundle:ProductSizeMeasurement')
            ->find($measurement_id);
        $form = $this->createForm(new ProductSizeMeasurementType('edit'), $entity);
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

#-----------------------------------------------------------------------
    public function fooAction($id = 0) {
        #return new response(json_encode($this->get('admin.helper.product')->productDetailSizeArray($id)));      
        return new response(json_encode($this->get('admin.helper.camera_mask_specs')->getMaskSpecs()));
    }
#-----------------------------------------------------------------------
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

#---------------Get Brand Based on the Retailer---------------------#

    public function findBrandBaseOnRetailerAction(Request $request) {
        $target_array = $request->request->all();
        return new response(json_encode($this->get('admin.helper.retailer')->findBrandBaseOnRetailer($target_array['retailer_id'])));
    }
#-----------------------------------------------------------------------------

    #---------------Multiple Image Uploading --------------------------#
    public function multplieImageUploadAction(Request $request) {
        $error_str = '';
        foreach ($_FILES["file"]["error"] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                $file_name = $_FILES["file"]["name"][$key]; // avoid same file name collision
                $parsed_details = $this->get('admin.helper.product')->breakFileName($file_name, $_POST['product_id']);
                if ($parsed_details['success'] == 'false') {
                    #return new Response($parsed_details['message']);
                    $error_str = $error_str . $file_name .' : ' . $parsed_details['message'] . ', ';
                } else {
                    $product_item = $this->get('admin.helper.product')->findProductColorSizeItemViewByTitle($parsed_details);
                    $imageFile = $request->files->get('file');
                    #return new response(json_encode($parsed_details));                               
                    if (array_key_exists('view_title', $parsed_details)) {
                        #find matching color view object
                        $product_color_view = $product_item->getProductColorViewByTitle($parsed_details['view_title']);
                        #create new piece & set item & color view
                        $product_item_piece = $this->get('admin.helper.product.item.piece')->findOrCreateNew($product_item, $product_color_view);
                        #set file
                        $product_item_piece->file = $imageFile[$key];
                        #save & upload
                        $this->get('admin.helper.product.item.piece')->save($product_item_piece);
                        #$this->get('admin.helper.product.item.piece')->saveWithoutUpload($product_item_piece);                    
                    } else {
                        $product_item->file = $imageFile[$key];
                        $product_item->upload();
                        $this->get('admin.helper.productitem')->save($product_item);
                    }
                }
            }
        }
        if (strlen($error_str)==0){
            return new Response('Successfully Processed');
        }else{
            return new Response('Successfully Processed, except for:'.$error_str);
        }
    }
#-------------------------------------------------------------------------------

    public function _multplieImageUploadAction(Request $request){
        // return new response(json_encode($this->get('admin.helper.product')->findItemMultipleImpagesUploading(true,true)));
        $em = $this->getDoctrine()->getManager();
        foreach ($_FILES["file"]["error"] as $key => $error){
            if ($error == UPLOAD_ERR_OK){
                $random_num=rand(00,99);  // random number
                $name = $_FILES["file"]["name"][$key]; // avoid same file name collision

                $itemId=$this->get('admin.helper.product')->findItemMultipleImpagesUploading($name,$_POST['product_id']);
                //return new response(json_encode($itemId));
                if(!empty($itemId)){
                    $productItem = $this->get('admin.helper.productitem')->find($itemId);

                    $imageFile=$request->files->get('file');
                    $productItem->file=$imageFile[$key];//$_FILES["file"]["tmp_name"][0];
                    $productItem->upload();
                }
            }

        }
        if(!empty($productItem)){
            $em->persist($productItem);
            $em->flush();
        }
        return new response(json_encode("Done"));
    }
#----------------------------------------------------------------------

    public function imageUploadIndexAction($product_id=0) {
        $product = $this->get('admin.helper.product')->find($product_id);
        return $this->render('LoveThatFitAdminBundle:Product:_image_uploader.html.twig', array(
            'product' => $product,
        ));
    }
    #----------------------------------------------------------------------
    public function imageUploadAction() {
        $allowed = array('png', 'jpg');
        $product_id=$_POST['product_id'];
        $request=$this->getRequest();
        if(isset($_FILES['upl']) && $_FILES['upl']['error'] == 0){
            $extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);
            $file_name=$_FILES['upl']['name'];

            #--------------------------------------------------------------

            $parsed_details = $this->get('admin.helper.product')->breakFileName($file_name, $_POST['product_id']);

            if ($parsed_details['success'] == 'false') {
                #return new Response($parsed_details['message']);
                $error_str = $error_str . $file_name .' : ' . $parsed_details['message'] . ', ';
            } else {
                $product_item = $this->get('admin.helper.product')->findProductColorSizeItemViewByTitle($parsed_details);
                $imageFile =  $request->files->get('upl');
                #return new response(json_encode($parsed_details));
                if (array_key_exists('view_title', $parsed_details)) {
                    #find matching color view object
                    $product_color_view = $product_item->getProductColorViewByTitle($parsed_details['view_title']);
                    #create new piece & set item & color view
                    $product_item_piece = $this->get('admin.helper.product.item.piece')->findOrCreateNew($product_item, $product_color_view);
                    #set file
                    $product_item_piece->file = $imageFile;
                    #save & upload
                    $this->get('admin.helper.product.item.piece')->save($product_item_piece);
                    #$this->get('admin.helper.product.item.piece')->saveWithoutUpload($product_item_piece);
                    return new response('{"status":"view updated"}');
                } else {
                    $product_item->file = $imageFile;
                    $product_item->upload();
                    $this->get('admin.helper.productitem')->save($product_item);
                    return new response('{"status":"fitting room image updated"}');
                }
            }

            #--------------------------------------------------------------
            /*               
               if(!in_array(strtolower($extension), $allowed)){
                   return new response('{"status":"error"}');                       
               }
               $dirpath=__DIR__ . '/../../../../web/uploads/ltf/products/display/';
               if(move_uploaded_file($_FILES['upl']['tmp_name'], $dirpath.$_FILES['upl']['name'])){
                       return new response('{"status":"success"}');
               }
                * 
            */
        }
        return new response('{"status":"error"}');
    }

    public function statusChangeAction(Request $request)
    {
        $status = $request->get('status');
        $id = $request->get('id');
        $entity = $this->get('admin.helper.product')->find($id);
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }
        if ($status == "disable") {
            $entity->setDisabled(0); //0 enable it
        } else {
            $entity->setDisabled(1); //1 disable it
        }
        $this->get('admin.helper.product')->update($entity);
        $output['data'] = [
            'id' => $entity->getId(),
            'control_number' => $entity->getControlNumber(),
            'BName' => "", //$fData["BName"],
            'ClothingType' => "", //$fData["cloting_type"],
            'gender' => $entity->getGender(),
            'PName' => $entity->getName(),
            'created_at' => $entity->getCreatedAt()->format('Y-m-d H:i:s'),
            'status'    => ($entity->getStatus() == 1) ? "Enable" : "Disable"
        ];

        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
    }

    public function productSizeDisableAction(Request $request)
    {
        $product_id = $request->get('id');
        $size_id = $request->get('size_id');
        $status = $request->get('status');
        $entity = $this->get('admin.helper.productsizes')->find($size_id);
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Unable to find Size.');
            return new response('{"status":"error"}');
        } else {
            if ($status == "disable") {
                $entity->setDisabled(1);
            } else {
                $entity->setDisabled(0);
            }

            $this->get('admin.helper.productsizes')->update($entity);
            $this->get('session')->setFlash('success', 'Successfully Updated Size.');
            return new response('{"status":"ok"}');
        }
    }

#----------------------------------------------------------------------
    public function imageUploadProductDetailAction()
    {
        $allowed = array('png', 'jpg');
        $product_id = $_POST['product_id'];
        $request = $this->getRequest();
        if (isset($_FILES['upl']) && $_FILES['upl']['error'] == 0) {
            $extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);
            $file_name = $_FILES['upl']['name'];

            #--------------------------------------------------------------

            $parsed_details = $this->get('admin.helper.product')->breakFileNameProductDetail($file_name, $_POST['product_id']);

            if ($parsed_details['success'] == 'false') {
                //return new Response($file_name.$parsed_details['message']);
                $this->error_string .= $this->get('session')->getFlash('error-on-addcolor') . $file_name . ' : ' . $parsed_details['message']. ' , ';
            } else {

                $product = $this->get('admin.helper.product')->find($parsed_details['product_id']);

                /* Checked Color are available for this product */
                $color_id_result = $this->get('admin.helper.productcolor')->findColorByProductTitle(strtolower($parsed_details['color_title']), $parsed_details['product_id']);
                if (count($color_id_result) == 0) {
                    /* Create Product Item for this product */
                    $color_id_result = new ProductColor();
                    $color_id_result->setProduct($product);
                    $color_id_result->setTitle(strtolower(strtolower($parsed_details['color_title'])));
                    $this->get('admin.helper.productcolor')->save($color_id_result);
                }

                // Find if the new color has been successfully added
                $added_color_id_result = $this->get('admin.helper.productcolor')->findColorByProductTitle(strtolower($parsed_details['color_title']), $parsed_details['product_id']);

                //Make Default Color
                if($parsed_details['set_default'] == "yes"){
                    $this->get('admin.helper.product')->updateDisplayColor($product, $added_color_id_result);
                }

                /*Add color pattern and color image*/
                if(array_key_exists('image_type',$parsed_details)){
                    $imageFile = $request->files->get('upl');
                    $updated_image_message = '';

                    /*Color Pattern Type */
                    if($parsed_details['image_type'] == 'colorpatterntype'){
                        $added_color_id_result->file = $imageFile;
                        $pattern_type = $added_color_id_result->uploadTemporaryImage();
                        $added_color_id_result->tempPattern = $pattern_type['image_name'];
                        $added_color_id_result->savePattern();
                        $this->get('admin.helper.productcolor')->save($added_color_id_result);
                        $updated_image_message = 'Pattern Image Updated';
                    }
                    /*Color Image Type */
                    if($parsed_details['image_type'] == 'colorimagetype'){
                        $added_color_id_result->file = $imageFile;
                        $image_type = $added_color_id_result->uploadTemporaryImage();
                        $added_color_id_result->tempImage = $image_type['image_name'];
                        $added_color_id_result->saveImage();
                        $this->get('admin.helper.productcolor')->save($added_color_id_result);
                        $updated_image_message = 'Image Updated';
                    }
                    $this->get('session')->setFlash('success', $updated_image_message);
                    return new response('{"status":'.$updated_image_message.'}');

                }
                /*Add color pattern and color image*/
                $find_size_by_title_productid = $this->get('admin.helper.productsizes')->findSizeByProductTitle(strtolower($parsed_details['size_title']), $parsed_details['product_id']);

                /* If size not available then show error */
                if(count($find_size_by_title_productid) == 0){
                    $this->error_string .= $this->get('session')->getFlash('error-on-addcolor') . $file_name . ' : Size not available ,';
                    $this->get('session')->setFlash('error-on-addcolor', $this->error_string);
                    return new response('{"status":"error"}');
                }
                /* Checked Product item are available for this product */
                $product_id_result = $this->get('admin.helper.productitem')->getProductItemByProductId($parsed_details['product_id'], $added_color_id_result->getId(), $find_size_by_title_productid->getId());

                if (count($product_id_result) == 0) {
                    //**/
                    /* Create Product Item for this product */
                    $p_color = $this->get('admin.helper.productcolor')->findColorByProductTitle(strtolower($parsed_details['color_title']), $parsed_details['product_id']);
                    $p_size = $this->get('admin.helper.productsizes')->findSizeByProductTitle(strtolower($parsed_details['size_title']), $parsed_details['product_id']);
                    $this->get('admin.helper.productitem')->addItem($product, $p_color, $p_size);
                }

                $product_item = $this->get('admin.helper.product')->findProductColorSizeItemViewByTitle($parsed_details);
                $imageFile = $request->files->get('upl');

                if (array_key_exists('view_title', $parsed_details)) {

                    #find matching color view object
                    $product_color_view = $product_item->getProductColorViewByTitle($parsed_details['view_title']);
                    #create new piece & set item & color view
                    $product_item_piece = $this->get('admin.helper.product.item.piece')->findOrCreateNew($product_item, $product_color_view);
                    #set file
                    $product_item_piece->file = $imageFile;
                    #save & upload
                    $this->get('admin.helper.product.item.piece')->save($product_item_piece);
                    $this->get('session')->setFlash('success', 'Product Item has been created');
                    return new response('{"status":"view updated"}');
                } else {
                    $product_item->file = $imageFile;
                    $product_item->upload();
                    $this->get('admin.helper.productitem')->save($product_item);
                    $this->get('session')->setFlash('success', 'Remaining Product Items and Colors have been added/updated');
                    return new response('{"status":"Remaining Product Items and Colors have been added"}');
                }
            }
        }
        $this->get('session')->setFlash('error-on-addcolor', $this->error_string);
        return new response('{"status":"error"}');

    }

    public function exportAction()
    {
        $products_and_items = $this->getDoctrine()
            ->getRepository('LoveThatFitAdminBundle:Product')
            ->listProductsAndItems();
        if (!empty($products_and_items)) {
            $count = 0;
            foreach ($products_and_items as $row) {
                $products_and_items[$count]['status']     = ($row["status"] == 1 ? "Disabled" : "Enabled");
                $products_and_items[$count]['gender']     = ($row["gender"] == "f" ? "Female" : "Male");
                $products_and_items[$count]['created_at'] = (date_format(date_create($row["created_at"]), "d/m/Y H:i"));
                $count++;
            }
        } else {
            $products_and_items = array('product_id' => '', 'product_name' => '', 'gender' => '', 'brand_name' => '', 'clothing_type' => '', 'color' => '', 'retailer' => '', 'size' => '', 'item_id' => '', 'created_at' => '', 'control_number' => '', 'hem_length' => '', 'neckline' => '', 'sleeve_styling' => '', 'rise' => '', 'fabric_weight' => '', 'size_title_type' => '', 'fit_type' => '', 'horizontal_stretch' => '', 'vertical_stretch' => '', 'styling_type' => '');
            /*$this->get('session')->setFlash('warning', 'No Record Found!');
            $totalRecords = $this->get('admin.helper.product')->countAllRecord();
            $femaleProducts  = $this->get('admin.helper.product')->countProductsByGender('f');
            $maleProducts    = $this->get('admin.helper.product')->countProductsByGender('m');
            return $this->render('LoveThatFitAdminBundle:User:index_new.html.twig',
                array('rec_count' => count($totalRecords),
                    'femaleProduct'     => $femaleProducts,
                    'maleProduct'       => $maleProducts,
                )
            );*/
        }
        $this->get('admin.helper.utility')->exportToCSV($products_and_items, 'product_item_color_sizes_statuses');
        return new Response('');
    }

    #------------------Product Manage Description---------------------------------------#
    public function productManageDescriptionAction(Request $request, $id)
    {
        $entity = $this->get('admin.helper.product')->find($id);
        $productSpecification = $this->get('admin.helper.product.specification')->getProductSpecification();
        $form = $this->createForm(new ProductDescriptionType($this->get('admin.helper.product.specification'), $this->get('admin.helper.size')->getAllSizeTitleType()), $entity);
        return $this->render('LoveThatFitAdminBundle:Product:product_detail_manage_description.html.twig', array(
            'form' => $form->createView(),
            'entity' => $entity,
            'productSpecification' => $productSpecification,
        ));
    }

    #------------------Product Description Update Method---------------------------------------#

    public function productDescriptionUpdateAction(Request $request, $id)
    {
        $entity = $this->get('admin.helper.product')->find($id);
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }
        $data = $request->request->all();
        $productArray = $this->get('admin.helper.product')->productDetailArray($data, $entity);
        $this->get('session')->setFlash('success', 'Product description updated.');
        return $this->redirect($this->generateUrl('admin_product_manage_description', array('id' => $id)));
    }
	
	
	public function ajaxloadcategoriesAction($id) {
       $getselectedcategories = $this->get('admin.helper.product')->getSelectedProductCategories($id);
	   $success = 0;
	   if(count($getselectedcategories) > 0)
	   {
		  $success = "1";
	   }
	   
		 return new Response($success );
    }

    public function exportProductCategoriesAction()
    {
        $products_and_items = $this->getDoctrine()
            ->getRepository('LoveThatFitAdminBundle:Product')
            ->listProductsAndCategories();
        if (!empty($products_and_items)) {

        } else {
            $products_and_items = array('product_id' => '', 'product_name' => '', 'categories_name' => '');
        }
        $this->get('admin.helper.utility')->exportToCSV($products_and_items, 'product_with_categories');
        return new Response('');
    }

}
