<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;


class ProductImageHelper {

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
        $roductimage = new $class();
        return $roductimage;
    }

    public function save($entity,$file,$product,$decoded) {      
          foreach ($file['name'] as $key => $value) {          
            $entity->setProduct($product);
            $entity->setImage($this->upload($file['name'][$key],$file['tmp_name'][$key]));
            $entity->setImageTitle($decoded['image_title'][$key]);
            $entity->setImageSort($decoded['image_sort'][$key]);
            $entity->getProduct()->setUpdatedAt(new \DateTime('now'));
            $this->em->persist($entity);
            $this->em->flush();
            }  
            return array('message' => 'Prodcut images succesfully uploaded.',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );       
    }

    //-------------- Image Upload ---------------------
    public function upload($filename,$temp_name) {
        $yaml = new Parser();
        $productImageModelPath =  $yaml->parse(file_get_contents('../app/config/image_helper.yml'));
        $target_path = $productImageModelPath['image_category']['product_models']['original']['dir'];
      //  $target_path = str_replace('\\', '/', getcwd()). '/uploads/ltf/product_models/';
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $ext;
        move_uploaded_file($temp_name, $target_path.$fileName);
    return $fileName;
    }
    
    public function findByProductId($id) {
        return $this->repo->findByProductId($id);
    }
    
    public function find($id) {
        return $this->repo->find($id);
    }
    
     public function delete($id) {

        $entity = $this->repo->find($id);
        if ($entity) {
            $entity->getProduct()->setUpdatedAt(new \DateTime('now'));
            $this->em->remove($entity);
            $this->em->flush();
            return array('productimage' => $entity,
                'message' => 'The Product Model Recod has been Deleted!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {

            return array('productimage' => $entity,
                'message' => 'product images not found!',
                'message_type' => 'warning',
                'success' => false,
            );
        }
    }
    
     public function update($entity) {
         $entity->getProduct()->setUpdatedAt(new \DateTime('now'));
        $this->em->persist($entity);    
        $this->em->flush();
        return array('message' => 'Product Modle Images succesfully updated!',
            'field' => 'all',
            'message_type' => 'success',
            'success' => true,
        );        
    }
    
    
}