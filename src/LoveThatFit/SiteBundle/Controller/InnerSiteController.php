<?php
namespace LoveThatFit\SiteBundle\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Yaml\Dumper;
use LoveThatFit\SiteBundle\Comparison;
use LoveThatFit\SiteBundle\Algorithm;
use LoveThatFit\SiteBundle\Cart;
use LoveThatFit\AdminBundle\ImageHelper;
use LoveThatFit\AdminBundle\Entity\ClothingType;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ProductItem;
use LoveThatFit\AdminBundle\Entity\Brand;
use LoveThatFit\UserBundle\Entity\Measurement;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\SiteBundle\Entity\UserItemTryHistory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InnerSiteController extends Controller {

    //-------------------------------------------------------------------------

    public function indexAction($list_type) {
        return $this->render('LoveThatFitSiteBundle:InnerSite:index.html.twig', array(
            'list_type'=>$list_type,
           ));
    }

        //-------------------------------------------------------------------------

    public function homeAction($page_number = 0, $limit = 0) {
       $gender= $this->get('security.context')->getToken()->getUser()->getGender();
       $user_id= $this->get('security.context')->getToken()->getUser()->getId();
        $latest = $this->get('admin.helper.product')->listByType(array('limit'=>5, 'list_type'=>'latest'));
        if(count($this->get('admin.helper.product')->listByType(array('limit'=>3, 'list_type'=>'faviourite')))>0)
        {
            $favourite = $this->get('admin.helper.product')->listByType(array('limit'=>3, 'list_type'=>'faviourite'));
        }else
        {
            $favourite = $this->get('admin.helper.product')->findByGenderRandom('F',3);
        }
        if(count($this->get('admin.helper.product')->listByType(array('limit'=>3, 'list_type'=>'tried')))>0)
        {
            $tried_on = $this->get('admin.helper.product')->listByType(array('limit'=>3, 'list_type'=>'tried'));
        }else
        {
            $tried_on = $this->get('admin.helper.product')->findByGenderRandom('F',3);
        }
       $recomended = $this->get('admin.helper.product')->findByGenderBrandName($gender,'H&M' ,$page_number, $limit);        
         return $this->render('LoveThatFitSiteBundle:InnerSite:home.html.twig', array(
            'latest'=>$latest,
            'tried_on'=>$tried_on,
            'favourite'=>$favourite,
            'recomended'=>$recomended,            
           ));
    }
    
    

////////////////////////////////// Product Slider /////////////////////////////////////////////////////////////////
    
      public function productsByTypeAction($list_type='latest', $page_number = 0, $limit = 0) {
          $user_id = $this->get('security.context')->getToken()->getUser()->getId();
          $gender = $this->get('security.context')->getToken()->getUser()->getGender();
          $options = array('gender'=>$gender, 'user_id'=>$user_id, 'list_type'=>$list_type, 'page_number' => $page_number, 'limit' => $limit);
          $entity=$this->get('admin.helper.product')->listByType($options);        
          return $this->renderProductTemplate($entity, $page_number, $limit);
    }
    
    //-------------------------------------------------------------------------
    public function productsAction($gender, $page_number = 0, $limit = 0) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findByGender($gender, $page_number, $limit);
        return $this->renderProductTemplate($entity, $page_number, $limit);
    }

    //----------------------------------- Whats New ..............
    public function productsLatestAction($gender, $page_number = 0, $limit = 0) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findByGenderLatest($gender, $page_number, $limit);
        return $this->renderProductTemplate($entity, $page_number, $limit);
    }
    
    //----------------------------------- 
    public function productsHotestAction($gender, $page_number = 0, $limit = 0)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findMostTriedOnByGender($gender, $page_number, $limit);
        return $this->renderProductTemplate($entity, $page_number, $limit);
    }
//----------------------------------- 
    public function productsRecomendedAction($gender, $page_number = 0, $limit = 0)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findMostTriedOnByGender($gender, $page_number, $limit);
        return $this->renderProductTemplate($entity, $page_number, $limit);
    }
    //----------------------------------- 
    public function productsRecentlyTriedOnByUserAction($page_number = 0, $limit = 0)
    {
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findRecentlyTriedOnByUser($user_id, $page_number, $limit);
        return $this->renderProductTemplate($entity, $page_number, $limit);
    }
   //-----------------------------------  
    public function productsMostFavoriteAction($gender,$page_number = 0, $limit = 0)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findProductByItemUser($gender,$page_number, $limit);
        return $this->renderProductTemplate($entity, $page_number, $limit);
    }
    //----------------------------------- 
    public function productsLTFRecommendationAction($gender, $page_number = 0, $limit = 0)
    {
        $brand='Ellie';
        $em = $this->getDoctrine()->getManager();
        $count =count($em->getRepository('LoveThatFitAdminBundle:Product')->findOneByName($brand));
        if($count>0)
        {        
          $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findProductByEllieHM($brand,$gender,$page_number, $limit);
        }else
        {
            $brand='H&M';
            $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findProductByEllieHM($brand,$gender,$page_number, $limit);
        }
        return $this->renderProductTemplate($entity, $page_number, $limit);
    }
    
    //----------------------------------- by Brand ..............
    public function productsByBrandAction($gender, $brand_id, $page_number = 0, $limit = 0) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findByGenderBrand($gender, $brand_id, $page_number, $limit);
        return $this->renderProductTemplate($entity, $page_number, $limit);
    }

    //----------------------------------- By Clothing Type ..............
    public function productsByClothingTypeAction($gender, $clothing_type_id, $page_number = 0, $limit = 0) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findByGenderClothingType($gender, $clothing_type_id, $page_number, $limit);
        return $this->renderProductTemplate($entity, $page_number, $limit);
    }

//------------------------------------------- render method ----------------------------------------
    private function renderProductTemplate($entity, $page_number, $limit) {
        return $this->render('LoveThatFitSiteBundle:InnerSite:_products.html.twig', array('products' => $entity, 'page_number' => $page_number, 'limit' => $limit, 'row_count' => count($entity)));
    }
    
///////////////////////////////////////////////////////////////////////////////////////////////////
    //----------------------------------- Sample Clothing Type ..............
    public function productsClothingTypeAction($gender) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findSampleClothingTypeGender($gender);
        return $this->render('LoveThatFitSiteBundle:InnerSite:_products.html.twig', array('products' => $entity));
    }

    //----------------------------------- List Clothing Types ..............
    public function clothingTypesAction() {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:ClothingType')->findAll();
        return $this->render('LoveThatFitSiteBundle:InnerSite:_clothingTypes.html.twig', array('clothing_types' => $entity));
    }

//----------------------------------- List Brands ..............
    public function brandsAction() {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Brand')->findAll();
        return $this->render('LoveThatFitSiteBundle:InnerSite:_brands.html.twig', array('brands' => $entity));
    }

//----------------------------------- Product Detail ..............        
    public function productDetailAction($id, $product_color_id, $product_size_id) {
        $product_color = null;
        $product_size = null;
        $product_item = null;
// find product
        $product = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->find($id);
// find product color if get color id param
        if ($product_color_id) {
            $product_color = $this->getDoctrine()
                    ->getRepository('LoveThatFitAdminBundle:ProductColor')
                    ->find($product_color_id);
        } else {// find default product color if not params for color id
            $product_color = $product->getDisplayProductColor();
            $product_color_id = $product_color->getId();
        }

        //get color size array, sizes that are available in this color 
        $color_sizes_array = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ProductColor')
                ->getSizeArray($product_color_id);
        $size_id = null;
// find size id is not in param gets the first size id for this color
        if (!$product_size_id) {
            $psize = array_shift($color_sizes_array);
            $size_id = $psize['id'];
        } else {
// if gets the size id in params,  check if this size is available in this color, if not get the first one
            foreach ($color_sizes_array as $csa) {
                if ($csa['id'] == $product_size_id) {
                    $size_id = $csa['id'];
                }
            }
            if ($size_id == null) {
// gets the first size id for this color
                $psize = array_shift($color_sizes_array);
                $size_id = $psize['id'];
            }
        }
        $product_size_id = $size_id;
        $product_size = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ProductSize')
                ->find($product_size_id);

        //2) color & size can get an item

        if ($product_size && $product_color) {
            $product_item = $this->getDoctrine()
                    ->getRepository('LoveThatFitAdminBundle:ProductItem')
                    ->findByColorSize($product_color->getId(), $product_size->getId());
        }

        if (!$product) {
            throw $this->createNotFoundException('Unable to find Product.');
        }

        return $this->render('LoveThatFitSiteBundle:InnerSite:_product_detail.html.twig', array('product' => $product,
                    'productColor' => $product_color,
                    'productSize' => $product_size,
                    'productItem' => $product_item,
        ));
    }

    //----------------------------------------------------------------------------------    
    public function productsMostLikedAction($page_number = 0, $limit = 0) {       
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findMostLikedProducts($page_number, $limit);
        return $this->render('LoveThatFitSiteBundle:InnerSite:_most_liked_products.html.twig', array('product' => $entity));
    }

//----------------------------------------------------------------------------------  
    
    
//----------------------------------------------------------------------------------    
    public function productsByMyClosetAction($page_number = 0, $limit = 0) {
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findProductItemByUser($user_id, $page_number, $limit);
        return $this->render('LoveThatFitSiteBundle:InnerSite:_closet_products.html.twig', array('product' => $entity));
    }
    
    public function productFriendsFavouritesAction($page_number = 0, $limit = 0)
    {
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findProductItemByUser($user_id, $page_number, $limit);
        return $this->renderProductTemplate($entity, $page_number, $limit);        
    }
    
    
    

//----------------------------------------------------------------------------------    
    public function countMyColosetAction() {
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $brandObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->countMyCloset($user_id);
        $rec_count = count($brandObj->countMyCloset($user_id));
        return new Response($rec_count);
    }

//----------------------------------------------------------------------------------
    public function deleteMyClosetAction($id) {
        $user = $this->get('security.context')->getToken()->getUser();
        $product_item = $this->getProductItemById($id);
        $em = $this->getDoctrine()->getManager();
        $product_item->removeUser($user);
        $user->removeProductItem($product_item);
        $em->persist($product_item);
        $em->persist($user);
        $em->flush();
        $this->get('session')->setFlash('success', 'Product Item Successfully Deleted.');
        return $this->getMyClosetList();
    }

    
    
 #---Delete My Closet at For Ajax
 //----------------------------------------------------------------------------------
    public function deleteMyClosetAjaxAction($product_item_id) {
  
        $user = $this->get('security.context')->getToken()->getUser();
        $product_item = $this->getProductItemById($product_item_id);
        $em = $this->getDoctrine()->getManager();
        $product_item->removeUser($user);
        $user->removeProductItem($product_item);
        $em->persist($product_item);
        $em->persist($user);
        $em->flush();
       return new response('success');   
        
    }

 #--End of Delete My Closet ForAjax   
    //-------------------------------------------------------------------
    public function ajaxAction() {
        return $this->render('LoveThatFitSiteBundle:InnerSite:ajax.html.twig');
    }

    //-------------------------------------------------------------------
    public function emailAction($id) {

        // $user= $this->get('security.context')->getToken()->getUser();
        $product = $this->getProduct($id);

        $session = $this->get("session");
    }

    //-------------------------------------------------------------------
    public function getFeedBackJSONAction($user_id, $product_item_id) {

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('LoveThatFitUserBundle:User')->find($user_id);
        $productItem = $this->getProductItemById($product_item_id);        

        if (!$user)
            return new Response("User Not found!");

        if (!$productItem)
            return new Response("Product not found!");

        $fit = new Algorithm($user, $productItem);

        return $this->render('LoveThatFitSiteBundle:InnerSite:determine.html.twig', array('data' => $fit->getFeedBackJson(),
        ));
    }

    //-------------------------------------------------------------------
    public function getFeedBackListAction($product_item_id) {
        $user = $this->get('security.context')->getToken()->getUser();
        $productItem = $this->getProductItemById($product_item_id);
        
        if (!is_object($this->get('security.context')->getToken()->getUser()))
            return new Response("User Not found, Log in required!");

        if (!$productItem)
            return new Response("Product not found!");

        $fit = new Algorithm($user, $productItem);
        $json_feedback = $fit->getFeedBackJson();
        $fits = $fit->fit();
        $product_id=$this->getProductByItemId($productItem);
        $product_id=$product_id[0]['id'];        
        $this->createUserItemTryHistory($user,$product_id, $productItem, $json_feedback, $fits);
        return $this->render('LoveThatFitSiteBundle:InnerSite:_fitting_feedback.html.twig', array('product' => $productItem->getProduct(), 'product_item' => $productItem, 'data' => $fit->getFeedBackArray()));
    }

    //-------------------------------------------------------------------

    public function addToCloestAction($product_item_id) {
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $brandObj = $this->getDoctrine()->getRepository('LoveThatFitAdminBundle:Product');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->countMyCloset($user_id);
        $rec_count = count($brandObj->countMyCloset($user_id));
        if ($rec_count >= 25) {
            $this->get('session')->setFlash('warning', 'Please Remove Some Like You can not like more than 25.');
            return new response(0);
        } else {
            $user = $this->get('security.context')->getToken()->getUser();
            $product_item = $this->getProductItemById($product_item_id);
            $em = $this->getDoctrine()->getManager();
            $product_item->addUser($user);
            $user->addProductItem($product_item);
            $em->persist($product_item);
            $em->persist($user);
            $em->flush();
            return new response('success');
        }
    }

    //User Item Try History----------------------------------------------
    private function createUserItemTryHistory($user,$product_id,$productItem, $json_feedback, $fits) {
        $product=  $this->getProduct($product_id);
        $rec_count = $this->countUserItemTryHistory($user,$product,$productItem);      
        if ($rec_count>0) {
        $em = $this->getDoctrine()->getEntityManager();
        $userItemTry = $this->getDoctrine()->getRepository('LoveThatFitSiteBundle:UserItemTryHistory')->findby(array('product'=>$product,'productitem' => $productItem, 'user' => $user));
        foreach ($userItemTry as $userTryItem) {
            $usertryItemId = $userTryItem->getId();
            $counts= $userTryItem->getCount();            
            $userItemTryId = $this->getDoctrine()->getRepository('LoveThatFitSiteBundle:UserItemTryHistory')->find($usertryItemId);
        }       
        $count=$counts+1;
        $userItemTryId->setCount($count);
        $userItemTryId->setFeedback($json_feedback);
        $userItemTryId->setFit($fits);
        $userItemTryId->setUpdatedAt(new \DateTime('now'));
        $em->persist($userItemTryId);
        $em->flush();
        } else {            
            $useritemtryhistory = new UserItemTryHistory();
            $useritemtryhistory->setCount(1);
            $useritemtryhistory->setFit($fits);
            $useritemtryhistory->setCreatedAt(new \DateTime('now'));
            $useritemtryhistory->setUpdatedAt(new \DateTime('now'));
            $useritemtryhistory->setProductitem($productItem);
            $useritemtryhistory->setProduct($product);            
            $useritemtryhistory->setUser($user);
            $useritemtryhistory->setFeedback($json_feedback);
            $em = $this->getDoctrine()->getManager();
            $em->persist($useritemtryhistory);
            $em->flush();
        }      
        return true;
    }

    //-------------------------------------------------------------------
    private function getProduct($id) {
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->find($id);
        return $entity;
    }
    
    private function getProductByItemId($productItem) {
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ProductItem')
                ->findProductByItemId($productItem);
        return $entity;
    }

    //-------------------------------------------------------------------
    private function getMeasurement($id) {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('LoveThatFitUserBundle:Measurement')->findOneByUserId($id);
    }

    //-------------------------------------------------------------------
    private function getProductItemById($id) {
        $product_item = $this->getDoctrine()
                ->getRepository('LoveThatFitAdminBundle:ProductItem')
                ->find($id);
        return $product_item;
    }

//-------------------------------------------------------------------
    private function getMyClosetList($page_number = 0, $limit = 0) {
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoveThatFitAdminBundle:Product')->findProductItemByUser($user_id, $page_number = 0, $limit = 0);
        return $this->render('LoveThatFitSiteBundle:InnerSite:_closet_products.html.twig', array('product' => $entity));
    }
    
    private function countUserItemTryHistory($user,$product,$productItem)
   {
        $em = $this->getDoctrine()->getManager();
        $useritemtryhistoryobj = $this->getDoctrine()->getRepository('LoveThatFitSiteBundle:UserItemTryHistory');
        $entity = $this->getDoctrine()
                ->getRepository('LoveThatFitSiteBundle:UserItemTryHistory')
                 ->findUserItemAllTryHistory($user,$product,$productItem);
		$rec_count = count($useritemtryhistoryobj->findUserItemAllTryHistory($user,$product,$productItem));
        return $rec_count;
   }   
   
}
?>

