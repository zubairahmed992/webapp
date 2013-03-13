<?php

namespace LoveThatFit\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Session\Session;
use LoveThatFit\SiteBundle\Cart;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CartController extends Controller {

    //-------------------------------------------------------------------------

    public function indexAction() {
        $cart = $this->getCart();
        return $this->renderCart($cart);
    }

    //-------------------------------------------------------------------------
    public function addToCartAction($id) {
        $cart = $this->getCart();
        $cart->addToCart($this->getProduct($id));
        return $this->renderCart($cart);
    }

//-------------------------------------------------------------------------

    public function removeFromCartAction($id) {
        $cart = $this->getCart();
        $cart->removeFromCart($this->getProduct($id));
        return $this->renderCart($cart);
    }

    //-------------------------------------------------------------------------
//-------------------------------------------------------------------------


    private function getCart() {
        $session = $this->get("session");
        if ($session->has('cart')) {
            $cart = new Cart($session->get('cart'));
        } else {
            $cart = new Cart(null);
        }
        return $cart;
    }

    //-------------------------------------------------------------------
    private function getProduct($id) {
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->find($id);
        return $entity;
    }

    //-------------------------------------------------------------------------

    private function renderCart($cart) {
        $session = $this->get("session");
        $session->set("cart", $cart->getCart());
        //return new Response(json_encode($cart->getCart()));
        return $this->render('LoveThatFitSiteBundle:InnerSite:_cart.html.twig', array('cart' => $cart->getCart(), 'total_amount' => $cart->getTotal()));
    }

}
?>

