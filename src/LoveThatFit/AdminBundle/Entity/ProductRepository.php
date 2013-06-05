<?php

namespace LoveThatFit\AdminBundle\Entity;

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

    /* --------------------------------------------------------- */

    public function findByGender($gender, $page_number = 0, $limit = 0) {
        if ($page_number <= 0 || $limit <= 0) {

            $query = $this->getEntityManager()
                            ->createQuery('
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            WHERE p.gender = :gender'
                            )->setParameter('gender', $gender);
        } else {
            $query = $this->getEntityManager()
                    ->createQuery('
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            WHERE p.gender = :gender'
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
                            ->createQuery('
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            WHERE p.gender = :gender 
            ORDER BY p.created_at DESC'
                            )->setParameter('gender', $gender);
        } else {
            $query = $this->getEntityManager()
                    ->createQuery('
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            WHERE p.gender = :gender 
            ORDER BY p.created_at DESC'
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

    public function findByGenderBrand($gender, $brand_id, $page_number = 0, $limit = 0) {
        if ($page_number <= 0 || $limit <= 0) {
            $query = $this->getEntityManager()
                            ->createQuery('
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            JOIN p.brand b
            WHERE b.id = :id
            AND p.gender = :gender'
                            )->setParameters(array('id' => $brand_id, 'gender' => $gender));
        } else {
            $query = $this->getEntityManager()
                    ->createQuery('
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            JOIN p.brand b
            WHERE b.id = :id
            AND p.gender = :gender'
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
                            ->createQuery('
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            JOIN p.clothing_type ct
            WHERE ct.id = :clothing_type_id
            AND p.gender = :gender'
                            )->setParameters(array('clothing_type_id' => $clothing_type_id, 'gender' => $gender));
        } else {
            $query = $this->getEntityManager()
                    ->createQuery('
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            JOIN p.clothing_type ct
            WHERE ct.id = :clothing_type_id
            AND p.gender = :gender'
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
                        ->createQuery('
            SELECT p FROM LoveThatFitAdminBundle:Product p 
            JOIN p.clothing_type ct
            WHERE p.gender = :gender'
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
      SELECT p.id,p.name,p.adjustment,ct.name as clothing_type , b.name as brand_name,
      b.id as brand_id,ct.id as clothing_type_id, pc.image as product_image
      FROM LoveThatFitAdminBundle:Product p 
      JOIN p.clothing_type ct
      JOIN p.brand b 
      JOIN p.product_colors pc
      ");

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    //-------------------------------------------------------------------------
    public function productListByBrand($brand_id, $gender) {
        $query = $this->getEntityManager()
                        ->createQuery("
      SELECT p.id,p.name,p.adjustment,ct.name as clothing_type ,p.gender,
      b.name as brand_name,b.id as brand_id,ct.id as clothing_type_id, pc.image as product_image
      FROM LoveThatFitAdminBundle:Product p 
      JOIN p.clothing_type ct
      JOIN p.brand b 
      JOIN p.product_colors pc
      WHERE
      p.gender = :gender
      AND b.id = :brand_id"
                        )->setParameters(array('gender' => $gender, 'brand_id' => $brand_id));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function productListByClothingType($clothing_type_id, $gender) {
        $query = $this->getEntityManager()
                        ->createQuery("
      SELECT p.id,p.name,p.adjustment,ct.name as clothing_type ,p.gender,
      b.name as brand_name,b.id as brand_id,ct.id as clothing_type_id, pc.image as product_image
      FROM LoveThatFitAdminBundle:Product p 
      JOIN p.clothing_type ct
      JOIN p.brand b 
      JOIN p.product_colors pc
      WHERE
      p.gender = :gender
      AND ct.id = :clothing_type_id"
                        )->setParameters(array('gender' => $gender, 'clothing_type_id' => $clothing_type_id));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function productListByBrandClothingType($brand_id, $clothing_type_id, $gender) {
        $query = $this->getEntityManager()
                        ->createQuery("
      SELECT p.id,p.name,p.adjustment,ct.name as clothing_type ,p.gender,
      b.name as brand_name,b.id as brand_id,ct.id as clothing_type_id, pc.image as product_image
      FROM LoveThatFitAdminBundle:Product p 
      JOIN p.clothing_type ct
      JOIN p.brand b 
      JOIN p.product_colors pc
      WHERE
      p.gender = :gender
      AND ct.id = :clothing_type_id
      AND b.id = :brand_id"
                        )->setParameters(array('gender' => $gender, 'clothing_type_id' => $clothing_type_id, 'brand_id' => $brand_id));

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function productDetail($product_id) {
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
      WHERE  p.id=:id")->setParameter('id', $product_id);
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;

        }
    }


 public function findProductItemByUser($user_id , $page_number=0 , $limit=0) {
            $query = $this->getEntityManager()
                        ->createQuery("
     SELECT p,pi FROM LoveThatFitAdminBundle:Product p
     JOIN p.product_items pi
     JOIN pi.users u
     WHERE
     u.id = :id"  )->setParameters(array('id' => $user_id)) ;
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
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
    
    

}
