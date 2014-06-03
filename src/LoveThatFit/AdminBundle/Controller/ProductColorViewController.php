<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ProductColor;
use LoveThatFit\AdminBundle\Entity\ProductSize;
use LoveThatFit\AdminBundle\Entity\ProductItem;
use LoveThatFit\AdminBundle\Entity\ProductItemPiece;
use LoveThatFit\AdminBundle\Entity\ProductSizeMeasurement;

class ProductColorViewController extends Controller {

    
    public function listAction($product_color_id) {
        $product_color=' should get views LIST for product color ';
        return $this->render('LoveThatFitAdminBundle:ProductColorView:_list.html.twig');
    }
    public function addAction() {
        $product_color=' should get product color view ADD form';
        return $this->render('LoveThatFitAdminBundle:ProductColorView:_add.html.twig', $product_color);
    }
    public function createAction() {
        $product_color=' should CREATE product color view';
        return  new Reponse($product_color);
    }
    public function editAction($id) {
        $product_color=' should get product color view EDIT form';
        return $this->render('LoveThatFitAdminBundle:ProductColorView:_edit.html.twig', $product_color);
    }
    public function updateAction() {
        $product_color=' should UPDATE product color view';
        return  new Reponse($product_color);
    }
    public function deleteAction($id) {
        $product_color=' should DELETE product color view =' . $id;
        return  new Reponse($product_color);
    }

}
