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
#-------------------- Multiple Product CSV Pars ------------------------#
    public function csvBrandSpecificationAction()
    {
        //$brandObj = json_encode($this->get('admin.helper.brand')->getBrandNameId());
        $brandNames = $this->get('admin.helper.brand')->getBrandNameId();
        // var_dump($brandObj);

        $size_types1 = array(
            'woman' => array('letter' => array('XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL', 'XXXXL',
                '1XL', '2XL', '3XL', '4XL', '1X', '2X', '3X', '4X'
            ),
                'number' => array(00, 0, 1, 2, 4, 6, 8, 10, 12, 14, 16, 18, 20, 22, 24, 26, 28, 30),
                'waist' => array(23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36)));

        $size_types_letters = array('Calculation', 'XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL', 'XXXXL',
            '1XL', '2XL', '3XL', '4XL', '1X', '2X', '3X', '4X');
        $size_types_number = array('Calculation', '00', 0, 2, 4, 6, 8, 10, 12, 14, 16, 18, 20, 22, 24, 26, 28, 30);
        $size_types_waist = array('Calculation', 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36);

        $fit_points = array('tee_knit', 'neck', 'shoulder_across_front', 'shoulder_across_back', 'shoulder_length', 'arm_length',
            'bicep', 'triceps', 'wrist', 'bust', 'chest', 'back_waist', 'waist', 'cf_waist', 'waist_to_hip', 'hip', 'outseam', 'inseam', 'thigh', 'knee', 'calf', 'ankle', 'hem_length');

        $size_fit_points = array($fit_points, $size_types_letters, $size_types_number, $size_types_waist);

        return $this->render('LoveThatFitAdminBundle:ProductData:brand_format.html.twig', array('brandNames' => $brandNames, 'size_fit_points' => $size_fit_points));
        die("csvBrandSpecification");
    }

//    public function AutoCompleteProductSearchResultAction(Request $request) {
//        $decoded  = $request->request->all();
//        $search_result_user_data = $this->get('admin.helper.product')->getSearchData($decoded["term"]);
//        return new Response(json_encode($search_result_user_data));
    //$id = $request->request->get('sizeValue');

//    }
    public function brandDescriptionAction(Request $request)
    {

        $request = $request->request->all();
        $brand_name = $request['brand_name'];

        $data = $this->getDoctrine()->getManager()
            ->createQueryBuilder()
            ->select('bfi.brand_description')
            ->from('LoveThatFitAdminBundle:BrandFormatImport', 'bfi')
            ->Where('bfi.brand_name =:brandName')
            ->setParameters(array('brandName' => $brand_name))
            ->getQuery()
            ->getResult();


        return new Response(json_encode($data));
    }

    public function saveBrandSpecificationAction(Request $request)
    {
        foreach ($_POST as $name => $value) {
            $val[$name] = $value;
        }
        $em = $this->getDoctrine()->getManager();
        $pc = new BrandFormatImport();
        $pc->setBrandName($val['brand_name']);
        $pc->setBrandDescription($val['brand_description']);
        $pc->setBrandFormat(json_encode($val));
        $em->persist($pc);
        $em->flush();
        print_r($val);
        die("Successfully Save");

    }

    public function csvMultipleBrandImportFormAction()
    {
        $brandNames = $this->get('admin.helper.brand')->getBrandNameId();
        return $this->render('LoveThatFitAdminBundle:ProductData:import_multiple_brand_csv.html.twig', array('brandNames' => $brandNames));
    }

    public  function csvMapperRunTimeAction()
    {
        $row = 0;
        if (($handle = fopen($_FILES['productImport']['tmp_name'], "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 100000000, ",")) !== FALSE) {
                $csvData[$row] = $data;
                $row++;
            }
            fclose($handle);
        }
         //  echo "<pre>";
        //print_r($csvData);
        $product_attribute = array('garment_name', 'stretch_type', 'horizontal_stretch', 'vertical_stretch', 'gender', 'styling_type', 'style', 'neck_line', 'sleeve_styling', 'rise','hem_length', 'fabric_weight', 'structural_detail', 'fit_type', 'layring', 'size_title_type');
        return $this->render('LoveThatFitAdminBundle:ProductData:csv_mapper_run_time.html.twig', array('csvData' => $csvData , 'product_attribute' => $product_attribute));


        //        echo "<h1> Multiple Product CSV Read</h1>";
        //        echo "<html><body><table border='1'>\n\n";
        //        $f = fopen($_FILES['productImport']['tmp_name'], "r");
        //        while (($line = fgetcsv($f)) !== false) {
        //            echo "<tr>";
        //            foreach ($line as $key => $cell) {
        //                echo "<td class='getValueCell'>" . htmlspecialchars($cell) .$key. "</td>";
        //            }
        //            echo "</tr>\n";
        //        }
        //        fclose($f);
        //        echo "\n</table></body></html>";

    }
    public function csvMultipleBrandImportAction()
    {
        $brand_description_value = $_POST['brand_Description_value'];
        $data = $this->getDoctrine()->getManager()
            ->createQueryBuilder()
            ->select('bfi.brand_format')
            ->from('LoveThatFitAdminBundle:BrandFormatImport', 'bfi')
            ->Where('bfi.brand_description =:brandName')
            ->setParameters(array('brandName' => $brand_description_value))
            ->getQuery()
            ->getResult();
        $datJson = $data[0]['brand_format'];

        $productData = json_decode($datJson, true);
        echo "<pre>";
        //print_r($productData);

        $row = 0;
        if (($handle = fopen($_FILES['productImport']['tmp_name'], "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000000, ",")) !== FALSE) {
                $raowValue[$row] = $data;
                $row++;
            }
            fclose($handle);
        }
        $rows = 0;
        foreach ($raowValue as $key => $value) {
            $num = count($value);
            for ($c = 0; $c < $num; $c++) {
                $aaa = $rows .",". $c;
                if (in_array($aaa, $productData)) {
                    $key1 = array_search($aaa, $productData);
                    $productSave[$key1] = $raowValue[$rows][$c];
                }
            }
            $rows++;
        }

//        $fit_points = array('sizes', 'tee_knit', 'neck', 'shoulder_across_front', 'shoulder_across_back', 'shoulder_length', 'arm_length',
//            'bicep', 'triceps', 'wrist', 'bust', 'chest', 'back_waist', 'waist', 'cf_waist',
//            'waist_to_hip', 'hip', 'outseam', 'inseam', 'thigh', 'knee', 'calf', 'ankle', 'hem_length');
        $sizes = array('Garment Dimension', 'Garment Stretch', 'Grade Rule', 'Min Calc',	'Min Actual', 'Ideal Low', 'Fit Model','Ideal High', 'Max Actual', 'Max Calc', 'Range Conf');
        $fit_point_trim = trim($productData['fit_point'],'[');
        $fit_point_trim_value = trim($fit_point_trim,']');
        $fit_points = explode(',', $fit_point_trim_value);
        array_unshift($fit_points, 'sizes');
        $product_size = trim($productData['select_size'], '[');
        $product_size_value = trim($product_size, ']');
        $size = explode(',', $product_size_value);
        echo "<pre>";
      // print_r($productSave);

        foreach ($fit_points as $keys => $fit_point_val) {
            if($fit_point_val == 'sizes') continue;
            $flag_formula=true;
            foreach ($size as $ke => $selected_size_val) {
                if($flag_formula)
                    $fit_point_formula[trim($fit_point_val, '""') . "_" . trim($selected_size_val, '""')] = '';
                $fit_point[trim($fit_point_val, '""') . "_" . trim($selected_size_val, '""')] = '';
                $flag_formula = false;
            }
        }

        $result_array = array_intersect_key($productSave, $fit_point);
        $formula_arry = array_intersect_key($productData, $fit_point_formula);
        echo "<pre>";
        $data=array();
        //print_r($result_array);
        foreach ($productSave as $key => $val) {

            if (array_key_exists($key, $result_array)) {
                continue;
            } else {
                $data[$key] = $val;
                echo $key . " : " . $val . "<br>";
            }
        }
        echo "<pre>";
       // print_r($data);
       // die();
      //////////////////////////////        Size Code ///////////////////////////////////////////////

        echo "<br>";
        echo "<p><table>";
        $sizes = array('Garment Dimension', 'Garment Stretch', 'Grade Rule', 'Min Calc',	'Min Actual', 'Ideal Low', 'Fit Model','Ideal High', 'Max Actual', 'Max Calc', 'Range Conf');

        // remove first elemnt of array
        array_shift($fit_points);
        $na = "N/A";
        $min_calc = 0;
        $max_calc =0;
        $ideal_low = 0;
        $ideal_heigh = 0;
        $fit_model=null;
        $grade_rule_value = 0;
        // Combine array value and keys
        foreach ($fit_points as $ke => $fit_point_size_val) {
            $fit_point_sizes [] = trim($fit_point_size_val, '""')."_Calculation";
        }
        $fit_point_value = array_combine($fit_point_sizes, $fit_points);
        // End Combine array value and keys

        foreach ($size as $ke => $selected_size_val) {
            $flag = true;
            if(trim($selected_size_val, '""') == "Calculation") continue;

            //echo "<tr><td>" . $selected_size_val . "</td>";
            foreach ($sizes as $key => $size_labels){
               // echo "<td>" . $size_labels . "</td>";
            }
            foreach ($fit_point_value as $fit_point_keys => $fit_point_vals) {
                $fit_points_key = trim($fit_point_vals,'""')."_".trim($selected_size_val, '""');

                if(!empty($formula_arry[$fit_point_keys]) && isset($result_array[$fit_points_key])) {

                    $formula = explode(" ", $formula_arry[$fit_point_keys] );
                    switch ($formula[1]) {
                        case '*':
                            //   echo "Multiplie";
                            $fit_model = $result_array[$fit_points_key] * $formula[2];
                            break;
                        case '/':
                            //   echo "Divide";
                            $fit_model = $result_array[$fit_points_key] / $formula[2];
                            break;
                        case '+':
                            //  echo "Addition";
                            $fit_model = $result_array[$fit_points_key] + $formula[2];
                            break;
                        case '-':
                            // echo "Subtration";
                            $fit_model = $result_array[$fit_points_key] - $formula[2];
                            break;
                    }
                }
                // echo $fit_model;
                //die();
                ///////////// Calculate the Grade Rule Value  /////////////////////////
                if (array_key_exists($fit_points_key, $result_array)) {

                    if( isset($size[$ke + 1]) )
                        $fit_point = trim($fit_point_vals,'""') . "_" . trim($size[$ke + 1], '""');
                    // else
                    //   $fit_point = trim($fit_point_vals,'""') . "_" . trim($size[$ke], '""');


                    if (isset($result_array[$fit_point])) {//
                        //   $data['sizes'][$selected_size_val] = array();
                        $sizess['sizes'][$selected_size_val][$fit_point_vals]=array();
                        $fit_model = $result_array[$fit_points_key];
                        // $fit_model = $result_array[$fit_points_key];
                        $fit_model_next = $result_array[$fit_point];
                        $grade_rule_value = $fit_model_next - $fit_model;
                        $min_calc = $fit_model - (2.5*$grade_rule_value);
                        $max_calc = $fit_model + (2.5*$grade_rule_value);
                        $ideal_low = $fit_model - $grade_rule_value;
                        $ideal_heigh = $fit_model + $grade_rule_value;
                        $sizess['sizes'][$selected_size_val][$fit_point_vals]['garment_measurement_flat'] = $fit_model;
                        $sizess['sizes'][$selected_size_val][$fit_point_vals]['garment_measurement_stretch_fit'] = $fit_model;
                        $sizess['sizes'][$selected_size_val][$fit_point_vals]['grade_rule'] = $grade_rule_value;
                        $sizess['sizes'][$selected_size_val][$fit_point_vals]['min_calculated'] = $min_calc;
                        $sizess['sizes'][$selected_size_val][$fit_point_vals]['min_body_measurement'] = $ideal_low;
                        $sizess['sizes'][$selected_size_val][$fit_point_vals]['ideal_body_size_low'] = $fit_model;
                        $sizess['sizes'][$selected_size_val][$fit_point_vals]['fit_model'] = $fit_model;
                        $sizess['sizes'][$selected_size_val][$fit_point_vals]['ideal_body_size_high'] = $fit_model;
                        $sizess['sizes'][$selected_size_val][$fit_point_vals]['maximum_body_measurement'] = $ideal_heigh;
                        $sizess['sizes'][$selected_size_val][$fit_point_vals]['max_calculated'] = $max_calc;
                    } else {
                        $grade_rule_value = "N/A";
                        $min_calc = 0;
                        $max_calc = 0;
                        $ideal_low = 0;
                        $ideal_heigh = 0;
                        $fit_model = 0;
                    }
                }
                ///////////// End Calculate the Grade Rule Value  /////////////////////////


                // $grade_rule = $grade_rule + 1;
                // echo $grade_rule;
                //$fit_points_key = $fit_point_val."_".trim($size[$ke+$grade_rule], '""');
                // die("asdfasdfadf");
                // $gr = $fit_point_val . "_" . trim($size[$grade_rule], '"');
                // $gr = $fit_point_vals . "_" . trim($size[$grade_rule], '"');

                //$size_second = array_values($result_array);

                //echo $b."-";
                //echo $grade_rule_value;
//                echo "</tr><tr><td>" . $fit_point_vals . "</td>";
//                if (!empty($productData[$fit_points_key])){
//                    echo "<td>" . $na . "</td>";
//                    echo "<td>" . $na . "</td>";
//                    echo "<td>" . $grade_rule_value . "</td>";
//                    echo "<td>" . $min_calc . "</td>";
//                    echo "<td>" . $min_calc . "</td>";
//                    echo "<td>" . $ideal_low . "</td>";
//                    echo "<td>" . $fit_model . "</td>";
//                    echo "<td>" . $ideal_heigh . "</td>";
//                    echo "<td>" . $max_calc . "</td>";
//                    echo "<td>" . $max_calc . "</td>";
//                    echo "<td>" . $ideal_low . "</td>";
//                } else {
//                    echo "<td></td>";
//                    echo "<td></td>";
//                    echo "<td></td>";
//                    echo "<td></td>";
//                    echo "<td></td>";
//                    echo "<td></td>";
//                    echo "<td></td>";
//                    echo "<td></td>";
//                    echo "<td></td>";
//                    echo "<td></td>";
//                    echo "<td></td>";
//                }
                // }
//                        $fit_points_key = $fit_point_val . "_" . trim($selected_size_val, '""');
//                        if (array_key_exists($fit_points_key, $result_array)) {
//                            echo "<td><input type='text' style='width:40px' value=" . $result_array[$fit_points_key] . "> </td>";
//                            $gr = $fit_point_val . "_" . trim($size[$grade_rule], '"');
//                            if (isset($result_array[$gr])) {
//                                $gr_value = $result_array[$gr] - $result_array[$fit_points_key];
//                                echo "<td><input type='text' style='width:40px' value=" . $gr_value . "> </td>";
//                            } else {
//                                echo "<td><input type='text' style='width:40px'> </td>";
//                            }
//                        } else {
//                            echo "<td><input type='text' style='width:40px'></td>";
//                            echo "<td><input type='text' style='width:40px'></td>";
//                        }



            } // end inner loop
            echo "</tr>";

        } // end Sizes loop

        // Fill next Array Level
        function strstr_after($haystack, $needle, $case_insensitive = false) {
            $strpos = ($case_insensitive) ? 'stripos' : 'strpos';
            $pos = $strpos($haystack, $needle);
            if (is_int($pos)) {
                return substr($haystack, $pos + strlen($needle));
            }
            // Most likely false or null
            return $pos;
        }
        $fit_prority    = array();
        $fabric_content = array();
        $product_color  = array();
        foreach ($productSave as $fit => $fitpriority) {
            // $email = 'hello_stackoverflow';
            if(strstr_after($fit, 'fitpriority_'))
                $fit_prority[strstr_after($fit, 'fitpriority_')] = $fitpriority;
            if(strstr_after($fit, 'fabriccontent_'))
                $fabric_content[strstr_after($fit, 'fabriccontent_')] = $fitpriority;
            if(strstr_after($fit, 'productcolor_'))
                $product_color[strstr_after($fit, 'productcolor_')] = $fitpriority;

        }


        $data['fit_priority']   = $fit_prority;
        $data['fabric_content'] = $fabric_content;
        $data['product_color']  = $product_color;
        $data['sizes']          = $sizess['sizes'];
        // End fill next Level Array
       // print_r(array_keys($data['sizes']));
        $sizes = array_keys($data['sizes']);
        $keysize = $sizes[0];
        $keysizedimension = $data['sizes'][$keysize];

        $keysizedimensionvalue = array_keys($keysizedimension);
        $keydimentionvaluew = $keysizedimensionvalue[0];

        $dimension = array_keys($data['sizes'][$keysize][$keydimentionvaluew]);
       // print_r($dimension);
      //  die("okay");
        echo "<table border=5>";
        foreach ($data['sizes'] as $keys => $item) {
            echo "<tr><th>" . $keys . "</th>";
            foreach ($dimension as $deminsionkey => $dimensionvalue) {
                if($dimensionvalue == 'fit_model')
                    echo "<th bgcolor='#7fff00'>" . $dimensionvalue . "</th>";
                else
                    echo "<th>" . $dimensionvalue . "</th>";
            }
            echo "</tr><tr>";
            foreach ($item as $size_keys => $size_values) {
                echo "<td>".$size_keys."</td>";
                $tr = 0;
                foreach ($size_values as $size_key => $sizevalues) {
                    $tr++;
                    if($tr == 7)
                        echo "<td bgcolor='#7fffd4'>".$sizevalues."</td>";
                    else
                    echo "<td>".$sizevalues."</td>";



                }
                echo "</tr>";

            }
        }
        echo "</tr>";
        echo "</table>";
        echo "<pre>";
       // $data['brand_name'] = 'citizens of humanity';
       //  $data['clothing_type'] =  'jean';
       //  $data['retailer_name'] = 'citizens of humanity';
        $data['size_title_type'] = 'waist';
        $data['body_type'] = 'Regular';
       // $data['stretch_type']   = '';
       // $data['style']   = '';
       // $data['neck_line']   = '';
       // $data['sleeve_styling']   = '';
       // $data['rise']   = '';
       // $data['hem_length']   = '';
       // $data['fabric_weight']   = '';
       // $data['fit_type']   = '';

      //  print_r($data);
        //die();
//        $em = $this->getDoctrine()->getManager();
//        $pcsv = new ProductCSVDataUploader();
//        $product = $pcsv->fillProduct($data);
//        $clothingType = $this->get('admin.helper.clothingtype')->findOneByGenderName(strtolower($data['gender']), strtolower($data['clothing_type']));
//        $brand = $this->get('admin.helper.brand')->findOneByName($data['retailer_name']);
//        $product->setBrand($brand);
//        $product->setClothingType($clothingType);
//        $retailer = $this->get('admin.helper.retailer')->findOneByName($data['retailer_name']);
//        $product->setRetailer($retailer);
//        $em->persist($product);
//        $em->flush();
//        $this->addProductSizesFromArray($product, $data);
//        $this->addProductColorsFromArray($product, $data);
        //$src = str_replace('\\', '/', getcwd()). '/uploads/ltf/csvproducts/';
        $src =  getcwd(). '/uploads/ltf/products/raw_products_csv/';
        $name = $_FILES['productImport']['name'];
        move_uploaded_file($_FILES['productImport']['tmp_name'], $src.$name);
        rename($src.$name, $src.$brand_description_value.".csv");
        die("save Data");
    }
#-------------------- End Multiple Product CSV Pars ------------------------#
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
        $size_types = array(
            'woman' => array('letter' => array('XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL', 'XXXXL',
                    '1XL', '2XL', '3XL', '4XL', '1X', '2X', '3X', '4X'
                ),
                'number' => array('00', '0', '1', '2', '4', '6', '8', '10', '12', '14', '16', '18', '20', '22', '24', '26', '28', '30'),
                'waist' => array('23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35', '36')));
        
        $fit_points = array('tee_knit', 'neck', 'shoulder_across_front', 'shoulder_across_back', 'shoulder_length', 'arm_length',
            'bicep', 'triceps', 'wrist', 'bust', 'chest', 'back_waist', 'waist', 'cf_waist', 'waist_to_hip', 'hip', 'outseam', 'inseam', 'thigh', 'knee', 'calf', 'ankle', 'hem_length');

        #return new Response(json_encode(array($size_types, $fit_points)));
        return $this->render('LoveThatFitAdminBundle:ProductData:foo.html.twig',  array(
            'size_types' => $size_types,
            'fit_points'   => $fit_points,
        ));
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
            $src = str_replace('\\', '/', getcwd()). '/uploads/ltf/products/display/iphone_list';
            $dir = str_replace('\\', '/', getcwd()). '/uploads/ltf/products/display';
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
