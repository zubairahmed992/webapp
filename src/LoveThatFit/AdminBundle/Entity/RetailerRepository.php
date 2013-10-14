<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * RetailerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RetailerRepository extends EntityRepository
{
    public function listAllRetailer($page_number = 0, $limit = 0, $sort = 'id') {


        if ($page_number <= 0 || $limit <= 0) {
            $query = $this->getEntityManager()
                    ->createQuery('SELECT r.id,r.title FROM LoveThatFitAdminBundle:Retailer r ORDER BY r.' . $sort . ' ASC');
        } else {
            $query = $this->getEntityManager()
                    ->createQuery('SELECT r.id,r.title FROM LoveThatFitAdminBundle:Retailer r ORDER BY r.' . $sort . ' ASC')
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
                ->createQuery('SELECT r FROM LoveThatFitAdminBundle:Retailer r');
        try {
            return $total_record->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

//duplicate method	 
    public function findBrandBy($name) {
        $total_record = $this->getEntityManager()
                        ->createQuery("SELECT r FROM LoveThatFitAdminBundle:Retailer r     
        WHERE
        r.title = :name"
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
                        ->createQuery("SELECT r FROM LoveThatFitAdminBundle:Retailer r     
                                WHERE r.title = :name")
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
                    ->createQuery('SELECT r FROM LoveThatFitAdminBundle:Retailer b ORDER BY r.' . $sort . ' ASC');
        } else {
            $query = $this->getEntityManager()
                    ->createQuery('SELECT r FROM LoveThatFitAdminBundle:Retailer b ORDER BY r.' . $sort . ' ASC')
                    ->setFirstResult($limit * ($page_number - 1))
                    ->setMaxResults($limit);
        }
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return "null";
        }
    }
    
    public function getRecordsCountWithCurrentRetailerLimit($retailer_id){
        
            $query = $this->getEntityManager()
                    ->createQuery("SELECT count(r.id) as id FROM LoveThatFitAdminBundle:Retailer r WHERE r.id <=:retailer_id")
                   ->setParameters(array('retailer_id' => $retailer_id));
                     try {
                     return $query->getResult();
                } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
                }
        } 
        
        
   public function getRetailerBrandByRetailerAndBrand($retailer,$brand)
   {
       $query = $this->getEntityManager()
                    ->createQuery("SELECT r,b FROM LoveThatFitAdminBundle:Brand b
                    JOIN b.retailers r   
                    WHERE r.id =:retailer
                    AND
                          b.id=:brand")
                   ->setParameters(array('retailer' => $retailer,'brand'=>$brand));
                     try {
                     return $query->getResult();
                } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
                }
   }

   
   public function getBrandByRetailer($retailer)
   {
       $query = $this->getEntityManager()
                    ->createQuery("SELECT r,b FROM LoveThatFitAdminBundle:Brand b
                    JOIN b.retailers r   
                    WHERE r.id =:retailer")
                   ->setParameters(array('retailer' => $retailer));
                     try {
                     return $query->getResult();
                } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
                }
   }
   
   
   

   
    public function isDuplicateEmail($id, $email) {
        try {

            $entityByEmail = $this->findOneBy(array('email' => $email));

            if (!($id) && !($entityByEmail)) {
                return false;
            } else {
                $entityById = $this->find($id);

                if ($entityByEmail) {
                    return ($entityByEmail->getEmail() == $entityById->getEmail()) ? false : true;
                } else {
                    return false;
                }
            }
        } catch (\Exception $e) {
            return $e;
        }
    }
}
