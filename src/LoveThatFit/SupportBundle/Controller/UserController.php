<?php

namespace LoveThatFit\SupportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\AdminBundle\Form\Type\UserMeasurementType;
use LoveThatFit\AdminBundle\Form\Type\UserProfileSettingsType;
use LoveThatFit\AdminBundle\Form\Type\MannequinTestType;
use LoveThatFit\AdminBundle\Form\Type\RetailerSiteUserType;


class UserController extends Controller {

    //--------------------------User List-------------------------------------------------------------
    public function indexAction($page_number, $sort = 'id') {
       echo "test";die;
    }


}
