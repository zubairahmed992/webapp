<?php

namespace LoveThatFit\SupportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\SupportBundle\Form\Type\AlgoritumTestlType;
use LoveThatFit\SupportBundle\Form\Type\AlgoritumProductTestlType;
use LoveThatFit\SiteBundle\Comparison;
use LoveThatFit\SiteBundle\DependencyInjection\FitAlgorithm2;

class EvaluationSheetController extends Controller {


    ### User Test Demo Products copy of User Marathon products ###
    #--------------------------------------------------
    public function indexAction() {
        $userForm = $this->createForm(new AlgoritumTestlType());
        $users = $this->get('user.helper.user')->findAllUsersAsc();
        return $this->render('LoveThatFitSupportBundle:EvaluationSheet:index.html.twig', array(
            'userForm' => $userForm->createView(),
            'users' => $users,
        ));
    }

    #-------------------------------------------------- User Test Demo Products Ajax Call copy of Marathon
    public function sampleAction() {
        //$decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        //$arr=$this->test_demo_data($decoded['user_id']);
        $decoded = $this->get('request')->request->all();

        $arr=$arr=$this->test_demo_data(
            $decoded['user_id'],
            $decoded['sorting_col'],
            $decoded['sorting_order']
        );

        return $this->render('LoveThatFitSupportBundle:EvaluationSheet:sample.html.twig', $arr);
    }
    #-------------------------------------------------- User Test Demo Products Ajax Call copy of Marathon
    public function cartAction() {
        //$decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $decoded = $this->get('request')->request->all();
        $user = $this->get('user.helper.user')->find($decoded['user_id']);
        $cart=$user->getCart();
        $pa= array();

        $algo = new FitAlgorithm2($user);
        $serial = 1;
        $arr=array();
        //foreach ($cart as $c) {
        //    $arr[$c->getId()] = $c->getProductItem()->getProduct()->getId();
        //}
        //return new response(json_encode($arr));
        //die;
        foreach ($cart as $c) {
            $p=$c->getProductItem()->getProduct();
            $algo->setProduct($p);

                $fb = $algo->getFeedBackForSizeTitle($c->getProductItem()->getProductSize()->getTitle());
                $product_color = $c->getProductItem()->getProductColor()->getTitle();
                if (is_array($fb) && array_key_exists('feedback', $fb)) {
                    $pa[$c->getId()] = array(
                        'product_id' => $p->getId(),
                        'brand' => $p->getBrand()->getName(),
                        'name' => $p->getName(),
                        'fit_index'=>$fb["feedback"]['fit_index'],
                        'clothing_type' => $p->getClothingType()->getName(),
                        #'size'=> $this->getEncodedSize($fb["feedback"]['title']),
                        'size'=> $fb["feedback"]['title'],
                        'color'=> $product_color,
                        'serial'=>$serial,
                        'fits'=>$fb["feedback"]['fits'],
                        'recommended_size'=> '',
                        'recommended_fit_index'=>'',
                    );
                    if(is_array($fb) && array_key_exists('recommendation', $fb)){
                        $pa[$c->getId()]['recommended_size']= $fb["recommendation"]['title'];
                        $pa[$c->getId()]['recommended_fit_index']=$fb["recommendation"]['fit_index'];
                    }
                }
            $serial++;
        }
        if ($decoded['sorting_col'] != "" && $decoded['sorting_order'] != "") {
            if ($decoded['sorting_order'] == "up") {
                uasort($pa, $this->make_comparer(array($decoded['sorting_col'], SORT_ASC)));
            } elseif($decoded['sorting_order'] == "down") {
                uasort($pa, $this->make_comparer(array($decoded['sorting_col'], SORT_DESC)));
            }
        }

        return $this->render('LoveThatFitSupportBundle:EvaluationSheet:cart.html.twig', array('products' => $pa,'user' => $user));
    }

    #-------------------------------------------------- Favourite products of user
    public function favouriteAction() {
        //$decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $decoded = $this->get('request')->request->all();
        $user = $this->get('user.helper.user')->find($decoded['user_id']);
        $favourite=$user->getProductItems();
        $pa= array();

        $algo = new FitAlgorithm2($user);
        $serial = 1;
        $arr=array();
        //foreach ($cart as $c) {
        //    $arr[$c->getId()] = $c->getProductItem()->getProduct()->getId();
        //}
        //return new response(json_encode($arr));
        //die;
        foreach ($favourite as $c) {
            $p=$c->getProduct();
            $algo->setProduct($p);

            $fb = $algo->getFeedBackForSizeTitle($c->getProductSize()->getTitle());
            $product_color = $c->getProductColor()->getTitle();
            if (is_array($fb) && array_key_exists('feedback', $fb)) {
                $pa[$c->getId()] = array(
                    'product_id' => $p->getId(),
                    'brand' => $p->getBrand()->getName(),
                    'name' => $p->getName(),
                    'fit_index'=>$fb["feedback"]['fit_index'],
                    'clothing_type' => $p->getClothingType()->getName(),
                    #'size'=> $this->getEncodedSize($fb["feedback"]['title']),
                    'size'=> $fb["feedback"]['title'],
                    'color'=> $product_color,
                    'serial'=>$serial,
                    'fits'=>$fb["feedback"]['fits'],
                    'recommended_size'=> '',
                    'recommended_fit_index'=>'',
                );
                if(is_array($fb) && array_key_exists('recommendation', $fb)){
                    $pa[$c->getId()]['recommended_size']= $fb["recommendation"]['title'];
                    $pa[$c->getId()]['recommended_fit_index']=$fb["recommendation"]['fit_index'];
                }
            }
            $serial++;
        }

        if ($decoded['sorting_col'] != "" && $decoded['sorting_order'] != "") {
            if ($decoded['sorting_order'] == "up") {
                uasort($pa, $this->make_comparer(array($decoded['sorting_col'], SORT_ASC)));
            } elseif($decoded['sorting_order'] == "down") {
                uasort($pa, $this->make_comparer(array($decoded['sorting_col'], SORT_DESC)));
            }
        }

        return $this->render('LoveThatFitSupportBundle:EvaluationSheet:favourite.html.twig', array('products' => $pa,'user' => $user));
    }

    #--------------------------------------------------

    public function onhandFitIndexAction() {
        $decoded = $this->get('request')->request->all();

        $arr=$arr=$this->test_demo_data_fit_index(
            $decoded['user_id'],
            $decoded['sorting_col'],
            $decoded['sorting_order']
        );

        return $this->render('LoveThatFitSupportBundle:EvaluationSheet:onhandFitIndex.html.twig', $arr);
    }


    #--------------------------------------------------
    private function test_demo_data($user_id, $sorting_col, $sorting_order)
    {
        $user = $this->get('user.helper.user')->find($user_id);
        $ids= array (472,473,474,475,476,479,540,541,490,491,492,494,495,496,497,499,500,501,502,503,504,505,506,507,508,509,510,512,513,514,515,516,517,518,519,520,522,524,525,532,535,536,537,538,539,544,546,547,548,549,552,554);
        $try_sizes = array ('472'=>'NA','473'=>'NA','474'=>'NA','475'=>'NA','476'=>'NA','479'=>'NA','540'=>'NA','541'=>'NA','490'=>'2', '491'=>'S', '492'=>'2', '494'=>'4', '495'=>'XS', '496'=>'XS', '497'=>'XS', '499'=>'S', '500'=>'XS', '501'=>'XS', '502'=>'S', '503'=>'XS', '504'=>'S', '505'=>'XS', '506'=>'XS', '507'=>'S', '508'=>'XS', '509'=>'S', '510'=>'S', '512'=>'XS', '513'=>'S', '514'=>'XS', '515'=>'S', '516'=>'S', '517'=>'S', '518'=>'S', '519'=>'4', '520'=>'2', '522'=>'S', '524'=>'4', '525'=>'2', '532'=>'XS', '535'=>'0', '536'=>'XS', '537'=>'XS', '538'=>'S', '539'=>'S', '544'=>'4', '546'=>'25', '547'=>'25', '548'=>'25', '549'=>'25', '552'=>'25', '554'=>'2');
        $products = $this->get('admin.helper.product')->listProductByIds($ids);
        
        $pa= array();

        $algo = new FitAlgorithm2($user);
        $serial = 1;
        foreach ($products as $p) {
            $algo->setProduct($p);
            if ($try_sizes[$p->getId()]=='NA'){
                $fb = $algo->getFeedBack();
                if (array_key_exists('recommendation', $fb)) {
                    $pa[$p->getId()] = array('name' => $p->getName(),
                        'brand' => $p->getBrand()->getName(),
                        'fit_index'=>$fb['recommendation']['fit_index'],
                        'clothing_type' => $p->getClothingType()->getName(),
                        'size'=> $fb["recommendation"]['title'],
                        'color'=> $p->getdisplayProductColor()->getTitle(),
                        'serial'=>$serial,
                        'fits'=>$fb["recommendation"]['fits'],
                        'recommended_size'=> '-',
                        'recommended_fit_index'=>'-',
                    );
                }
            }
            else{
                $fb = $algo->getFeedBackForSizeTitle($try_sizes[$p->getId()]);
                if (is_array($fb) && array_key_exists('feedback', $fb)) {
                    $pa[$p->getId()] = array('name' => $p->getName(),
                        'brand' => $p->getBrand()->getName(),
                        'fit_index'=>$fb["feedback"]['fit_index'],
                        'clothing_type' => $p->getClothingType()->getName(),
                        #'size'=> $this->getEncodedSize($fb["feedback"]['title']),
                        'size'=> $fb["feedback"]['title'],
                        'color'=> $p->getdisplayProductColor()->getTitle(),
                        'serial'=>$serial,
                        'fits'=>$fb["feedback"]['fits'],
                        'recommended_size'=> '',
                        'recommended_fit_index'=>'',
                    );
                    if(is_array($fb) && array_key_exists('recommendation', $fb)){
                            $pa[$p->getId()]['recommended_size']= $fb["recommendation"]['title'];
                            $pa[$p->getId()]['recommended_fit_index']=$fb["recommendation"]['fit_index'];
                    }
                }
            }
            $serial++;
        }
        if ($sorting_col != "" && $sorting_order != "") {
            if ($sorting_order == "up") {
                uasort($pa, $this->make_comparer(array($sorting_col, SORT_ASC)));
            } elseif($sorting_order == "down") {
                uasort($pa, $this->make_comparer(array($sorting_col, SORT_DESC)));
            }
        }

        return array(
            'products' => $pa,
            'user' => $user,
        );
    }

    private function test_demo_data_fit_index($user_id, $sorting_col, $sorting_order)
    {
        $user = $this->get('user.helper.user')->find($user_id);
        $ids= array (472,473,474,475,476,479,540,541,490,491,492,494,495,496,497,499,500,501,502,503,504,505,506,507,508,509,510,512,513,514,515,516,517,518,519,520,522,524,525,532,535,536,537,538,539,544,546,547,548,549,552,554);
        $try_sizes = array ('472'=>'NA','473'=>'NA','474'=>'NA','475'=>'NA','476'=>'NA','479'=>'NA','540'=>'NA','541'=>'NA','490'=>'2', '491'=>'S', '492'=>'2', '494'=>'4', '495'=>'XS', '496'=>'XS', '497'=>'XS', '499'=>'S', '500'=>'XS', '501'=>'XS', '502'=>'S', '503'=>'XS', '504'=>'S', '505'=>'XS', '506'=>'XS', '507'=>'S', '508'=>'XS', '509'=>'S', '510'=>'S', '512'=>'XS', '513'=>'S', '514'=>'XS', '515'=>'S', '516'=>'S', '517'=>'S', '518'=>'S', '519'=>'4', '520'=>'2', '522'=>'S', '524'=>'4', '525'=>'2', '532'=>'XS', '535'=>'0', '536'=>'XS', '537'=>'XS', '538'=>'S', '539'=>'S', '544'=>'4', '546'=>'25', '547'=>'25', '548'=>'25', '549'=>'25', '552'=>'25', '554'=>'2');
        $products = $this->get('admin.helper.product')->listProductByIds($ids);
        
        $pa     = array();
        $result = array();

        $algo = new FitAlgorithm2($user);
        $serial = 1;
        foreach ($products as $p) {
            $algo->setProduct($p);
            if ($try_sizes[$p->getId()] !='NA'){
                $fb = $algo->getFeedBackForSizeTitle($try_sizes[$p->getId()]);
                if (is_array($fb) && array_key_exists('feedback', $fb)) {
                    $pa[$p->getId()] = array('name' => $p->getName(),
                        'brand' => $p->getBrand()->getName(),
                        'fit_index'=>$fb["feedback"]['fit_index'],
                        'clothing_type' => $p->getClothingType()->getName(),
                        'size'=> $fb["feedback"]['title'],
                        'color'=> $p->getdisplayProductColor()->getTitle(),
                        'serial'=>$serial,
                        'fits'=>$fb["feedback"]['fits'],
                        'recommended_size'=> '',
                        'recommended_fit_index'=>'',
                    );
                    if(is_array($fb) && array_key_exists('recommendation', $fb)){
                            $pa[$p->getId()]['recommended_size']= $fb["recommendation"]['title'];
                            $pa[$p->getId()]['recommended_fit_index']=$fb["recommendation"]['fit_index'];
                    }
                }
            }
            $serial++;
        }

        foreach ($pa as $res) {
            if ($res['size'] == $res['recommended_size']) {
                if ($res['fit_index'] > 0 && $res['recommended_fit_index'] > 0) {
                    $result[] = $res;
                }
            }
        }

        if ($sorting_col != "" && $sorting_order != "") {
            if ($sorting_order == "up") {
                uasort($result, $this->make_comparer(array($sorting_col, SORT_ASC)));
            } elseif($sorting_order == "down") {
                uasort($result, $this->make_comparer(array($sorting_col, SORT_DESC)));
            }
        }

        return array(
            'products' => $result,
            'user' => $user,
        );
    }

    private function make_comparer() {
        $criteriaNames = func_get_args();
        $comparer = function($first, $second) use ($criteriaNames) {
            // Do we have anything to compare?
            while(!empty($criteriaNames)) {
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
                }
                else if ($first[$criterion] > $second[$criterion]) {
                    return 1 * $sortOrder;
                }

            }
            // Nothing more to compare with, so $first == $second
            return 0;
        };
        return $comparer;
    }


    #--------------------------------------------------
    public function printAction() {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        #return new Response($decoded['ids']);
        #if range ...................
        $user = $this->get('user.helper.user')->find($decoded['user_id']);

        if (strlen(ltrim($decoded['ids']))>0){
            $ids=explode(',', $decoded['ids']);

            $products = $this->get('admin.helper.product')->listProductByIds($ids);
            #$products = $this->get('admin.helper.product')->listProductsByGenderAndIds($user->getGender(), $ids);
        }else{
            #$products = $this->get('admin.helper.product')->listAll($decoded['page'], $decoded['limit']);
            $products = $this->get('admin.helper.product')->listAllByGender($user->getGender(), $decoded['page'],$decoded['limit']);
        }

        $pa= array();
        #$user = $this->get('user.helper.user')->find($decoded['user_id']);
        $algo = new FitAlgorithm2($user);
        $serial = ($decoded['page']*$decoded['limit'])+1;
        $last_item='';
        foreach ($products as $p) {
            $algo->setProduct($p);
            $fb = $algo->getFeedBack();
            if (array_key_exists('recommendation', $fb)) {
                if($p->getClothingType()->getName() == $last_item){
                    $name = '';
                }else{
                    $name = $p->getClothingType()->getName();
                }
                $pa[$p->getId()] = array('name' => $p->getName(),
                    'fit_index'=>$fb['recommendation']['fit_index'],
                    'clothing_type' => $name,
                    'size'=>$this->getEncodedSize($fb['recommendation']['title']),
                    'actual_size'=>$fb['recommendation']['title'],
                    'serial'=>$serial,
                );
            }
            $serial++;
            $last_item = $p->getClothingType()->getName();
        }
        return $this->render('LoveThatFitSupportBundle:EvaluationSheet:print.html.twig', array(
            'products' => $pa,
            'user' => $user,
        ));
    }

    function csvAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());

        #return new Response($decoded['ids']);
        #if range ...................
        $user = $this->get('user.helper.user')->find($decoded['user_id']);
        if (strlen(ltrim($decoded['ids']))>0){
            $ids=explode(',', $decoded['ids']);

            $products = $this->get('admin.helper.product')->listProductByIds($ids);
            #$products = $this->get('admin.helper.product')->listProductsByGenderAndIds($user->getGender(), $ids);
        }else{
            #$products = $this->get('admin.helper.product')->listAll($decoded['page'], $decoded['limit']);
            $products = $this->get('admin.helper.product')->listAllByGender($user->getGender(), $decoded['page'],$decoded['limit']);
        }

        $pa= array();
        #$user = $this->get('user.helper.user')->find($decoded['user_id']);
        $algo = new FitAlgorithm2($user);

        header('Content-Type: application/csv');
        header('Content-Disposition: attachement; filename="data.csv";');
        $f = fopen('php://output', 'w');
        $is_first_element=true;
        $last_item='';

        # Then loop through the rows
        foreach ($products as $p) {
            $algo->setProduct($p);
            $fb = $algo->getFeedBack();
            if (array_key_exists('recommendation', $fb)) {
                if($p->getClothingType()->getName() == $last_item){
                    $name = '';
                }else{
                    $name = $p->getClothingType()->getName();
                }
                $pa = array(
                    'user_id' => $decoded['user_id'],
                    'email' => $user->getEmail(),
                    'clothing_type' => $name,
                    'product_id' => $p->getId(),
                    'name' => $p->getName(),
                    'size'=>$this->getEncodedSize($fb['recommendation']['title']),
                    'fit_index'=>$fb['recommendation']['fit_index'],
                );

                if ($is_first_element){

                    fputcsv($f, array_keys($pa));
                    fputcsv($f, $pa);
                    $is_first_element=false;
                }else{
                    fputcsv($f, $pa);
                }
            }

            //$last_item = $p->getClothingType()->getName();
        }
        # Close the stream off
        fclose($f);
        return new Response('');

    }
    public function getEncodedSize($size){
        $size_array = array(
            "32" => 'A',
            "31" => 'B',
            "30" => 'C',
            "29" => 'D',
            "28" => 'E',
            "27" => 'F',
            "26" => 'G',
            "25" => 'H',
            "24" => 'I',
            "32" => 'J',
            "XXL" => 'K',
            "XL" => 'L',
            "L" => 'M',
            "M" => 'N',
            "S" => 'O',
            "XS" => 'P',
            "16" => 'Q',
            "14" => 'R',
            "12" => 'S',
            "10" => 'T',
            "8" => 'U',
            "6" => 'V',
            "4" => 'W',
            "2" => 'X',
            "0" => 'Y',
            "00" => 'Z'
        );

            if(array_key_exists($size,$size_array)){
                return $size_array[$size];
            }

    }
}
