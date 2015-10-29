<?php

namespace LoveThatFit\UserBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Yaml\Parser;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\UserBundle\Entity\Measurement;

class DummyUserHelper {

    protected $conf;

    //--------------------------------------------------------------------
    public function __construct() {
        $conf_yml = new Parser();
        $this->conf = $conf_yml->parse(file_get_contents('../app/config/sizes_ltf.yml'));
    }

}