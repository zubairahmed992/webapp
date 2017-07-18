<?php
/**
 * Created by PhpStorm.
 * User: haris.khalique
 * Date: 7/13/2017
 * Time: 4:19 PM
 */

namespace LoveThatFit\AdminBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultClothingController extends Controller
{
    public function indexAction($page_number, $sort = 'id') {
        return $this->render('LoveThatFitAdminBundle:DefaultClothing:index.html.twig', array());
    }

    public function paginateAction(Request $request)
    {
        $requestData = $this->get('request')->request->all();
        $output      = $this->get('admin.helper.clothingtype')->findDefaultClothing( $requestData );

        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
    }

    public function newAction()
    {
        $clothingTypes = $this->get('admin.helper.clothingtype')->findClothingTypeByTarget();
        return $this->render('LoveThatFitAdminBundle:DefaultClothing:new.html.twig', array(
            'clothingType' => $clothingTypes
        ));
    }

    public function getProductByClothingTypeAction(Request $request){
        $html = "";
        $data = $request->request->all();
        $products = $this->get('admin.helper.clothingtype')->getProductByClothingTypeId($data['id']);
        foreach ($products as $product){
            $html .= "<option value='".$product['id']."'>".$product['name']."</option>";
        }

        return new Response($html);
    }

    public function markDefaultClothingProductAction( Request $request ){
        $data = $request->request->all();
        $entity = $this->get('admin.helper.product')->find($data['product']);
        $clothing_type = $this->get('admin.helper.clothingtype')->find( $data['default_clothing'] );
        $this->get('admin.helper.product')->markOtherProductDefaultToZero( $clothing_type );


        $products = $this->get('admin.helper.product')->markProductDefault($entity);

        $this->get('session')->setFlash('success', 'Default clothing added.');
        return $this->redirect($this->generateUrl('admin_default_clothing_new'));
    }
}