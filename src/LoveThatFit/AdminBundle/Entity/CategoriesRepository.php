<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CategoriesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoriesRepository extends EntityRepository
{
	
/*-----------------------------------------------------------------
Written:Raghib
Description: Find all product with limit and sort 
param:limit, page_number,limit,sort	 
------------------------------------------------------------------*/
	 public function findAllCategories($page_number = 0, $limit = 0 ,$sort='id'  ) {
				   
             if ($page_number <= 0 || $limit <= 0) {
            $query = $this->getEntityManager()
                    ->createQuery('SELECT c FROM LoveThatFitAdminBundle:Categories c ORDER BY c.' . $sort . ' ASC');
        } else {
            $query = $this->getEntityManager()
                    ->createQuery('SELECT c FROM LoveThatFitAdminBundle:Categories c ORDER BY c.' . $sort . ' ASC')
                    ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        }
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
   
  /*-----End Of Function-----------------*/
	
	 /*-----------------------------------------------------------------
      Written:Raghib
	  Description:Count all Records
	  param:limit:
	 ------------------------------------------------------------------*/ 
     public function countAllRecord()
	 {
	  $total_record= $this->getEntityManager()
	   ->createQuery('SELECT c FROM LoveThatFitAdminBundle:Categories c');
	  try 
	    {
		 return $total_record->getResult();
		}
		catch (\Doctrine\ORM\NoResultException $e) 
		 {
		   return null;
		 }						
	  }   
	 
	public function findCategoriesBy($name) {
        $total_record = $this->getEntityManager()
        ->createQuery("SELECT ct FROM LoveThatFitAdminBundle:Categories ct
        WHERE
        ct.name = :name"
                        )->setParameters(array('name' => $name));
        try {
            return $total_record->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    
    public function listAllCategories($page_number = 0, $limit = 0, $sort = 'id') {


        if ($page_number <= 0 || $limit <= 0) {
            $query = $this->getEntityManager()
                    ->createQuery('SELECT c FROM LoveThatFitAdminBundle:Categories c ORDER BY c.' . $sort . ' ASC');
        } else {
            $query = $this->getEntityManager()
                    ->createQuery('SELECT c FROM LoveThatFitAdminBundle:Categories c ORDER BY c.' . $sort . ' ASC')
                    ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        }
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return "null";
        }
    }

    public function findAllAvailableRecords()
    {
    $query = $this->getEntityManager()
        ->createQuery('SELECT c FROM LoveThatFitAdminBundle:Categories c where c.disabled=0  order by c.id,c.gender, c.name');
    try {
      return $query->getResult();
    } catch (\Doctrine\ORM\NoResultException $e) {
      return null;
    }
    }
        
    public function findAllRecord()
    {
      $query = $this->getEntityManager()
                ->createQuery('SELECT c, c.image as image2 FROM LoveThatFitAdminBundle:Categories c order by c.id,c.gender, c.name');
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    
  #-------------Find All Clothing type for Web Service -----#
    public function findAllBrandWebService() {

        $query = $this->getEntityManager()
                ->createQuery("SELECT c.id as id ,c.name as name ,'categories' AS type
                    FROM LoveThatFitAdminBundle:Categories c
                    WHERE c.disabled=0 ORDER BY name asc ");
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    
    public function findOneByName($name) {
        $record = $this->getEntityManager()
                        ->createQuery("SELECT c FROM LoveThatFitAdminBundle:Categories c
                                WHERE c.name = :name")
                        ->setParameters(array('name' => $name));
        try {
            return $record->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    
    public function findCategoriesByName($name) {
        $record = $this->getEntityManager()
                        ->createQuery("SELECT c FROM LoveThatFitAdminBundle:Categories c
                                WHERE c.name = :name")
                        ->setParameters(array('name' =>$name));
        try {
            return $record->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
     public function findOneByGenderName($gender, $name) {
        $record = $this->getEntityManager()
                        ->createQuery("SELECT c FROM LoveThatFitAdminBundle:Categories c
                                WHERE c.name = :name AND c.gender = :gender")
                        ->setParameters(array('name' =>$name, 'gender' => $gender));
        try {
            return $record->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    public function findCategoriesByProduct($product)
    {
      $query = $this->getEntityManager()
                        ->createQuery("
     SELECT b FROM LoveThatFitAdminBundle:Categories b
     WHERE
     b.id=:id
    "  )->setParameters(array('id' => $product)) ;
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
  
  
  
  
    public function findCategoriesByGender($gender) {
      $query = $this->getEntityManager()
                        ->createQuery("
     SELECT b FROM LoveThatFitAdminBundle:Categories b
     WHERE
     b.gender=:gender
    "  )->setParameters(array('gender' => $gender)) ;
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
  
#------------------------------------------------------------------------------#  
    public function getRecordsCountWithCurrentCategoriesLimit($categories){
     $query = $this->getEntityManager()
                    ->createQuery("SELECT count(c.id) as id  FROM LoveThatFitAdminBundle:Categories c WHERE c.id <=:categories")
                   ->setParameters(array('categories' => $categories));
                     try {
                     return $query->getResult();
                } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
                }
    }
 #--------------Find Categories By Gender---------------------------------#
    public function findByGender($gender){
       $query = $this->getEntityManager()
                        ->createQuery("
     SELECT ct.id as id,ct.name as name FROM LoveThatFitAdminBundle:Categories ct
     WHERE
     ct.gender=:gender
    "  )->setParameters(array('gender' => $gender)) ;
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }

    }
#--------------Find Categories By ID---------------------------------#
    public function findById($id){
       $query = $this->getEntityManager()
                        ->createQuery("
     SELECT ct.name as name,ct.gender as gender FROM LoveThatFitAdminBundle:Categories ct
     WHERE
     ct.id=:id
    "  )->setParameters(array('id' => $id)) ;
        try {
            return $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }

    }

#--------------Add Parent Id in Child category ---------------------------------#
    public function addParentIdInChild($selected_category_id, $id){

        $record = $this->getEntityManager()
            ->createQuery("UPDATE LoveThatFitAdminBundle:Categories c
                            Set c.parent_id = :parent_id
                                WHERE c.id = :id")
            ->setParameters(array('parent_id' => $id,'id' => $selected_category_id));
        try {
            return $record->execute();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }

    }

#--------------Find Categories By ID---------------------------------#
    public function findAllBrandDropdown($parent_id = 0) {

        if($parent_id == 0){
            $query = $this->getEntityManager()
                ->createQuery("SELECT c.id as id, c.name as name, c.parent_id as parent_id, c.gender as gender
                FROM LoveThatFitAdminBundle:Categories c
                WHERE c.disabled=0 and c.parent_id is null ORDER BY c.id asc ");

        }else {

            $query = $this->getEntityManager()
                ->createQuery("SELECT c.id as id ,c.name as name, c.parent_id as parent_id, c.gender as gender
                FROM LoveThatFitAdminBundle:Categories c
                WHERE c.disabled=0 AND c.parent_id =:parent_id  ORDER BY c.id asc ")
                ->setParameters(array('parent_id' => $parent_id));
        }
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

#--------------Find Categories By ID---------------------------------#
    public function search(
        $data,
        $page = 0,
        $max = NULL,
        $order,
        $getResult = true
    )
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $search = isset($data['query']) && $data['query']?$data['query']:null;
        $query
            ->select('
            e.id,
            e.name,
            e.gender,
            e.image,
            e.disabled,
            e.created_at'
            )
            ->from('LoveThatFitAdminBundle:Categories', 'e');

        if ($search) {
            $query
                ->andWhere('e.name like :search')
                ->orWhere('e.gender like :search')
                ->setParameter('search', "%".$search."%");
        }
        if (is_array($order)) {
            $orderByColumn    = $order[0]['column'];
            $orderByDirection = $order[0]['dir'];
            $query->OrderBy("e.id", $orderByDirection);
        }

        if ($max) {
            $preparedQuery = $query->getQuery()
                ->setMaxResults($max)
                ->setFirstResult(($page) * $max);
        } else {
            $preparedQuery = $query->getQuery();
        }
        return $getResult?$preparedQuery->getResult():$preparedQuery;
    }

    #--------------Find Categories By ID---------------------------------#
    public function findAllBrands($gender = null) {
        if($gender != null) {
            $gender_condition = " AND c.gender = '".$gender."'";
        }

        $query = $this->getEntityManager()
        ->createQuery("SELECT c.id as id, c.name as name, c.parent_id as parent_cat_id,
                    c.gender as gender,c.image as image
                    FROM LoveThatFitAdminBundle:Categories c
                    LEFT JOIN c.children d
                    WHERE c.disabled=0 ".$gender_condition."
                    GROUP BY c.id");
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    #--------------Find Top Level Category ---------------------------------#
    public function getTopLevelCategory($id) {

        $query = $this->getEntityManager()
            ->createQuery("SELECT toplevel.id
                        FROM LoveThatFitAdminBundle:Categories toplevel
                        LEFT JOIN toplevel.children firstlevel
                        LEFT JOIN firstlevel.children secondlevel
                        LEFT JOIN secondlevel.children thirdlevel
                        LEFT JOIN thirdlevel.children fourthlevel
                        LEFT JOIN fourthlevel.children fifthlevel
                        LEFT JOIN fifthlevel.children sixthlevel
                        LEFT JOIN sixthlevel.children seventhlevel
                        where toplevel.id=:pid or firstlevel.id=:pid or
                        secondlevel.id=:pid or thirdlevel.id=:pid  or
                        fourthlevel.id=:pid  or fifthlevel.id=:pid or
                        sixthlevel.id=:pid  or seventhlevel.id=:pid")
                        ->setParameters(array('pid' => $id));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }


    #--------------Add Parent Id in Child category ---------------------------------#
    public function addTopLevelInChild($id, $top_level_category){

        $record = $this->getEntityManager()
            ->createQuery("UPDATE LoveThatFitAdminBundle:Categories c
                            Set c.top_id = :top_id
                                WHERE c.id = :id")
            ->setParameters(array('top_id' => $top_level_category,'id' => $id));
        try {
            return $record->execute();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }


    #--------------Find Categories By ID---------------------------------#
    public function getSelectedCategories($id = null) {
        $query = $this->getEntityManager()
            ->createQuery("
                        SELECT c.id FROM LoveThatFitAdminBundle:Product p
                         JOIN p.categories c
                        WHERE
                        p.id = :id"
                        )->setParameters(array('id' => $id)) ;
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    #--------------Find Categories By ID---------------------------------#
    public function saveProductCategories($productId, $getselectedcategories){


        //Place query here, let's say you want all the users that have blue as their favorite color
        $sql = "DELETE FROM category_products WHERE product_id = :productid";

        //set parameters
        //you may set as many parameters as you have on your query
        $params['productid'] = $productId;
        $query = $this->getEntityManager()->getConnection()
            ->prepare($sql);
        $query->execute($params);

        if(!empty($getselectedcategories)){
            // Don't forget to protect against SQL injection :)
            $query = $this->getEntityManager()->getConnection();
            foreach($getselectedcategories as $value){
                $aValues = array('categories_id' => $value, 'product_id' => $params['productid']);
                $query->insert('category_products', $aValues);
            }
        }
        return true;
    }

    public function findOneByGenderNameCategory($gender, $name, $cat_id) {

        if($cat_id == null){
            $record = $this->getEntityManager()
                ->createQuery("SELECT c FROM LoveThatFitAdminBundle:Categories c
                                WHERE c.name = :name AND c.gender = :gender AND c.parent IS NULL ")
                ->setParameters(array('name' =>$name, 'gender' => $gender));
        }else{
            $record = $this->getEntityManager()
                ->createQuery("SELECT c FROM LoveThatFitAdminBundle:Categories c
                                WHERE c.name = :name AND c.gender = :gender AND c.parent = :cat_id")
                ->setParameters(array('name' =>$name, 'gender' => $gender, 'cat_id' => $cat_id));
        }

        try {
            return $record->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
}
