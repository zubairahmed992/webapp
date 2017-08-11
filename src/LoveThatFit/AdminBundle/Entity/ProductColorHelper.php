<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\AdminBundle\Event\productcolorEvent;

class ProductColorHelper {

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

    public function find($id) {
        return $this->repo->find($id);
    }

    public function findColorByProductTitle($title, $productid) {
        return $this->repo->findColorByProductTitle($title, $productid);
    }

    public function getSizeItemImageUrlArray($id) {
        return $this->repo->getSizeItemImageUrlArray($id);
    }

    public function getSizeArray($id) {
        return $this->repo->getSizeArray($id);
    }

    public function uploadSave($entity) {

        $entity->saveImage(); //----- file move from temp to permanent folder
        $entity->savePattern(); //----- file upload method 
        $this->save($entity);

        return array('message' => 'Product Color succesfully created.',
            'message_type' => 'success',
            'status' => true,
        );
    }

    public function save($entity) {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function findColorByProduct($id)
    {
        return $this->repo->findColorByProduct($id);
    }

    public function getDistinctColors()
    {
        $colors = $this->repo->findUniqueColor();
        $colors_indexed = [];
        foreach ($colors as $key => $val) {
            $colors_indexed[] = strtolower(trim($val['title']));
        }
        $colors = array_unique(array_values($colors_indexed));
        foreach ($colors as $key => $val) {
            $colors[$key] = array('title' => $val);
        }
        return $colors;
    }

    public function getProductColorIDsByTitle($title)
    {
        return $this->repo->findProductColorIDsByTitle($title);
    }
}