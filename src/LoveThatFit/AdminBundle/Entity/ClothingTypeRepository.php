<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ClothingTypeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ClothingTypeRepository extends EntityRepository
{

    /*-----------------------------------------------------------------
    Written:Suresh
    Description: Find all product with limit and sort
    param:limit, page_number,limit,sort
    ------------------------------------------------------------------*/
    public function findAllClothingType($page_number = 0, $limit = 0, $sort = 'id')
    {

        if ($page_number <= 0 || $limit <= 0) {
            $query = $this->getEntityManager()
                ->createQuery('SELECT c FROM LoveThatFitAdminBundle:ClothingType c ORDER BY c.' . $sort . ' ASC');
        } else {
            $query = $this->getEntityManager()
                ->createQuery('SELECT c FROM LoveThatFitAdminBundle:ClothingType c ORDER BY c.' . $sort . ' ASC')
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
     Written:Suresh
     Description:Count all Records
     param:limit:
    ------------------------------------------------------------------*/
    public function countAllRecord()
    {
        $total_record = $this->getEntityManager()
            ->createQuery('SELECT c FROM LoveThatFitAdminBundle:ClothingType c');
        try {
            return $total_record->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function findClothingTypeBy($name, $target)
    {
        $total_record = $this->getEntityManager()
            ->createQuery("SELECT ct FROM LoveThatFitAdminBundle:ClothingType ct      
        WHERE
        ct.name = :name
        AND ct.target=:target"
            )->setParameters(array('name' => $name, 'target' => $target));
        try {
            return $total_record->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }


    public function listAllClothingType($page_number = 0, $limit = 0, $sort = 'id')
    {


        if ($page_number <= 0 || $limit <= 0) {
            $query = $this->getEntityManager()
                ->createQuery('SELECT c FROM LoveThatFitAdminBundle:ClothingType c ORDER BY c.' . $sort . ' ASC');
        } else {
            $query = $this->getEntityManager()
                ->createQuery('SELECT c FROM LoveThatFitAdminBundle:ClothingType c ORDER BY c.' . $sort . ' ASC')
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
            ->createQuery('SELECT c FROM LoveThatFitAdminBundle:ClothingType c where c.disabled=0  order by c.id,c.gender, c.target, c.name');
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function findAllRecord()
    {
        $query = $this->getEntityManager()
            ->createQuery('SELECT c.id, c.name, c.gender FROM LoveThatFitAdminBundle:ClothingType c order by c.id,c.gender, c.target, c.name');
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    #-----------------------------------------------------------------------------
    /*  public function findAllRecordDistinct(){
        $query = $this->getEntityManager()
        ->createQuery('SELECT DISTINCT(c.name) as name,c.id as id,c.target as target FROM LoveThatFitAdminBundle:ClothingType c  group by c.name order by c.id');
          try {
              return $query->getResult();
          } catch (\Doctrine\ORM\NoResultException $e) {
              return null;
          }

      }*/

    public function findStatisticsBy($target)
    {
        $query = $this->getEntityManager()
            ->createQuery("SELECT ct FROM LoveThatFitAdminBundle:ClothingType ct      
        WHERE        
        ct.target=:target"
            )
            ->setParameter('target', $target);
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }


    #-------------Find All Clothing type for Web Service -----#
    public function findAllBrandWebService()
    {

        $query = $this->getEntityManager()
            ->createQuery("SELECT c.id as id ,c.name as name ,'clothing_type' AS type
                    FROM LoveThatFitAdminBundle:ClothingType c
                    WHERE c.disabled=0 ORDER BY name asc ");
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }


    public function findOneByName($name)
    {
        $record = $this->getEntityManager()
            ->createQuery("SELECT c FROM LoveThatFitAdminBundle:ClothingType c    
                                WHERE c.name = :name")
            ->setParameters(array('name' => $name));
        try {
            return $record->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }


    public function findClothingTypeByName($name)
    {
        $record = $this->getEntityManager()
            ->createQuery("SELECT c FROM LoveThatFitAdminBundle:ClothingType c    
                                WHERE c.name = :name")
            ->setParameters(array('name' => $name));
        try {
            return $record->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function findOneByGenderName($gender, $name)
    {
        $record = $this->getEntityManager()
            ->createQuery("SELECT c FROM LoveThatFitAdminBundle:ClothingType c    
                                WHERE c.name = :name AND c.gender = :gender")
            ->setParameters(array('name' => $name, 'gender' => $gender));
        try {
            return $record->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    //change by umer
    public function findOneByGenderNameCSV($gender, $name)
    {
        $plurals = array('blouses' => 'blouse', 'jackets' => 'jacket', 'sweaters' => 'sweater', 'trousers' => 'trouser', 'jeans' => 'jean', 'skirts' => 'skirt', 'dresses' => 'dress');
        $name = array_key_exists($name, $plurals) ? $plurals[$name] : $name;
        $record = $this->getEntityManager()
            ->createQuery("SELECT c FROM LoveThatFitAdminBundle:ClothingType c    
                                WHERE c.name LIKE :name AND c.gender = :gender")
            ->setParameters(array('name' => $name . '%', 'gender' => $gender));
        try {
            return $record->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }


    public function findClothingTypeByProduct($product)
    {
        $query = $this->getEntityManager()
            ->createQuery("
     SELECT b FROM LoveThatFitAdminBundle:ClothingType b     
     WHERE
     b.id=:id     
    ")->setParameters(array('id' => $product));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }


    public function findClothingTypsByGender($gender)
    {
        $query = $this->getEntityManager()
            ->createQuery("
     SELECT b FROM LoveThatFitAdminBundle:ClothingType b     
     WHERE
     b.gender=:gender   
    ")->setParameters(array('gender' => $gender));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

#------------------------------------------------------------------------------#  
    public function getRecordsCountWithCurrentClothingTYpeLimit($clothing_type)
    {
        $query = $this->getEntityManager()
            ->createQuery("SELECT count(c.id) as id  FROM LoveThatFitAdminBundle:ClothingType c WHERE c.id <=:clothing_type")
            ->setParameters(array('clothing_type' => $clothing_type));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    #--------------Find Clothing Type By Gender---------------------------------#
    public function findByGender($gender)
    {
        $query = $this->getEntityManager()
            ->createQuery("
     SELECT ct.id as id,ct.name as name FROM LoveThatFitAdminBundle:ClothingType ct     
     WHERE
     ct.gender=:gender     
    ")->setParameters(array('gender' => $gender));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }

    }

#--------------Find Clothing Type By ID---------------------------------#
    public function findById($id)
    {
        $query = $this->getEntityManager()
            ->createQuery("
     SELECT ct.name as name,ct.target as target FROM LoveThatFitAdminBundle:ClothingType ct     
     WHERE
     ct.id=:id     
    ")->setParameters(array('id' => $id));
        try {
            return $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    #--------------Find Clothing Type By ID and Gender ---------------------------------#
    public function findByTargetGender($target, $gender)
    {
        $query = $this->getEntityManager()
            ->createQuery("
       SELECT ct1.name as name,ct1.target as target,ct1.id as id FROM LoveThatFitAdminBundle:ClothingType ct1     
       where ct1.target=:target AND ct1.gender=:gender
      ")->setParameters(array('target' => $target, 'gender' => $gender));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }

    }

    #--------------Find Clothing Type By ID and Gender ---------------------------------#
    public function findByTargetAndGender($target, $gender)
    {
        $query = $this->getEntityManager()
            ->createQuery("
       SELECT ct1.name as name,ct1.id as id FROM LoveThatFitAdminBundle:ClothingType ct1     
       where ct1.target=:target AND ct1.gender=:gender
      ")->setParameters(array('target' => $target, 'gender' => $gender));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }

    }

    public function findDefaultClothing($data, $page = 0, $max = NULL, $order, $getResult = true)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $search = isset($data['query']) && $data['query'] ? $data['query'] : null;

        $query
            ->select('
                p.id,
                p.name,
                p.control_number,
                p.gender,
                b.name as BName,
                ct.name as cloting_type,
                p.created_at,
                p.disabled,
				p.description,
				p.item_name,				
				p.country_origin,
				p.item_details,								
				p.care_label,				
                p.status,
                ct.target
            ')
            ->from('LoveThatFitAdminBundle:Product', 'p')
            ->join('p.brand', 'b')
            ->join('p.clothing_type', 'ct')
            ->join('p.product_colors', 'pc')
            ->andWhere('p.deleted=0')
            ->andWhere('p.default_clothing = 1')
            ->groupBy('p.id');

        if ($search) {
            $query
                ->andWhere('p.id like :search or 
                            p.name like :search or 
                            p.control_number like :search or 
                            p.gender like :search or 
                            ct.name like :search or 
                            b.name like :search or 
                            p.status like :search')
                ->setParameter('search', "%" . $search . "%");
        }

        if (is_array($order)) {
            $orderByColumn = $order[0]['column'];
            $orderByDirection = $order[0]['dir'];
            if ($orderByColumn == 0) {
                $orderByColumn = "p.id";
            } elseif ($orderByColumn == 1) {
                $orderByColumn = "p.control_number";
            } elseif ($orderByColumn == 2) {
                $orderByColumn = "b.name";
            } elseif ($orderByColumn == 4) {
                $orderByColumn = "p.gender";
            } elseif ($orderByColumn == 5) {
                $orderByColumn = "p.name";
            } elseif ($orderByColumn == 6) {
                $orderByColumn = "p.created_at";
            } elseif ($orderByColumn == 7) {
                $orderByColumn = "p.status";
            } elseif ($orderByColumn == 8) {
                $orderByColumn = "p.disabled";
            }
            $query->OrderBy($orderByColumn, $orderByDirection);
        }
        /*echo $query->getSQL(); die;
            return $query->getResult(); */
        if ($max) {
            $preparedQuery = $query->getQuery()
                ->setMaxResults($max)
                ->setFirstResult(($page) * $max);
        } else {
            $preparedQuery = $query->getQuery();
        }

        /*echo $preparedQuery->getSQL(); die;*/
        return $getResult ? $preparedQuery->getResult() : $preparedQuery;
    }

    public function findClothingTypeByTarget($target)
    {
        $query = $this->getEntityManager()
            ->createQuery("
       SELECT ct1.name as name,ct1.id as id, ct1.target as target FROM LoveThatFitAdminBundle:ClothingType ct1     
       where ct1.target in (:target)
      ")->setParameter('target', $target, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY);
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }

    }

    public function getProductByClothingTypeId( $clothing_type_id ){
        $query = $this->getEntityManager()
            ->createQuery("
      SELECT p.id as id,p.name as name
      FROM LoveThatFitAdminBundle:Product p
      JOIN p.clothing_type ct
      JOIN p.brand b
      JOIN p.product_colors pc
      WHERE ct.id = :clothing_type_id AND p.disabled=0 AND p.deleted=0 AND p.displayProductColor!='' GROUP by p.id"
            )->setParameters(array('clothing_type_id' => $clothing_type_id));
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
}
