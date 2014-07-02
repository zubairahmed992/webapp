<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ProductItemRepository 
 */
class ProductItemRepository extends EntityRepository
{
    public function findProductItemByUser($user_id , $page_number=0 , $limit=0) {
            $query = $this->getEntityManager()
                        ->createQuery("
     SELECT p,pi FROM LoveThatFitAdminBundle:Product p
      JOIN p.product_items pi
      JOIN pi.users u
      WHERE
      u.id = :id"                         
                        )->setParameters(array('id' => $user_id)) ;
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    
    public function findByColorSize($color_id , $size_id) {
            $query = $this->getEntityManager()
                        ->createQuery("
     SELECT pi FROM LoveThatFitAdminBundle:ProductItem pi
      JOIN pi.product_size ps
      JOIN pi.product_color pc
      WHERE
      pc.id = :color_id AND
      ps.id = :size_id "                         
                        )->setParameters(array('color_id' => $color_id, 'size_id' => $size_id)) ;
        try {
            return $query->getOneOrNullResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    
    public function findProductByItemId($productItem) {
     $query = $this->getEntityManager()
                        ->createQuery("
     SELECT p.id FROM LoveThatFitAdminBundle:Product p
     JOIN p.product_items pi     
     WHERE
     pi.id=:product_id"  )->setParameters(array('product_id' => $productItem)) ;
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
  #-------------------Get all Item base on Product ----------------------------#  
    public function getAllItemBaseProduct($product_id){
     $query = $this->getEntityManager()
                    ->createQuery("SELECT pi,ps,pc FROM LoveThatFitAdminBundle:ProductItem pi
                        JOIN pi.product_size ps
                        JOIN pi.product_color pc
                        JOIN pi.product p
                        WHERE
                        p.id = :product_id ORDER BY ps.body_type ,ps.title "                         
       )->setParameters(array('product_id' => $product_id)) ;
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }  
    }
    
    public function findItemBySizeAndProductAndColor($product_size_id,$product_id){
     $query = $this->getEntityManager()
                    ->createQuery("SELECT pi,ps,p FROM LoveThatFitAdminBundle:ProductItem pi                        
                       JOIN pi.product_size ps                       
                       JOIN pi.product p
                        WHERE
                        pi.product_size = :size_id
                        AND
                        pi.product= :product_id
                      "                         
       )->setParameters(array('size_id' => $product_size_id,'product_id' => $product_id)) ;
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }  
    }
#----------------------------------Find Item By SKU---------------------------#
  public function findItemBySku($sku){
       $query = $this->getEntityManager()
                    ->createQuery("SELECT pi,ps,pc FROM LoveThatFitAdminBundle:ProductItem pi
                        JOIN pi.product_size ps
                        JOIN pi.product_color pc
                        JOIN pi.product p
                        WHERE
                        pi.sku = :sku "                         
       )->setParameters(array('sku' => $sku)) ;
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }  
  }
   
  public function findDetailsByVariants($variants){
       $query = $this->getEntityManager()
                    ->createQuery("SELECT p FROM LoveThatFitAdminBundle:Product p") ;
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }  
  }
  public function _findDetailsByVariants($variants){
       $query = $this->getEntityManager()
                    ->createQuery("SELECT pi,ps,pc,p,b FROM LoveThatFitAdminBundle:ProductItem pi
                        JOIN pi.product_size ps
                        JOIN pi.product_color pc
                        JOIN pi.product p
                        JOIN p.brand b
                        WHERE
                        p.name = :product_name
                        
                        "                         
       )->setParameters(array('product_name' => $variants['product_name'])) ;
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }  
  }
    
}
