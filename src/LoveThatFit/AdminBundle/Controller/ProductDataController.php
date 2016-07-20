<?php

namespace LoveThatFit\AdminBundle\Controller;

use LoveThatFit\AdminBundle\Entity\BrandFormatImport;
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
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;

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

    public function csvBrandSpecificationAction()
    {
        //$brandObj = json_encode($this->get('admin.helper.brand')->getBrandNameId());
        $brandNames = $this->get('admin.helper.brand')->getBrandNameId();
       // var_dump($brandObj);
        $sizeConfiguration = array(
                                    array('size cm','S','M','L','XL','XXL','Tolran ce'),
                                    array('Cross Ches','','','','','',''),
                                    array('Cross Chest','','','','','',''),
                                    array('HSP Lenght','','','','','',''),
                                    array('NECK OPENING','','','','','',''),

                                  );
       // $sizeConfiguration[1] = ('Cross Chest',);
        //$sizeConfiguration[2] = ('HSP Lenght','','','','','','');
       //// $sizeConfiguration[3] = ('NECK OPENING','','','','','','');
       // $sizeConfiguration[4] = ('Bsck Neck Depth','','','','','','');

        return $this->render('LoveThatFitAdminBundle:ProductData:brand_format.html.twig',array('brandNames' => $brandNames,'sizeConfiguration'=>$sizeConfiguration));
        die( "csvBrandSpecification");
    }

    public function saveBrandSpecificationAction()
    {
        var_dump($_POST);
        foreach ($_POST as $name => $value) {
            $val[$name] = $value;
        }
        print_R($val['product_Brand']);
//        $em = $this->getDoctrine()->getManager();
//        $pc = new BrandFormatImport();
//        $pc->setBrandName($val['product_Brand']);
//        $pc->setBrandFormat(json_encode($val));
//        $em->persist($pc);
//        $em->flush();
        print_r($val);
       // $data = implode(',',$_POST);
       // print_r(explode(",",$data));
        die("csv_brand_specification_save");
    }

    public function csvMultipleBrandImportFormAction()
    {
        //$brandObj = json_encode($this->get('admin.helper.brand')->getBrandNameId());
       $brandNames = $this->get('admin.helper.brand')->getBrandNameId();

        // var_dump($brandObj);
        return $this->render('LoveThatFitAdminBundle:ProductData:import_multiple_brand_csv.html.twig',array('brandNames' => $brandNames));
        die("okadfasdf");
    }


    public function csvMultipleBrandImportAction()
    {
        $value = 'AEO';
        $data =  $this->getDoctrine()->getManager()
            ->createQueryBuilder()
            ->select('bfi.brand_format')
            ->from('LoveThatFitAdminBundle:BrandFormatImport','bfi')
            ->Where('bfi.brand_name =:brandName')
            ->setParameters(array('brandName' => $value))
            ->getQuery()
            ->getResult();
        $datJson = $data[0]['brand_format'];
      //  echo $datJson;
        $productData= json_decode($datJson,true);
        print_r($productData);
       // echo $_POST['productImport'];
       var_dump($_FILES['productImport']['tmp_name']);
     //   die("ofkayasfdsd");


        $row = 0;
        if (($handle = fopen($_FILES['productImport']['tmp_name'], "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                $raowValue[$row] = $data;

                //echo $data[0][0];
               // echo "<p> $num fields in line $row: <br /></p>\n";
                $row++;
                for ($c=0; $c < $num; $c++) {
                   // if(in_array($data[$c],$productData))
                 //   echo $data[$c]. "<br />\n";
                }
            }
            fclose($handle);
        }
        echo "<pre>";
       //  print_r($raowValue[0][0]);
        echo "<br>";
        $rows=0;
        foreach($raowValue as $key => $value){
            $num = count($value);
           // echo $num;
            for ($c=0; $c < $num; $c++) {
                // if(in_array($data[$c],$productData))
                //   echo $data[$c]. "<br />\n";
              //    echo $raowValue[$rows][$c]. "<br />\n";
                $aaa = $rows.",".$c;
                if(in_array($aaa, $productData)){
                    $key1 = array_search ($aaa, $productData);
                  ///  echo $key1."<br>";
                     $productSave[$key1] = $raowValue[$rows][$c];
                }



            }
            $rows++;


        }

        echo "<pre>";
       print_r($productSave);
       // print_r($raowValue[0][1]);
        die("asdfsadfsad");



        echo "<br>";



        //print_r($data[0]['control_number']);
        print_r($data);

        die("csvMultipleBrandImport");
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
    /**
     * @return string
     * Create new device type images and store images into given devie type also add missing files into selected directory
     */
    public function productImageGenrateAction()
    {
        $message = array();
        $yaml = new Parser();
        $conf = $yaml->parse(file_get_contents('../app/config/image_helper.yml'));
        $directoryList = $conf['image_category']['product'];
        foreach($directoryList as $key => $value  ){
            if( array_key_exists('width',$value)){
                $directory[$key] = $value['width'].",".$value['height'].",".$key;
            }
        }

        if(isset($_POST['deviceListName'])) {
            $newDirectoryList = explode(',', $_POST['deviceListName']);
            $width = $newDirectoryList[0];
            $height = $newDirectoryList[1];
            $newDirectory = $newDirectoryList[2];
            $src = $_SERVER['DOCUMENT_ROOT'].'webappBK/web/uploads/ltf/products/display/iphone_list';
            $dir = $_SERVER['DOCUMENT_ROOT'].'/webappBK/web/uploads/ltf/products/display';
            // Get total Directory From Destination Path
            $totalDirectory =  $this->get('admin.helper.productimagegenrate')->getTotalDirectories($dir);
            $dest = $dir . "/" . $newDirectory;
            $srcfilesCount = $this->get('admin.helper.productimagegenrate')->getCountFiles($src);
            $dstfilesCount = $this->get('admin.helper.productimagegenrate')->getCountFiles($dest);
            if (!in_array($newDirectory, $totalDirectory)) {
                mkdir($dir . "/" . $newDirectory, 0777, true);
                $contents = $this->get('admin.helper.productimagegenrate')->getImages($src);
                foreach ($contents as $file) {
                    if ($file == ".") continue;
                    if ($file == "..") continue;
                    $array = explode('.', $file);
                    $extension = end($array);
                    $src_path = $src . '/' . $file;
                    $dest_path = $dest . '/' . $file;
                    $this->get('admin.helper.productimagegenrate')->setPathResizeDimentions($src_path, $dest_path, $extension, $width, $height);
                }
                $message = array("Successfully File Coipied");
            } else if ($srcfilesCount != $dstfilesCount) {
                $srcFiels = $this->get('admin.helper.productimagegenrate')->getImages($src);
                $destFiels = $this->get('admin.helper.productimagegenrate')->getImages($dest);
                foreach ($srcFiels as $file) {
                    if ($file == ".") continue;
                    if ($file == "..") continue;
                    if (!in_array($file, $destFiels)) {
                        $array = explode('.', $file);
                        $missingImages[] = $file;
                        $extension = end($array);
                        $src_path = $src . '/' . $file;
                        $dest_path = $dest . '/' . $file;
                        $this->get('admin.helper.productimagegenrate')->setPathResizeDimentions($src_path, $dest_path, $extension, $width, $height);
                    }
                }
                $messingcount = "Total Missing Images are " . count($missingImages);
                $message = array($messingcount." Missing File Successfully Updated ");
            } else {
                $message = array("No changes Made");
            }
        }

        return $this->render('LoveThatFitAdminBundle:ProductData:product_image_genrate.html.twig',  array(
            'deviceList' => $directory,
            'message'   => $message,
        ));
    }

}
