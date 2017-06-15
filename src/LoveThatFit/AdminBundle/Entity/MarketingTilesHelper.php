<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\AdminBundle\Event\MarketingTilesEvent;

class MarketingTilesHelper {

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
        $marketing_tiles = new $class();
        return $marketing_tiles;
    }

    public function save($entity) {
        $entity->setCreatedAt(new \DateTime('now'));
        $entity->setUpdatedAt(new \DateTime('now'));
        $entity->upload();
        $this->em->persist($entity);
        $this->em->flush();
        return array('message' => 'Marketing Tiles succesfully created.',
            'field' => 'all',
            'message_type' => 'success',
            'success' => true,
        );
    }

    public function find($id) {
        return $this->repo->find($id);
    }

    public function findWithMarketingTilesId($id) {
        $result = $this->repo->findWithMarketingTilesId($id);
        return $result;
    }

    public function update($entity) {
        $entity->setUpdatedAt(new \DateTime('now'));
        $entity->upload();
        $this->em->persist($entity);
        $this->em->flush();
        return array('message' => 'Marketing Tiles ' . $entity->getTitle() . ' succesfully updated!',
            'field' => 'all',
            'message_type' => 'success',
            'success' => true,
        );
    }

    
    public function delete($id) {
        $entity = $this->repo->find($id);
        if ($entity) {
            $entity->deleteImages();
            $this->em->remove($entity);
            $this->em->flush();
            return array('marketing_tiles' => $entity,
                'message' => 'The Marketing Tiles has been Deleted!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {
            return array('marketing_tiles' => $entity,
                'message' => 'Marketing Tiles not found!',
                'message_type' => 'warning',
                'success' => false,
            );
        }
    }

    #-----------------------------------------------
   public function findAll(){
        return $this->repo->findAllRecord();      
    }

    #-----------------------------------------------
   public function findMarketingTiles(){
        return $this->repo->findMarketingTiles();
    }

   #-----------------------------------------------------------------#
    public function findById($id){
        return $this->repo->findById($id);      
        
    }

#-------------------------getRecordsCountWithCurrentBrandLimit------------------#
    public function getRecordsCountWithCurrentMarketingTilesLimit($marketing_tiles){
         return $this->repo->getRecordsCountWithCurrentMarketingTilesLimit($marketing_tiles);
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
                'title' => $fData["title"],
                'button_title' => $fData["button_title"],
                'button_action' => $fData["button_action"],
                'created_at' => ($fData["created_at"]->format('d-m-Y')),
                'image' => $image_path,
                'disabled'   => ($fData["disabled"] == 1) ? "Disabled" : "Enable"
            ];

        }
        return $output;
    }

    #-----------------Get all Banner which Parent id is null---------------------------------#
    public function editBannerSorting($sorting_number, $action,$db_banner_sorting = 0){
        $result = $this->repo->editBannerSorting($sorting_number, $action, $db_banner_sorting);
        return $result;
    }

    #-----------------Get Maximum sorting Number---------------------------------#
    public function maxSortingNumber(){
        $result = $this->repo->maxSortingNumber();
        return $result;
    }
}