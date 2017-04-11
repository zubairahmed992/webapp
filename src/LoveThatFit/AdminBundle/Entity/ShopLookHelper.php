<?php
/**
 * Created by PhpStorm.
 * User: haris.khalique
 * Date: 1/16/2017
 * Time: 7:16 PM
 */

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use LoveThatFit\AdminBundle\Entity\ShopLook;
use Symfony\Component\Yaml\Parser;

class ShopLookHelper
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

    private $container;

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class) {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }

    public function createNew() {
        $class = $this->class;
        $shopLook = new $class();
        return $shopLook;
    }

    public function save($entity,$file,$decoded) {
        $entity->setShopModelImage($this->upload($file['name'],$file['tmp_name']));
        $entity->setName($decoded['name']);
        $entity->setSorting($decoded['sorting']);
        $disabled = 0;
        if(isset($decoded['disabled'])){
            $disabled = 1;
        }
        $entity->setDisabled($disabled);
        $entity->setCreatedAt(new \DateTime('now'));
        $entity->setUpdatedAt(new \DateTime('now'));
        $this->em->persist($entity);
        $this->em->flush();
        return $entity;
        //$shopProduct-


    }


    //-------------- Image Upload ---------------------
    public function upload($filename,$temp_name) {
        $yaml = new Parser();
        $productImageModelPath =  $yaml->parse(file_get_contents('../app/config/image_helper.yml'));
        $target_path = $productImageModelPath['image_category']['shop_look']['original']['dir'];
        //  $target_path = str_replace('\\', '/', getcwd()). '/uploads/ltf/product_models/';
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $ext;
        move_uploaded_file($temp_name, $target_path.$fileName);
        return $fileName;
    }

    #-----------------------------------------------
    public function findAll(){
        return $this->repo->findAllRecord();
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

        $yaml = new Parser();
        $productImageModelPath =  $yaml->parse(file_get_contents('../app/config/image_helper.yml'));
        $directorypath = $productImageModelPath['image_category']['shop_look']['original']['dir'];
        //echo $target_path;die();
        //$directorypath = dirname($target_path).'/';

        foreach ($finalData as $fData) {
            $image_path = '';
            if($fData["shop_model_image"] != ''){
                $image_path = $data['base_path'].$directorypath.$fData["shop_model_image"];
            }
            $output['data'][] = [
                'id'         => $fData["id"],
                'name' => $fData["name"],
                'created_at' => ($fData["created_at"]->format('d-m-Y')),
                'shop_model_image' => $image_path,
                'sorting' => $fData["sorting"],
                'disabled'   => ($fData["disabled"] == 1) ? "Disabled" : "Enable"
            ];

        }
        return $output;
    }


    #-----------------Get all Banner which Parent id is null---------------------------------#
    public function editBannerSorting($sorting_number, $action){
        $result = $this->repo->editBannerSorting($sorting_number, $action);
        return $result;
    }

    #-----------------Get Maximum sorting Number---------------------------------#
    public function maxSortingNumber(){
        $result = $this->repo->maxSortingNumber();
        return $result;
    }

    public function find($id) {
        return $this->repo->find($id);
    }

    public function delete($id) {
        $entity = $this->repo->find($id);
        if ($entity) {
            $yaml = new Parser();
            $productImageModelPath =  $yaml->parse(file_get_contents('../app/config/image_helper.yml'));
            $target_path = $productImageModelPath['image_category']['shop_look']['original']['dir'];
            $model_image = $entity->getShopModelImage();
            $modelimage = $target_path.$model_image;

            if (is_readable($modelimage )){
                @unlink($modelimage );
            }
            $this->em->remove($entity);
            $this->em->flush();
            return array('shop_look' => $entity,
                'message' => 'Shop the Look has been Deleted!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {

            return array('shop_look' => $entity,
                'message' => 'Shop the Look not found!',
                'message_type' => 'warning',
                'success' => false,
            );
        }
    }
}