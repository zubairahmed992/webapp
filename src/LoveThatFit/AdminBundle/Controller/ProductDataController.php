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

class ProductDataController extends Controller
{
    //------------------------------------------------------------------------------------------
    #--------------------------------------------------------------
#-----------------------Form Upload CSV File------------------#

    public function csvIndexAction()
    {
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

        $size_types1 = array(
            'woman' => array('letter' => array('XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL', 'XXXXL',
                '1XL', '2XL', '3XL', '4XL', '1X', '2X', '3X', '4X'
            ),
                'number' => array(00, 0, 1, 2, 4, 6, 8, 10, 12, 14, 16, 18, 20, 22, 24, 26, 28, 30),
                'waist' => array(23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36)));

        $size_types_letters = array('XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL', 'XXXXL',
            '1XL', '2XL', '3XL', '4XL', '1X', '2X', '3X', '4X');
        $size_types_number = array(00, 0, 1, 2, 4, 6, 8, 10, 12, 14, 16, 18, 20, 22, 24, 26, 28, 30);
        $size_types_waist = array(23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36);

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


    public function saveBrandSpecificationAction(Request $request)
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
        die(json_decode($text));
        var_dump($_POST);
        foreach ($_POST as $name => $value) {
            $val[$name] = $value;
        }
        $em = $this->getDoctrine()->getManager();
        $pc = new BrandFormatImport();
        $pc->setBrandName($val['product_Brand']);
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


    public function csvMultipleBrandImportAction()
    {
        $value = '17 Sundays';
        $data = $this->getDoctrine()->getManager()
            ->createQueryBuilder()
            ->select('bfi.brand_format')
            ->from('LoveThatFitAdminBundle:BrandFormatImport', 'bfi')
            ->Where('bfi.brand_name =:brandName')
            ->setParameters(array('brandName' => $value))
            ->getQuery()
            ->getResult();
        $datJson = $data[0]['brand_format'];

        $productData = json_decode($datJson, true);
        echo "<h1> Multiple Product CSV Read</h1>";
        $row = 0;
        if (($handle = fopen($_FILES['productImport']['tmp_name'], "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                $raowValue[$row] = $data;
                $row++;
            }
            fclose($handle);
        }
        $rows = 0;
        foreach ($raowValue as $key => $value) {
            $num = count($value);
            for ($c = 0; $c < $num; $c++) {
                $aaa = $rows . "," . $c;
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
        $fit_points = explode(',', $productData['fit_point']);
        array_unshift($fit_points, 'sizes');

      //      print_r($fit_points);

        $product_size = trim($productData['select_size'], '[');
        $product_size_value = trim($product_size, ']');
        $size = explode(',', $product_size_value);
        foreach ($fit_points as $keys => $fit_point_val) {
            foreach ($size as $ke => $selected_size_val) {
                $fit_point[$fit_point_val . "_" . trim($selected_size_val, '""')] = '';
            }
        }
        echo "<pre>";
        //print_r($productSave);
        $result_array = array_intersect_key($productSave, $fit_point);
        echo "<pre>";
        foreach ($productSave as $key => $val) {

            if (array_key_exists($key, $result_array)) {
                break;
            } else {
                echo $key . " : " . $val . "<br>";
            }
        }
        echo "<pre>";
        echo "<p><table>";
        foreach ($fit_points as $keys => $fit_point_val) {
            echo "<tr><td>" . $fit_point_val . "</td>";
            $grade_rule = 0;
            $count = 0;
            foreach ($size as $ke => $selected_size_val) {
                $grade_rule = $grade_rule + 1;
                if ($fit_point_val == "sizes") {
                    echo "<td>" . trim($selected_size_val, '""') . "</td>";
                    echo "<td>GR </td>";
                } else {
                    $fit_points_key = $fit_point_val . "_" . trim($selected_size_val, '""');
                    if (array_key_exists($fit_points_key, $result_array)) {
                        $count = $count + 1;
                        echo "<td><input type='text' style='width:40px' value=" . $result_array[$fit_points_key] . "> </td>";
                        $gr = $fit_point_val . "_" . trim($size[$grade_rule], '"');
                        if (isset($result_array[$gr])) {
                            $gr_value = $result_array[$gr] - $result_array[$fit_points_key];
                            echo "<td><input type='text' style='width:40px' value=" . $gr_value . "> </td>";
                        } else {
                            echo "<td><input type='text' style='width:40px'> </td>";
                        }
                    } else {
                        echo "<td><input type='text' style='width:40px'></td>";
                        echo "<td><input type='text' style='width:40px'></td>";
                    }
                }

            }
            echo "</tr>";
        }
        echo "</table>";
        //////////////////////////////        Size Code ///////////////////////////////////////////////

        echo "<pre>";
        //echo $count;
       // $size_selected = array_chunk($result_array,$count);

     //  print_r($size_selected[0]);
      //  print array_search('50',$size_selected[0]);
      //  print_r($size);
        //die();
      // print_r($result_array);

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
        foreach ($size as $ke => $selected_size_val) {
                echo "<tr><td>" . $selected_size_val . "</td>";
                foreach ($sizes as $key => $size_labels){
                    echo "<td>" . $size_labels . "</td>";
                }
                foreach ($fit_points as $keys => $fit_point_vals) {
                    $fit_points_key = $fit_point_vals."_".trim($selected_size_val, '""');
                   // echo "<br>".$fit_points_key;
                      ///////////// Calculate the Grade Rule Value  /////////////////////////
                    if (array_key_exists($fit_points_key, $result_array)) {
                        $fit_point = $fit_point_vals . "_" . trim($size[$ke + 1], '""');
                        if (isset($result_array[$fit_point])) {
                            $fit_model = $result_array[$fit_points_key];
                            $fit_model_next = $result_array[$fit_point];
                            $grade_rule_value = $fit_model_next - $fit_model;
                            $min_calc = $fit_model - (2.5*$grade_rule_value);
                            $max_calc = $fit_model + (2.5*$grade_rule_value);
                            $ideal_low = $fit_model - $grade_rule_value;
                            $ideal_heigh = $fit_model + $grade_rule_value;
                        } else {
                            $grade_rule_value = "N/A";
                            $min_calc = 0;
                            $max_calc = 0;
                            $ideal_low = 0;
                            $ideal_heigh = 0;
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
                    echo "</tr><tr><td>" . $fit_point_vals . "</td>";
                      if (!empty($productData[$fit_points_key])){
                        echo "<td>" . $na . "</td>";
                        echo "<td>" . $na . "</td>";
                        echo "<td>" . $grade_rule_value . "</td>";
                        echo "<td>" . $min_calc . "</td>";
                        echo "<td>" . $min_calc . "</td>";
                        echo "<td>" . $ideal_low . "</td>";
                        echo "<td>" . $result_array[$fit_points_key] . "</td>";
                        echo "<td>" . $ideal_heigh . "</td>";
                        echo "<td>" . $max_calc . "</td>";
                        echo "<td>" . $max_calc . "</td>";
                        echo "<td>" . $result_array[$fit_points_key] . "</td>";
                       } else {
                          echo "<td>" . $na . "</td>";
                          echo "<td>" . $na . "</td>";
                          echo "<td>" . $na . "</td>";
                          echo "<td>" . $na . "</td>";
                          echo "<td>" . $na . "</td>";
                          echo "<td>" . $na . "</td>";
                          echo "<td>" . $na . "</td>";
                          echo "<td>" . $na . "</td>";
                          echo "<td>" . $na . "</td>";
                          echo "<td>" . $na . "</td>";
                          echo "<td>" . $na . "</td>";
                      }
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


        echo "</table>";


        die();
    }

#------------------------------------------------------------#
    public function csvUploadAction(Request $request)
    {
        $form = $this->getCsvUploadForm();
        $form->bindRequest($request);
        $product_id = $form->get('products')->getData();
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
        } elseif ($raw_only) {
            $data = $pcsv->read();
            return new Response(json_encode($data));
        } else {
            if ($product_id) {
                $product = $this->get('admin.helper.product')->find($product_id);
                $ar = $this->updateProduct($pcsv, $product);
            } else {
                $ar = $this->savecsvdata($pcsv);
            }

        }
        #$data = $pcsv->map();
        #return new Response(json_encode($data));
        if ($ar['success'] == false) {
            $this->get('session')->setFlash('warning', $ar['msg']);
        } else {
            $this->get('session')->setFlash('success', $ar['msg']);
        }

        return $this->render('LoveThatFitAdminBundle:ProductData:import_csv.html.twig', array('form' => $form->createView(), 'product' => $ar['obj'])
        );

    }

#------------------------------------------------------------#
    public function csvReadAction(Request $request)
    {
        $form = $this->getCsvUploadForm();
        $form->bindRequest($request);
        $file = $form->get('csvfile');
        $filename = $file->getData();
        $pcsv = new ProductCSVHelper($filename);
        $data = $pcsv->read();
        return new Response(json_encode($data));
    }

    //------------------------------------------------------
    private function savecsvdata($pcsv)
    {
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
        } elseif ($clothingType == Null) {
            $return_ar['msg'] = "Clothing Type did not match";
            $return_ar['success'] = false;
        } elseif ($brand == Null) {
            $return_ar['msg'] = 'Brand name did not match';
            $return_ar['success'] = false;
        } else {

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

    private function updateProduct($pcsv, $product)
    {
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
        } elseif ($clothingType == Null) {
            $return_ar['msg'] = "Clothing Type did not match";
            $return_ar['success'] = false;
        } elseif ($brand == Null) {
            $return_ar['msg'] = 'Brand name did not match';
            $return_ar['success'] = false;
        } else {


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
    private function addProductColorsFromArray($product, $data)
    {
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

    private function updateProductColorsFromArray($product, $data)
    {
        foreach ($data['product_color'] as $c) {
            $pc = $this->get('admin.helper.productcolor')->findColorByProductTitle(strtolower($c), $product->getId());
            if ($pc == null) {
                $pc = new ProductColor;
                $pc->setProduct($product);
            }
            $pc->setTitle(strtolower($c));
            $this->get('admin.helper.productcolor')->save($pc);
        }
        return;
    }

    #------------------------------------------------------------
    private function addProductSizesFromArray($product, $data)
    {
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

    private function updateProductSizesFromArray($product, $data)
    {
        foreach ($data['sizes'] as $key => $value) {
            if ($this->sizeMeasurementsAvailable($value)) {

                $ps = $this->get('admin.helper.productsizes')->findSizeByProductTitle($key, $product->getId());
                if ($ps == null) {
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
    private function sizeMeasurementsAvailable($data)
    {
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
    private function addProductSizeMeasurementFromArray($size, $data)
    {
        $em = $this->getDoctrine()->getManager();
        foreach ($data as $key => $value) {
            if ($key != 'key') {
                $psm = new ProductSizeMeasurement;
                $psm->setTitle($key);
                $psm->setProductSize($size);
                array_key_exists('garment_measurement_flat', $value) ? $psm->setGarmentMeasurementFlat($value['garment_measurement_flat']) : null;
                array_key_exists('stretch_type_percentage', $value) ? $psm->setStretchTypePercentage($value['stretch_type_percentage']) : null;
                array_key_exists('garment_measurement_stretch_fit', $value) ? $psm->setGarmentMeasurementStretchFit($value['garment_measurement_stretch_fit']) : null;
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

    private function updateProductSizeMeasurementFromArray($size, $data)
    {
        $em = $this->getDoctrine()->getManager();
        foreach ($data as $key => $value) {
            if ($key != 'key') {
                $psm = $size->fitpointMeasurements($key);
                if ($psm == null) {
                    $psm = new ProductSizeMeasurement;
                    $psm->setTitle($key);
                    $psm->setProductSize($size);
                }
                array_key_exists('garment_measurement_flat', $value) ? $psm->setGarmentMeasurementFlat($value['garment_measurement_flat']) : null;
                array_key_exists('stretch_type_percentage', $value) ? $psm->setStretchTypePercentage($value['stretch_type_percentage']) : null;
                array_key_exists('garment_measurement_stretch_fit', $value) ? $psm->setGarmentMeasurementStretchFit($value['garment_measurement_stretch_fit']) : null;
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
    private function getCsvUploadForm()
    {
        $products = $this->get('admin.helper.product')->idNameList();
        return $this->createFormBuilder()
            ->add('products', 'choice', array(
                'choices' => $products,
                'required' => false,
                'empty_value' => 'Select Product',))
            ->add('csvfile', 'file')
            ->add('preview', 'checkbox', array(
                'label' => 'preview only',
                'required' => false,
            ))
            ->add('raw', 'checkbox', array(
                'label' => 'raw data',
                'required' => false,
            ))
            ->getForm();
    }

    ###########################################################################

    public function importIndexAction()
    {
        $products = $this->get('admin.helper.product')->getListWithPagination();
        return $this->render('LoveThatFitAdminBundle:ProductData:import_index.html.twig', array(
                'products' => $products,
            )
        );
    }

    #-------------------------------------------------------
    public function dbProductShowAction($product_id, $json = false)
    {
        $pcsv = new ProductCSVDataUploader(null);
        $product = $this->get('admin.helper.product')->find($product_id);
        if ($json) {
            return new Response(json_encode($pcsv->DBProductToArray($product)));
        } else {
            return $this->render('LoveThatFitAdminBundle:ProductData:preview_csv.html.twig', array('product' => $pcsv->DBProductToArray($product), 'pcsv' => $pcsv));
        }
    }

    #-------------------------------------------------------
    public function csvProductShowAction()
    {

        $decoded = $this->getRequest()->request->all();
        $pcsv = new ProductCSVDataUploader($_FILES["csv_file"]["tmp_name"]);
        if ($decoded['json'] == 'true') {
            return new Response(json_encode($pcsv->read()));
        } else {
            return $this->render('LoveThatFitAdminBundle:ProductData:preview_csv.html.twig', array('product' => $pcsv->read(), 'pcsv' => $pcsv));
        }
    }

    #-------------------------------------------------------
    public function fooAction()
    {
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
        foreach ($directoryList as $key => $value) {
            if (array_key_exists('width', $value)) {
                $directory[$key] = $value['width'] . "," . $value['height'] . "," . $key;
            }
        }

        if (isset($_POST['deviceListName'])) {
            $newDirectoryList = explode(',', $_POST['deviceListName']);
            $width = $newDirectoryList[0];
            $height = $newDirectoryList[1];
            $newDirectory = $newDirectoryList[2];
            $src = $_SERVER['DOCUMENT_ROOT'] . 'webappBK/web/uploads/ltf/products/display/iphone_list';
            $dir = $_SERVER['DOCUMENT_ROOT'] . '/webappBK/web/uploads/ltf/products/display';
            // Get total Directory From Destination Path
            $totalDirectory = $this->get('admin.helper.productimagegenrate')->getTotalDirectories($dir);
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
                $message = array($messingcount . " Missing File Successfully Updated ");
            } else {
                $message = array("No changes Made");
            }
        }

        return $this->render('LoveThatFitAdminBundle:ProductData:product_image_genrate.html.twig', array(
            'deviceList' => $directory,
            'message' => $message,
        ));
    }

}
