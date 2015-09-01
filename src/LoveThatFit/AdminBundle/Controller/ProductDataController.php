<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\AdminBundle\Form\Type\ProductDataType;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ProductColor;
use LoveThatFit\AdminBundle\Entity\ProductSize;
use LoveThatFit\AdminBundle\Entity\ProductSizeMeasurement;
use LoveThatFit\AdminBundle\Entity\ProductItem;
use LoveThatFit\AdminBundle\Entity\ProductCSVHelper;
use LoveThatFit\AdminBundle\Entity\ProductCSVDataUploader;

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

    


#--------------------------------------------------------------
#-----------------------Form Upload CSV File------------------#

    public function csvIndexAction() {
        $form = $this->getCsvUploadForm();
        return $this->render('LoveThatFitAdminBundle:ProductData:import_csv.html.twig', array('form' => $form->createView())
        );
    }

#------------------------------------------------------------#
    public function csvUploadAction(Request $request) {
        $form = $this->getCsvUploadForm();
        $form->bindRequest($request);
        
        $preview_only = $form->get('preview')->getData();
        $raw_only = $form->get('raw')->getData();
        
        $file = $form->get('csvfile');
        $filename = $file->getData();
        $pcsv = new ProductCSVDataUploader($filename);        
        
        if ($preview_only){
            $data = $pcsv->read();
            return $this->render('LoveThatFitAdminBundle:ProductData:preview_csv.html.twig', array('product'=>$pcsv->read()));        
            
        }elseif ($raw_only){
            $data = $pcsv->read();
            return new Response(json_encode($data));
        }else{
            $data = $pcsv->read();
            $ar = $this->savecsvdata($pcsv, $data);
        }
        #$data = $pcsv->map();
        #return new Response(json_encode($data));
        if ($ar['success']==false) {
            $this->get('session')->setFlash('warning',$ar['msg']);
        } else {
            $this->get('session')->setFlash('success',$ar['msg']);
        }
        
        return $this->render('LoveThatFitAdminBundle:ProductData:import_csv.html.twig', array('form' => $form->createView(),'product'=>$ar['obj'])
        );
        
    }
#------------------------------------------------------------#
    public function csvReadAction(Request $request) {
        $form = $this->getCsvUploadForm();
        $form->bindRequest($request);        
        $file = $form->get('csvfile');
        $filename = $file->getData();
        $pcsv = new ProductCSVHelper($filename);        
        $data = $pcsv->read();        
        return new Response(json_encode($data));
    }

    //------------------------------------------------------
    private function savecsvdata($pcsv, $data) {

        $retailer = $this->get('admin.helper.retailer')->findOneByName($data['retailer_name']);
        $clothingType = $this->get('admin.helper.clothingtype')->findOneByGenderName(strtolower($data['gender']), strtolower($data['clothing_type']));
        $brand = $this->get('admin.helper.brand')->findOneByName($data['brand_name']);
        $return_ar = array();
        $return_ar['msg'] = '';
        $return_ar['obj'] = null;
        if ($data['gender'] == Null) {
            $return_ar['msg'] = 'Gender did not match or provided';
            $return_ar['success'] = false;
        }elseif ($clothingType == Null) {
            $return_ar['msg'] = "Clothing Type did not match";
            $return_ar['success'] = false;
        } elseif ($brand == Null) {
            $return_ar['msg'] = 'Brand name did not match';
            $return_ar['success'] = false;
        }  else{
            
            $em = $this->getDoctrine()->getManager();
            $product = $pcsv->fillProduct($data);
            $product->setBrand($brand);
            $product->setClothingType($clothingType);
            $product->setRetailer($retailer);
            $em->persist($product);
            $em->flush();
            #----
            $this->addProductSizesFromArray($product, $data);
            $this->addProductColorsFromArray($product, $data); 
            $return_ar['obj'] = $product;             
            $return_ar['msg'] = 'Product successfully added';            
            $return_ar['success'] = true;
        }
        return $return_ar;
    }
    #------------------------------------------------------------
    private function addProductColorsFromArray($product, $data) {
        $em = $this->getDoctrine()->getManager();

        foreach ($data['product_color'] as $c) {
            $pc = new ProductColor;
            $pc->setTitle(strtolower($c));
            $pc->setProduct($product);
            $em->persist($pc);
            $em->flush();
        }
        return;
    }
    #------------------------------------------------------------
    private function addProductSizesFromArray($product, $data) {
        $em = $this->getDoctrine()->getManager();
        foreach ($data['sizes'] as $key => $value) {
            if ($this->sizeMeasurementsAvailable($value)) {
                $ps = new ProductSize;
                $ps->setTitle($key);
                $ps->setProduct($product);
                $ps->setBodyType($data['body_type']);                
                $em->persist($ps);
                $em->flush();
                $this->addProductSizeMeasurementFromArray($ps, $value);
            }
        }
        return $product;
    }
    #-----------------------------------------------------
    private function sizeMeasurementsAvailable($data) {
        $has_values = false;
        foreach ($data as $key => $value) {
            if ($key != 'key') {
                if ($value['garment_measurement_flat'] || $value['ideal_body_size_high'] || $value['ideal_body_size_low']) {
                    $has_values = true;
                }
            }
        }
        return $has_values;
    }
    #------------------------------------------------------
    private function addProductSizeMeasurementFromArray($size, $data) {
        $em = $this->getDoctrine()->getManager();
        foreach ($data as $key => $value) {
            if($key!='key'){
            $psm = new ProductSizeMeasurement;
            $psm->setTitle($key);
            $psm->setProductSize($size);
            array_key_exists('garment_measurement_flat',$value)?$psm->setGarmentMeasurementFlat($value['garment_measurement_flat']):null;
            array_key_exists('stretch_type_percentage',$value)?$psm->setStretchTypePercentage($value['stretch_type_percentage']):null;
            array_key_exists('garment_measurement_stretch_fit',$value)?$psm->setGarmentMeasurementStretchFit($value['garment_measurement_stretch_fit']):null;
            $psm->setMaxBodyMeasurement($value['maximum_body_measurement']);
            $psm->setIdealBodySizeHigh($value['ideal_body_size_high']);
            $psm->setIdealBodySizeLow($value['ideal_body_size_low']);
            $psm->setMinBodyMeasurement($value['min_body_measurement']);
            $psm->setFitModelMeasurement($value['fit_model']);
            $psm->setMaxCalculated($value['max_calculated']);
            $psm->setMinCalculated($value['min_calculated']);
            $psm->setGradeRule($value['grade_rule']);
            $em->persist($psm);
            $em->flush();
            }
            
        }
        return;
    }
    #------------------------------------------------------
    private function getCsvUploadForm(){
           return $this->createFormBuilder()
                ->add('csvfile', 'file')                     
                ->add('preview', 'checkbox', array(
                  'label'     => 'preview only',
                  'required'  => false,
                    ))   
                   ->add('raw', 'checkbox', array(
                  'label'     => 'raw data',
                  'required'  => false,
                    ))   
                ->getForm();
    }
    
    ###########################################################################
    
    public function importIndexAction() {
        $products = $this->get('admin.helper.product')->getListWithPagination();        
        return $this->render('LoveThatFitAdminBundle:ProductData:import_index.html.twig', array(
                    'products' => $products,                    
                )
        );
    }
    #-------------------------------------------------------
    public function showCurrentAction($product_id) {
        $product = $this->get('admin.helper.product')->find($product_id);        
        return $this->render('LoveThatFitAdminBundle:ProductData:_db_values.html.twig', array(
                    'product' => $product,                    
                )
        );
    }
}
