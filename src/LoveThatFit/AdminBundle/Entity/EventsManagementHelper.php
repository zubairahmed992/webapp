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
        $eventsManagement = new $class();
        return $eventsManagement;
    }

    public function save($entity, $data)
    {
        $msg_array = $this->validateForCreate($data['event_name']);
        if ($msg_array == null) {
            if (!isset($data["disabled"])) {
                $entity->setDisabled(0);
            }else {
                $entity->setDisabled($data["disabled"]);
            }
            $entity->setEventName($data["event_name"]);
            $entity->setCreatedAt(new \DateTime('now'));
            $entity->setUpdatedAt(new \DateTime('now'));

            $this->em->persist($entity);
            $this->em->flush();
            return array('message' => 'Event succesfully created.',
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

    public function update($entity, $data)
    {
        $msg_array = $this->validateForUpdate($entity);
        if ($msg_array == null) {
            $entity->setUpdatedAt(new \DateTime('now'));
            $this->em->persist($entity);
            $this->em->flush();

            return array('message' => 'Event updated succesfully updated!',
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
        $this->em->remove($entity);
        $this->em->flush();
        return array('eventsManagement' => $entity,
            'message' => 'The Event has been Deleted!',
            'message_type' => 'success',
            'success' => true,
        );
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
    private function validateForCreate($event_name)
    {
        if (count($this->findByEventName($event_name)) > 0) {
            return array('message' => 'The Event Name already exists!',
                'field' => 'event_name',
                'message_type' => 'warning',
                'success' => false,
            );
        }
        return;
    }

//----------------------------------------------------------
    private function validateForUpdate($entity)
    {
        $events = $this->findByEventName($entity->getEventName());
        if ($events && $events[0]['id'] != $entity->getId()) {
            return array('message' => 'Clothing Type Name already exists!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }
        return;
    }

    public function findByEventName($name) {
        return $this->repo->findByEventName($name);
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
                'disabled'   => ($fData["disabled"] == 1) ? "Disabled" : "Enable",
                'created_at' => ($fData["created_at"]->format('d-m-Y'))
            ];
            
        }
        return $output;
    }
}