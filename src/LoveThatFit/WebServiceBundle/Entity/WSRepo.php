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
                        ->select('p.id product_id,p.name,p.description,ct.target as target,ct.name as clothing_type ,pc.image as product_image,pc.title as product_color,r.id as retailer_id, r.title as retailer_title, b.id as brand_id, b.name as brand_name, coalesce(MAX(pi.price), 0) as price')
                        ->from('LoveThatFitAdminBundle:Product', 'p')
                        ->innerJoin('p.displayProductColor', 'pc')
                        ->innerJoin('p.clothing_type', 'ct')
                        ->innerJoin('p.brand', 'b')
                        ->innerJoin('p.product_items', 'pi')
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
                        ->select('p.id product_id,p.name,p.description,ct.target as target,ct.name as clothing_type ,pc.image as product_image,r.id as retailer_id, r.title as retailer_title, b.id as brand_id, b.name as brand_name, coalesce(MAX(pi.price), 0) as price')
                        ->from('LoveThatFitAdminBundle:Product', 'p')
                        ->innerJoin('p.displayProductColor', 'pc')
                        ->innerJoin('p.clothing_type', 'ct')
                        ->innerJoin('p.brand', 'b')
                        ->innerJoin('p.product_items', 'pi')
                        ->leftJoin('p.retailer', 'r')
                        ->where('p.gender=:gender')
                        ->andWhere("p.displayProductColor!=''")
                        ->andWhere ('p.disabled=0')
                        ->groupBy('p.id')
                        ->setParameters(array('gender' => $gender))
                        ->getQuery()
                        ->getResult();
    }}
    
#-------------------------------------------------------
    public function userLikedProductIds($user_id) {
        $query = $this->em
                        ->createQuery(
                                "SELECT distinct p.id product_id
                         FROM LoveThatFitAdminBundle:Product p 
                        JOIN p.product_items pi            
                        JOIN pi.users u
                        WHERE u.id=:user_id "
                        )->setParameters(array('user_id' => $user_id));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
#-------------------------------------------------------------------
    public function productList($user, $list_type = null) {
        switch ($list_type) {

            case 'recent':
                $query = $this->em
                                ->createQuery("
            SELECT p.id product_id, p.name, p.description,p.description,
            ct.target as target,ct.name as clothing_type ,
            pc.image as product_image,
            r.id as retailer_id, r.title as retailer_title, 
            b.id as brand_id, b.name as brand_name,
            coalesce(pi.price, 0) as price, pi.id as product_item_id,
            CASE WHEN (pu.id IS NULL) THEN 'false' ELSE 'true' END AS favourite
            FROM LoveThatFitAdminBundle:Product p             
            JOIN p.brand b
            LEFT JOIN p.retailer r
            JOIN p.user_item_try_history uih
            JOIN uih.productitem pi
            JOIN pi.product_color pc            
            JOIN p.clothing_type ct
            LEFT JOIN pi.users pu            
            WHERE (pu.id IS NULL OR pu.id=:user_id) AND uih.user=:user_id AND p.disabled=0 AND p.displayProductColor!=''  
            ORDER BY uih.count DESC"
                                )->setParameters(array('user_id' => $user->getId()));
                break;
            case 'favourite':
                $query = $this->em
                                ->createQuery("
            SELECT p.id product_id, p.name, p.description,p.description,
            ct.target as target,ct.name as clothing_type ,
            pc.image as product_image,
            r.id as retailer_id, r.title as retailer_title, 
            b.id as brand_id, b.name as brand_name,
            coalesce(pi.price, 0) as price, pi.id as product_item_id,
            'true' AS favourite
            FROM LoveThatFitAdminBundle:Product p 
            JOIN p.product_items pi
            JOIN pi.product_color pc                        
            JOIN p.brand b
            LEFT JOIN p.retailer r
            JOIN pi.users u
            JOIN p.clothing_type ct
            
            WHERE u.id=:user_id AND p.disabled=0 AND p.displayProductColor!=''  
            ORDER BY p.name"
                      )->setParameters(array('user_id' => $user->getId()));
                break;
            default:
                #by default it gets the latest 10 records
                $query = $this->em
                                ->createQuery("
            SELECT p.id product_id, p.name, p.description,p.description,
            ct.target as target,ct.name as clothing_type ,
            pc.image as product_image,
            r.id as retailer_id, r.title as retailer_title, 
            b.id as brand_id, b.name as brand_name,
            coalesce(pi.price, 0) as price, pi.id as product_item_id,
            'false' AS favourite
            FROM LoveThatFitAdminBundle:Product p 
            JOIN p.displayProductColor pc            
            JOIN p.brand b
            JOIN p.product_items pi
            LEFT JOIN p.retailer r
            JOIN p.clothing_type ct
            
            WHERE p.gender=:gender AND p.disabled=0 AND p.displayProductColor!=''  
            GROUP BY p.id 
            ORDER BY p.id DESC"
                                )->setParameters(array('gender' => $user->getGender()))->setMaxResults(10);
                break;
        };
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
#--------------------------------------------------------------
    public function productDetail($id, $user) {
        $query = $this->em
                                ->createQuery("
            SELECT p.id product_id, p.name, p.description,p.description,
            ct.target as target,ct.name as clothing_type ,
            pc.image as product_image,
            r.id as retailer_id, r.title as retailer_title, 
            b.id as brand_id, b.name as brand_name
            FROM LoveThatFitAdminBundle:Product p 
            JOIN p.product_colors pc            
            JOIN p.brand b
            LEFT JOIN p.retailer r
            JOIN p.clothing_type ct
            
            WHERE p.id=:product_id AND p.disabled=0 AND p.displayProductColor!=''  
            "
                                )->setParameters(array('product_id' => $id));
                return $query->getResult();
        try {
                    
                } catch (\Doctrine\ORM\NoResultException $e) {
                    return null;
                }

    }

############################################################
#################################################################
    
        public function findUser($id) {
            return $this->em
                        ->createQueryBuilder()
                        ->select('u.id as user_id,  u.email,  u.firstName as first_name, u.lastName as last_name,  u.gender,  u.birthDate as birth_date,  u.image, u.authToken as auth_token,  u.authTokenCreatedAt as auth_token_created_at,  u.avatar,  u.zipcode,  u.authTokenWebService  as auth_token_web_service,
                            m.weight, m.height, m.waist, m.hip, m.bust, m.arm, m.inseam, m.shoulder_across_front,
                            m.updated_at, m.shoulder_height, m.shoulder_length, m.chest, m.outseam, m.sleeve, m.neck, m.iphone_shoulder_height, m.iphone_outseam, m.bra_size, m.body_types, m.body_shape, m.thigh, m.shoulder_width, m.bust_height, m.waist_height, m.hip_height, m.bust_width, m.waist_width, m.hip_width, m.shoulder_across_back, m.bicep, m.tricep, m.wrist, m.center_front_waist, m.waist_hip, m.knee, m.calf, m.ankle, m.iphone_foot_height, m.belt, m.iphone_head_height')
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
            
             #--------------------------------------------------------------

    public function userAdminList() {
            return $this->em
                        ->createQueryBuilder()
                        ->select('u.id as user_id,  u.email,  u.gender,  u.image, u.authToken as auth_token, u.image_device_type')
                        ->from('LoveThatFitUserBundle:User', 'u')                        
                        ->getQuery()
                        ->getResult(); 
            }
}