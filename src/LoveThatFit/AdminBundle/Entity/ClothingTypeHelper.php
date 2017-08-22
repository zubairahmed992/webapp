<?php

namespace LoveThatFit\AdminBundle\Entity;

use LoveThatFit\AdminBundle\Entity\ClothingType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\AdminBundle\Event\ClothingTypeEvent;

class ClothingTypeHelper
{

    protected $dispatcher;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repo;

    /**
     * @var string
     */
    protected $class;

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class)
    {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }

    public function createNew()
    {
        $class = $this->class;
        $clothing_types = new $class();
        return $clothing_types;
    }

    public function save($entity)
    {
        //$msg_array = null;        
        $name = $entity->getName();
        $msg_array = $this->validateForCreate($name);
        if ($msg_array == null and $name != null) {
            $entity->setCreatedAt(new \DateTime('now'));
            $entity->setUpdatedAt(new \DateTime('now'));
            $this->em->persist($entity);
            $this->em->flush();
            return array('message' => 'Clothing Type succesfully created.',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );
        } else {
            return $msg_array;
        }
    }

    public function find($id)
    {
        return $this->repo->find($id);
    }

    public function update($entity)
    {

        $msg_array = $this->validateForUpdate($entity);

        if ($msg_array == null) {
            $entity->setUpdatedAt(new \DateTime('now'));
            $entity->upload();
            $this->em->persist($entity);
            $this->em->flush();

            return array('message' => 'ClothingType ' . $entity->getName() . ' succesfully updated!',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );
        } else {
            return $msg_array;
        }
    }


    public function delete($id)
    {
        $entity = $this->repo->find($id);
        $entity_name = $entity->getName();
        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();
            return array('clothing_types' => $entity,
                'message' => 'The Clothing Type ' . $entity_name . ' has been Deleted!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {

            return array('clothing_types' => $entity,
                'message' => 'clothing types not found!',
                'message_type' => 'warning',
                'success' => false,
            );
        }
    }

    public function getListWithPagination($page_number, $sort)
    {
        $yaml = new Parser();
        $pagination_constants = $yaml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
        $limit = $pagination_constants["constants"]["pagination"]["limit"];

        $entity = $this->repo->listAllClothingType($page_number, $limit, $sort);
        $rec_count = count($this->repo->countAllRecord());
        $cur_page = $page_number;

        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }
        return array('clothing_types' => $entity,
            'rec_count' => $rec_count,
            'no_of_pagination' => $no_of_paginations,
            'limit' => $cur_page,
            'per_page_limit' => $limit,
            'criteriaTop' => $this->countStatistics('Top'),
            'criteriaBottom' => $this->countStatistics('Bottom'),
            'criteriaDress' => $this->countStatistics('Dress'),
            'sort' => $sort,
        );
    }


//-------------------------------------------------------
    private function initialCap($str)
    {
        return str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($str))));
    }

//-------------------------------------------------------
    public function findOneByName($name)
    {
        return $this->repo->findOneByName($name);
    }

//-------------------------------------------------------
    public function findClothingTypeByName($name)
    {
        return $this->repo->findClothingTypeByName($name);
    }

    //-------------------------------------------------------
    public function findOneByGenderName($gender, $name)
    {
        return $this->repo->findOneByGenderName($gender, $name);
    }

    //change by umer
    public function findOneByGenderNameCSV($gender, $name)
    {
        return $this->repo->findOneByGenderNameCSV($gender, $name);
    }

    #-----------------------------------------------
    public function findAll()
    {
        return $this->repo->findAllRecord();
    }

    public function findAllList()
    {
        return $this->repo->findAllList();
    }

#--------------------------------------------------------------------------
    /*  public function findAllDistinct(){
          return $this->repo->findAllRecord();
      }*/

#-----------------Find By Gender---------------------------------# 
    public function findByGender($gender)
    {
        return $this->repo->findByGender($gender);
    }

    #-----------------------------------------------------------------#
    public function findById($id)
    {
        return $this->repo->findById($id);

    }

    //-------------------------------------------------------
    //Private Methods    
//----------------------------------------------------------
    private function validateForCreate($name)
    {
        if (count($this->findClothingTypeByName($name)) > 0) {
            return array('message' => 'clothing types Name already exists!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }
        return;
    }

//----------------------------------------------------------
    private function validateForUpdate($entity)
    {
        //$clothing_types = $this->findClothingTypeByName($entity->getName());
        $clothing_type = $this->findOneByGenderName($entity->getGender(), $entity->getName());
        if ($clothing_type && $clothing_type->getId() != $entity->getId()) {
            return array('message' => 'Clothing Type Name already exists!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }
        return;
    }

    private function countStatistics($target)
    {
        return $rec_count = count($this->repo->findStatisticsBy($target));
    }

#-------------------------getRecordsCountWithCurrentBrandLimit------------------#
    public function getRecordsCountWithCurrentClothingTYpeLimit($clothing_type)
    {
        return $this->repo->getRecordsCountWithCurrentClothingTYpeLimit($clothing_type);
    }

#-------------------------------------------------------------------------------#
    public function getArray()
    {
        $cat_list = $this->repo->findAllRecord();
        $cat_array = array();
        foreach ($cat_list as $key => $value) {
            if ($value["gender"] == 'f') {
                $cat_array['woman'][$value["id"]] = $value["name"];
                //   $cat_array['woman']['target'][$value->getId()]=$value->getTarget();
            } else {
                $cat_array['man'][$value["id"]] = $value["name"];
                //  $cat_array['man']['target'][$value->getId()]=$value->getTarget();
            }
        }
        return $cat_array;
    }

    #----------------------------------------------
    public function getMixArray()
    {
        $cat_array = array();
        foreach ($this->repo->findAllRecord() as $key => $value) {
            $cat_array[$value->getName()] = $value->getName();
        }
        return $cat_array;
    }

    #-------------------------------------------------------------------------------#
    public function getDescriptionArray($gender = null, $base_path)
    {
        $cat_list = $this->repo->findAllAvailableRecords();
        $cat_array['woman'] = array();
        $cat_array['man'] = array();
        foreach ($cat_list as $key => $value) {
            if ($value->getGender() == 'f') {
                #array_push($cat_array['woman'],array('id'=> $value->getId(), 'clothing_type'=>$value->getName(), 'target'=>$value->getTarget(),'caption'=>'caption', 'image'=>'image url'));
                array_push($cat_array['woman'], array('clothing_type' => $value->getName(), 'caption' => $this->getCaptionEncoded($value->getName()), 'image' => $base_path . $value->getWebPath()));
            } else {
                #$cat_array['man'][$value->getName()]=array('id'=> $value->getId(), 'clothing_type'=>$value->getName(), 'target'=>$value->getTarget(),'caption'=>'caption', 'image'=>'image url');
                array_push($cat_array['man'], array('clothing_type' => $value->getName(), 'caption' => 'caption', 'image' => $base_path . $value->getWebPath()));
            }
        }
        return $gender ? $cat_array[$gender == 'm' ? 'man' : 'woman'] : $cat_array;
    }

    private function getCaptionEncoded($caption)
    {
        if ($caption == 'tank_knit') {
            $caption = "tank tops";
        }
        if ($caption == 'tee_knit') {
            $caption = "tee shirts";
        }
        $caption_start = explode("_", $caption);
        if (count($caption_start) > 1) {
            $caption_end = ucfirst($caption_start[0]) . " " . ucfirst($caption_start[1]);

        } else {
            $caption_end = ucfirst($caption_start[0]);
        }
        return $caption_end;
    }

    #-----------------Find By Clothing Type By Gender---------------------------------# 
    public function findClothingTypsByGender($gender)
    {
        return $this->repo->findClothingTypsByGender($gender);
    }

    #-----------------Find By Clothing Type By ID and  Gender---------------------------------#
    public function findByTargetGender($target, $gender)
    {
        /*return $this->repo->findBy(
            array('target' => $target, 'gender' => $gender)
        );*/
        return $this->repo->findByTargetGender($target, $gender);
    }

    #-----------------Find By Clothing Type By ID and  Gender---------------------------------#
    public function findByTargetAndGender($target, $gender)
    {
        return $this->repo->findByTargetAndGender($target, $gender);
    }

    public function findDefaultClothing($data){


        $draw = isset ( $data['draw'] ) ? intval( $data['draw'] ) : 0;
        //length
        $length  = $data['length'];
        $length  = $length && ($length!=-1) ? $length : null;
        //limit
        $start   = $data['start'];
        $start   = $length ? ($start && ($start!=-1) ? $start : 0) / $length : 0;
        //order by
        $order   = $data['order'];
        //search data
        $search  = (isset($data['search']) ? $data['search']: "");

        $filters = [
            'query'     => @$search['value'],
        ];


        $finalData = $this->repo->findDefaultClothing($filters, $start, $length, $order);

        $output = array(
            "draw"            => $draw,
            'recordsFiltered' => count($this->repo->findDefaultClothing($filters, 0, false, $order)),
            'recordsTotal'    => count($this->repo->findDefaultClothing(array(), 0, false, $order)),
            'data'            => array()
        );


        foreach ($finalData as $fData) {
            $output['data'][] = [
                'id' => $fData["id"],
                'control_number' => $fData["control_number"],
                'BName' => $fData["BName"],
                'ClothingType' => $fData["cloting_type"],
                'gender' => $fData["gender"],
                'PName' => $fData['name'],
                'description' => $fData['description'],
                'item_name' => $fData['item_name'],
                'country_origin' => $fData['country_origin'],
                'item_details' => $fData['item_details'],
                'care_label' => $fData['care_label'],
                'target' => $fData['target'],
                'created_at' => $fData['created_at']->format('Y-m-d H:i:s'),
                'status'    => ($fData['disabled'] == 1) ? "Disabled" : "Enabled",
                'pstatus'   => ($fData['status']) ? ucfirst($fData['status']) : "Pending"
            ];
        }

        return $output;
    }

    public function findClothingTypeByTarget($clothingTarget = array('top', 'bottom'))
    {
        return $this->repo->findClothingTypeByTarget( $clothingTarget );
    }

    public function getProductByClothingTypeId( $clothing_id)
    {
        return $this->repo->getProductByClothingTypeId( $clothing_id );
    }

    public function checkClothingTypeForDefaultClothing(\LoveThatFit\AdminBundle\Entity\ClothingType $entity)
    {
        if(is_object($entity))
        {
            $reusltArray = $this->repo->checkForDefaultClothing( $entity->getId() );
            if(!empty($reusltArray))
                return 1;
            else
                return 0;
        }

        return 0;
    }
}