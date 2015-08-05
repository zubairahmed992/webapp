<?php

namespace LoveThatFit\WebServiceBundle\Entity;

use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

class WSRepo {
    
    private $em;

    public function __construct(EntityManager $entityManager){
        $this->em = $entityManager;
    }
    #-------------------------------------------------------------------
    public function productSync($gender,$date_format=Null) {
        if($date_format){         
            return $this->em
                        ->createQueryBuilder()
                        ->select('p.id product_id,p.name,p.description,ct.target as target,ct.name as clothing_type ,pc.image as product_image,r.title as retailer_title,r.id as retailer_id')
                        ->from('LoveThatFitAdminBundle:Product', 'p')
                        ->innerJoin('p.product_colors', 'pc')
                        ->innerJoin('p.clothing_type', 'ct')
                        ->innerJoin('p.brand', 'b')
                        ->leftJoin('p.retailer', 'r')
                        ->where('p.gender=:gender')
                        ->andWhere('p.updated_at>=:update_date')
                        ->andWhere("p.displayProductColor!=''")
                        ->andWhere ('p.disabled=0')
                        ->groupBy('p.id')
                        ->setParameters(array('gender' => $gender,'update_date'=>$date_format))
                        ->getQuery()
                        ->getResult();            
            
        }else{
         
        return $this->em
                        ->createQueryBuilder()
                        ->select('p.id product_id,p.name,p.description,ct.target as target,ct.name as clothing_type ,pc.image as product_image,b.name as brand_name,b.id as brandId')
                        ->from('LoveThatFitAdminBundle:Product', 'p')
                        ->innerJoin('p.product_colors', 'pc')
                        ->innerJoin('p.clothing_type', 'ct')
                        ->innerJoin('p.brand', 'b')
                        ->where('p.gender=:gender')
                        ->andWhere("p.displayProductColor!=''")
                        ->andWhere ('p.disabled=0')
                        ->groupBy('p.id')
                        ->setParameters(array('gender' => $gender))
                        ->getQuery()
                        ->getResult();
    }}
    
#-------------------------------------------------------
    
}