<?php

namespace Acme\StoreBundle\Controller;

use Acme\StoreBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('AcmeStoreBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function createAction()
{
    $product = new Product();
    $product->setName('Roap');
    $product->setPrice('9.99');
    $product->setDescription('Lorem ipsum dolor');
    $em = $this->getDoctrine()->getManager();
    $em->persist($product);
    $em->flush();

    $product = new Product();
    $product->setName('Bag');
    $product->setPrice('29.99');
    $product->setDescription('Lorem ipsum dolor');
    $em = $this->getDoctrine()->getManager();
    $em->persist($product);
    $em->flush();

    $product = new Product();
    $product->setName('boots');
    $product->setPrice('39.99');
    $product->setDescription('Lorem ipsum dolor');
    $em = $this->getDoctrine()->getManager();
    $em->persist($product);
    $em->flush();
    
    return new Response('Created product id '.$product->getId());
}

public function updateAction($id)
{
    $em = $this->getDoctrine()->getManager();
    $product = $em->getRepository('AcmeStoreBundle:Product')->find($id);

    if (!$product) {
        throw $this->createNotFoundException(
            'No product found for id '.$id
        );
    }

    $product->setName('New product name!');
    $em->flush();

    return $this->redirect($this->generateUrl('homepage'));
}
}
