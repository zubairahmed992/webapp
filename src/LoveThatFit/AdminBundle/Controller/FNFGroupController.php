<?php
/**
 * Created by PhpStorm.
 * User: haris.khalique
 * Date: 3/3/2017
 * Time: 11:18 PM
 */

namespace LoveThatFit\AdminBundle\Controller;

use LoveThatFit\AdminBundle\Form\Type\FNFUserForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\AdminBundle\Form\Type\DeleteType;


class FNFGroupController extends Controller
{
    public function indexAction()
    {
        $totalGroupRecords = $this->get('fnfgroup.helper.fnfgroup')->countAllFNFGroupRecord();

        return $this->render('LoveThatFitAdminBundle:FNFUser:index_group.html.twig',
            array(
                'rec_group_count' => count($totalGroupRecords)
            ));
    }
}