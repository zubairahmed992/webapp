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

class ProductWizardController extends Controller {
    
    public function indexAction($page_number, $sort = 'id') {        
        $product_with_pagination = $this->get('admin.helper.product')->getListWithPagination($page_number, $sort);
        return $this->render('LoveThatFitAdminBundle:ProductWizard:index.html.twig', $product_with_pagination);
    }
    
    public function productEntryWizardNewAction()
    {
        $productForm = $this->createForm(new ProductDetailType());
        return $this->render('LoveThatFitAdminBundle:ProductWizard:product_wizarad_detail_new.html.twig', array(
                    'form' => $productForm->createView(),
                )); 
        
    }
    
    public function productEntryWizardCreateAction(Request $request)
    {
      
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
            return $this->render('LoveThatFitAdminBundle:ProductWizard:_productColors.html.twig', array('product'=>$entity,
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
           return $this->render('LoveThatFitAdminBundle:ProductWizard:_product_entry_detail.html.twig', array('product'=>$entity,
                    'form' => $form->createView(),
                ));
        }else
        {
            $this->get('session')->setFlash('warning', 'Product Detail cannot be created.');
            return $this->render('LoveThatFitAdminBundle:ProductWizard:product_wizarad_detail_new.html.twig', array(
                    'form' => $form->createView(),
                ));
        }
        
        
    }
    
}

