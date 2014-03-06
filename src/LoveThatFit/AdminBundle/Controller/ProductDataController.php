<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\AdminBundle\Form\Type\DeleteType;
use LoveThatFit\AdminBundle\Form\Type\ProductDataType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use LoveThatFit\AdminBundle\Form\Type\AlgoritumTestlType;
use LoveThatFit\AdminBundle\Form\Type\AlgoritumProductTestlType;
use LoveThatFit\SiteBundle\FitEngine;
use LoveThatFit\SiteBundle\Comparison;
use LoveThatFit\UserBundle\Entity\Measurement;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ProductColor;
use LoveThatFit\AdminBundle\Entity\ProductSize;
use LoveThatFit\AdminBundle\Entity\ProductSizeMeasurement;
use LoveThatFit\AdminBundle\Entity\ProductItem;

class ProductDataController extends Controller {

    //------------------------------------------------------------------------------------------
    public function indexAction() { 
        $productDataForm = $this->createForm(new ProductDataType());             
        return $this->render('LoveThatFitAdminBundle:ProductData:index.html.twig',array(
                    'form' => $productDataForm->createView(),                                                          
                ));
    }
    
    public function createProductDataAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();        
        $productId=$data['productdata']['Product'];
        $brandId=$data['productdata']['Brand'];
        $productname=$data['productdata']['name'];                
        $product=$this->getProductForProdutData($productId);
        $brand=$this->getBrandForProdutData($brandId);
        $products=new Product();
        $products->setName($productname);
        $products->setBrand($brand);
        $products->setClothingType($product->getClothingType());        
        $products->setCreatedAt(new \DateTime('now'));
        $products->setUpdatedAt(new \DateTime('now'));  
        $products->setGender($product->getGender());
        $products->setDisabled($product->getDisabled());       
        $products->setFabricContent($product->getFabricContent());
        $products->setFabricWeight($product->getFabricWeight());
        $products->setFitPriority($product->getFitPriority());
        $products->setFitType($product->getFitType());
        $products->setGarmentDetail($product->getGarmentDetail());
        $products->setHemLength($product->getHemLength());
        $products->setHorizontalStretch($product->getHorizontalStretch());
        $products->setLayering($product->getLayering());
        $products->setNeckline($product->getNeckline());
        $products->setRise($product->getRise());
        $products->setSizeTitleType($product->getSizeTitleType());
        $products->setSleeveStyling($product->getSleeveStyling());
        $products->setStretchType($product->getStretchType());
        $products->setStructuralDetail($product->getStructuralDetail());
        $products->setStylingType($product->getStylingType());
        $products->setVerticalStretch($product->getVerticalStretch());        
        $products->setDescription($product->getDescription());
        $em->persist($products);    
        $em->flush();
        $productcolor=$this->getProductColorForProdutData($product->getId());
        $colorproduct=$this->getProductForProdutData($products->getId());                        
        foreach($productcolor as $color)
        {
            $productcolors=new ProductColor();
            $productcolors->setProduct($colorproduct); 
            $productcolors->setTitle($color->getTitle());
            $productcolors->setImage($color->getImage());
            $productcolors->setPattern($color->getPattern());
            $em->persist($productcolors);
            $em->flush();
        if ($color->displayProductColor or $products->displayProductColor == NULL) {
                $this->createDisplayDefaultColor($products, $productcolors); //--add  product  default color 
            }        
        }
        
        //$productcolors=new ProductColor();
        
        $sizes=$this->getProductSizesForProdutData($product->getId());
        foreach($sizes as $size)
        {
          $productSize=new ProductSize();
          $productSize->setProduct($colorproduct);
          $productSize->setTitle($size->getTitle());
          $productSize->setBodyType($size->getBodyType());
          $em->persist($productSize);
          $em->flush();         
          $this->addProductSizeMeasurement($size->getId(),$productSize->getId());
          $this->addProductItem($size->getId(),$productSize,$product->getId(),$products,$productcolors);
        }
        
        
        
       return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $products->getId())));
    }
 
  //-----------------------------Get Product-----------------------------------------------  
    private function getProductForProdutData($id)
    {
        $entity = $this->get('admin.helper.product')->find($id); 
        return $entity;
    }
  
   //-----------------------------Get Brand----------------------------------------------- 
    private function getBrandForProdutData($id)
    {
        $entity = $this->get('admin.helper.brand')->find($id); 
        return $entity;
    }
    //---------------------------Get Product Colors---------------------------------------
    private function getProductColorForProdutData($id)
    {
        $entity = $this->get('admin.helper.productcolor')->findColorByProduct($id); 
        return $entity;
    }
    //----------------------------Get Product Sizes----------------------------------------
    private Function getProductSizesForProdutData($id)
    {
        $entity = $this->get('admin.helper.productsizes')->findSizesByProductId($id); 
        return $entity;
    }
    //------------------------product Size measurement-------------------------------------
    
    
    private function addProductSizeMeasurement($id,$product_size_id)
    {
        $em = $this->getDoctrine()->getManager();
        $size=$this->get('admin.helper.productsizes')->find($product_size_id);
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ProductSizeMeasurement')
                ->findBySizeId($id);       
        foreach ($entity as $measurement) {          
            $psm = new ProductSizeMeasurement();
            $psm->setTitle($measurement->getTitle());
            $psm->setProductSize($size);
            $psm->setGarmentMeasurementFlat($measurement->getGarmentMeasurementFlat());
            $psm->setStretchTypePercentage($measurement->getStretchTypePercentage());
            $psm->setGarmentMeasurementStretchFit($measurement->getGarmentMeasurementStretchFit());
            $psm->setMaxBodyMeasurement($measurement->getMaxBodyMeasurement());
            $psm->setIdealBodySizeHigh($measurement->getIdealBodySizeHigh());
            $psm->setIdealBodySizeLow($measurement->getIdealBodySizeLow());
            $em->persist($psm);
            $em->flush();
        }
        return;
    }
   
   private function addProductItem($sizeId,$productSize,$productId,$products,$productcolors)
   {
       $em = $this->getDoctrine()->getManager();  
       $productItem = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ProductItem')
                ->findItemBySizeAndProductAndColor($sizeId,$productId);   
       foreach ($productItem as $item) {          
            $psm = new ProductItem();
            $psm->setProductSize($productSize);
            $psm->setProduct($products);
            $psm->setProductColor($productcolors);
            $psm->setLineNumber($item->getLineNumber());
            $psm->setImage($item->getImage());                        
            $em->persist($psm);
            $em->flush();
        }
        return;
   }
   
   
   private function getProductColor($id) {
        return $this->getDoctrine()
                        ->getRepository('LoveThatFitAdminBundle:ProductColor')
                        ->find($id);
    }

    //------------------------------------------------------------------
    private function createDisplayDefaultColor($products, $productcolors) {

        $em = $this->getDoctrine()->getManager();
        $products->setDisplayProductColor($productcolors);
        $em->persist($products);
        $em->flush();
    }
}
