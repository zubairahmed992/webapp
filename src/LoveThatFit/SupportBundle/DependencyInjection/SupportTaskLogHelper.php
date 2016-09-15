<?php

namespace LoveThatFit\SupportBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;


class SupportTaskLogHelper {

     /**
     * Holds the Symfony2 event dispatcher service
     */
    protected $dispatcher;
     /**
     * Holds the Doctrine entity manager for database interaction
     * @var EntityManager 
     */
    protected $em;

    /**
     * Entity-specific repo, useful for finding entities, for example
     * @var EntityRepository
     */
    protected $repo;

    /**
     * The Fully-Qualified Class Name for our entity
     * @var string
     */
    protected $class;

    private $container;
    
   public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, Container $container){
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }
    
//-------------------------------------------------------
    public function createNew() {
        $class = $this->class;
        $supportTaskLog = new $class();

        return $supportTaskLog;
    }   
//-------------------------------------------------------    
    public function fill($stl_obj, $stl){
        
        array_key_exists('member_email', $stl)?$stl_obj->setMemberEmail($stl['member_email']):'';
        array_key_exists('support_user_name', $stl)?$stl_obj->setSupportUserName($stl['support_user_name']):'';
        array_key_exists('duration', $stl)?$stl_obj->setDuration($stl['duration']):'';
        array_key_exists('log_type', $stl)?$stl_obj->setLogType($stl['log_type']):'';
        array_key_exists('start_time', $stl)?$stl_obj->setStartTime($stl['start_time']):'';
        array_key_exists('end_time', $stl)?$stl_obj->setEndTime($stl['end_time']):'';

        return $stl_obj;
    }
   
//-------------------------------------------------------
    public function saveAsNew($stl_array) {
        $end_time   = date("Y-m-d H:i:s");
        $start_time = date("Y-m-d H:i:s", 
            strtotime($end_time) - $stl_array['duration']
        );
        $entity=$this->fill($this->createNew(), $stl_array);
        $entity->setStartTime(new \DateTime($start_time));
        $entity->setEndTime(new \DateTime($end_time));
        $entity->setCreatedAt(new \DateTime('now'));
        $entity->setSupportAdminUser($stl_array['supportUsers']);
        $entity->setArchive($stl_array['archive']);

        $this->save($entity);            
    }
//-------------------------------------------------------
    
    public function save($entity) {        
            $this->em->persist($entity);
            $this->em->flush();
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

        $a = 1;

        foreach ($finalData as $fData) {
            $output['data'][] = [ 
                'Sno'       => $a,
                'user_name' => $fData["user_name"],
                'log_type'  => $fData["log_type"],
                'slow'      => gmdate("H:i:s", $fData["slow"]),
                'fast'      => gmdate("H:i:s", $fData["fast"]),
                'avrg'      => gmdate("H:i:s", number_format($fData["avrg"], 2, '.', ',')),
                'total'     => $fData["total"],
                'userid'    => $fData["id"]
            ];

            $a++;
        }

        return $output;
    }

    public function findSupprtUser($id)
    {
        $data = $this->repo->findSupprtUser($id);
        if (!empty($data[0])) {
            $above_avg = $this->repo->findAboveAverage($id, $data[0]['avrg'], $data[0]['slow']);
            $data[0]['above_avg'] = isset($above_avg[0]['above_avg']) ? $above_avg[0]['above_avg'] : 0;

            $below_avg = $this->repo->findBelowAverage($id, $data[0]['avrg'], $data[0]['fast']);
            $data[0]['below_avg'] = isset($below_avg[0]['below_avg']) ? $below_avg[0]['below_avg'] : 0;
        }
        return $data;
    }

    public function showSearch($data)
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
        $userid   = $data['userid'];

        $finalData = $this->repo->showSearch(
                $filters,
                $start,
                $length,
                $order,
                $userid
            );
        
        $output = array( 
            "draw"            => $draw,
            'recordsFiltered' => count($this->repo->showSearch(
                    $filters,
                    0,
                    false,
                    $order,
                    $userid
                )
            ), 
            'recordsTotal'    => count($this->repo->showSearch(
                    array(),
                    0,
                    false,
                    $order,
                    $userid
                )
            ),
            'data'            => array()
        );

        $a = 1;
        foreach ($finalData as $fData) {
            $output['data'][] = [ 
                'Sno'          => $a,
                'log_type'     => $fData["log_type"],
                'member_email' => $fData["member_email"],
                'date'         => ($fData["start_time"] == "") ? "00-00-0000" : ($fData["start_time"]->format('m-d-Y')),
                'start_time'   => ($fData["start_time"] == "") ? "00:00:00" : $fData["start_time"]->format('h:i:s') ,
                'end_time'     => ($fData["end_time"] == "") ? "00:00:00" : $fData["end_time"]->format('h:i:s') ,
                'duration'     => $fData["duration"]
            ];

            $a++;
        }

        return $output;
    }

    public function saveAssignPendingUsers($data)
    {
        $entity=$this->fill($this->createNew(), $data);
        $entity->setLogType("calibration");
        $entity->setCreatedAt(new \DateTime('now'));
        $entity->setSupportAdminUser($data['supportUsers']);
        $entity->setArchive($data['archive']);

        $this->save($entity);
    }

    public function findByAssingnedIdMemberEmail($archive, $member_email)
    {
        return $this->repo->findByAssingnedIdMemberEmail($archive, $member_email);
    }

    public function UnAssignPendingUsers($data)
    {
        $decode = $this->repo->findByAssingnedIdMemberEmail(
                $data['archive'],
                $data['member_email']
            );
        if (!empty($decode)) {
            $entity = $this->repo->find($decode[0]['id']);
            if ($entity) {
                $this->em->remove($entity);
                $this->em->flush();
                return "success";
            }
        }
    }

    public function findByAssingnedIdSupportIDMemberEmail(
        $archive,
        $support_admin_user,
        $member_email
    ) {
        return $this->repo->findByAssingnedIdSupportIDMemberEmail(
            $archive,
            $support_admin_user,
            $member_email
        );
    }
    
    public function update($data)
    {
        $end_time   = date("Y-m-d H:i:s");
        $start_time = date("Y-m-d H:i:s", 
            strtotime($end_time) - $data['duration']
        );

        $entity = $this->repo->find($data['id']);
        $entity->setSupportUserName($data['support_user_name']);
        $entity->setDuration($data['duration']);
        $entity->setLogType($data['log_type']);
        $entity->setStartTime(new \DateTime($start_time));
        $entity->setEndTime(new \DateTime($end_time));
        
        $this->em->persist($entity);
        $this->em->flush();
    }
}