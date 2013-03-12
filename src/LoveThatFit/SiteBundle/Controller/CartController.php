<?php

namespace LoveThatFit\SiteBundle\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use LoveThatFit\SiteBundle\Cart;
use LoveThatFit\AdminBundle\Entity\ClothingType;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\Brand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CartController extends Controller {

    //-------------------------------------------------------------------------

    public function indexAction() {
        return $this->render('LoveThatFitSiteBundle:Cart:index.html.twig');
    }

    
}


?>

