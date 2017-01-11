<?php

namespace LoveThatFit\ProductIntakeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class MappingController extends Controller
{
    public function indexAction(){
        return new Response('product intake..');
    }
}
