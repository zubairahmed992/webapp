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
        $entity = new Product();      
        $form = $this->createForm(new ProductDetailType(), $entity);
        $form->bind($request);
        $gender=$entity->getGender();
       $clothing_type= $entity->getClothingType()->getTarget();
       if($gender=='M' and $clothing_type=='Dress')
       {
           $form->get('gender')->addError(new FormError('Dresses can not be selected  for Male'));           
           $this->get('session')->setFlash('warning', 'Dresses can not be selected for male.');
           return $this->render('LoveThatFitAdminBundle:ProductWizard:product_wizarad_detail_new.html.twig', array(
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
           return $this->redirect($this->generateUrl('admin_product_wizard_detail_show', array('id' => $entity->getId())));        
        }else
        {
            $this->get('session')->setFlash('warning', 'Product Detail cannot be created.');
            return $this->render('LoveThatFitAdminBundle:ProductWizard:product_wizarad_detail_new.html.twig', array(
                    'form' => $form->createView(),
                ));
        }
    }    
    
    public function productEntryWizardShowAction($id) {
        $product = $this->getProduct($id);
        $productForm = $this->createForm(new ProductDetailType());
        if (!$product) {
            $this->get('session')->setFlash('warning', 'Unable to find Product.');
        }
        
        $colorform = $this->createForm(new ProductColorType());

        $imageUploadForm = $this->createForm(new ProductColorImageType());
        $patternUploadForm = $this->createForm(new ProductColorPatternType());
        return $this->render('LoveThatFitAdminBundle:ProductWizard:product_wizarad_detail_new.html.twig', array(
                    'product' => $product,
                    'form' => $productForm->createView(),
                    'colorform' => $colorform->createView(),
                    'imageUploadForm' => $imageUploadForm->createView(),
                    'patternUploadForm' => $patternUploadForm->createView(),
        ));
    }
    
    public function getProduct($id) {
        return $this->getDoctrine()
                        ->getRepository('LoveThatFitAdminBundle:Product')
                        ->find($id);
    }
    
    
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
    
    public function productEntryWizardColorCreateAction(Request $request, $id)
    {
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
           return $this->redirect($this->generateUrl('admin_product_wizard_detail_show', array('id' => $id)));        
            //return $this->redirect($this->generateUrl('admin_product_detail_show', array('id' => $id)));
        } else {
            $this->get('session')->setFlash('warning', 'Product Detail color cannot been created.');
        }
    }
    
    
    public function createDisplayDefaultColor($product, $productColor) {

        $em = $this->getDoctrine()->getManager();
        $product->setDisplayProductColor($productColor);
        $em->persist($product);
        $em->flush();
    }
    
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
    
    private function addItem($product, $p_color, $p_size) {
        $em = $this->getDoctrine()->getManager();
        $p_item = new ProductItem();
        $p_item->setProduct($product);
        $p_item->setProductSize($p_size);
        $p_item->setProductColor($p_color);
        $em->persist($p_item);
        $em->flush();
    }
}

