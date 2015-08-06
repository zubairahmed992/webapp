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
############################################################
#################################################################
    
        public function findUser($id) {
            return $this->em
                        ->createQueryBuilder()
                        ->select('u.id as user_id,  u.email,  u.firstName as first_name, u.lastName as last_name,  u.gender,  u.birthDate as birth_date,  u.image, u.authToken as auth_token,  u.authTokenCreatedAt as auth_token_created_at,  u.avatar,  u.zipcode,  u.authTokenWebService  as auth_token_web_service,
                            m.weight, m.height, m.waist, m.hip, m.bust, m.arm, m.inseam, m.shoulder_across_front,
                            m.updated_at, m.shoulder_height, m.chest, m.outseam, m.sleeve, m.neck, m.iphone_shoulder_height, m.iphone_outseam, m.bra_size, m.body_types, m.body_shape, m.thigh, m.shoulder_width, m.bust_height, m.waist_height, m.hip_height, m.bust_width, m.waist_width, m.hip_width, m.shoulder_across_back, m.bicep, m.tricep, m.wrist, m.center_front_waist, m.waist_hip, m.knee, m.calf, m.ankle, m.iphone_foot_height, m.belt, m.iphone_head_height')
                        ->from('LoveThatFitUserBundle:User', 'u')
                        ->innerJoin('u.measurement', 'm')
                        ->where('u.id=:id')
                        ->setParameters(array('id' => $id))
                        ->getQuery()
                        ->getResult(); 
            }

            public function findUserByAuthToken($token) {
            return $this->em
                        ->createQueryBuilder()
                        ->select('u.id as user_id,  u.salt,  u.password,  u.email,  u.is_active,  u.first_name,  u.last_name,  u.gender,  u.birth_date,  u.image,  u.created_at,  u.updated_at,  u.auth_token,  u.auth_token_created_at,  u.avatar,  u.zipcode,  u.auth_token_web_service,  u.iphoneImage,  u.secret_question,  u.secret_answer,  u.time_spent,  u.image_updated_at,  u.image_device_type')
                        ->from('LoveThatFitUserBundle:User', 'u')
                        ->innerJoin('u.measurement', 'm')
                        ->where('u.auth_token=:token')
                        ->setParameters(array('token' => $token))
                        ->getQuery()
                        ->getResult();            
            
            }
    
}