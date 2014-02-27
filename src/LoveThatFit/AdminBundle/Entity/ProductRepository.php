<?php

namespace LoveThatFit\AdminBundle\Entity;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityRepository;

/**
 * ProductRepository
 */
class ProductRepository extends EntityRepository {

    public function findAllProduct($page_number = 0, $limit = 0, $sort = 'id') {

        if ($page_number <= 0 || $limit <= 0) {
            $query = $this->getEntityManager()
                    ->createQuery('SELECT p FROM LoveThatFitAdminBundle:Product p ORDER BY p.' . $sort . ' ASC');
        } else {
            $query = $this->getEntityManager()
                    ->createQuery('SELECT p FROM LoveThatFitAdminBundle:Product p ORDER BY p.' . $sort . ' ASC')
                    ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        }
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    /* ------------------------------------------------------------------ */

    public function countAllRecord() {

        $total_record = $this->getEntityManager()
                ->createQuery('SELECT p FROM LoveThatFitAdminBundle:Product p');
        try {
            return $total_record->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
//-------------------------------------------------------------------------------------
    public function listAllProduct($page_number = 0, $limit = 0, $sort = 'id') {


        if ($page_number <= 0 || $limit <= 0) {
            $query = $this->getEntityManager()
                    ->createQuery('SELECT p FROM LoveThatFitAdminBundle:Product p   ORDER BY p.' . $sort . ' ASC');
        } else {
            $query = $this->getEntityManager()
                    ->createQuery('SELECT p FROM LoveThatFitAdminBundle:Product p  ORDER BY p.' . $sort . ' ASC')
                    ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        }
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return "null";
        }
    }
    
    /* --------------------------------------------------------- */

    public function findByGender($gender, $page_number = 0, $limit = 0) {
        if ($page_number <= 0 || $limit <= 0) {

            $query = $this->getEntityManager()
                            ->createQuery("
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            WHERE p.gender = :gender
            AND p.disabled=0"
                            )->setParameter('gender', $gender);
        } else {
            $query = $this->getEntityManager()
                    ->createQuery("
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            WHERE p.gender = :gender
            AND p.disabled=0 AND p.displayProductColor!=''"
                    )->setParameter('gender', $gender)
                    ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        }

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    //-----------------------------------------------------------------

    public function findByGenderLatest($gender, $page_number = 0, $limit = 0) {
        if ($page_number <= 0 || $limit <= 0) {

            $query = $this->getEntityManager()
                            ->createQuery("
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            WHERE p.gender = :gender AND p.disabled=0 AND p.displayProductColor!=''
            ORDER BY p.created_at DESC"
                            )->setParameter('gender', $gender);
        } else {
            $query = $this->getEntityManager()
                    ->createQuery("
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            WHERE p.gender = :gender AND p.disabled=0 AND p.displayProductColor!=''
            ORDER BY p.created_at DESC"
                    )->setParameter('gender', $gender)
                    ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        }

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    #_-----------------------------------------------------------------
    public function findByGenderRandom($gender, $limit) {
        
        $sql = "SELECT * FROM product p WHERE p.gender = '".$gender."' AND p.disabled=0 AND p.display_Product_Color_Id IS NOT NULL ORDER BY RAND() LIMIT ".$limit;
        
        $rsm = new ResultSetMapping;
        $rsm->addEntityResult('LoveThatFit\AdminBundle\Entity\Product', 'p');
        $rsm->addFieldResult('p', 'id', 'id');
        
        $query = $this->getEntityManager()
                    ->createNativeQuery($sql,$rsm)
                    ->setParameter('gender', $gender);
        
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

//-----------------------------------------------------------------

    public function findByGenderBrand($gender, $brand_id, $page_number = 0, $limit = 0) {
        if ($page_number <= 0 || $limit <= 0) {
            $query = $this->getEntityManager()
                            ->createQuery("
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            JOIN p.brand b
            WHERE b.id = :id
            AND p.gender = :gender AND p.disabled=0 AND p.disabled=0 AND p.displayProductColor!=''"
                            )->setParameters(array('id' => $brand_id, 'gender' => $gender));
        } else {
            $query = $this->getEntityManager()
                    ->createQuery("
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            JOIN p.brand b
            WHERE b.id = :id
            AND p.gender = :gender AND p.disabled=0 AND p.disabled=0 AND p.displayProductColor!=''"
                    )->setParameters(array('id' => $brand_id, 'gender' => $gender))
                    ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        }


        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

//-----------------------------------------------------------------

    public function findByGenderClothingType($gender, $clothing_type_id, $page_number = 0, $limit = 0) {
        if ($page_number <= 0 || $limit <= 0) {

            $query = $this->getEntityManager()
                            ->createQuery("
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            JOIN p.clothing_type ct
            WHERE ct.id = :clothing_type_id
            AND p.gender = :gender AND p.disabled=0 AND p.disabled=0 AND p.displayProductColor!=''"
                            )->setParameters(array('clothing_type_id' => $clothing_type_id, 'gender' => $gender));
        } else {
            $query = $this->getEntityManager()
                    ->createQuery("
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            JOIN p.clothing_type ct
            WHERE ct.id = :clothing_type_id
            AND p.gender = :gender AND p.disabled=0 AND p.disabled=0 AND p.displayProductColor!=''"
                    )->setParameters(array('clothing_type_id' => $clothing_type_id, 'gender' => $gender))
                    ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        }

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

//-----------------------------------------------------------------

    public function findSampleClothingTypeGender($gender) {
        $query = $this->getEntityManager()
                        ->createQuery("
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            JOIN p.clothing_type ct
            WHERE p.gender = :gender AND p.disabled=0 AND p.disabled=0 AND p.displayProductColor!=''"
                        )->setParameter('gender', $gender);

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

//-----------------------------------------------------------------
    public function productList() {

        $query = $this->getEntityManager()
                ->createQuery("
      SELECT p.id,p.name,p.adjustment,p.disabled,ct.name as clothing_type , b.name as brand_name,
      b.id as brand_id,ct.id as clothing_type_id, pc.image as product_image
      FROM LoveThatFitAdminBundle:Product p 
      JOIN p.clothing_type ct
      JOIN p.brand b 
      JOIN p.product_colors pc
      where p.disabled=0 AND p.disabled=0 AND p.displayProductColor!=''
      ");

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    //-------------------------------------------------------------------------
    public function productListByBrand($brand_id, $gender) {
        $query = $this->getEntityManager()->createQuery("
      SELECT p.id,p.name,p.adjustment,p.disabled,ct.name as clothing_type ,p.gender,
      b.name as brand_name,b.id as brand_id,ct.id as clothing_type_id, pc.image as product_image
      FROM LoveThatFitAdminBundle:Product p 
      JOIN p.clothing_type ct
      JOIN p.brand b 
      JOIN p.product_colors pc
      WHERE
      p.gender = :gender
      AND b.id = :brand_id AND p.disabled=0 AND p.disabled=0 AND p.displayProductColor!=''"
                        )->setParameters(array('gender' => $gender, 'brand_id' => $brand_id));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
//-------------------------------------------------------------------------------------
    public function productListByClothingType($clothing_type_id, $gender) {
        $query = $this->getEntityManager()
                        ->createQuery("
      SELECT p.id,p.name,p.adjustment,p.disabled,ct.name as clothing_type ,p.gender,
      b.name as brand_name,b.id as brand_id,ct.id as clothing_type_id, pc.image as product_image
      FROM LoveThatFitAdminBundle:Product p 
      JOIN p.clothing_type ct
      JOIN p.brand b 
      JOIN p.product_colors pc
      WHERE
      p.gender = :gender
      AND ct.id = :clothing_type_id AND p.disabled=0 AND p.disabled=0 AND p.displayProductColor!=''"
                        )->setParameters(array('gender' => $gender, 'clothing_type_id' => $clothing_type_id));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
//-------------------------------------------------------------------------------------
    public function productListByBrandClothingType($brand_id, $clothing_type_id, $gender) {
        $query = $this->getEntityManager()
                        ->createQuery("
      SELECT p.id,p.name,p.adjustment,p.disabled=0,ct.name as clothing_type ,p.gender,
      b.name as brand_name,b.id as brand_id,ct.id as clothing_type_id, pc.image as product_image
      FROM LoveThatFitAdminBundle:Product p 
      JOIN p.clothing_type ct
      JOIN p.brand b 
      JOIN p.product_colors pc
      WHERE
      p.gender = :gender
      AND ct.id = :clothing_type_id
      AND b.id = :brand_id AND p.disabled=0 AND p.disabled=0 AND p.displayProductColor!=''"
                        )->setParameters(array('gender' => $gender, 'clothing_type_id' => $clothing_type_id, 'brand_id' => $brand_id));

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

//-------------------------------------------------------------------------------------

 public function findProductItemByUser($user_id , $page_number=0 , $limit=0) {
            $query = $this->getEntityManager()
                        ->createQuery("
     SELECT p,pi,ps,pc FROM LoveThatFitAdminBundle:Product p
     JOIN p.product_items pi
     JOIN pi.product_color pc
     JOIN pi.product_size ps
     JOIN pi.users u
     WHERE
     u.id = :id"  )->setParameters(array('id' => $user_id)) ;
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    

    
//-------------------------------------------------------------------------------------    
    public function countMyCloset($user_id)
    {
        $total_record= $this->getEntityManager()
	   ->createQuery("SELECT p FROM LoveThatFitAdminBundle:ProductItem p
      JOIN p.users u
      WHERE
      u.id = :id"      
                        )->setParameters(array('id' => $user_id));
	  try 
	    {
		 return $total_record->getResult();
		}
		catch (\Doctrine\ORM\NoResultException $e) 
		 {
		   return null;
		 }						
	  } 
    
//-------------------------------------------------------------------------------------    
    public function findPrductByGender($gender)
    {
     $query = $this->getEntityManager()
        ->createQuery("SELECT p FROM LoveThatFitAdminBundle:Product p      
        WHERE        
        p.gender=:gender"
                        )
             ->setParameter('gender',$gender);
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
//-------------------------------------------------------------------------------------    
    public function findPrductByType($target)
    {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT p FROM LoveThatFitAdminBundle:Product p
     JOIN p.clothing_type ct     
     WHERE
     ct.target = :target"  )->setParameters(array('target' => $target)) ;
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
//-------------------------------------------------------------------------------------    
    public function findPrductByBrand()
    {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT count(p.brand) as brand,b.name FROM LoveThatFitAdminBundle:Product p
     JOIN p.brand b group by b.name    
     ");
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
//-------------------------------------------------------------------------------------    
    public function findListAllProduct() {
     $query = $this->getEntityManager()
                    ->createQuery('SELECT p FROM LoveThatFitAdminBundle:Product p');
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
//-------------------------------------------------------------------------------------    
    public function findProductByItemId($product_item_id) {
     $query = $this->getEntityManager()
                        ->createQuery("
     SELECT p FROM LoveThatFitAdminBundle:Product p
     JOIN p.product_items pi     
     WHERE
     p.id=:product_id"  )->setParameters(array('product_id' => $product_item_id)) ;
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    
 #--------------------------------Web Service for Product list ------------------------#
    
         public function newproductDetailDBStructureWebService($gender) {

        return $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select(' p.id as productId,pc.title as colorTitle,pc.pattern as pattern, pc.image as colorImage,ps.body_type as bodyType,ps.id as sizeId,
ps.title as sizeTitle, pi.id as itemId, pi.image as itemImage')
                        ->from('LoveThatFitAdminBundle:Product', 'p')
                        ->innerJoin('p.product_items', 'pi')
                        ->innerJoin('p.product_colors', 'pc')
                        ->innerJoin('p.product_sizes', 'ps')
                       
                         ->where('p.gender=:gender')
                        ->setParameters(array('gender' => $gender))
                        ->getQuery()
                        ->getResult();
    }


    
    public function newproductListingWebService($gender) {

        return $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select('p.id,p.name,p.description,ct.target as target ,pc.image as product_image,b.name as brand_name,b.id as brandId')
                        ->from('LoveThatFitAdminBundle:Product', 'p')
                        ->innerJoin('p.product_colors', 'pc')
                        ->innerJoin('p.clothing_type', 'ct')
                        ->innerJoin('p.brand', 'b')
                        ->where('p.gender=:gender')
                        ->groupBy('p.id')
                        ->setParameters(array('gender' => $gender))
                        ->getQuery()
                        ->getResult();
    }

 public function findProductByBrandWebService($id, $gender) {

        return $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select('p.id,p.name,p.description,ct.target as target ,pc.image as product_image')
                        ->from('LoveThatFitAdminBundle:Product', 'p')
                        ->innerJoin('p.product_colors', 'pc')
                        ->innerJoin('p.clothing_type', 'ct')
                        ->innerJoin('p.brand', 'b')
                        ->where('p.gender=:gender')
                        ->andWhere('b.id=:brand_id')
                        ->groupBy('p.id')
                        ->setParameters(array('gender' => $gender, 'brand_id' => $id))
                        ->getQuery()
                        ->getResult();
    }

#--------------------------------------------------------------------------------------------#

    public function findProductByClothingTypeWebService($id, $gender) {

        return $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select('p.id,p.name,p.description,ct.target as target ,pc.image as product_image')
                        ->from('LoveThatFitAdminBundle:Product', 'p')
                        ->innerJoin('p.product_colors', 'pc')
                        ->innerJoin('p.clothing_type', 'ct')
                        ->where('p.gender=:gender')
                        ->andWhere('ct.id=:clothing_type_id')
                        ->groupBy('p.id')
                        ->setParameters(array('gender' => $gender, 'clothing_type_id' => $id))
                        ->getQuery()
                        ->getResult();
    }
#---------------------------------------------------------------------------------------------------------#
    public function findLattestProductWebService($gender) {
         return $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select('p.id,p.name,p.description,ct.target as target ,pc.image as product_image')
                        ->from('LoveThatFitAdminBundle:Product', 'p')
                        ->innerJoin('p.product_colors', 'pc')
                        ->innerJoin('p.clothing_type', 'ct')
                        ->where('p.gender=:gender')
                        ->andWhere('p.disabled=0')
                        ->andWhere("p.displayProductColor!=''")
                        ->groupBy('p.id')
                        ->orderBy('p.updated_at','desc')
                        ->setMaxResults(2)
                        ->setParameter('gender', $gender)
                        ->getQuery()
                        ->getResult();
         
    }
 #--------------------------------------------------------------------------------------------------------------#
  public function findhottestProductWebService($gender) {
         return $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select('p.id,p.name,p.description,ct.target as target ,pc.image as product_image')
                        ->from('LoveThatFitAdminBundle:Product', 'p')
                        ->innerJoin('p.user_item_try_history','uih')
                        ->innerJoin('p.product_colors', 'pc')
                        ->innerJoin('p.clothing_type', 'ct')
                        ->where('p.gender=:gender')
                        ->andWhere('p.disabled=0')
                        ->andWhere("p.displayProductColor!=''")
                        ->groupBy('uih.product')
                        ->orderBy('uih.count','DESC')
                        ->setParameter('gender', $gender)
                        ->getQuery()
                        ->getResult();                              

    }   
#-----------------------------------Product Detail---------------------------------------#
     public function productDetail($product_id) {
        $query = $this->getEntityManager()
                        ->createQuery("SELECT
      p.id as product_id,p.name as product_name,p.description as product_description,p.gender as product_gender,
     ct.name as clothing_type ,ct.target as clothing_target ,
      b.name as brand_name,b.id as brand_id
      FROM LoveThatFitAdminBundle:Product p 
      JOIN p.clothing_type ct
      JOIN p.brand b 
      WHERE  
    p.disabled=0 AND  
    p.id=:id AND p.disabled=0 AND p.displayProductColor!=''")->setParameter('id', $product_id);
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;

        }
    }
 
  #--------------------------------------------------------------------------------------------#  
    public function productDetails($product_id) {
        $query = $this->getEntityManager()
                        ->createQuery("
      SELECT p.id,p.name,p.adjustment,p.description,p.gender,
      ct.name as clothing_type ,ct.target as clothing_target ,
      b.name as brand_name,b.id as brand_id,b.image as brand_image,pc.title as color_title,
      pc.pattern as color_pattern, pc.image as color_image,
      ps.title as size_title,ps.inseam as size_inseam,ps.outseam as size_outseam,ps.hip as size_hip,ps.bust as size_bust,
      ps.back as size_back,ps.hem as size_hem,
      ps.length as size_lenght,ps.waist as size_waist,
      ct.id as clothing_type_id, pc.image as product_image,
      pi.id as porduct_item_id, ps.id as product_size_id , pc.id as prodcut_color_id
      FROM LoveThatFitAdminBundle:Product p 
      JOIN p.clothing_type ct
      JOIN p.brand b 
      JOIN p.product_items pi
      JOIN pi.product_color pc
      JOIN pi.product_size ps
      WHERE  
    p.disabled=0 AND  
    p.id=:id AND p.disabled=0 AND p.displayProductColor!=''")->setParameter('id', $product_id);
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;

        }
    }
#-------------------User Favourite List Web Service-----------------------------#     
    public function favouriteByUser($user_id) {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT p.id as product_id,pi.id as id,p.name as name,ct.target as target,b.name as description,ps.title,pc.image as product_image,pi.image as fitting_room_image FROM LoveThatFitAdminBundle:Product p
     JOIN p.product_items pi
     JOIN p.clothing_type ct
     JOIN pi.product_color pc
     JOIN pi.product_size ps
     JOIN pi.users u
     JOIN p.brand b
     WHERE
     u.id = :id")->setParameters(array('id' => $user_id));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

//-------------------------------------------------------------------------------------    
     public function tryOnHistoryWebService($user_id) {
         
  return $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select('pi.id as product_id ,p.name as product_name,b.name as brand_name,ps.title as size, pc.title as color ,pi.image as item_image')
                        ->from('LoveThatFitAdminBundle:ProductItem', 'pi')
                        ->innerJoin('pi.product','p')
                        ->innerJoin('p.brand', 'b')
                        ->innerJoin('pi.product_color','pc')
                        ->innerJoin('pi.product_size','ps')
                        ->innerJoin('pi.user_item_try_history','uih')
                        ->where('uih.user = :id')
                        ->orderBy('uih.count','DESC')
                        ->setMaxResults(20)
                        ->setParameter('id', $user_id)
                        ->getQuery()
                        ->getResult(); 
    }
#---------------------------------End of Web Service----------------------------------#   
     public function findProductByTitle($name) {
        $record = $this->getEntityManager()
                        ->createQuery("SELECT p FROM LoveThatFitAdminBundle:Product p    
                                WHERE p.name = :name")
                        ->setParameters(array('name' => ucwords($name)));
        try {
            return $record->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
//-------------------------------------------------------------------------------------    
    public function findMostTriedOnByGender($gender, $page_number = 0, $limit = 0)
    {        
        $query = $this->getEntityManager()
        ->createQuery("SELECT p.id as id,count(ut.product) as countproducts
        FROM LoveThatFitAdminBundle:Product p
        JOIN p.user_item_try_history ut
        WHERE p.gender = :gender 
        AND 
        p.disabled=0 
        AND 
        p.displayProductColor!='' 
        GROUP BY p.id ORDER BY countproducts DESC
            ")->setParameter('gender', $gender);        
        $ids = $query->getResult();    
        if($ids){
        if ($page_number <= 0 || $limit <= 0) {
            $query = $this->getEntityManager()
                            ->createQuery("
            SELECT p,uih FROM LoveThatFitAdminBundle:Product p 
            JOIN p.user_item_try_history uih
            WHERE p.gender = :gender AND 
            p.disabled=0 AND 
            p.displayProductColor!='' AND
            p.id in (:ids)
            ")->setParameters(array('gender'=> $gender, 'ids' => $ids));
        } else {
            $query = $this->getEntityManager()
                    ->createQuery("
            SELECT p,uih FROM LoveThatFitAdminBundle:Product p 
            JOIN p.user_item_try_history uih
            WHERE p.gender = :gender  AND 
            p.disabled=0 AND 
            p.displayProductColor!='' AND
            p.id in (:ids)
            ")->setParameters(array('gender'=> $gender, 'ids' =>$ids))
                    ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        }
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
        }else{
               return null;
        }
    }
    //-------------------------------------------------------------------------------------    
    public function findMostFavoriteByGender($gender, $page_number = 0, $limit = 0)
    {        
        $query = $this->getEntityManager()
        ->createQuery("SELECT p.id as id, count(p.id) as likedproducts
        FROM LoveThatFitAdminBundle:Product p
        JOIN p.product_items pi
        JOIN pi.users upi
        WHERE p.gender = :gender 
        AND 
        p.disabled=0 
        
        GROUP BY p.id ORDER BY likedproducts DESC
            ")->setParameter('gender', $gender);        
        $ids = $query->getResult();    
        
        if($ids){
        if ($page_number <= 0 || $limit <= 0) {
            $query = $this->getEntityManager()
                            ->createQuery("
            SELECT p,uih FROM LoveThatFitAdminBundle:Product p 
            JOIN p.user_item_try_history uih
            WHERE p.gender = :gender AND 
            p.disabled=0 AND 
            p.displayProductColor!='' AND
            p.id in (:ids)
            ")->setParameters(array('gender'=> $gender, 'ids' => $ids));
        } 
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
        }else{
               return null;
        }
    }
    
    
//-------------------------------------------------------------------------------------    
    
    
    public function findRecentlyTriedOnByUser($user_id, $page_number = 0, $limit = 20)
    {
        if ($page_number <= 0 || $limit <= 0) {
            $query = $this->getEntityManager()
                            ->createQuery("
            SELECT p,uih FROM LoveThatFitAdminBundle:Product p 
            JOIN p.user_item_try_history uih
            WHERE p.disabled=0 AND p.displayProductColor!='' AND uih.user=:user_id
            ORDER BY uih.count DESC"
                            )->setParameters(array('user_id' =>$user_id));
        } else {
            $query = $this->getEntityManager()
                    ->createQuery("
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            JOIN p.user_item_try_history uih
            WHERE p.disabled=0 AND p.displayProductColor!='' AND uih.user=:user_id
            ORDER BY uih.count DESC "
                    )->setParameters(array('user_id' =>$user_id))
                    ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        }
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
//-------------------------------------------------------------------------------------    
    public function findOneByName($name) {
        $record = $this->getEntityManager()
                        ->createQuery("SELECT p FROM LoveThatFitAdminBundle:Product p     
                             JOIN p.brand b   
                             WHERE b.name = :name")
                        ->setParameters(array('name' => $name));
        try {
            return $record->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
//-------------------------------------------------------------------------------------    
    public function findByGenderBrandName($gender, $brand, $page_number=0, $limit=0)
    {      if ($page_number <= 0 || $limit <= 0) {
            $query = $this->getEntityManager()
                            ->createQuery("
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            JOIN p.brand b
            WHERE b.name = :name
            AND p.gender = :gender AND p.disabled=0 AND p.displayProductColor!=''"
                            )->setParameters(array('name'=>$brand,'gender' => $gender));
        } else {
            $query = $this->getEntityManager()
                    ->createQuery("
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            JOIN p.brand b
            WHERE b.name = :name
            AND p.gender = :gender AND p.disabled=0 AND p.disabled=0 AND p.displayProductColor!=''"
                    )->setParameters(array('name'=>$brand,'gender' => $gender))
                    ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        }


        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }  
    }    
    
//-------------------------------------------------------------------------------------    
    public function findProductByEllieHM($brand,$gender,$page_number, $limit)
    {      if ($page_number <= 0 || $limit <= 0) {
            $query = $this->getEntityManager()
                            ->createQuery("
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            JOIN p.brand b
            WHERE b.name = :name
            AND p.gender = :gender AND p.disabled=0 AND p.displayProductColor!=''"
                            )->setParameters(array('name'=>$brand,'gender' => $gender));
        } else {
            $query = $this->getEntityManager()
                    ->createQuery("
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            JOIN p.brand b
            WHERE b.name = :name
            AND p.gender = :gender AND p.disabled=0 AND p.disabled=0 AND p.displayProductColor!=''"
                    )->setParameters(array('name'=>$brand,'gender' => $gender))
                    ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        }


        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }  
    }
//-------------------------------------------------------------------------------------    
    
    public function findTryProductHistory($user_id , $page_number , $limit)
    {
              if ($page_number <= 0 || $limit <= 0) {
            $query = $this->getEntityManager()
                    ->createQuery("
            SELECT uih,pi,ps,pc FROM LoveThatFitAdminBundle:ProductItem pi                         
            JOIN pi.product_color pc
            JOIN pi.product_size ps            
            JOIN pi.user_item_try_history uih            
        WHERE uih.user = :id ORDER BY uih.count DESC"  )->setParameters(array('id' => $user_id)) ;                   
        
        }else{
            
             $query = $this->getEntityManager()
                    ->createQuery("
            SELECT uih,pi,ps,pc FROM LoveThatFitAdminBundle:ProductItem pi                         
            JOIN pi.product_color pc
            JOIN pi.product_size ps            
            JOIN pi.user_item_try_history uih            
            WHERE uih.user = :id ORDER BY uih.count DESC"  )->setParameters(array('id' => $user_id)) 
                ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        
        }
    try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }      
    }
//-------------------------------------------------------------------------------------
    public function findDefaultProductByColorId($product_color)
    {
        $query = $this->getEntityManager()
                    ->createQuery('
            SELECT p FROM LoveThatFitAdminBundle:Product p           
            JOIN p.displayProductColor pc
            where p.displayProductColor=:product_color
            ')->setParameters(array('product_color' =>$product_color));       
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Product Listing ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

  /*  public function findByGenderMostLiked($gender,$page_number=0, $limit=0) {
            $query = $this->getEntityManager()
                        ->createQuery("
     SELECT p,pi,ps,pc FROM LoveThatFitAdminBundle:Product p
     JOIN p.product_items pi
     JOIN pi.product_color pc
     JOIN pi.product_size ps
     JOIN pi.users u
     where p.gender=:gender")->setParameters(array('gender' =>$gender));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }*/
#-----------------Image Downloading Functions----------------------------------#    
    public function getProductColorArray($product_id){
        
     $query = $this->getEntityManager()
                        ->createQuery("
            SELECT p.id as product_id ,p.name as product_name,pc.image as product_color_images, pc.pattern as product_pattern_images
            FROM LoveThatFitAdminBundle:ProductColor pc                         
            JOIN pc.product p
            WHERE  p.displayProductColor!='' and p.id = :id ")->setParameters(array('id' => $product_id));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }   
    }
#------------------------------------------------------------------------------#
    public function getRecordsCountWithCurrentProductLimit($product_id){
        
            $query = $this->getEntityManager()
                    ->createQuery("SELECT count(p.id) as id FROM LoveThatFitAdminBundle:Product p  WHERE p.id<=:product_id")
                     ->setParameters(array('product_id' => $product_id));
                     try {
                     return $query->getResult();
                } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
                }
        } 
 #-----------------------------------------------------------------------------#
        #---------Searching Quries-------------------------------#
  public function searchProduct($brand_id,$male,$female,$target,$category_id,$start,$per_page){

      
      
     /* $query= $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select('p.id,p.name,b.name as brand_name,ct.name as clothing_name,p.description,p.gender,ct.target as target,p.disabled,pc.image as product_image')
                        ->from('LoveThatFitAdminBundle:Product', 'p')
                        ->innerJoin('p.product_colors', 'pc')
                        ->innerJoin('p.clothing_type', 'ct')
                        ->innerJoin('p.brand', 'b')
                        ->Where('b.id=:brand_id')
                        ->orWhere('p.gender=:female')
                        ->orWhere('p.gender=:male')
                        ->setParameters(array('brand_id' => $brand_id,'female'=>$female,'male'=>$male))
                                  
                        ->orWhere('ct.name IN(:category_id)')
                        ->orWhere('ct.target IN(:target)')
                        ->groupBy('p.id')
                        ->setParameter('category_id',$category_id)
                        ->setParameter('target',$target)
                        ->setFirstResult($start)
                        ->setMaxResults($per_page)
                        ->getQuery();
             return $query->getResult();            
*/
  
      $str = "SELECT p.id,p.name,b.name as brand_name,ct.name as clothing_name,p.description,p.gender,ct.target as target,p.disabled,pc.image as product_image  FROM LoveThatFitAdminBundle:Product p Join p.product_colors pc Join p.clothing_type ct Join p.brand b";
             
             if($brand_id){
                 $str=$str." WHERE b.id = ". $brand_id;
               
             }
             if($male || $female){
                 $str=$str." AND ";
                if($male && $female){
                    $str=$str."(p.gender= '".$male."' OR p.gender='".$female."') ";
                }elseif($male){
                    $str=$str."p.gender='".$male."'";
                }elseif($female){
                    $str=$str."p.gender='".$female."'";
                }
               
             }
             if($target){
                 $str=$str." AND ct.target IN ('".implode("','", $target)."') ";
             }
             if($category_id){                 
                 $str=$str." AND ct.id IN (".implode(", ", $category_id).") ";                 
             }
                 $str=$str." group by p.id";
      $query = $this->getEntityManager()
                        ->createQuery($str);              
                     return $query->getResult();
              
  }
  
  
#------------Count Search Record---------------------------#
 public function countSearchProduct($brand_id,$male,$female,$target,$category_id){
       
      return $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select('p.id,p.name,b.name as brand_name,ct.name as clothing_name,p.description,p.gender,ct.target as target,p.disabled,pc.image as product_image')
                        ->from('LoveThatFitAdminBundle:Product', 'p')
                        ->innerJoin('p.product_colors', 'pc')
                        ->innerJoin('p.clothing_type', 'ct')
                        ->innerJoin('p.brand', 'b')
                         ->Where('b.id=:brand_id')
                        ->orWhere('p.gender=:female')
                        ->orWhere('p.gender=:male')
                        ->orWhere('ct.name IN(:category_id)')
                        ->orWhere('ct.target IN(:target)')
                        ->groupBy('p.id')
                        ->setParameters(array( 'brand_id' => $brand_id,'female'=>$female,'male'=>$male))
                        ->setParameter('category_id',$category_id)
                        ->setParameter('target',$target)
                        ->getQuery()
                        ->getResult(); 
      
  }
  
#------------------Search Categfory ------------------------------------------#
 public function searchCategory($target){
    
      $query = $this->getEntityManager()
                    ->createQuery("SELECT ct.id as id,ct.name as name, ct.target as target FROM LoveThatFitAdminBundle:ClothingType ct WHERE ct.target IN (:target)")
                     ->setParameter('target',$target['target']);
                     try {
                     return $query->getResult();
                } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
                }
     
 }
 
 public function findTryProfileProductHistory($user_id , $page_number , $limit)
    {
              if ($page_number <= 0 || $limit <= 0) {
            $query = $this->getEntityManager()
                    ->createQuery("
            SELECT uih,pi,ps,pc FROM LoveThatFitAdminBundle:ProductItem pi                         
            JOIN pi.product_color pc
            JOIN pi.product_size ps            
            JOIN pi.user_item_try_history uih            
            WHERE uih.user = :id ORDER BY uih.updated_at DESC"  )->setParameters(array('id' => $user_id)) ;                   
        
        }else{
            
             $query = $this->getEntityManager()
                    ->createQuery("
            SELECT uih,pi,ps,pc FROM LoveThatFitAdminBundle:ProductItem pi                         
            JOIN pi.product_color pc
            JOIN pi.product_size ps            
            JOIN pi.user_item_try_history uih            
            WHERE uih.user = :id ORDER BY uih.updated_at DESC"  )->setParameters(array('id' => $user_id)) 
                ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        
        }
    try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }      
    }
 
 
}
