<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\CartBundle\Form\Type\BillingShippingType;
use LoveThatFit\CartBundle\Form\Type\CountryType;
use LoveThatFit\CartBundle\Form\Type\StateType;

class VisitorsController extends Controller {

    public function indexAction()
    {
        return $this->render('LoveThatFitAdminBundle:Visitors:index.html.twig');
    }

    public function visitorsAction()
    {
        return $this->render('LoveThatFitAdminBundle:Visitors:visitors.html.twig');
    }
    
    public function visitorsPaginateAction(Request $request)
    {
        $requestData = $this->get('request')->request->all();
        $requestData['base_path'] = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';
        
        $output = $this->get('site.helper.visitor')->search($requestData);

        return new Response(json_encode($output), 200, ['Content-Type' =>'application/json']); 
    }

    public function visitorsExportAction(Request $request)
    {
       
        $rsRecord = $this->get('site.helper.visitor')->findvisitorsList();
        if (!empty($rsRecord)) {
            header('Content-Type: application/csv');
            header('Content-Disposition: attachement; filename="visitorslog.csv";');
            $output = fopen('php://output', 'w');
            fputcsv($output, array(
                    'ID',
                    'Email',
                    'Ip Address',
                    'Created At',                    
                )
            );
            foreach ($rsRecord as $rs) {
                $csv['id']      = $rs["id"];
                $csv['email']       = $rs["email"];
                $csv['ip_address']      = $rs["ip_address"];                
                $csv['created_at'] = ($rs["created_at"]->format('d-m-Y'));

                fputcsv($output, $csv);
            }
            # Close the stream off
            fclose($output);
            return new Response('');
        } else {
            $this->get('session')->setFlash('warning', 'No Record Found!');
            return $this->render('LoveThatFitAdminBundle:Visitors:visitors.html.twig');
        }
    }
    
}