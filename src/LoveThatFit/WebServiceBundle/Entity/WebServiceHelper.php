<?php

namespace LoveThatFit\WebServiceBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;


class WebServiceHelper {

     private $container;

    public function __construct(Container $container) {
      $this->container = $container;
    }
    
    
}