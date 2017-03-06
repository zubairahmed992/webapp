<?php
/**
 * Created by PhpStorm.
 * User: haris.khalique
 * Date: 3/1/2017
 * Time: 4:54 PM
 */

namespace LoveThatFit\AdminBundle\Entity;

use LoveThatFit\AdminBundle\Entity\AdminConfig;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class FNFGroupHelper
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
        $fnfGroup = new $class();
        return $fnfGroup;
    }

    public function save($entity) {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function getGroups()
    {
        return $this->repo->findBy(array('isArchive' => 0));
    }

    public function addNewGroup( $groupData = array())
    {
        if(!empty( $groupData )){
            $groupEntity = $this->createNew();
            $groupEntity->setDiscount($groupData['discount']);
            $groupEntity->setGroupTitle($groupData['groupTitle']);
            $groupEntity->setMinAmount($groupData['min_amount']);
            $groupEntity->setStartAt( new \DateTime($groupData['start_at']));
            $groupEntity->setEndAt( new \DateTime($groupData['end_at']));

            $this->save( $groupEntity );
            return $groupEntity;
        }
    }

    public function findById( $id )
    {
        return $this->repo->findOneBy( array( 'id' => $id ));
    }

    public function countAllFNFGroupRecord()
    {
        return $this->repo->countAllFNFGroupRecord();
    }

    public function searchFNFGroup( $data )
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
            'query'     => @$search['value'],
        ];

        $finalData = $this->repo->searchFNFGroups($filters, $start, $length, $order);

        $output = array(
            "draw"            => $draw,
            'recordsFiltered' => count($this->repo->searchFNFGroups($filters, 0, false, $order)),
            'recordsTotal'    => count($this->repo->searchFNFGroups(array(), 0, false, $order)),
            'data'            => array()
        );

        foreach ($finalData as $fData) {
            $output['data'][] = [
                'id' => $fData["id"],
                'groupTitle' => $fData["groupTitle"],
                'discount' => $fData["discount"],
                'min_amount' => $fData["min_amount"],
            ];
        }

        return $output;
    }

    public function getGroupDataById( $groupId )
    {
        $data = $this->repo->getGroupDataById( $groupId );
        $returnArray = array();

        foreach ($data as $row){
            $returnArray['discount']            = $row['discount'];
            $returnArray['min_amount']          = $row['min_amount'];
            $temp['id']                         = $row['id'];
            $temp['email']                      = $row['email'];
            $returnArray['users'][]             = $temp;
        }

        return $returnArray;
    }

    public function getAllGroupUsers(FNFGroup $group)
    {
        $data = $this->repo->getGroupDataById( $group->getId() );
        $returnArray = array();

        foreach ($data as $row){
            $returnArray[]             = $row['id'];
        }

        return $returnArray;
    }

    public function removeFNFUsers( FNFGroup $group, FNFUser $fnfUser){

        var_dump( $fnfUser ); die;

        $group->removeFnfUser( $fnfUser );
        $this->em->persist( $group );
        $this->em->flush();
    }

    public function markedGroupAsArchived( FNFGroup $group)
    {
        $group->setIsArchive( true );
        $this->save( $group );

        return $group;
    }

}