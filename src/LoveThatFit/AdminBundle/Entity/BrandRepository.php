<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * BrandRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BrandRepository extends EntityRepository {
    /* -----------------------------------------------------------------
      Written:Suresh
      Description: Find all Brands with limit and sort
      param:limit, page_number,limit,sort
      ------------------------------------------------------------------ */

    public function findAllBrand($page_number = 0, $limit = 0, $sort = 'id') {


        if ($page_number <= 0 || $limit <= 0) {
            $query = $this->getEntityManager()
                    ->createQuery('SELECT b.id,b.name,b.image FROM LoveThatFitAdminBundle:Brand b ORDER BY b.' . $sort . ' ASC');
        } else {
            $query = $this->getEntityManager()
                    ->createQuery('SELECT b.id,b.name,b.image FROM LoveThatFitAdminBundle:Brand b ORDER BY b.' . $sort . ' ASC')
                    ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        }
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return "null";
        }
    }

    /* -----End Of Function----------------- */

    /* -----------------------------------------------------------------
      Written:Suresh
      Description:Count all Records
      param:limit:
     * ------------------------------------------------------------------ */

    public function countAllRecord() {
        $total_record = $this->getEntityManager()
                ->createQuery('SELECT b FROM LoveThatFitAdminBundle:Brand b');
        try {
            return $total_record->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

//duplicate method	 
    public function findBrandBy($name) {
        $total_record = $this->getEntityManager()
                        ->createQuery("SELECT b FROM LoveThatFitAdminBundle:Brand b     
        WHERE
        b.name = :name"
                        )->setParameters(array('name' => $name));
        try {
            return $total_record->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    //--------------------------------------------------------------------------

    public function findOneByName($name) {
        $record = $this->getEntityManager()
                        ->createQuery("SELECT b FROM LoveThatFitAdminBundle:Brand b     
                                WHERE b.name = :name")
                        ->setParameters(array('name' => $name));
        try {
            return $record->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    //---------------------------------------------------------------------------


    public function listAllBrand($page_number = 0, $limit = 0, $sort = 'id') {


        if ($page_number <= 0 || $limit <= 0) {
            $query = $this->getEntityManager()
                    ->createQuery('SELECT b FROM LoveThatFitAdminBundle:Brand b ORDER BY b.' . $sort . ' ASC');
        } else {
            $query = $this->getEntityManager()
                    ->createQuery('SELECT b FROM LoveThatFitAdminBundle:Brand b ORDER BY b.' . $sort . ' ASC')
                    ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        }
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return "null";
        }
    }

    #---------------------------Brand List For Web Service-----------------------------------#

    public function findAllBrandWebService($date_format=Null) {
        if($date_format){
            
                $query = $this->getEntityManager()->createQuery("SELECT b.id as brandId, b.name as name,'brand' AS type,b.image as image ,b.updated_at FROM LoveThatFitAdminBundle:Brand b
                WHERE b.disabled=0  AND b.updated_at>=:date_format ORDER BY name asc")
                ->setParameters(array('date_format' => $date_format)) ;
                try {
                    return $query->getResult();
                } catch (\Doctrine\ORM\NoResultException $e) {
                    return null;
                }
        }
        else{
            $query = $this->getEntityManager()->createQuery("SELECT b.id as brandId, b.name as name,'brand' AS type,b.image as image FROM LoveThatFitAdminBundle:Brand b
            WHERE b.disabled=0 ORDER BY name asc");
                try {
                    return $query->getResult();
                } catch (\Doctrine\ORM\NoResultException $e) {
                    return null;
                }
        }
    }
    
    
   public function findBrandByProduct($product)
  {
      $query = $this->getEntityManager()
                        ->createQuery("
     SELECT b FROM LoveThatFitAdminBundle:Brand b     
     WHERE
     b.id=:id     
    "  )->setParameters(array('id' => $product)) ;
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
  }
  
  public function getRecordsCountWithCurrentBrandLimit($brand_id){
        
            $query = $this->getEntityManager()
                    ->createQuery("SELECT count(b.id) as id FROM LoveThatFitAdminBundle:Brand b WHERE b.id <=:brand_id")
                   ->setParameters(array('brand_id' => $brand_id));
                     try {
                     return $query->getResult();
                } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
                }
        } 
        
 public function getRetailerBrandById($id)
 {
    $query = $this->getEntityManager()
                    ->createQuery("SELECT r,b FROM LoveThatFitAdminBundle:Brand b
                    JOIN b.retailers r   
                    WHERE r.id =:retailer")
                   ->setParameters(array('retailer' => $id));
                     try {
                     return $query->getResult();
                } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
                } 
 }
  
 public function getBrnadList()
 {
     $query = $this->getEntityManager()
                    ->createQuery('SELECT b FROM LoveThatFitAdminBundle:Brand b');
  try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return "null";
        }   
 }
  public function getBrnadArray()
 {
     $query = $this->getEntityManager()
                    ->createQuery('SELECT b.id as id, b.name as name FROM LoveThatFitAdminBundle:Brand b');
  try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return "null";
        }   
 }
 
#Get Brand Base on Reailer 
 public function getBrandBaseRetailer($id){
     $query = $this->getEntityManager()
                    ->createQuery("SELECT b.id as id ,b.name as name FROM LoveThatFitAdminBundle:Brand b
                    JOIN b.retailer_brand rb   
                    WHERE rb.retailer_id =:retailer")
                   ->setParameters(array('retailer' => $id));
                     try {
                     return $query->getResult();
                } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
                } 
     
 }
 
 #--------------------------Get Brand name and Id-------------------------------#
 public function getBrandNameId(){
     $query = $this->getEntityManager()
                    ->createQuery("SELECT b.id as id ,b.name as name FROM LoveThatFitAdminBundle:Brand b");
                     try {
                     return $query->getResult();
                } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
                } 
     
 }

 #-- --------------Get Top Brand From the Size Chart For Male ------------------#
 public function getTopBrandForMaleBaseOnSizeChart(){     
     
      $query = $this->getEntityManager()
                        ->createQuery("SELECT b FROM LoveThatFitAdminBundle:Brand b
                            JOIN b.sizechart sc
                            WHERE
                            sc.gender='M'
                            AND sc.target='Top'
                            GROUP BY b.id");
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
 }
 #-- --------------Get Bottom Brand From the Size Chart For Male ------------------#
 public function getBottomBrandForMaleBaseOnSizeChart(){     
     $query = $this->getEntityManager()
                        ->createQuery("SELECT b FROM LoveThatFitAdminBundle:Brand b
                            JOIN b.sizechart sc
                            WHERE
                            sc.gender='M'
                            AND sc.target='Bottom'
                            GROUP BY b.id");
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
 }
 
 #------------------Get Top Brand From the Size Chart For Female ------------------#
 public function getTopBrandForFemaleBaseOnSizeChart(){     
     
      $query = $this->getEntityManager()
                        ->createQuery("SELECT b FROM LoveThatFitAdminBundle:Brand b
                            JOIN b.sizechart sc
                            WHERE
                            sc.gender='F'
                            AND sc.target='Top'
                            GROUP BY b.id");
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
 }
 
 #------------------Get Bottom Brand From the Size Chart For Female ------------------#
 public function getBottomBrandForFemaleBaseOnSizeChart(){     
     
      $query = $this->getEntityManager()
                        ->createQuery("SELECT b FROM LoveThatFitAdminBundle:Brand b
                            JOIN b.sizechart sc
                            WHERE
                            sc.gender='F'
                            AND sc.target='Bottom'
                            GROUP BY b.id");
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
 }
 
 #------------------Get Bottom Brand From the Size Chart For Female ------------------#
 public function getDressBrandForFemaleBaseOnSizeChart(){     
     
      $query = $this->getEntityManager()
                        ->createQuery("SELECT b FROM LoveThatFitAdminBundle:Brand b
                            JOIN b.sizechart sc
                            WHERE
                            sc.gender='F'
                            AND sc.target='dress'
                            GROUP BY b.id");
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
 }
 
 #-- Get Brand Id  base on Brand Name for web service step 2----------------#
 public function getBrandIdBaseOnBrandName($brandName){
      $query = $this->getEntityManager()
                    ->createQuery("SELECT b.id as brand_id  FROM LoveThatFitAdminBundle:Brand b
                        WHERE b.name =:brandName")
                   ->setParameters(array('brandName' => $brandName));

                     try {
                     return $query->getResult();
                } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
                } 
     
 }
 
 #---------------------------Get all retailer brand list for web service---------#
   public function getBrandRetailerList(){
      $query = $this->getEntityManager()
               ->createQuery("
 SELECT b.id as brand_id,b.name as brand_name,r.id as ret_id,r.title as title
 FROM LoveThatFitAdminBundle:Brand b
 LEFT  JOIN b.retailers r");
 try {
  return $query->getResult();
 } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
  } 
}
   
}
