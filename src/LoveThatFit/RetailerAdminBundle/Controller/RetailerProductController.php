<?php

namespace LoveThatFit\RetailerAdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class RetailerProductController extends Controller {

//---------------------------------------------------------------------

    public function indexAction($page_number, $sort = 'id') {
        //$this->productSaveYaml();      
        $product_with_pagination = $this->get('admin.helper.product')->getListWithPagination($page_number, $sort);
        return $this->render('LoveThatFitRetailerAdminBundle:Product:index.html.twig', $product_with_pagination);
    }

}

