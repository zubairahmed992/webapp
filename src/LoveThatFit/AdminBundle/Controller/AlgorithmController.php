<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\AdminBundle\Form\Type\AlgoritumTestlType;
use LoveThatFit\AdminBundle\Form\Type\AlgoritumProductTestlType;
use LoveThatFit\SiteBundle\Comparison;
use LoveThatFit\SiteBundle\DependencyInjection\FitAlgorithm2;

class AlgorithmController extends Controller {

    //------------------------------------------------------------------------------------------
################################################################
#   Fit Algorithm 2    
################################################################

    public function fitAlgorithmIndexAction() {
        $userForm = $this->createForm(new AlgoritumTestlType());
        $productForm = $this->createForm(new AlgoritumProductTestlType());
        return $this->render('LoveThatFitAdminBundle:Algoritm:algo2_index.html.twig', array(
                    'userForm' => $userForm->createView(),
                    'productForm' => $productForm->createView(),
                    'user' => '',
                ));
    }

//------------------------------------------------------------------------------------------

    public function fitAlgorithmCompareAction($user_id, $product_id, $json = 0) {
        $product = $this->get('admin.helper.product')->find($product_id);
        $user = $this->get('user.helper.user')->find($user_id);
        $fe = new FitAlgorithm2($user, $product);

        if ($json == 0) {
            return $this->render('LoveThatFitAdminBundle:Algoritm:_algo2_comparison.html.twig', array(
                        'product' => $product, 'user' => $user, 'data' => $fe->getFeedback(),
                    ));
        } elseif ($json == 1) {
            return new Response(json_encode($fe->getFeedback()));
        } elseif ($json == 2) {
            return new Response($fe->getStrippedFeedBackJSON());
        }
    }

    //------------------------------------------------------------------------------------------
    
    #--------------------------------------------------
    public function productListAction() {
        $userForm = $this->createForm(new AlgoritumTestlType());
        $users = $this->get('user.helper.user')->findAll();
        return $this->render('LoveThatFitAdminBundle:Algoritm:product_list_index.html.twig', array(
                    'userForm' => $userForm->createView(),        
                    'users' => $users,
                ));
    }
    

    #--------------------------------------------------
    public function userProductMarathonAction() {
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
        foreach ($products as $p) {
            $algo->setProduct($p);
            $fb = $algo->getFeedBack();
            if (array_key_exists('recommendation', $fb)) {
                $pa[$p->getId()] = array('name' => $p->getName(),
                    'fit_index'=>$fb['recommendation']['fit_index'],
                    'clothing_type' => $p->getClothingType()->getName(),
                    'size'=> $fb['recommendation']['description'],
                    'serial'=>$serial,
                    );
            }
            $serial++;
        }
        return $this->render('LoveThatFitAdminBundle:Algoritm:_recommendations.html.twig', array(                    
            'products' => $pa,
                ));
    }
    ### User Test Demo Products copy of User Marathon products ###
    #--------------------------------------------------
    public function userTestDemoAction() {
        $userForm = $this->createForm(new AlgoritumTestlType());
        $users = $this->get('user.helper.user')->findAll();
        return $this->render('LoveThatFitAdminBundle:Algoritm:_demoproducts.html.twig', array(
            'userForm' => $userForm->createView(),
            'users' => $users,
        ));
    }

    #-------------------------------------------------- User Test Demo Products Ajax Call copy of Marathon
    public function userTestDemoProductsAction() {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = $this->get('user.helper.user')->find($decoded['user_id']);
        $ids= array (490,491,492,494,495,496,497,499,500,501,502,503,504,505,506,507,508,509,510,512,513,514,515,516,517,518,519,520,522,524,525,532,535,536,537,538,539,544,546,547,548,549,552,554);
        $try_sizes = array ('490'=>'2', '491'=>'S', '492'=>'2', '494'=>'4', '495'=>'XS', '496'=>'XS', '497'=>'XS', '499'=>'S', '500'=>'XS', '501'=>'XS', '502'=>'S', '503'=>'XS', '504'=>'S', '505'=>'XS', '506'=>'XS', '507'=>'S', '508'=>'XS', '509'=>'S', '510'=>'S', '512'=>'XS', '513'=>'S', '514'=>'XS', '515'=>'S', '516'=>'S', '517'=>'S', '518'=>'S', '519'=>'4', '520'=>'2', '522'=>'S', '524'=>'4', '525'=>'2', '532'=>'XS', '535'=>'0', '536'=>'XS', '537'=>'XS', '538'=>'S', '539'=>'S', '544'=>'4', '546'=>'25', '547'=>'25', '548'=>'25', '549'=>'25', '552'=>'25', '554'=>'2');

        if (strlen(ltrim($decoded['ids']))>0){
            $ids=explode(',', $decoded['ids']);
            $products = $this->get('admin.helper.product')->listProductByIds($ids);
        }



        $pa= array();

        $algo = new FitAlgorithm2($user);
        $serial = 1;
        foreach ($products as $p) {
            $algo->setProduct($p);
            $fb = $algo->getFeedBackForSizeTitle($try_sizes[$p->getId()]);
            if (is_array($fb) && array_key_exists('feedback', $fb)) {
                $pa[$p->getId()] = array('name' => $p->getName(),
                    'fit_index'=>$fb["feedback"]['fit_index'],
                    'clothing_type' => $p->getClothingType()->getName(),
                    #'size'=> $this->getEncodedSize($fb["feedback"]['title']),
                    'size'=> $fb["feedback"]['title'],
                    'serial'=>$serial,
                );
            }else{
                $pa[$p->getId()] = array('name' => $p->getName(),
                    'fit_index'=>'',
                    'clothing_type' => $p->getClothingType()->getName(),
                    'size'=> '',
                    'serial'=>$serial,
                );
            }
            $serial++;
        }
        return $this->render('LoveThatFitAdminBundle:Algoritm:_recommendations_test_demo_products.html.twig', array(
            'products' => $pa,
        ));
    }
    #--------------------------------------------------
    public function printUserProductMarathonAction() {
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
                    'serial'=>$serial,
                );
            }
            $serial++;
            $last_item = $p->getClothingType()->getName();
        }
        return $this->render('LoveThatFitAdminBundle:Algoritm:_print_recommendations.html.twig', array(
            'products' => $pa,
        ));
    }

    function downloadCsvResultsAction()
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
            "W" => '4',
            "X" => '2',
            "Y" => '0',
            "Z" => '0.0'
        );

            if(array_key_exists($size,$size_array)){
                return $size_array[$size];
            }

    }
}
