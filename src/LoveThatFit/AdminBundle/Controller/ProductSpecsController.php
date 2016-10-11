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

class ProductSpecsController extends Controller {

    public function fooAction(){
        $size_specs=$this->get('admin.helper.size')->getAllSizeTitleType();
        $product_specs = $this->get('admin.helper.product.specification')->getProductSpecification();
         return $this->render('LoveThatFitAdminBundle:ProductSpecs:foo.html.twig', array(
            'product_specs' => $product_specs,
             'sizes' => $size_specs,
             'product_specs_json' => json_encode($product_specs),
             'size_specs_json' => json_encode($size_specs),
             )
        );
        return new Response(json_encode($product_specs));
    }

}
