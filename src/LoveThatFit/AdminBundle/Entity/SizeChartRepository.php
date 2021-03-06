<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * SizeChartRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SizeChartRepository extends EntityRepository {

    public function findAllSizeChart($page_number = 0, $limit = 0, $sort = 'id') {

        if ($page_number <= 0 || $limit <= 0) {
            $query = $this->getEntityManager()
                    ->createQuery('SELECT sc FROM LoveThatFitAdminBundle:SizeChart sc ORDER BY sc.' . $sort . ' ASC');
        } else {
            $query = $this->getEntityManager()
                    ->createQuery('SELECT sc FROM LoveThatFitAdminBundle:SizeChart sc ORDER BY sc.' . $sort . ' ASC')
                    ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        }
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function listAllSizeChart($page_number = 0, $limit = 0, $sort = 'id') {


        if ($page_number <= 0 || $limit <= 0) {
            $query = $this->getEntityManager()
                    ->createQuery('SELECT sc FROM LoveThatFitAdminBundle:SizeChart sc ORDER BY sc.' . $sort . ' ASC');
        } else {
            $query = $this->getEntityManager()
                    ->createQuery('SELECT sc FROM LoveThatFitAdminBundle:SizeChart sc ORDER BY sc.' . $sort . ' ASC')
                    ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        }
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return "null";
        }
    }
    
    
    public function findOneByName($title) {
        $record = $this->getEntityManager()
                        ->createQuery("SELECT sc FROM LoveThatFitAdminBundle:SizeChart sc     
                                WHERE sc.title=:title")
                        ->setParameters(array('title' => $title));
        try {
            return $record->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
 
    //------------------------------------------------------------------------
    public function findMatchingTitleBrandGenderBodyTypeTarget($title, $brand, $gender, $bodytype, $target) {      
    $query = $this->getEntityManager()
                        ->createQuery("SELECT sc FROM LoveThatFitAdminBundle:SizeChart sc
     WHERE
     sc.brand=:brand
     AND sc.title=:title
     AND sc.gender=:gender
     AND sc.target=:target
     AND sc.bodytype=:body_type"
                        )->setParameters(array('brand' => $brand, 'title' =>$title, 'gender' => $gender, 'target' => $target,'body_type'=>$bodytype));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    
    //------------------------------------------------------------------------

    public function getBrandsByTarget($target)
    {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT distinct(b.name) as name,b.id FROM LoveThatFitAdminBundle:SizeChart sc
     JOIN sc.brand b    
     WHERE
        sc.target=:target"
            )->setParameters(array('target'=>$target)) ;     
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
        
    }
    
    
     //-------------------------------For Web Service-----------------------------------------

    public function getBrandList()
    {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT distinct(b.name) as name,b.id FROM LoveThatFitAdminBundle:SizeChart sc
     JOIN sc.brand b    
     "
     ) ;     
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
        
    }
      //------------------------------------------------------------------------

    public function findByBrandGenderTarget($brand_id, $gender, $target)
    {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT distinct(sc.title) as title, sc.id as id FROM LoveThatFitAdminBundle:SizeChart sc
     JOIN sc.brand b    
     WHERE
        b.id=:brand_id AND
        sc.gender=:gender AND
        sc.target=:target"
            )->setParameters(array('brand_id' => $brand_id, 'target' => $target, 'gender' => $gender)) ;     
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
        
    }
    
    
       //------------Created By Suresh For Front End added Body Type ------------------------------------------------------------

    public function findByBrandGenderTargetBodyType($brand_id, $gender, $target,$bodytype)
    {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT distinct(sc.title) as title, sc.id as id FROM LoveThatFitAdminBundle:SizeChart sc
     JOIN sc.brand b    
     WHERE
        b.id=:brand_id AND
        sc.gender=:gender AND
        sc.target=:target AND
        sc.bodytype=:bodytype"
                        )->setParameters(array('brand_id' => $brand_id, 'target' => $target, 'gender' => $gender, 'bodytype' => $bodytype));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    //-------------------------------Web Service-----------------------------------------
        
        #--------------Web Service For Size with target Top -----------#
    
    public function getSizeChartByBrandGenderBodyTypeTopSize($gender,$bodytype,$target_top,$top_size)
    {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT sc.id as size_chart_id,
     sc.neck as top_neck,sc.bust as top_bust,sc.chest as top_chest,sc.waist as top_waist,sc.sleeve as top_sleeve
     
     FROM LoveThatFitAdminBundle:SizeChart sc
     JOIN sc.brand b    
     WHERE
     b.id=sc.brand AND
     sc.target='Top' AND
     sc.disabled='0' AND
     b.name=:brand_name AND
     sc.gender=:gender AND
     sc.bodytype=:bodytype AND
    sc.title=:top_size")->setParameters(array('brand_name' =>$target_top,'gender' => $gender,'bodytype'=>$bodytype,'top_size'=>$top_size)) ;     
     try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }   
    
    
      #--------------Web Service For Size with target Bottom -----------#
    
    public function getSizeChartByBrandGenderBodyTypeBottomSize($gender,$bodytype,$target_bottom,$bottom_size)
    {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT sc.id as size_chart_id,
     sc.waist as bottom_waist,sc.hip as bottom_hip,sc.inseam as bottom_inseam
     
     FROM LoveThatFitAdminBundle:SizeChart sc
     JOIN sc.brand b    
     WHERE
     b.id=sc.brand AND
     sc.target='Bottom' AND
     sc.disabled='0' AND
     b.name=:brand_name AND
     sc.gender=:gender AND
     sc.bodytype=:bodytype AND
     sc.title=:bottom_size")->setParameters(array('brand_name' => $target_bottom,'gender' => $gender,'bodytype'=>$bodytype,'bottom_size'=>$bottom_size)) ;     
     try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }   
    
    
      #--------------Web Service For Size with target Dress -----------#
    
    public function getSizeChartByBrandGenderBodyTypeDressSize($gender,$bodytype,$target_dress,$dress_size)
    {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT
      sc.id as size_chart_id,sc.bust as dress_bust,sc.waist as dress_waist,sc.hip as dress_hip,sc.sleeve as dress_sleeve
     
     FROM LoveThatFitAdminBundle:SizeChart sc
     JOIN sc.brand b    
     WHERE
     b.id=sc.brand AND
     sc.target='Dress' AND
     sc.disabled='0' AND
     b.name=:brand_name AND
     sc.gender=:gender AND
     sc.bodytype=:bodytype AND
    sc.title=:dress_size")->setParameters(array('brand_name' =>$target_dress,'gender' => $gender,'bodytype'=>$bodytype,'dress_size'=>$dress_size)) ;     
     try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }   
    //---------------------------------------------------------------
    //---------------------------------------------------------------
    //---------------------------------------------------------------
    //---------------------------------------------------------------
    
    
    
    //------------------------------------------------------------------------
    public function findBrandByTop($target) {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT distinct(b.name) as brandtop,b.id FROM LoveThatFitAdminBundle:SizeChart sc
     JOIN sc.brand b    
     WHERE
     b.id=sc.brand
     AND sc.target=:target"
                        )->setParameters(array('target' => $target));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    //------------------------------------------------------------------------
    public function findBrandByBottom($target) {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT distinct(b.name) as brandbottom ,b.id FROM LoveThatFitAdminBundle:SizeChart sc
     JOIN sc.brand b    
     WHERE
     b.id=sc.brand
     AND sc.target= :target"
                        )->setParameters(array('target' => $target));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    //------------------------------------------------------------------------
    public function findBrandByDresses($target) {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT distinct(b.name) as branddress,b.id  FROM LoveThatFitAdminBundle:SizeChart sc
     JOIN sc.brand b    
     WHERE
     b.id=sc.brand
     AND sc.target = :target"
                        )->setParameters(array('target' => $target));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    //------------------------------------------------------------------------

    public function findSizeByTop($target) {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT distinct(sc.title),sc.id FROM LoveThatFitAdminBundle:SizeChart sc
     JOIN sc.brand b    
     WHERE
     b.id=sc.brand
     AND sc.target=:target"
                        )->setParameters(array('target' => $target));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    //------------------------------------------------------------------------
    public function findSizeByBottom($target) {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT distinct(sc.title),sc.id FROM LoveThatFitAdminBundle:SizeChart sc
     JOIN sc.brand b    
     WHERE
     b.id=sc.brand
     AND sc.target= :target"
                        )->setParameters(array('target' => $target));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
 

    //------------------------------------------------------------------------
    public function findSizeByDresses($target) {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT distinct(sc.title),sc.id  FROM LoveThatFitAdminBundle:SizeChart sc
     JOIN sc.brand b    
     WHERE
     b.id=sc.brand
     AND sc.target = :target"
                        )->setParameters(array('target' => $target));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    

 #---------Size Chart Id 
 public function findSizeChartTitleById($size_id) {
      
     $query = $this->getEntityManager()
                        ->createQuery("
     SELECT sc.id as id FROM LoveThatFitAdminBundle:SizeChart sc
     WHERE
     sc.id=:target"
                        )->setParameter('target',$size_id);
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
 }
#--------------------Searching Size Chart-------------------------------------#
 /*public function searchSizeChart($brand_id,$male,$female,$bodyType,$target,$start,$per_page){
     
      return $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select('sc.id as size_id,b.name as brand_name,sc.gender,sc.title,sc.target,sc.bodytype,sc.waist,sc.hip,sc.bust,sc.chest,sc.inseam,sc.outseam,sc.neck,sc.sleeve,sc.back,sc.thigh')
                        ->from('LoveThatFitAdminBundle:SizeChart', 'sc')
                        ->innerJoin('sc.brand', 'b')
                        ->Where('b.id=:brand_id')
                        ->andWhere('sc.gender=:female')
                        ->orWhere('sc.gender=:male')
                        ->andWhere('sc.target IN(:target)')
                        ->andWhere('sc.bodytype IN(:bodytype)')
                       ->setParameters(array( 'brand_id' => $brand_id,'female'=>$female,'male'=>$male))
                        ->setParameter('target',$target)
                        ->setParameter('bodytype',$bodyType)
                        ->setFirstResult($start)
                        ->setMaxResults($per_page)
                        ->getQuery()
                        ->getResult(); 
 }*/
 
 
 /*
#-------------Counting Of Size Chart-----------------------------------------#
 public function countSearchSizeChart($brand_id,$male,$female,$bodyType,$target){
     
      return $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select('b.name as brand_name,sc.gender,sc.title,sc.target,sc.bodytype,sc.waist,sc.hip,sc.bust,sc.chest,sc.inseam,sc.outseam,sc.neck,sc.sleeve,sc.back,sc.thigh')
                        ->from('LoveThatFitAdminBundle:SizeChart', 'sc')
                        ->innerJoin('sc.brand', 'b')
                        ->Where('b.id=:brand_id')
                        ->andWhere('sc.gender=:female')
                        ->orWhere('sc.gender=:male')
                        ->andWhere('sc.target IN(:target)')
                        ->andWhere('sc.bodytype IN(:bodytype)')
                       ->setParameters(array( 'brand_id' => $brand_id,'female'=>$female,'male'=>$male))
                        ->setParameter('target',$target)
                        ->setParameter('bodytype',$bodyType)
                        ->getQuery()
                        ->getResult(); 
 }
 */
 
 #-------------Counting Of Size Chart-----------------------------------------#
 public function getIdBaseOnTargetGender($brand_id,$gender,$target,$size_title,$body_type){
     
      return $this->getEntityManager()
                        ->createQueryBuilder()
                        ->select('sc.id')
                        ->from('LoveThatFitAdminBundle:SizeChart', 'sc')
                        ->innerJoin('sc.brand', 'b')
                        ->Where('b.id=:brand_id')
                        ->andWhere('sc.gender=:gender')
                        ->andWhere('sc.target=:target')
                        ->andWhere('sc.title=:title')
                        ->andWhere('sc.bodytype=:bodytype')
                       ->setParameters(array( 'brand_id' => $brand_id,'gender'=>$gender,'title'=>$size_title,'bodytype'=>$body_type))
                        ->setParameter('target',$target)
                        ->getQuery()
                        ->getResult(); 
 }
 
 #-----------Find Size Title and Target Base on Brand Id For Web Services------#
 public function findSizeTitleTarget($gender = null) {

        if ($gender == null) {
            $query = $this->getEntityManager()
                    ->createQuery("
     SELECT sc.id as sizeChartId ,b.name as brandName,sc.title as title,sc.gender as gender,sc.target as target,sc.bodytype as bodyType FROM LoveThatFitAdminBundle:SizeChart sc
     JOIN sc.brand b");
            try {
                return $query->getResult();
            } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
            }
        } else {
            $query = $this->getEntityManager()
                            ->createQuery("
     SELECT sc.id as sizeChartId ,b.name as brandName,sc.title as title,sc.gender as gender,sc.target as target,sc.bodytype as bodyType FROM LoveThatFitAdminBundle:SizeChart sc
     JOIN sc.brand b
     WHERE sc.gender = :gender")->setParameter('gender', $gender);
            try {
                return $query->getResult();
            } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
            }
        }
    }
    
public function findSizeTitleTargetByGender($gender) {

        if ($gender == null) {
            $query = $this->getEntityManager()
                    ->createQuery("
     SELECT sc.id as size_chart_id ,b.name as brand_name,sc.title as title,sc.gender as gender,sc.target as target,sc.bodytype as body_type FROM LoveThatFitAdminBundle:SizeChart sc
     JOIN sc.brand b");
            try {
                return $query->getResult();
            } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
            }
        } else {
            $query = $this->getEntityManager()
                            ->createQuery("
     SELECT sc.id as size_chart_id ,b.name as brand_name,sc.title as title,sc.gender as gender,sc.target as target,sc.bodytype as body_type FROM LoveThatFitAdminBundle:SizeChart sc
     JOIN sc.brand b
     WHERE sc.gender = :gender")->setParameter('gender', $gender);
            try {
                return $query->getResult();
            } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
            }
        }
    }

    public function findSizeChartBrand()
{
   $query = $this->getEntityManager()
                        ->createQuery("
     SELECT distinct(b.name),b.id FROM LoveThatFitAdminBundle:SizeChart sc
     JOIN sc.brand b");
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        } 
}
    
#-----------------------find size chart by Brand----------------------------------------
 public function findSizeChartByBrand($brand) {
        $query = $this->getEntityManager()
                        ->createQuery("
     SELECT sc,b FROM LoveThatFitAdminBundle:SizeChart sc 
     JOIN sc.brand b     
     WHERE
     sc.brand=:brand_id ORDER BY sc.gender,sc.target,sc.bodytype desc")->setParameter('brand_id',$brand);     
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
 #-----------------------------------------------------------------
    //------------------------------------------------------------------------
    public function findOneByMatchingParams($size_title, $brand_name, $gender, $body_type, $target) {      
    $query = $this->getEntityManager()
                        ->createQuery("SELECT sc FROM LoveThatFitAdminBundle:SizeChart sc
                                                                                JOIN sc.brand b 
     WHERE
     b.name=:brand_name
     AND sc.title=:size_title
     AND sc.gender=:gender
     AND sc.target=:target
     AND sc.bodytype=:body_type"
                        )->setParameters(array('brand_name' => $brand_name, 'size_title' =>$size_title, 'gender' => $gender, 'target' => $target,'body_type'=>$body_type));
         
        try {
            return $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
}
