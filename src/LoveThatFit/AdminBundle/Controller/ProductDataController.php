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
        $productcolors=new ProductColor();
        $productcolors->setProduct($colorproduct); 
        $productcolors->setTitle($productcolor->getTitle());
        $productcolors->setImage($productcolor->getImage());
        $productcolors->setPattern($productcolor->getPattern());
        $em->persist($productcolors);
        $em->flush();
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
    
   
}
