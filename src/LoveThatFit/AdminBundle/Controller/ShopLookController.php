<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;


class ShopLookController extends Controller {

    private $product_image_path;

    public function __construct(){
        $yaml = new Parser();
        $productImageModelPath =  $yaml->parse(file_get_contents('../app/config/image_helper.yml'));
        $this->product_image_path = $productImageModelPath['image_category']['shop_look']['original']['dir'];
        $directory_path =  __DIR__ . '/../../../../web/uploads/ltf/shop_look/';
        if (!is_dir($directory_path)) {
            try {
                @mkdir($directory_path, 0700);
            }catch (\Exception $e)
            { $e->getMessage();}
        }
    }

//-----------------------------Shop Look-------------------------------------------------------------

    public function indexAction($page_number, $sort = 'id') {
        $image_path = $this->product_image_path;
        $shoplook = $this->get('admin.helper.shoplook')->findAll();
        return $this->render('LoveThatFitAdminBundle:ShopLook:index.html.twig', array('shoplook' => $shoplook));
    }

    //------------------------------Create New Banner------------------------------------------------------------
    public function newAction() {
        $getAllProductList = $this->get('admin.helper.product')->idNameListEnabledProduct();
        $image_path = $this->product_image_path;
        $entity = $this->get('admin.helper.shoplook')->createNew();
        return $this->render('LoveThatFitAdminBundle:ShopLook:new.html.twig', array(
            'getAllProductList' => $getAllProductList,
        ));
    }

    public function createAction(Request $request) {
        $getAllProductList = $this->get('admin.helper.product')->idNameListEnabledProduct();
        $image_path = $this->product_image_path;
        $decoded = $request->request->all();
        $file = $_FILES["shop_model_image"];
        $entity = $this->get('admin.helper.shoplook')->createNew();

        /*If User added random sort number which is greater than max sort number then max sort will be set*/
        $max_sorting_number = $this->get('admin.helper.shoplook')->maxSortingNumber();
        if($decoded['sorting'] > $max_sorting_number[0]['max_sort']) {
            $decoded['sorting'] = $max_sorting_number[0]['max_sort'] + 1;
        }
        $this->get('admin.helper.shoplook')->editBannerSorting($decoded['sorting'], 'add');
        $insertParent = $this->get('admin.helper.shoplook')->save($entity, $file,$decoded);

        if($insertParent != ''){
            /*Inserted Record Information*/
            $shoplook_entity = $insertParent;
            $this->get('admin.helper.shoplookproduct')->save($entity, $shoplook_entity, $decoded);
        }
        $this->get('session')->setFlash('success', 'Shop Product Look added');

        return $this->render('LoveThatFitAdminBundle:ShopLook:new.html.twig', array(
            'entity' => $entity,
            'getAllProductList' => $getAllProductList,
        ));
    }

    public function paginateAction(Request $request)
    {
        $requestData = $this->get('request')->request->all();
        $requestData['base_path'] = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';
        $output = $this->get('admin.helper.shoplook')->search($requestData);

        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
    }


}