<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\AdminBundle\Event\CategoriesEvent;

class CategoriesHelper {

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

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class) {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }

    public function createNew() {
        $class = $this->class;
        $categories = new $class();
        return $categories;
    }

    public function save($entity) {

        $name = $entity->getName();
        $gender = $entity->getGender();
        if($entity->getParentId() == '0'){
            $entity->setParentId(null);
        }

        $msg_array = $this->validateForCreate($entity);
        if ($msg_array == null and $name != null) {
            $entity->setCreatedAt(new \DateTime('now'));
            $entity->setUpdatedAt(new \DateTime('now'));
            $entity->upload();
            $this->em->persist($entity);
            $this->em->flush();
            return array('message' => 'Categories succesfully created.',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );
        } else {
            return $msg_array;
        }
    }

    public function find($id) {
        return $this->repo->find($id);
    }

    public function update($entity) {

        if($entity->getParentId() == '0'){
            $entity->setParentId(null);
        }

        $msg_array = $this->validateForUpdate($entity);
        if ($msg_array == null) {
            $entity->setUpdatedAt(new \DateTime('now'));
            $entity->upload();

            $this->em->persist($entity);
            $this->em->flush();

            return array('message' => 'Categories ' . $entity->getName() . ' succesfully updated!',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );
        } else {
            return $msg_array;
        }
    }

    
    public function delete($id) {
        $entity = $this->repo->find($id);
        $entity_name = $entity->getName();
        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();
            return array('categories' => $entity,
                'message' => 'The Categories ' . $entity_name . ' has been Deleted!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {

            return array('categories' => $entity,
                'message' => 'categories not found!',
                'message_type' => 'warning',
                'success' => false,
            );
        }
    }

    public function getListWithPagination($page_number, $sort) {
        $yaml = new Parser();
        $pagination_constants = $yaml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
        $limit = $pagination_constants["constants"]["pagination"]["limit"];

        $entity = $this->repo->listAllCategories($page_number, $limit, $sort);
        $rec_count = count($this->repo->countAllRecord());
        $cur_page = $page_number;

        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }
        return array('categories' => $entity,
            'rec_count' => $rec_count,
            'no_of_pagination' => $no_of_paginations,
            'limit' => $cur_page,
            'per_page_limit' => $limit,
            'criteriaTop' => $this->countStatistics('Top'),
            'criteriaBottom' => $this->countStatistics('Bottom'),
            'criteriaDress' => $this->countStatistics('Dress'),
             'sort'=>$sort,
        );
    }
    

//-------------------------------------------------------
    private function initialCap($str){
        return str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($str))));
    }
//-------------------------------------------------------    
    public function findOneByName($name) {
        return $this->repo->findOneByName($name);
    }
//-------------------------------------------------------
    public function findClothingTypeByName($name) {
        return $this->repo->findCategoriesByName($name);
    }
    //-------------------------------------------------------
    public function findOneByGenderName($gender, $name) {
        return $this->repo->findOneByGenderName($gender, $name);
    }
    #-----------------------------------------------
    public function findAll(){
        return $this->repo->findAllRecord();
    }

#-----------------Find By Gender---------------------------------#
    public function findByGender($gender){
        return $this->repo->findByGender($gender);
    }

    #-----------------------------------------------------------------#
    public function findById($id){
        return $this->repo->findById($id);
    }

    //-------------------------------------------------------
    //Private Methods    
    //----------------------------------------------------------
    private function validateForCreate($entity) {
        $gender = $entity->getGender();
        $gender_hash = array('m' => 'Male', 'f' => 'Female');
        $clothing_type = $this->findOneByGenderName($entity->getGender(), $entity->getName());
        if ($clothing_type && $clothing_type->getId() != $entity->getId()) {
            return array('message' => 'Category Name already exists for '.$gender_hash[$gender].'!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }
        return;
    }

    //----------------------------------------------------------
    private function validateForUpdate($entity) {
        $gender = $entity->getGender();
        $gender_hash = array('m' => 'Male', 'f' => 'Female');
        $clothing_type = $this->findOneByGenderName($entity->getGender(), $entity->getName());
        if ($clothing_type && $clothing_type->getId() != $entity->getId()) {
            return array('message' => 'Category Name already exists for '.$gender_hash[$gender].'!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }
        return;
    }

#-------------------------getRecordsCountWithCurrentBrandLimit------------------#
    public function getRecordsCountWithCurrentCategoriesLimit($categories){
         return $this->repo->getRecordsCountWithCurrentCategoriesLimit($categories);
    }

#-------------------------------------------------------------------------------#
    public function getArray(){
        $cat_list=$this->repo->findAllRecord();
        $cat_array=array();
        foreach($cat_list as $key=>$value){
            if($value->getGender()=='f'){
                $cat_array['woman'][$value->getId()]=$value->getName();
            }else{
                $cat_array['man'][$value->getId()]=$value->getName();
           }
        }
        return $cat_array;
    }
    
  #-------------------------------------------------------------------------------#
    public function getDescriptionArray($gender=null,$base_path){
        $cat_list=$this->repo->findAllAvailableRecords();
        $cat_array['woman']=array();
        $cat_array['man']=array();
        foreach($cat_list as $key=>$value){
            if($value->getGender()=='f'){
                array_push($cat_array['woman'],array('categories'=>$value->getName(), 'caption'=>$this->getCaptionEncoded($value->getName()), 'image'=> $base_path.$value->getWebPath()));
            }else{
                array_push($cat_array['man'],array('categories'=>$value->getName(), 'caption'=>'caption', 'image'=>  $base_path.$value->getWebPath()));
           }
        }
        return $gender?$cat_array[$gender=='m'?'man':'woman']:$cat_array;        
    }

    #-------------------------------------------------------------------------------#
    private function getCaptionEncoded($caption){
        if($caption == 'tank_knit'){
            $caption = "tank tops";
        }
        if($caption == 'tee_knit'){
            $caption = "tee shirts";
        }
        $caption_start = explode("_",$caption);
        if(count($caption_start) > 1){
            $caption_end = ucfirst($caption_start[0])." ".ucfirst($caption_start[1]);

        }else{
            $caption_end = ucfirst($caption_start[0]);
        }
        return $caption_end;
    }
    
    #-----------------Find By Categories By Gender---------------------------------#
    public function findCategoriesByGender($gender){
        return $this->repo->findCategoriesByGender($gender);
    }

    #-----------------find All Categories---------------------------------#
    public function findAllCategories(){
        return $this->repo->findAllBrandDropdown();
    }

    #-----------------Update Child Category parent_id field---------------------------------#
    public function updateParent($selected_category_id, $id){
        return $this->repo->addParentIdInChild($selected_category_id, $id);
    }

    #-----------------Get all Child Category parent_id field---------------------------------#
    public function getCategoriesTreeViewNew($parent = 0, $spacing = '', $category_tree_array = ''){
        if (!is_array($category_tree_array))
            $category_tree_array = array();

        $result = $this->repo->findAllBrandDropdown($parent);

        if (count($result) > 0) {
            foreach($result as $key => $value){
                $category_tree_array[] = array("id" => $value['id'], "name" => $spacing . $value['name'], "parent_id" => $value['parent_id'], "gender" => $value['gender']);
                $category_tree_array = $this->getCategoriesTreeViewNew($value['id'], $spacing . ' -> ', $category_tree_array);
            }
        }
        return $category_tree_array;
    }


    /*Datatable Grid*/
    public function search($data)
    {
        $draw = isset ( $data['draw'] ) ? intval( $data['draw'] ) : 0;
        //length
        $length  = $data['length'];
        $length  = $length && ($length!=-1) ? $length : 0;
        //limit
        $start   = $data['start'];
        $start   = $length ? ($start && ($start!=-1) ? $start : 0) / $length : 0;
        //order by
        $order   = $data['order'];
        //search data
        $search  = $data['search'];
        $filters = [
            'query' => @$search['value']
        ];



        $finalData = $this->repo->search($filters, $start, $length, $order);

        $output = array(
            "draw"            => $draw,
            'recordsFiltered' => count($this->repo->search($filters, 0, false, $order)),
            'recordsTotal'    => count($this->repo->search(array(), 0, false, $order)),
            'data'            => array()
        );

        /*Fetch the path of the image*/
        $result_new = $this->repo->findAllAvailableRecords();
        $path = '';
        foreach($result_new as $key=>$value){
            if($value->getWebPath() != null){
                $path = $value->getWebPath();
                break;
            }
        }
        $directorypath = dirname($path).'/';

        foreach ($finalData as $fData) {
            $image_path = '';
            if($fData["image"] != ''){
                $image_path = $data['base_path'].$directorypath.$fData["image"];
            }
            $output['data'][] = [
                'id'         => $fData["id"],
                'name' => $fData["name"],
                'gender' => $fData["gender"],
                'created_at' => ($fData["created_at"]->format('d-m-Y')),
                'image' => $image_path,
                'disabled'   => ($fData["disabled"] == 1) ? "Disabled" : "Enable"
            ];

        }
        return $output;
    }

    #-----------------Get all Child Category parent_id field---------------------------------#
    public function getCategoryListForService($base_path, $gender = null){

        $result = $this->repo->findAllBrands($gender);

        /*Fetch the path of the image*/
        $result_new = $this->repo->findAllAvailableRecords();
        $path = '';
        foreach($result_new as $key=>$value){
            if($value->getWebPath() != null){
                $path = $value->getWebPath();
                break;
            }
        }
        $directorypath = dirname($path).'/';
        foreach($result as $key => $value){
            if($result[$key]['image'] != null) {
                $result[$key]['image'] = $base_path.$directorypath.$result[$key]['image'];
            }
        }

        $sortedCategories = array();

        foreach( $result as &$category ){

            if ( !isset( $category['child_category'] ) ){
                // set the children
                $category['child_category'] = array();
                foreach( $result as &$subcategory ){
                    if( $category['id'] == $subcategory['parent_cat_id'] ){
                        $category['child_category'][] = &$subcategory;
                    }
                }
            }
            if($category['parent_cat_id'] == null) {
                $sortedCategories[] = &$category;
            }
        }
        return $sortedCategories;
    }


    #-----------------Get all Child Category parent_id field---------------------------------#
    public function getTopLevelCategory($id){
        $result = $this->repo->getTopLevelCategory($id);
        return $result;
    }

    #-----------------Update Child Category parent_id field---------------------------------#
    public function updateTopLevelCategory($id, $top_level_category){
        if (empty($top_level_category)) {
            return true;
        }
        return $this->repo->addTopLevelInChild($id, $top_level_category[0]['id']);
    }


    #-----------------Get Selected Categories pull from category_product---------------------------------#
    public function getSelectedCategories($id){
        return $this->repo->getSelectedCategories($id);
    }

    #-----------------Get Selected Categories pull from category_product---------------------------------#
    public function saveProductCategories($productId, $getselectedcategories){
        $this->repo->saveProductCategories($productId, $getselectedcategories);
        $entity = $this->em->getRepository('LoveThatFitAdminBundle:Product')->find($productId);
        $entity->setUpdatedAt(new \DateTime('now'));
        $this->em->persist($entity);
        $this->em->flush();

        return true;
    }

}