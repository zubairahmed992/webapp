<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ProductColor;
use LoveThatFit\AdminBundle\Entity\ProductSize;
use LoveThatFit\AdminBundle\Entity\ProductSizeMeasurement;
use LoveThatFit\AdminBundle\Entity\ProductItem;
use LoveThatFit\AdminBundle\Entity\ProductCSVHelper;
use LoveThatFit\AdminBundle\Entity\ProductCSVDataUploader;

class ProductDataController extends Controller {

    //------------------------------------------------------------------------------------------
   #--------------------------------------------------------------
#-----------------------Form Upload CSV File------------------#

    public function csvIndexAction() {
        $products = $this->get('admin.helper.product')->getListWithPagination();                
        $form = $this->getCsvUploadForm();
        return $this->render('LoveThatFitAdminBundle:ProductData:import_csv.html.twig', array('form' => $form->createView(),
            'products' => $products,)
        );
    }

#------------------------------------------------------------#
      public function csvUploadAction(Request $request) {
        $form = $this->getCsvUploadForm();
        $form->bindRequest($request);
        $product_id=$form->get('products')->getData();        
        $preview_only = $form->get('preview')->getData();
        $raw_only = $form->get('raw')->getData();
                
        $file = $form->get('csvfile');
        $filename = $file->getData();
        $pcsv = new ProductCSVDataUploader($filename);        
        
        ########################################
        
        if ($preview_only) {
            if ($product_id) {
                $product = $this->get('admin.helper.product')->find($product_id);
                $db_product = $pcsv->DBProductToArray($product);                
                #$csv_product = $pcsv->read();                
                #return new Response(json_encode($pcsv->compare_color_array($db_product['product_color'], $csv_product['product_color'])));
                
                return $this->render('LoveThatFitAdminBundle:ProductData:preview_db.html.twig', array('product' => $pcsv->read(), 'pcsv' => $pcsv, 'db_product' => $db_product));
            } else {
                return $this->render('LoveThatFitAdminBundle:ProductData:preview_csv.html.twig', array('product' => $pcsv->read(), 'pcsv' => $pcsv));
            }
        }elseif ($raw_only){
            $data = $pcsv->read();
            return new Response(json_encode($data));
        }else{
            if ($product_id) {
                $product = $this->get('admin.helper.product')->find($product_id);                
                $ar = $this->updateProduct($pcsv, $product);
            } else {
                $ar = $this->savecsvdata($pcsv);
            }
            
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
    private function savecsvdata($pcsv) {
        $data = $pcsv->read();
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
    
    private function updateProduct($pcsv, $product) {
        $data = $pcsv->read();
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
            
            
            $product = $pcsv->fillProduct($data, $product);
            $product->setBrand($brand);
            $product->setClothingType($clothingType);
            $product->setRetailer($retailer);
            $this->get('admin.helper.product')->update($product);
            $this->updateProductSizesFromArray($product, $data);
            $this->updateProductColorsFromArray($product, $data); 
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
    private function updateProductColorsFromArray($product, $data) {        
        foreach ($data['product_color'] as $c) {
            $pc=$this->get('admin.helper.productcolor')->findColorByProductTitle(strtolower($c), $product->getId());            
            if($pc==null){
                $pc = new ProductColor;
                $pc->setProduct($product);                
            }
            $pc->setTitle(strtolower($c));            
            $this->get('admin.helper.productcolor')->save($pc);            
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
    private function updateProductSizesFromArray($product, $data) {
        foreach ($data['sizes'] as $key => $value) {
            if ($this->sizeMeasurementsAvailable($value)) {
                
                $ps=$this->get('admin.helper.productsizes')->findSizeByProductTitle($key, $product->getId());
                if($ps==null){
                    $ps = new ProductSize;
                    $ps->setTitle($key);
                    $ps->setProduct($product);                
                }
                $ps->setBodyType($data['body_type']);                
               $ps = $this->get('admin.helper.productsizes')->save($ps);
                $this->updateProductSizeMeasurementFromArray($ps, $value);
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
     
    private function updateProductSizeMeasurementFromArray($size, $data) {
        $em = $this->getDoctrine()->getManager();
        foreach ($data as $key => $value) {
            if($key!='key'){
            $psm=$size->fitpointMeasurements($key);    
             if($psm==null){
                 $psm = new ProductSizeMeasurement;
                 $psm->setTitle($key);
                 $psm->setProductSize($size);
             }
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
        $products= $this->get('admin.helper.product')->idNameList();        
           return $this->createFormBuilder()
                ->add('products','choice', array( 
                     'choices' => $products,
                    'required' => false,
                    'empty_value' => 'Select Product',))
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
    public function dbProductShowAction($product_id, $json=false) {
        $pcsv = new ProductCSVDataUploader(null);
        $product = $this->get('admin.helper.product')->find($product_id);        
        if($json){
            return new Response(json_encode($pcsv->DBProductToArray($product)));    
        }else{
            return $this->render('LoveThatFitAdminBundle:ProductData:preview_csv.html.twig', array('product'=>$pcsv->DBProductToArray($product), 'pcsv'=>$pcsv));        
        }
    }
    #-------------------------------------------------------
    public function csvProductShowAction() {
        
        $decoded = $this->getRequest()->request->all();
        $pcsv = new ProductCSVDataUploader($_FILES["csv_file"]["tmp_name"]);
        if($decoded['json']=='true'){                    
            return new Response(json_encode($pcsv->read()));
        }else{
            return $this->render('LoveThatFitAdminBundle:ProductData:preview_csv.html.twig', array('product'=>$pcsv->read(), 'pcsv'=>$pcsv));        
        }
    }
    #-------------------------------------------------------
    public function fooAction() {
        $decoded = $this->getRequest()->request->all();        
        return new Response(json_encode($decoded));
        
    }
}
