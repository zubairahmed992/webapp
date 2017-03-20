<?php

namespace LoveThatFit\ProductIntakeBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;


class ServiceHelper {

    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

}