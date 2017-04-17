<?php

namespace LoveThatFit\SupportBundle\Controller;

use LoveThatFit\SiteBundle\DependencyInjection\FitAlgorithm2;
use LoveThatFit\SupportBundle\Form\Type\AlgoritumTestlType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EvaluationSheetController extends Controller
{

    ### User Test Demo Products copy of User Marathon products ###
    #--------------------------------------------------
    public function indexAction()
    {
        $userForm = $this->createForm(new AlgoritumTestlType());
        $users    = $this->get('user.helper.user')->findAllUsersAsc();

        return $this->render('LoveThatFitSupportBundle:EvaluationSheet:index.html.twig', array(
            'userForm' => $userForm->createView(),
            'users'    => $users,
        ));
    }

    #-------------------------------------------------- User Test Demo Products Ajax Call copy of Marathon
    public function sampleAction()
    {
        $decoded = $this->get('request')->request->all();
        $arr     = $arr     = $this->test_demo_data(
            $decoded['user_id'],
            $decoded['sorting_col'],
            $decoded['sorting_order']
        );
        return $this->render('LoveThatFitSupportBundle:EvaluationSheet:sample.html.twig', $arr);
    }
    #-------------------------------------------------- User Test Demo Products Ajax Call copy of Marathon
    public function cartAction()
    {
        $decoded = $this->get('request')->request->all();
        $user    = $this->get('user.helper.user')->find($decoded['user_id']);
        $cart    = $user->getCart();
        $pa      = array();

        $algo   = new FitAlgorithm2($user);
        $serial = 1;
        $arr    = array();
        foreach ($cart as $c) {
            $p = $c->getProductItem()->getProduct();
            $algo->setProduct($p);

            $fb            = $algo->getFeedBackForSizeTitle($c->getProductItem()->getProductSize()->getTitle());
            $product_color = $c->getProductItem()->getProductColor()->getTitle();
            if (is_array($fb) && array_key_exists('feedback', $fb)) {
                $pa[$c->getId()] = array(
                    'product_id'            => $p->getId(),
                    'control_number'        => $p->getControlNumber(),
                    'brand'                 => $p->getBrand()->getName(),
                    'name'                  => $p->getName(),
                    'fit_index'             => $fb["feedback"]['fit_index'],
                    'clothing_type'         => $p->getClothingType()->getName(),
                    #'size'=> $this->getEncodedSize($fb["feedback"]['title']),
                    'size'                  => ($fb["feedback"]['title'] == "") ? 0 : $fb["feedback"]['title'],
                    'color'                 => $product_color,
                    'serial'                => $serial,
                    'fits'                  => ($fb["feedback"]['fits'] == "") ? 0 : $fb["feedback"]['fits'],
                    'recommended_size'      => '',
                    'recommended_fit_index' => '',
                );
                if (is_array($fb) && array_key_exists('recommendation', $fb)) {
                    $pa[$c->getId()]['recommended_size']      = $fb["recommendation"]['title'];
                    $pa[$c->getId()]['recommended_fit_index'] = $fb["recommendation"]['fit_index'];
                }
            }
            $serial++;
        }
        if ($decoded['sorting_col'] != "" && $decoded['sorting_order'] != "") {
            if ($decoded['sorting_order'] == "up") {
                uasort($pa, $this->make_comparer(array($decoded['sorting_col'], SORT_ASC)));
            } elseif ($decoded['sorting_order'] == "down") {
                uasort($pa, $this->make_comparer(array($decoded['sorting_col'], SORT_DESC)));
            }
        }

        return $this->render('LoveThatFitSupportBundle:EvaluationSheet:cart.html.twig', array('products' => $pa, 'user' => $user));
    }

    public function cartPrintAction()
    {
        $decoded = $this->get('request')->request->all();
        $user    = $this->get('user.helper.user')->find($decoded['user_id']);
        $cart    = $user->getCart();
        $pa      = array();

        $algo   = new FitAlgorithm2($user);
        $serial = 1;
        $arr    = array();
        foreach ($cart as $c) {
            $p = $c->getProductItem()->getProduct();
            $algo->setProduct($p);

            $fb            = $algo->getFeedBackForSizeTitle($c->getProductItem()->getProductSize()->getTitle());
            $product_color = $c->getProductItem()->getProductColor()->getTitle();
            if (is_array($fb) && array_key_exists('feedback', $fb)) {
                $pa[$c->getId()] = array(
                    'product_id'            => $p->getId(),
                    'control_number'        => $p->getControlNumber(),
                    'brand'                 => $p->getBrand()->getName(),
                    'name'                  => $p->getName(),
                    'fit_index'             => $fb["feedback"]['fit_index'],
                    'clothing_type'         => $p->getClothingType()->getName(),
                    'size'                  => ($fb["feedback"]['title'] == "") ? 0 : $fb["feedback"]['title'],
                    'color'                 => $product_color,
                    'serial'                => $serial,
                    'fits'                  => ($fb["feedback"]['fits'] == "") ? 0 : $fb["feedback"]['fits'],
                    'recommended_size'      => '',
                    'recommended_fit_index' => '',
                );
                if (is_array($fb) && array_key_exists('recommendation', $fb)) {
                    $pa[$c->getId()]['recommended_size']      = $fb["recommendation"]['title'];
                    $pa[$c->getId()]['recommended_fit_index'] = $fb["recommendation"]['fit_index'];
                }
            }
            $serial++;
        }
        if ($decoded['sorting_col'] != "" && $decoded['sorting_order'] != "") {
            if ($decoded['sorting_order'] == "up") {
                uasort($pa, $this->make_comparer(array($decoded['sorting_col'], SORT_ASC)));
            } elseif ($decoded['sorting_order'] == "down") {
                uasort($pa, $this->make_comparer(array($decoded['sorting_col'], SORT_DESC)));
            }
        }

        return $this->render('LoveThatFitSupportBundle:EvaluationSheet:printCart.html.twig', array('products' => $pa, 'user' => $user));
    }

    #-------------------------------------------------- Favourite products of user
    public function favouriteAction()
    {
        $decoded   = $this->get('request')->request->all();
        $user      = $this->get('user.helper.user')->find($decoded['user_id']);
        $favourite = $user->getProductItems();
        $pa        = array();

        $algo   = new FitAlgorithm2($user);
        $serial = 1;
        $arr    = array();
        foreach ($favourite as $c) {
            $p = $c->getProduct();
            $algo->setProduct($p);

            $fb            = $algo->getFeedBackForSizeTitle($c->getProductSize()->getTitle());
            $product_color = $c->getProductColor()->getTitle();
            if (is_array($fb) && array_key_exists('feedback', $fb)) {
                $pa[$c->getId()] = array(
                    'product_id'            => $p->getId(),
                    'control_number'        => $p->getControlNumber(),
                    'brand'                 => $p->getBrand()->getName(),
                    'name'                  => $p->getName(),
                    'fit_index'             => $fb["feedback"]['fit_index'],
                    'clothing_type'         => $p->getClothingType()->getName(),
                    'size'                  => ($fb["feedback"]['title'] == "") ? 0 : $fb["feedback"]['title'],
                    'color'                 => $product_color,
                    'serial'                => $serial,
                    'fits'                  => ($fb["feedback"]['fits'] == "") ? 0 : $fb["feedback"]['fits'],
                    'recommended_size'      => '',
                    'recommended_fit_index' => '',
                );
                if (is_array($fb) && array_key_exists('recommendation', $fb)) {
                    $pa[$c->getId()]['recommended_size']      = $fb["recommendation"]['title'];
                    $pa[$c->getId()]['recommended_fit_index'] = $fb["recommendation"]['fit_index'];
                }
            }
            $serial++;
        }

        if ($decoded['sorting_col'] != "" && $decoded['sorting_order'] != "") {
            if ($decoded['sorting_order'] == "up") {
                uasort($pa, $this->make_comparer(array($decoded['sorting_col'], SORT_ASC)));
            } elseif ($decoded['sorting_order'] == "down") {
                uasort($pa, $this->make_comparer(array($decoded['sorting_col'], SORT_DESC)));
            }
        }
        return $this->render('LoveThatFitSupportBundle:EvaluationSheet:favourite.html.twig', array('products' => $pa, 'user' => $user));
    }

    #--------------------------------------------------

    public function onhandAction()
    {
        $decoded = $this->get('request')->request->all();

        $arr = $this->test_demo_data_fit_index(
            $decoded['user_id'],
            $decoded['sorting_col'],
            $decoded['sorting_order']
        );
        return $this->render('LoveThatFitSupportBundle:EvaluationSheet:onhandFitIndex.html.twig', $arr);
    }

    public function onhandPrintAction()
    {
        $decoded = $this->get('request')->request->all();
        $arr     = $this->test_demo_data_fit_index(
            $decoded['user_id'],
            $decoded['sorting_col'],
            $decoded['sorting_order']
        );
        $result = [];
        $f      = 0;
        foreach ($arr['products'] as $product) {
            foreach ($result as $value) {
                if ($product['recommended_fit_index'] < $value['recommended_fit_index'] &&
                    $product['clothing_type'] == $value['clothing_type']
                ) {
                    $f = 1;
                }
            }
            if ($f == 0) {
                $result[] = $product;
            }
            $f = 0;
        }

        return $this->render('LoveThatFitSupportBundle:EvaluationSheet:printHighestFitIndex.html.twig', array('products' => $result, 'user' => $arr['user']));
    }

    /*
     * Pop up evaluation sheet
     * */

    public function onhandPopUpPrintAction()
    {

        $decoded = $this->get('request')->request->all();
        $arr     = $this->test_demo_data_fit_index_pop_up(
            $decoded['user_id'],
            $decoded['sorting_col'],
            $decoded['sorting_order']
        );
        $result = [];
        $f      = 0;
        return $this->render('LoveThatFitSupportBundle:EvaluationSheet:printHighestFitIndexPopup.html.twig', array('products' => $arr['products'], 'user' => $arr['user'], 'default_products' => $arr['default_products'], 'product_sizes_fit_index' => $arr['pop_up_product_fit_index'], 'recommended_size_info' => $arr['recommended_size_info']));
    }

    

    private function test_demo_data_fit_index_pop_up($user_id, $sorting_col, $sorting_order)
    {
        $user = $this->get('user.helper.user')->find($user_id);

        //Get Default product
        //Call LoveThatFitSupportBundle:EvaluationPopUpProducts
        $defaultProducts = $this->getEvaluationSheetDefaultProducts('LoveThatFitSupportBundle:EvaluationPopUpProducts');

        //Get Default Product IDs
        $ids = array_keys($defaultProducts['product_id_sizes']);

        //Get Default Product IDs & sizes
        $try_sizes = $defaultProducts['product_id_sizes'];

        $products = $this->get('admin.helper.product')->listProductByIds($ids);

        $pa     = array();
        $result = array();

        $algo                 = new FitAlgorithm2($user);
        $serial               = 1;
        $popUpProductFitIndex = array();
        foreach ($products as $p) {
            $algo->setProduct($p);
            if ($try_sizes[$p->getId()] != 'NA') {
                if (strpos($try_sizes[$p->getId()], ',') !== false) {
                    $breakSizes = explode(",", $try_sizes[$p->getId()]);

                    $fb        = "";
                    $fbExists  = 0;
                    $nameArray = array(
                        'name'           => $p->getName(),
                        'control_number' => $p->getControlNumber(),
                        'brand'          => $p->getBrand()->getName(),
                        'clothing_type'  => $p->getClothingType()->getName(),
                        'color'          => $p->getdisplayProductColor()->getTitle(),
                        'serial'         => $serial,
                    );

                    //exit;
                    for ($i = 0; $i < count($breakSizes); $i++) {

                        $fb = $algo->getFeedBackForSizeTitle($breakSizes[$i]);

                        $popUpProductFitIndex[$p->getId()][$breakSizes[$i]] = '0';
                        if (is_array($fb) && array_key_exists('feedback', $fb)) {

                            $popUpProductFitIndex[$p->getId()][$breakSizes[$i]] = $fb["feedback"]['fit_index'];

                            if (is_array($fb) && array_key_exists('recommendation', $fb)) {
                                $nameArray['recommended_size']      = $fb["recommendation"]['title'];
                                $nameArray['recommended_fit_index'] = $fb["recommendation"]['fit_index'];

                            }
                            if ($nameArray['recommended_size'] == $breakSizes[$i]) {
                                if ($nameArray['recommended_fit_index'] > 0 &&
                                    $fb["feedback"]['fit_index'] > 0
                                ) {
                                    $fbExists               = 1;
                                    $nameArray['fit_index'] = $fb["feedback"]['fit_index'];
                                    $nameArray['size']      = $fb["feedback"]['title'];
                                    $nameArray['fits']      = $fb["feedback"]['fits'];
                                }
                            }
                        }
                        if ($fbExists == 1) {
                            $pa[$p->getId()] = $nameArray;
                        }
                    }
                } else {
                    $fb = $algo->getFeedBackForSizeTitle($try_sizes[$p->getId()]);

                    if (is_array($fb) && array_key_exists('feedback', $fb)) {

                        $popUpProductFitIndex[$p->getId()][$try_sizes[$p->getId()]] = $fb["feedback"]['fit_index'];

                        if (is_array($fb) && array_key_exists('recommendation', $fb)) {
                            $rec_size    = $fb["recommendation"]['title'];
                            $rec_fit_ind = $fb["recommendation"]['fit_index'];

                        }

                        if ($rec_size == $fb["feedback"]['title']) {
                            if ($rec_fit_ind > 0 && $fb["feedback"]['fit_index'] > 0) {
                                $pa[$p->getId()] = array('name' => $p->getName(),
                                    'control_number'                => $p->getControlNumber(),
                                    'brand'                         => $p->getBrand()->getName(),
                                    'fit_index'                     => $fb["feedback"]['fit_index'],
                                    'clothing_type'                 => $p->getClothingType()->getName(),
                                    'size'                          => $fb["feedback"]['title'],
                                    'color'                         => $p->getdisplayProductColor()->getTitle(),
                                    'serial'                        => $serial,
                                    'fits'                          => $fb["feedback"]['fits'],
                                    'recommended_size'              => $fb["recommendation"]['title'],
                                    'recommended_fit_index'         => $fb["recommendation"]['fit_index'],
                                );
                            }
                        }
                    }
                }
            }

            $serial++;
        }

        if ($sorting_col != "" && $sorting_order != "") {
            if ($sorting_order == "up") {
                uasort($pa, $this->make_comparer(array($sorting_col, SORT_ASC)));
            } elseif ($sorting_order == "down") {
                uasort($pa, $this->make_comparer(array($sorting_col, SORT_DESC)));
            }
        }

        //Iterate fit index products
        foreach ($popUpProductFitIndex as $PID => $PFitIndex) {
            $highFitIndex = max($PFitIndex);
            $fitIndexSize = array_search($highFitIndex, $PFitIndex);

            $popUpProductFitIndex[$PID]['highFitIndex']['size']      = $fitIndexSize;
            $popUpProductFitIndex[$PID]['highFitIndex']['fit_index'] = $highFitIndex;

        }

        return array(
            'recommended_size_info'    => $pa,
            'products'                 => $products,
            'user'                     => $user,
            'default_products'         => $try_sizes,
            'pop_up_product_fit_index' => $popUpProductFitIndex,
        );
    }

    /*
     * End Pop-up
     * */

    /*
     * Return Array
     * */
    private function getEvaluationSheetDefaultProducts($popUpStore = false)
    {
        $dynamicEntity                                 = ($popUpStore) ? $popUpStore : 'LoveThatFitSupportBundle:EvaluationDefaultProducts';
        $em                                            = $this->getDoctrine()->getManager();
        $defaultProductList                            = $em->getRepository($dynamicEntity)->findAll();
        $defaultProductsArray['def_product_ids']       = array();
        $defaultProductsArray['def_product_ids_sizes'] = array();
        $defaultProductsArray['product_id_sizes']      = array();
        if ($defaultProductList) {
            //Old product Id's

            foreach ($defaultProductList as $defaultProduct) {
                $defaultProductsArray['def_product_ids'][]                                      = $defaultProduct->getProductID();
                $defaultProductsArray['def_product_ids_sizes'][$defaultProduct->getProductID()] = $defaultProduct->getProductSizes();
            }

            //Get Product ID's With their sizes
            $defaultProductSizesList = $this->get('admin.helper.product')->listProductByIds($defaultProductsArray['def_product_ids']);
            //$defaultProductSizesList =  $em->getRepository('LoveThatFitAdminBundle:Product')->findById($defaultProductsArray['def_product_ids']);
            //Iterate products
            foreach ($defaultProductSizesList as $product) {
                //get Product sizes
                $productSize = $product->getProductSizes();
                if ($productSize) {
                    $explodedProductSizes      = array();
                    $explodedProductSizesarray = array();
                    $explodedProductSizes      = explode(',', $defaultProductsArray['def_product_ids_sizes'][$product->getID()]);
                    foreach ($productSize as $size) {
                        //Check selected size exists in product sizes
                        if (in_array($size->getID(), $explodedProductSizes) && $size->getDisabled() == 0) {
                            $explodedProductSizesarray[] = $size->getTitle();
                        }

                        $productSizes[$size->getID()] = $size->getTitle();

                    } //end foreach loop. iteration for product sizes
                    //store product ID with their sizes
                    $defaultProductsArray['product_id_sizes'][$product->getID()] = implode(',', $explodedProductSizesarray);

                } //end if condition check product size exists

            } //End foreach loop for product iteration

        } //End if condition. Check default product selected

        //Hold default product info
        return $defaultProductsArray;

    } //End function that get Default evaluation default product

    #--------------------------------------------------
    private function test_demo_data($user_id, $sorting_col, $sorting_order)
    {

        /*$try_sizes = array ('564'=>'24,25,26,27,28,29,30,31,32','565'=>'S,M,L',
        '566'=>'S,M,L', '567'=>'S,M,L,XL', '568'=>'S,M,L,XL',
        '569'=>'S,M,L,XL', '570'=>'S,M,L', '571'=>'XS,S,M,L,XL',
        '572'=>'0,2,4,6,8,10,12,14,16', '573'=>'XS,S,M,L', '574'=>'XS,S,M,L',
        '575'=>'S,M,L', '577'=>'XS,S,M,L', '578'=>'XS,S,M,L,XL', '580'=>'XS,S,M,L,XL',
        '581'=>'XS,S,M,L,XL', '583'=>'SM,ML', '584'=>'SM,ML', '585'=>'SM,ML', '586'=>'SM,ML',
        '587'=>'24,25,26,27,28,29,30,31,32', '588'=>'24,25,26,27,28,29,30,31,32', '591'=>'OS',
        '592'=>'S,M,L', '593'=>'2,4,6,8,10,12,14,16', '594'=>'S,M,L', '602'=>'XS,S,M,L',
        '603'=>'XS,S,M,L', '604'=>'OS', '605'=>'OS', '606'=>'OS');*/

        $user = $this->get('user.helper.user')->find($user_id);
        // $ids= array (472,473,474,475,476,479,540,541,490,491,492,494,495,496,497,499,500,501,502,503,504,505,506,507,508,509,510,512,513,514,515,516,517,518,519,520,522,524,525,532,535,536,537,538,539,544,546,547,548,549,552,554);
        // $try_sizes = array ('472'=>'NA','473'=>'NA','474'=>'NA','475'=>'NA','476'=>'NA','479'=>'NA','540'=>'NA','541'=>'NA','490'=>'2', '491'=>'S', '492'=>'2', '494'=>'4', '495'=>'XS', '496'=>'XS', '497'=>'XS', '499'=>'S', '500'=>'XS', '501'=>'XS', '502'=>'S', '503'=>'XS', '504'=>'S', '505'=>'XS', '506'=>'XS', '507'=>'S', '508'=>'XS', '509'=>'S', '510'=>'S', '512'=>'XS', '513'=>'S', '514'=>'XS', '515'=>'S', '516'=>'S', '517'=>'S', '518'=>'S', '519'=>'4', '520'=>'2', '522'=>'S', '524'=>'4', '525'=>'2', '532'=>'XS', '535'=>'0', '536'=>'XS', '537'=>'XS', '538'=>'S', '539'=>'S', '544'=>'4', '546'=>'25', '547'=>'25', '548'=>'25', '549'=>'25', '552'=>'25', '554'=>'2');
        ##reason ids 514, 539

        //Get Default products
        $defaultProducts = $this->getEvaluationSheetDefaultProducts();

        /*$ids= array (564, 565, 566, 567, 568, 569, 570, 571, 572, 573, 574,
        575, 577, 578, 580, 581, 583, 584, 585, 586, 587, 588, 591,
        592, 593, 594, 602, 603, 604, 605, 606);*/
        //Get Default Product IDs
        $ids = array_keys($defaultProducts['product_id_sizes']);

        /*$try_sizes = array ('564'=>'24,25,26,27,28,29,30,31,32','565'=>'S,M,L',
        '566'=>'S,M,L', '567'=>'S,M,L,XL', '568'=>'S,M,L,XL',
        '569'=>'S,M,L,XL', '570'=>'S,M,L', '571'=>'XS,S,M,L,XL',
        '572'=>'0,2,4,6,8,10,12,14,16', '573'=>'XS,S,M,L', '574'=>'XS,S,M,L',
        '575'=>'S,M,L', '577'=>'XS,S,M,L', '578'=>'XS,S,M,L,XL', '580'=>'XS,S,M,L,XL',
        '581'=>'XS,S,M,L,XL', '583'=>'SM,ML', '584'=>'SM,ML', '585'=>'SM,ML', '586'=>'SM,ML',
        '587'=>'24,25,26,27,28,29,30,31,32', '588'=>'24,25,26,27,28,29,30,31,32', '591'=>'OS',
        '592'=>'S,M,L', '593'=>'2,4,6,8,10,12,14,16', '594'=>'S,M,L', '602'=>'XS,S,M,L',
        '603'=>'XS,S,M,L', '604'=>'OS', '605'=>'OS', '606'=>'OS');*/

        //Get Default Product sizes
        $try_sizes = $defaultProducts['product_id_sizes'];

        $products = $this->get('admin.helper.product')->listProductByIds($ids);

        $pa     = array();
        $algo   = new FitAlgorithm2($user);
        $serial = 1;
        foreach ($products as $p) {
            $algo->setProduct($p);
            if ($try_sizes[$p->getId()] == 'NA') {
                $fb = $algo->getFeedBack();
                if (array_key_exists('recommendation', $fb)) {
                    $pa[$p->getId()] = array('name' => $p->getName(),
                        'control_number'                => $p->getControlNumber(),
                        'brand'                         => $p->getBrand()->getName(),
                        'fit_index'                     => $fb['recommendation']['fit_index'],
                        'clothing_type'                 => $p->getClothingType()->getName(),
                        'size'                          => $fb["recommendation"]['title'],
                        'color'                         => $p->getdisplayProductColor()->getTitle(),
                        'serial'                        => $serial,
                        'fits'                          => $fb["recommendation"]['fits'],
                        'recommended_size'              => '-',
                        'recommended_fit_index'         => '-',
                    );
                }
            } else {
                if (strpos($try_sizes[$p->getId()], ',') !== false) {
                    $breakSizes = explode(",", $try_sizes[$p->getId()]);
                    $fb         = "";
                    $fbExists   = 0;
                    $nameArray  = array(
                        'name'           => $p->getName(),
                        'control_number' => $p->getControlNumber(),
                        'brand'          => $p->getBrand()->getName(),
                        'clothing_type'  => $p->getClothingType()->getName(),
                        'color'          => $p->getdisplayProductColor()->getTitle(),
                        'serial'         => $serial,
                    );
                    for ($i = 0; $i < count($breakSizes); $i++) {
                        $fb = $algo->getFeedBackForSizeTitle($breakSizes[$i]);
                        if (is_array($fb) && array_key_exists('feedback', $fb)) {
                            $fbExists                                 = 1;
                            $nameArray["multiSizes"][$breakSizes[$i]] = array(
                                'fit_index' => $fb["feedback"]['fit_index'],
                                'size'      => $fb["feedback"]['title'],
                                'fits'      => $fb["feedback"]['fits'],
                            );
                        }
                        if (is_array($fb) && array_key_exists('recommendation', $fb)) {
                            $nameArray['recommended_size']      = $fb["recommendation"]['title'];
                            $nameArray['recommended_fit_index'] = $fb["recommendation"]['fit_index'];
                        }

                        if ($fbExists == 1) {
                            $pa[$p->getId()] = $nameArray;
                        }
                    }
                } else {
                    $fb = $algo->getFeedBackForSizeTitle($try_sizes[$p->getId()]);
                    if (is_array($fb) && array_key_exists('feedback', $fb)) {
                        $pa[$p->getId()] = array('name' => $p->getName(),
                            'control_number'                => $p->getControlNumber(),
                            'brand'                         => $p->getBrand()->getName(),
                            'fit_index'                     => $fb["feedback"]['fit_index'],
                            'clothing_type'                 => $p->getClothingType()->getName(),
                            'size'                          => $fb["feedback"]['title'],
                            'color'                         => $p->getdisplayProductColor()->getTitle(),
                            'serial'                        => $serial,
                            'fits'                          => $fb["feedback"]['fits'],
                            'recommended_size'              => '',
                            'recommended_fit_index'         => '',
                        );
                        if (is_array($fb) && array_key_exists('recommendation', $fb)) {
                            $pa[$p->getId()]['recommended_size']      = $fb["recommendation"]['title'];
                            $pa[$p->getId()]['recommended_fit_index'] = $fb["recommendation"]['fit_index'];
                        }
                    }
                }
            }
            $serial++;
        }

        if ($sorting_col != "" && $sorting_order != "") {
            if ($sorting_order == "up") {
                uasort($pa, $this->make_comparer(array($sorting_col, SORT_ASC)));
            } elseif ($sorting_order == "down") {
                uasort($pa, $this->make_comparer(array($sorting_col, SORT_DESC)));
            }
        }
        return array(
            'products' => $pa,
            'user'     => $user,
        );
    }

    private function test_demo_data_fit_index($user_id, $sorting_col, $sorting_order)
    {
        // $ids= array (472,473,474,475,476,479,540,541,490,491,492,494,495,496,497,499,500,501,502,503,504,505,506,507,508,509,510,512,513,514,515,516,517,518,519,520,522,524,525,532,535,536,537,538,539,544,546,547,548,549,552,554);
        // $try_sizes = array ('472'=>'NA','473'=>'NA','474'=>'NA','475'=>'NA','476'=>'NA','479'=>'NA','540'=>'NA','541'=>'NA','490'=>'2', '491'=>'S', '492'=>'2', '494'=>'4', '495'=>'XS', '496'=>'XS', '497'=>'XS', '499'=>'S', '500'=>'XS', '501'=>'XS', '502'=>'S', '503'=>'XS', '504'=>'S', '505'=>'XS', '506'=>'XS', '507'=>'S', '508'=>'XS', '509'=>'S', '510'=>'S', '512'=>'XS', '513'=>'S', '514'=>'XS', '515'=>'S', '516'=>'S', '517'=>'S', '518'=>'S', '519'=>'4', '520'=>'2', '522'=>'S', '524'=>'4', '525'=>'2', '532'=>'XS', '535'=>'0', '536'=>'XS', '537'=>'XS', '538'=>'S', '539'=>'S', '544'=>'4', '546'=>'25', '547'=>'25', '548'=>'25', '549'=>'25', '552'=>'25', '554'=>'2');
        $user = $this->get('user.helper.user')->find($user_id);
        //Get Default product
        $defaultProducts = $this->getEvaluationSheetDefaultProducts();
        //Get Default Product IDs
        $ids = array_keys($defaultProducts['product_id_sizes']);
        //Get Default Product IDs & sizes
        $try_sizes = $defaultProducts['product_id_sizes'];
        /*  $ids= array (564, 565, 566, 567, 568, 569, 570, 571, 572, 573, 574,
        575, 577, 578, 580, 581, 583, 584, 585, 586, 587, 588, 591,
        592, 593, 594, 602, 603, 604, 605, 606);*/

        /*$try_sizes = array ('564'=>'24,25,26,27,28,29,30,31,32','565'=>'S,M,L',
        '566'=>'S,M,L', '567'=>'S,M,L,XL', '568'=>'S,M,L,XL',
        '569'=>'S,M,L,XL', '570'=>'S,M,L', '571'=>'XS,S,M,L,XL',
        '572'=>'0,2,4,6,8,10,12,14,16', '573'=>'XS,S,M,L', '574'=>'XS,S,M,L',
        '575'=>'S,M,L', '577'=>'XS,S,M,L', '578'=>'XS,S,M,L,XL', '580'=>'XS,S,M,L,XL',
        '581'=>'XS,S,M,L,XL', '583'=>'SM,ML', '584'=>'SM,ML', '585'=>'SM,ML', '586'=>'SM,ML',
        '587'=>'24,25,26,27,28,29,30,31,32', '588'=>'24,25,26,27,28,29,30,31,32', '591'=>'OS',
        '592'=>'S,M,L', '593'=>'2,4,6,8,10,12,14,16', '594'=>'S,M,L', '602'=>'XS,S,M,L',
        '603'=>'XS,S,M,L', '604'=>'OS', '605'=>'OS', '606'=>'OS');*/

        $products = $this->get('admin.helper.product')->listProductByIds($ids);

        $pa     = array();
        $result = array();

        $algo   = new FitAlgorithm2($user);
        $serial = 1;
        foreach ($products as $p) {
            $algo->setProduct($p);
            /* echo $p->getId()." -> ". $p->getName();
            echo "<hr>";*/
            if ($try_sizes[$p->getId()] != 'NA') {
                if (strpos($try_sizes[$p->getId()], ',') !== false) {
                    $breakSizes = explode(",", $try_sizes[$p->getId()]);
                    $fb         = "";
                    $fbExists   = 0;
                    $nameArray  = array(
                        'name'           => $p->getName(),
                        'control_number' => $p->getControlNumber(),
                        'brand'          => $p->getBrand()->getName(),
                        'clothing_type'  => $p->getClothingType()->getName(),
                        'color'          => $p->getdisplayProductColor()->getTitle(),
                        'serial'         => $serial,
                    );

                    //exit;
                    for ($i = 0; $i < count($breakSizes); $i++) {

                        $fb = $algo->getFeedBackForSizeTitle($breakSizes[$i]);

                        if (is_array($fb) && array_key_exists('feedback', $fb)) {
                            if (is_array($fb) && array_key_exists('recommendation', $fb)) {
                                $nameArray['recommended_size']      = $fb["recommendation"]['title'];
                                $nameArray['recommended_fit_index'] = $fb["recommendation"]['fit_index'];
                            }
                            if ($nameArray['recommended_size'] == $breakSizes[$i]) {
                                if ($nameArray['recommended_fit_index'] > 0 &&
                                    $fb["feedback"]['fit_index'] > 0
                                ) {
                                    $fbExists               = 1;
                                    $nameArray['fit_index'] = $fb["feedback"]['fit_index'];
                                    $nameArray['size']      = $fb["feedback"]['title'];
                                    $nameArray['fits']      = $fb["feedback"]['fits'];
                                }
                            }
                        }
                        if ($fbExists == 1) {
                            $pa[$p->getId()] = $nameArray;
                        }
                    }
                } else {
                    $fb = $algo->getFeedBackForSizeTitle($try_sizes[$p->getId()]);
                    if (is_array($fb) && array_key_exists('feedback', $fb)) {
                        if (is_array($fb) && array_key_exists('recommendation', $fb)) {
                            $rec_size    = $fb["recommendation"]['title'];
                            $rec_fit_ind = $fb["recommendation"]['fit_index'];
                        }

                        if ($rec_size == $fb["feedback"]['title']) {
                            if ($rec_fit_ind > 0 && $fb["feedback"]['fit_index'] > 0) {
                                $pa[$p->getId()] = array('name' => $p->getName(),
                                    'control_number'                => $p->getControlNumber(),
                                    'brand'                         => $p->getBrand()->getName(),
                                    'fit_index'                     => $fb["feedback"]['fit_index'],
                                    'clothing_type'                 => $p->getClothingType()->getName(),
                                    'size'                          => $fb["feedback"]['title'],
                                    'color'                         => $p->getdisplayProductColor()->getTitle(),
                                    'serial'                        => $serial,
                                    'fits'                          => $fb["feedback"]['fits'],
                                    'recommended_size'              => $fb["recommendation"]['title'],
                                    'recommended_fit_index'         => $fb["recommendation"]['fit_index'],
                                );
                            }
                        }
                    }
                }
            }

            $serial++;
        }

        if ($sorting_col != "" && $sorting_order != "") {
            if ($sorting_order == "up") {
                uasort($pa, $this->make_comparer(array($sorting_col, SORT_ASC)));
            } elseif ($sorting_order == "down") {
                uasort($pa, $this->make_comparer(array($sorting_col, SORT_DESC)));
            }
        }

        return array(
            'products' => $pa,
            'user'     => $user,
        );
    }

    /***** all products highest fit index start *****/

    public function onhandAllAction()
    {
        $decoded = $this->get('request')->request->all();
        $arr = $this->onhandAllData(
            $decoded['user_id'],
            $decoded['sorting_col'],
            $decoded['sorting_order']
        );
        return $this->render(
                'LoveThatFitSupportBundle:EvaluationSheet:onhandFitIndexAll.html.twig',
                $arr
            );
    }

    private function onhandAllData($user_id, $sorting_col, $sorting_order)
    {
        $requestData = $this->get('request')->request->all();
        $user   = $this->get('user.helper.user')->find($requestData['user_id']);
        //Get all product
        $proIds = $this->get('admin.helper.product')->getAllProductsIds($requestData);
        $sizes  = [];
        foreach ($proIds as $pro) {
            $ids[] = $pro['id'];
            $sizes[] = $this->get('admin.helper.productsizes')
                ->getSizesByProductId($pro['id']);
        }
        $try_sizes = [];
        foreach ($sizes as $key => $size) {
            $try_sizes[array_keys($size)[0]] = array_values($size)[0];
        }
        $products = $this->get('admin.helper.product')->listProductByIds($ids);
        $result = [];
        $algo   = new FitAlgorithm2($user);
        $serial = 0;
        foreach ($products as $p) {
            $algo->setProduct($p);
            if ($try_sizes[$p->getId()] != 'NA') {
                $fb        = "";
                $fbExists  = 0;
                $nameArray = array(
                'control_number' => $p->getControlNumber(),
                'clothing_type'  => $p->getClothingType()->getName(),
                'brand'          => $p->getBrand()->getName(),
                'name'           => $p->getName(),
                'color'          => (count($p->getdisplayProductColor()) > 0) ? $p->getdisplayProductColor()->getTitle() : "",
                );
                for ($i = 0; $i < count($try_sizes[$p->getId()]); $i++) {
                    $fb = $algo->getFeedBackForSizeTitle($try_sizes[$p->getId()][$i]);
                    if (is_array($fb) && array_key_exists('feedback', $fb)) {
                        if (is_array($fb) && array_key_exists('recommendation', $fb)) {
                            $nameArray['recommended_size']      = $fb["recommendation"]['title'];
                            $nameArray['recommended_fit_index'] = $fb["recommendation"]['fit_index'];
                        }

                        if ($nameArray['recommended_size'] == $try_sizes[$p->getId()][$i]) {
                            if ($nameArray['recommended_fit_index'] > 0 &&
                                $fb["feedback"]['fit_index'] > 0
                            ) {
                                $fbExists               = 1;
                                $nameArray['size']      = $fb["feedback"]['title'];
                                $nameArray['fits']      = $fb["feedback"]['fits'];
                                $nameArray['fit_index'] = $fb["feedback"]['fit_index'];
                             }
                        }
                    }
                    if ($fbExists == 1) {
                        $result[$p->getId()] = $nameArray;
                    }
                }
            }
            $serial++;
        }
        
        if ($sorting_col != "" && $sorting_order != "") {
            if ($sorting_order == "up") {
                uasort($result, $this->make_comparer(array($sorting_col, SORT_ASC)));
            } elseif ($sorting_order == "down") {
                uasort($result, $this->make_comparer(array($sorting_col, SORT_DESC)));
            }
        }
        return array(
            'products' => $result,
            'user'     => $user,
        );
    }

    /***** all products highest fit index end *****/

    private function make_comparer()
    {
        $criteriaNames = func_get_args();
        $comparer      = function ($first, $second) use ($criteriaNames) {
            // Do we have anything to compare?
            while (!empty($criteriaNames)) {
                // What will we compare now?
                $criterion = array_shift($criteriaNames);

                // Used to reverse the sort order by multiplying
                // 1 = ascending, -1 = descending
                $sortOrder = 1;
                if (is_array($criterion)) {
                    $sortOrder = $criterion[1] == SORT_DESC ? -1 : 1;
                    $criterion = $criterion[0];
                }

                // Do the actual comparison
                if ($first[$criterion] < $second[$criterion]) {
                    return -1 * $sortOrder;
                } else if ($first[$criterion] > $second[$criterion]) {
                    return 1 * $sortOrder;
                }

            }
            // Nothing more to compare with, so $first == $second
            return 0;
        };
        return $comparer;
    }

    #--------------------------------------------------
    public function printAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        #return new Response($decoded['ids']);
        #if range ...................
        $user = $this->get('user.helper.user')->find($decoded['user_id']);

        if (strlen(ltrim($decoded['ids'])) > 0) {
            $ids = explode(',', $decoded['ids']);

            $products = $this->get('admin.helper.product')->listProductByIds($ids);
            #$products = $this->get('admin.helper.product')->listProductsByGenderAndIds($user->getGender(), $ids);
        } else {
            #$products = $this->get('admin.helper.product')->listAll($decoded['page'], $decoded['limit']);
            $products = $this->get('admin.helper.product')->listAllByGender($user->getGender(), $decoded['page'], $decoded['limit']);
        }

        $pa = array();
        #$user = $this->get('user.helper.user')->find($decoded['user_id']);
        $algo      = new FitAlgorithm2($user);
        $serial    = ($decoded['page'] * $decoded['limit']) + 1;
        $last_item = '';
        foreach ($products as $p) {
            $algo->setProduct($p);
            $fb = $algo->getFeedBack();
            if (array_key_exists('recommendation', $fb)) {
                if ($p->getClothingType()->getName() == $last_item) {
                    $name = '';
                } else {
                    $name = $p->getClothingType()->getName();
                }
                $pa[$p->getId()] = array('name' => $p->getName(),
                    'fit_index'                     => $fb['recommendation']['fit_index'],
                    'clothing_type'                 => $name,
                    'size'                          => $this->getEncodedSize($fb['recommendation']['title']),
                    'actual_size'                   => $fb['recommendation']['title'],
                    'serial'                        => $serial,
                );
            }
            $serial++;
            $last_item = $p->getClothingType()->getName();
        }
        return $this->render('LoveThatFitSupportBundle:EvaluationSheet:print.html.twig', array(
            'products' => $pa,
            'user'     => $user,
        ));
    }

    public function csvAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        #return new Response($decoded['ids']);
        #if range ...................
        $user = $this->get('user.helper.user')->find($decoded['user_id']);
        if (strlen(ltrim($decoded['ids'])) > 0) {
            $ids = explode(',', $decoded['ids']);

            $products = $this->get('admin.helper.product')->listProductByIds($ids);
            #$products = $this->get('admin.helper.product')->listProductsByGenderAndIds($user->getGender(), $ids);
        } else {
            #$products = $this->get('admin.helper.product')->listAll($decoded['page'], $decoded['limit']);
            $products = $this->get('admin.helper.product')->listAllByGender($user->getGender(), $decoded['page'], $decoded['limit']);
        }

        $pa = array();
        #$user = $this->get('user.helper.user')->find($decoded['user_id']);
        $algo = new FitAlgorithm2($user);

        header('Content-Type: application/csv');
        header('Content-Disposition: attachement; filename="data.csv";');
        $f                = fopen('php://output', 'w');
        $is_first_element = true;
        $last_item        = '';

        # Then loop through the rows
        foreach ($products as $p) {
            $algo->setProduct($p);
            $fb = $algo->getFeedBack();
            if (array_key_exists('recommendation', $fb)) {
                if ($p->getClothingType()->getName() == $last_item) {
                    $name = '';
                } else {
                    $name = $p->getClothingType()->getName();
                }
                $pa = array(
                    'user_id'       => $decoded['user_id'],
                    'email'         => $user->getEmail(),
                    'clothing_type' => $name,
                    'product_id'    => $p->getId(),
                    'name'          => $p->getName(),
                    'size'          => $this->getEncodedSize($fb['recommendation']['title']),
                    'fit_index'     => $fb['recommendation']['fit_index'],
                );

                if ($is_first_element) {

                    fputcsv($f, array_keys($pa));
                    fputcsv($f, $pa);
                    $is_first_element = false;
                } else {
                    fputcsv($f, $pa);
                }
            }

            //$last_item = $p->getClothingType()->getName();
        }
        # Close the stream off
        fclose($f);
        return new Response('');

    }
    public function getEncodedSize($size)
    {
        $size_array = array(
            "32"  => 'A',
            "31"  => 'B',
            "30"  => 'C',
            "29"  => 'D',
            "28"  => 'E',
            "27"  => 'F',
            "26"  => 'G',
            "25"  => 'H',
            "24"  => 'I',
            "32"  => 'J',
            "XXL" => 'K',
            "XL"  => 'L',
            "L"   => 'M',
            "M"   => 'N',
            "S"   => 'O',
            "XS"  => 'P',
            "16"  => 'Q',
            "14"  => 'R',
            "12"  => 'S',
            "10"  => 'T',
            "8"   => 'U',
            "6"   => 'V',
            "4"   => 'W',
            "2"   => 'X',
            "0"   => 'Y',
            "00"  => 'Z',
        );

        if (array_key_exists($size, $size_array)) {
            return $size_array[$size];
        }
    }
}
