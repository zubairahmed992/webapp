<?php

namespace LoveThatFit\SiteBundle\Entity;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class VisitorHelper {

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

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, Container $container) {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }

//-------------------------------------------------------
    public function createNew() {
        $class = $this->class;
        $visitor = new $class();
        return $visitor;
    }

    //-------------------------------------------------------

    public function save($entity) {
        $this->em->persist($entity);
        $this->em->flush();
    }

#------------------------------------------------------

    public function find($id) {
        return $this->repo->find($id);
    }
#------------------------------------------------------

    public function findOneByEmail($email) {
        return $this->repo->findOneByEmail($email);
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
                'id'        => $fData["id"],
                'email'        => $fData["email"],
                'ip_address' => $fData["ip_address"],                
                'created_at'   => ($fData["created_at"]->format('d-m-Y')),
            ];
        }
        return $output;
    }

     public function findvisitorsList()
    {
        return $this->repo->findvisitorsList();
    }


}

