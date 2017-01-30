<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\AdminBundle\Event\BannerEvent;

class BannerHelper {

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
        $banner = new $class();
        return $banner;
    }

    public function save($entity) {
        $msg_array = null;

        $bannertype = $entity->getBannerType();
        $entity->setParentId(8);

        /* If bannertype id is 1 then pass null values on the following*/
        if($bannertype == '1' ) {
            $entity->setImagePosition(null);
            $entity->setDescription(null);
            $entity->setPriceMin(null);
            $entity->setPriceMax(null);
        }

        /* If bannertype id is 1 then pass null values on the following*/
        if($bannertype == '2' ) {
            $entity->setDescription(null);
            $entity->setPriceMin(null);
            $entity->setPriceMax(null);
        }


        $msg_array = $this->validateForCreate($bannertype);
        if ($msg_array == null and $bannertype != null) {
            $entity->setCreatedAt(new \DateTime('now'));
            $entity->setUpdatedAt(new \DateTime('now'));
            $entity->upload();

            $this->em->persist($entity);
            $this->em->flush();
            return array('message' => 'Banner succesfully created.',
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

    public function findWithCategoryName($id) {
        $result = $this->repo->findWithCategoryName($id);
        return $result;
    }

    public function update($entity) {

        $bannertype = $entity->getBannerType();
        if($entity->getImagePosition() == '0' || $bannertype == '1' ) {
            $entity->setImagePosition(null);
        }
        $msg_array = $this->validateForUpdate($entity);
        if ($msg_array == null) {
            $entity->setUpdatedAt(new \DateTime('now'));
            $entity->upload();

            $this->em->persist($entity);
            $this->em->flush();

            return array('message' => 'Banner ' . $entity->getName() . ' succesfully updated!',
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
        if ($entity) {
            $entity->deleteImages();
            $this->em->remove($entity);
            $this->em->flush();
            return array('banner' => $entity,
                'message' => 'The Banner has been Deleted!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {

            return array('banner' => $entity,
                'message' => 'banner not found!',
                'message_type' => 'warning',
                'success' => false,
            );
        }
    }

    public function getListWithPagination($page_number, $sort) {
        $yaml = new Parser();
        $pagination_constants = $yaml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
        $limit = $pagination_constants["constants"]["pagination"]["limit"];

        $entity = $this->repo->listAllBanner($page_number, $limit, $sort);
        $rec_count = count($this->repo->countAllRecord());
        $cur_page = $page_number;

        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }
        return array('banner' => $entity,
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
    private function validateForCreate($name) {
        if (count($this->findOneByName($name)) > 0) {
            return array('message' => 'banner Name already exists!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }
        return;
    }

//----------------------------------------------------------
    private function validateForUpdate($entity) {
        $clothing_type = $this->findOneByName($entity->getName());
        if ($clothing_type && $clothing_type->getId() != $entity->getId()) {
            return array('message' => 'Banner Name already exists!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }
        return;
    }

    private function countStatistics($target) {
        return $rec_count = count($this->repo->findStatisticsBy($target));
    }

#-------------------------getRecordsCountWithCurrentBrandLimit------------------#
    public function getRecordsCountWithCurrentBannerLimit($banner){
         return $this->repo->getRecordsCountWithCurrentBannerLimit($banner);
    }
#-------------------------------------------------------------------------------#
    public function getArray(){
        $cat_list=$this->repo->findAllRecord();
        $cat_array=array();
        foreach($cat_list as $key=>$value){
            if($value->getGender()=='f'){
                $cat_array['woman'][$value->getId()]=$value->getName();
             //   $cat_array['woman']['target'][$value->getId()]=$value->getTarget();
            }else{
                $cat_array['man'][$value->getId()]=$value->getName();
              //  $cat_array['man']['target'][$value->getId()]=$value->getTarget();
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
                #array_push($cat_array['woman'],array('id'=> $value->getId(), 'clothing_type'=>$value->getName(), 'target'=>$value->getTarget(),'caption'=>'caption', 'image'=>'image url'));
                array_push($cat_array['woman'],array('banner'=>$value->getName(), 'caption'=>$this->getCaptionEncoded($value->getName()), 'image'=> $base_path.$value->getWebPath()));
            }else{
                #$cat_array['man'][$value->getName()]=array('id'=> $value->getId(), 'clothing_type'=>$value->getName(), 'target'=>$value->getTarget(),'caption'=>'caption', 'image'=>'image url');
                array_push($cat_array['man'],array('banner'=>$value->getName(), 'caption'=>'caption', 'image'=>  $base_path.$value->getWebPath()));
           }
        }
        return $gender?$cat_array[$gender=='m'?'man':'woman']:$cat_array;        
    }
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
    
    #-----------------Find By Banner By Gender---------------------------------#
    public function findBannerByGender($gender){
        return $this->repo->findBannerByGender($gender);
    }

    #-----------------find All Banner---------------------------------#
    public function findAllBanner(){
        return $this->repo->findAllBrandDropdown();
    }

    #-----------------Update Child in parent_id field---------------------------------#
    public function updateParent($id, $parentid){

        return $this->repo->addParentIdInChild($id, $parentid);

    }

    #-----------------Get all Child Category parent_id field---------------------------------#
    public function getBannerTreeViewNew($parent = 0, $spacing = '', $category_tree_array = ''){
        if (!is_array($category_tree_array))
            $category_tree_array = array();

        $result = $this->repo->findAllBrandDropdown($parent);

        if (count($result) > 0) {
            foreach($result as $key => $value){
                $category_tree_array[] = array("id" => $value['id'], "name" => $spacing . $value['name'], "parent_id" => $value['parent_id']);
                $category_tree_array = $this->getBannerTreeViewNew($value['id'], $spacing . ' -> ', $category_tree_array);
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
                'created_at' => ($fData["created_at"]->format('d-m-Y')),
                'image' => $image_path,
                'cat_name' => $fData["cat_name"],
                'disabled'   => ($fData["disabled"] == 1) ? "Disabled" : "Enable"
            ];

        }
        return $output;
    }

    #-----------------Get all Child Category parent_id field---------------------------------#
    public function getBannerListForService($base_path, $displayscreen = ''){

        $result_new = $this->repo->findAllAvailableRecords();

        $path = '';
        foreach($result_new as $key=>$value){
            if($value->getWebPath() != null){
                $path = $value->getWebPath();
                break;
            }
        }
        $directorypath = dirname($path).'/';
        $results = $this->repo->findAllBanners($displayscreen);
        foreach($results as $key => $value){

            if($results[$key]['banner_type'] == 1) {
                    $results[$key]['title'] = null;
            }
            if(isset($results[$key]['banner_image'])) {
                if ($results[$key]['banner_image'] != null) {
                    $results[$key]['banner_image'] = $base_path . $directorypath . $results[$key]['banner_image'];
                }
            }
        }

        $fArray = [];
        $a = -1;
        foreach($results as $key=>$result) {

            if($result['parent_id'] == null) {
                $a++;
                $fArray[$a]['banner_type'] = $result['banner_type'];
                $fArray[$a]['display_screen'] = $result['display_screen'];
                $fArray[$a]['sorting'] = $result['sorting'];
                $fArray[$a]['type'] = $result['type'];

                unset($result['parent_id']);
                $fArray[$a]['object'][] = $result;
            }

            foreach($results as $sub_banner) {
                if( $result['id'] == $sub_banner['parent_id'] ){
                    unset($sub_banner['parent_id']);
                    $fArray[$a]['object'][] = $sub_banner;
                }
            }
        }

        return $fArray;
    }

    #-----------------Get all Banner which Parent id is null---------------------------------#
    public function getBannerlist($parent = 0, $spacing = '', $category_tree_array = ''){
        $result = $this->repo->findAllBannerDropdown($parent);
        return $result;
    }

    #-----------------Get all Banner which Parent id is null---------------------------------#
    public function editBannerSorting($sorting_number, $action, $parent_id, $display_screen,$db_banner_sorting = 0){
        $result = $this->repo->editBannerSorting($sorting_number, $action, $parent_id, $display_screen,$db_banner_sorting);
        return $result;
    }

    #-----------------Get Maximum sorting Number---------------------------------#
    public function maxSortingNumber($sorting_number, $parent_id, $display_screen){
        $result = $this->repo->maxSortingNumber($sorting_number, $parent_id, $display_screen);
        return $result;
    }
}