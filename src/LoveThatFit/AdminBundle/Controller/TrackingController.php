<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\CartBundle\Form\Type\BillingShippingType;
use LoveThatFit\CartBundle\Form\Type\CountryType;
use LoveThatFit\CartBundle\Form\Type\StateType;

class TrackingController extends Controller {

    public function indexAction()
    {
        return $this->render('LoveThatFitAdminBundle:Tracking:index.html.twig');
    }

    public function favoriteAction()
    {
        return $this->render('LoveThatFitAdminBundle:Tracking:favorite.html.twig');
    }
    
    public function favoritePaginateAction(Request $request)
    {
        $requestData = $this->get('request')->request->all();
        $requestData['base_path'] = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';
        
        $output = $this->get('site.helper.userfavitemhistory')->search($requestData);

        return new Response(json_encode($output), 200, ['Content-Type' =>'application/json']); 
    }

    public function favoriteExportAction(Request $request)
    {
        $favorites = $this->get('site.helper.userfavitemhistory')->findFavoriteList();
        if (!empty($favorites)) {
            header('Content-Type: application/csv');
            header('Content-Disposition: attachement; filename="favorites.csv";');
            $output = fopen('php://output', 'w');
            fputcsv($output, array(
                    'User Email',
                    'Product Name',
                    'Item Price',
                    'Item Size',
                    'Item Color',
                    'Favorite From',
                    'Status',
                    'Created At',
                )
            );
            foreach ($favorites as $favorite) {
                $csv['email']      = $favorite["email"];
                $csv['name']       = $favorite["name"];
                $csv['price']      = $favorite["price"];
                $csv['size']       = $favorite["size"];
                $csv['color']      = $favorite["color"];
                $csv['page']       = $favorite["page"];
                $csv['status']     = ($favorite["status"] == 0) ? "dislike" : "like";
                $csv['created_at'] = ($favorite["created_at"]->format('d-m-Y'));

                fputcsv($output, $csv);
            }
            # Close the stream off
            fclose($output);
            return new Response('');
        } else {
            $this->get('session')->setFlash('warning', 'No Record Found!');
            return $this->render('LoveThatFitAdminBundle:Tracking:favorite.html.twig');
        }
    }
    
}