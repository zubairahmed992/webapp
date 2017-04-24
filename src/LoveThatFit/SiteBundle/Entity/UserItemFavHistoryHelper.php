<?php

namespace LoveThatFit\SiteBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use \Symfony\Component\EventDispatcher\Event;

class UserItemFavHistoryHelper
{

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

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, Container $container)
    {
        $this->container  = $container;
        $this->dispatcher = $dispatcher;
        $this->em         = $em;
        $this->class      = $class;
        $this->repo       = $em->getRepository($class);

    }

    public function testFunction()
    {
        return "test function in user item fav history helper";
    }

    //-------------------------------------------------------
    public function createNew()
    {
        $class              = $this->class;
        $userItemTryHistory = new $class();
        return $userItemTryHistory;
    }

    //-------------------------------------------------------

    public function save($entity)
    {

        $this->em->persist($entity);
        $this->em->flush();

    }

    #------------------------------------------------------
    public function find($id)
    {
        return $this->repo->find($id);
    }

    #--------------------Site Bundle Refactoring--------------------/
    public function createUserItemFavHistory($user, $p, $items, $status, $page)
    {

        $useritemtryhistory = new UserItemFavHistory();
        $useritemtryhistory->setUser($user);
        $useritemtryhistory->setProduct($p);
        $useritemtryhistory->setProductitem($items);
        $useritemtryhistory->setStatus($status);
        if ($page != null) {
            $useritemtryhistory->setPage($page);
        }
        $this->save($useritemtryhistory);
        return true;
    }

    public function countUserItemFavHistory($user, $product, $productItem)
    {
        $entity    = $this->repo->findUserItemAllFavHistory($user, $product, $productItem);
        $rec_count = count($this->repo->findUserItemAllFavHistory($user, $product, $productItem));
        return $rec_count;
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
            $image_path = '';
            if($fData["image"] != ''){
                $image_path = $data['base_path'] ."/uploads/ltf/products/fitting_room/web/" . $fData["image"];
            }
            $output['data'][] = [ 
                'image'        => $image_path,
                'email'        => $fData["email"],
                'product_name' => $fData["name"],
                'price'        => $fData["price"],
                'size'         => $fData["size"],
                'color'        => $fData["color"],
                'status'       => ($fData["status"] == 0) ? "dislike" : "like",
                'page'         => $fData["page"],
                'created_at'   => ($fData["created_at"]->format('d-m-Y')),
            ];
        }
        return $output;
    }

    public function findFavoriteList()
    {
        return $this->repo->findFavoriteList();
    }

}
