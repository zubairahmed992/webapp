<?php

namespace LoveThatFit\AdminBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;

class EventsManagementHelper {

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
        $clothing_types = new $class();
        return $clothing_types;
    }

    public function save($entity) {
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

    public function find($id) {
        return $this->repo->find($id);
    }

    public function update($entity) {

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

    
    public function delete($id) {
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

    //-------------------------------------------------------    
    public function findOneByName($name) {
        return $this->repo->findOneByName($name);
    }
    
    #-----------------------------------------------
    public function findAll(){
        return $this->repo->findAllRecord();
    }
    #-----------------------------------------------------------------#
    public function findById($id){
        return $this->repo->findById($id);      
    }

    //-------------------------------------------------------
    //Private Methods    
    //----------------------------------------------------------
    private function validateForCreate($name) {
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
    private function validateForUpdate($entity) {
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

        foreach ($finalData as $fData) {
            $output['data'][] = [ 
                'id'         => $fData["id"],
                'event_name' => $fData["event_name"],
                'disabled'   => ($fData["disabled"] == 1) ? "Active" : "Inactive",
                'created_at' => ($fData["created_at"]->format('d-m-Y'))
            ];
            
        }
        return $output;
    }
}