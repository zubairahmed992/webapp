<?php

namespace LoveThatFit\CartBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;

class CartHelper {

    protected $dispatcher;

    /**
     * @var EntityManager 
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repo;

    /**
     * @var string
     */
    protected $class;

    private $container;

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class,Container $container) {
         $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }
//------------------------------- Add to Cart clicked -----------------------------------------------------///////////
	public function fillCart($product_item_id,$user,$qty) {
	  $product_item=$this->container->get('admin.helper.productitem')->find($product_item_id);
	  $result = $this->findCartByUserId($user,$product_item);
	  if(isset($result['qty']) >  0){
		$id = $result["id"];
		$cart=$this->findCartById($id);
		$setQty = $result["qty"]+1;
		$cart->setQty($setQty);
		return $this->save($cart);
	  }else{
		$cart = $this->createNew();
		$cart->setProductitem($product_item);
		$cart->setUser($user);
		$cart->setQty($qty);
		return $this->save($cart);

	  }
	}
  //------------------------------- Remove User Cart -----------------------------------------------------///////////
  public function removeUserCart($user) {
	return $this->repo->removeCartByUser($user);
  }


#------------------------------Find cart by id--------------------------------#
  public function findCartById($id){
	return $this->repo->find($id);
  }

  #------------------------------Count Items cart by user id--------------------------------#
  public function countCartItems($user){
	return $this->repo->countCartByUser($user);
  }
  #------------------------------Count Items cart by user id amd Quantity--------------------------------#
  public function countCartItemsByQuantity($user){
	return $this->repo->countCartByUserQuantity($user);
  }
#------------------------------ Get Cart Grand Total--------------------------------#
//  public function getCartGrandTotal($decoded){
//		print_r($decoded);die;
//	for($i=0;$i<=count($decoded);$i++){
//	  echo $decoded["product_item"][$i];die;
//	  $product_item=$this->container->get('admin.helper.productitem')->find($decoded["product_item"][$i]);
//	  echo $product_item->getPrice();
//	  //$grand_total+=
//	}
//  }
#------------------------------Find cart by id--------------------------------#
  public function findCartByUserId($user,$product_item){
	return $this->repo->findOneByUserItem($user,$product_item);
  }
	//-------- update Quantity of Cart if item already in cart ----------------------------------------////////////
  public function updateCart($decoded) {
	  $qty = $decoded["qty"];
	  for($i=0;$i<count($qty);$i++){
		$id = $decoded["id"][$i];
		$cart=$this->findCartById($id);
		$cart->setQty($decoded["qty"][$i]);
		$this->save($cart);
	  }
  }
  //-------- update Quantity of Cart Ajax for item already in cart ----------------------------------------////////////
  public function updateCartAjax($id,$qty) {
	  $cart=$this->findCartById($id);
	  $cart->setQty($qty);
	  $this->save($cart);
	  return true;
  }
#------------------------------Get Cart by User--------------------------------#
  public function getCart($user){
	$cart_array=array();
	foreach($user->getCart() as $ci){
	  	$cart_array['price'][]=$ci->getProductItem()->getPrice();
	  	$cart_array['total'][]=$ci->getProductItem()->getPrice()*$ci->getQty();
	}
	if(count($cart_array) == 0)
	{
	  $grand_total=0;
	}else{
	  $grand_total = array_sum($cart_array["total"]);
	}
	return $grand_total;
  }
#------------------------------Get Formatted Cart Data by User--------------------------------#
  public function getFormattedCart($user){
	$cart_array=array();
	foreach($user->getCart() as $ci){
	  $cart_array['price'][]=$ci->getProductItem()->getPrice();
	  $cart_array['total'][]=$ci->getProductItem()->getPrice()*$ci->getQty();
	  $cart_array['qty'][]=$ci->getQty();
	  $cart_array['item_id'][]=$ci->getProductItem()->getId();
	}
	return $cart_array;
  }

	//-------------------------
	public function save($cart) {
	  $class = $this->class;
	  $cart->setDateTime(new \DateTime('now'));
	  $this->em->persist($cart);
	  $this->em->flush();
	  return $cart;
	}
  //-------------------------Create New Brand--------------------------------------------

    public function createNew() {
        $class = $this->class;
        $cart = new $class();
        return $cart;
    }



//------------------Delete Brand------------------------------------------------------------------------

    public function delete($id) {

        $entity = $this->repo->find($id);
        //$entity_name = $entity->getName();
        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();
            return array('cart' => $entity,
                'message' => 'The Item has been Removed!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {

            return array('cart' => $entity,
                'message' => 'Cart not found!',
                'message_type' => 'warning',
                'success' => false,
            );
        }
    }

//----------------------Find Brand By ID----------------------------------------------------------------

    public function find($id) {
        return $this->repo->find($id);
    }
   #--------------------Find All Brands---------------------------------------------------------------------------------
  public function findAll(){
  return $this->repo->findAll();      
    }
//----------------------Find Brand By name----------------------------------------------------------------
    public function findOneByName($name) {
        return $this->repo->findOneByName($name);
    }

    
//----------------------Remove Brand----------------------------------------------------------------    
    public function removeBrand() {
        return $this->repo->removeBrand();
    }

    //--------------------------------------------------------------------------------------------------

    public function getListWithPagination($page_number, $sort) {
        $yaml = new Parser();
        $pagination_constants = $yaml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
        $limit = $pagination_constants["constants"]["pagination"]["limit"];

        $entity = $this->repo->listAllBrand($page_number, $limit, $sort);
        $rec_count = count($this->repo->countAllRecord());
        $cur_page = $page_number;

        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }
        return array('brands' => $entity,
            'rec_count' => $rec_count,
            'no_of_pagination' => $no_of_paginations,
            'limit' => $cur_page,
            'per_page_limit' => $limit,
            'sort'=>$sort,
        );
    }

    public function getRecordsCountWithCurrentBrandLimit($brand_id){
    
    return $this->repo->getRecordsCountWithCurrentBrandLimit($brand_id);
}


public function getRetailerBrandById($id)
{
    return $this->repo->getRetailerBrandById($id);
}
    
 public function getBrnadList()
 {
     return $this->repo->getBrnadList();
 }
    
 public function getBrnadArray()
 {
     return $this->repo->getBrnadArray();     
 }
  
 public function getBrandArray()
 {
     $brands = array();
     $brand_array= $this->repo->getBrnadArray();  
     foreach($brand_array as $key=>$brand)
     {
         $brands[$brand['id']] = $brand['name'];
        //$brands[$brand->getId()] = $brand->getName();
     }
     return $brands;
 }
 
    

    #----------------Get Brand and Id ----------------------------------------#
    public function getBrandNameId(){
     return $this->repo->getBrandNameId();   
    }
    
#-------------Get Brand For Male Top Size Chart--------------------------------#
    public function getTopBrandForMaleBaseOnSizeChart(){
        return $this->repo->getTopBrandForMaleBaseOnSizeChart();
        
    }
    
#-------------Get Brand For Male Bottom Size Chart--------------------------------#
    public function getBottomBrandForMaleBaseOnSizeChart(){
        return $this->repo->getBottomBrandForMaleBaseOnSizeChart();
        
    }

    
#-------------Get Brand For FeMale Top Size Chart--------------------------------#
    public function getTopBrandForFemaleBaseOnSizeChart(){
        return $this->repo->getTopBrandForFemaleBaseOnSizeChart();
        
    }

#-------------Get Brand For Female Bottomop Size Chart--------------------------------#
    public function getBottomBrandForFemaleBaseOnSizeChart(){
        return $this->repo->getBottomBrandForFemaleBaseOnSizeChart();
    }

#-------------Get Brand For Female Bottomop Size Chart--------------------------------#
    public function getDressBrandForFemaleBaseOnSizeChart(){
        return $this->repo->getDressBrandForFemaleBaseOnSizeChart();
    }
  
 #---------Get Brand id Base On Brand Name for Web SErvice---------------------#
   public function getBrandIdBaseOnBrandName($brandName){
       return $this->repo->getBrandIdBaseOnBrandName($brandName);
   } 
   
   #------Get All Retailer  and Brand List ---------------------------------------#
 public function super_unique($array)
{
  $result = array_map("unserialize", array_unique(array_map("serialize", $array)));

  foreach ($result as $key => $value)
  {
    if ( is_array($value) )
    {
      $result[$key] = $this->super_unique($value);
    }
  }

  return $result;
}
public function getBrandRetailerList($date_fromat){
    $data=$this->repo->getBrandRetailerList($date_fromat);
    
    foreach($data as $key){
   // if($key['title']!=null){
     // $arr[]=array('retId'=>$key['ret_id'],'name'=>$key['title'],'image'=>$key['ret_image']);
    //}
    if($key['brand_id']!=null){
        if($key['ret_id']==null){
            $key['ret_id']=0;
        }
      $arr2[]=array('brandId'=>$key['brand_id'],'name'=>$key['brand_name'],'image'=>$key['brand_image'],'retId'=>$key['ret_id']);
    }
   
  }
    $retList=$this->container->get('admin.helper.retailer')->reatailerListService();
    //$ret['retailer']=$this->super_unique($arr);
    $ret['retailer']=$retList;
   $ret['brand']=$arr2;
    return $ret;
}
#------------------Get Retailer base on Brand id -----------------------------#
#---------Get Brand id Base On Brand Name for Web SErvice---------------------#
   public function getRetailerTitleByBrandId($brandId){
      $ret=$this->repo->getRetailerTitleByBrandId($brandId);
      $rets=array();
      //return print_r($ret);
      foreach($ret as $key=>$value){
           foreach($value as $re){
               $rets[]=$re;
           }
       }
    
       return $rets;
       
        
   } 
#----------------------------------------------------
     public function getBrandSpecificationArray($id){
         $brand=$this->find($id);
         $bSpecs = $brand->getBrandSpecification();
         if ($bSpecs!=null){
            return $bSpecs->getSpecsArray();
         }else{
             return $this->container->get('admin.helper.size')->getDefaultArray();
         }
   }
    
   
 //Private Methods    
//------Validate to check brand name exit or not for create new-----------------------------------------
    private function validateForCreate($name) {
        if (count($this->findOneByName($name)) > 0) {
            return array('message' => 'Brand Name already exists!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }
        return;
    }

//------Validate to check brand name exit or not for update-----------------------------------------
    private function validateForUpdate($entity) {
        $brand = $this->findOneByName($entity->getName());
        if ($brand && $brand->getId() != $entity->getId()) {
            return array('message' => 'Brand Name already exists!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }
        return;
    }
     
   
}