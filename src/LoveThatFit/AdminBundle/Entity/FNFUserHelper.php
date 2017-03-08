<?php
/**
 * Created by PhpStorm.
 * User: haris.khalique
 * Date: 2/21/2017
 * Time: 6:04 PM
 */

namespace LoveThatFit\AdminBundle\Entity;

use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\AdminBundle\Entity\AdminConfig;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class FNFUserHelper
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

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, Container $container) {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
        $this->container = $container;
    }

    public function createNew() {
        $class = $this->class;
        $fnfuser = new $class();
        return $fnfuser;
    }

    public function save($entity) {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function getApplicableFNFUser( User $user ){
        if(is_object($user)){
            $user_id = $user->getId();
            return $this->repo->getApplicableUserForDiscount( $user_id );
        }

        return false;
    }

    public function getFNFUserById ( User $user ){
        if(is_object($user)){
            $fnfEntity = $this->repo->findBy(array(
                'users' => $user->getId(),
                'is_available' => 1
            ));
            if(is_array($fnfEntity) && !empty($fnfEntity)){
                return $fnfEntity[0];
            }
            return false;
        }

        return false;
    }

    public function saveFNFUser(FNFUser $FNFUser)
    {
        $this->save( $FNFUser);
        return $FNFUser;
    }

    public function removeFNFUser( FNFUser $FNFUser)
    {
        $this->em->remove( $FNFUser );
        $this->em->flush();

        return;
    }

    public function setIsAvailable( FNFUser $fnfEntity ){
        $fnfEntity->setIsAvailable(false);
        $this->save( $fnfEntity );

        return $fnfEntity;
    }

    public function countAllFNFUserRecord()
    {
       return $this->repo->countAllFNFUserRecord();
    }

    public function searchFNFUser( $data )
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

        $finalData = $this->repo->searchFNFUser($filters, $start, $length, $order);

        $output = array(
            "draw"            => $draw,
            'recordsFiltered' => count($this->repo->searchFNFUser($filters, 0, false, $order)),
            'recordsTotal'    => count($this->repo->searchFNFUser(array(), 0, false, $order)),
            'data'            => array()
        );

        foreach ($finalData as $fData) {
            $output['data'][] = [
                'id' => $fData["id"],
                'fnfid' => $fData["fnfid"],
                'group_id' => $fData["group_id"],
                'full_name' => ($fData["firstName"] . ' ' . $fData["lastName"]),
                'email' => $fData["email"],
                'group_title' => $fData['group_title'],
                'discount' => $fData["discount"],
                'availability' => ($fData["is_available"]) ? "available" : "not available",
                'original_user_id' => $fData["original_user_id"]
            ];
        }

        return $output;
    }

    public function findById( $id )
    {
        if($id)
        {
            $fnfUserEnttity = $this->repo->find( $id );
            return $fnfUserEnttity;
        }

        return false;
    }

    public function saveFNFUsers( FNFGroup $fnfGroup, $users = array())
    {
        if(is_object($fnfGroup))
        {

            $groupUsers = $this->container->get('fnfgroup.helper.fnfgroup')->getAllGroupUsers( $fnfGroup );
            $groupUsers = array_diff($groupUsers, $users);


            foreach($users as $user){
                $userEntity = $this->container->get('user.helper.user')->find( $user );
                if($userEntity){
                    if($this->checkIfUserExists($userEntity, $fnfGroup) == null)
                    {
                        
                        $fnfuserEntity = $this->repo->findOneBy(array('users'=>$user));
                        if( $fnfuserEntity == NULL){
                            $fnfuserEntity = $this->createNew();
                        }


                        $fnfuserEntity->addGroup($fnfGroup);
                        $fnfuserEntity->setUsers( $userEntity );

                        $this->save( $fnfuserEntity );
                    }
                    $this->removeUsers($fnfGroup, $groupUsers);
                    unset( $fnfuserEntity );
                }
            }

            return true;
        }

        return false;
    }

    private function checkIfUserExists( User $user, FNFGroup $FNFGroup)
    {
        $findEntity = $this->repo->checkUserInGroup( $FNFGroup->getId(), $user->getId());
        return $findEntity;
    }

    public function removeUsers( FNFGroup $group, $usersToRemove )
    {
        foreach($usersToRemove as $user)
        {
            $findEntity = $this->repo->checkUserInGroup( $group->getId(), $user);
            $fnfUser = $this->repo->findOneBy(array('id' => $findEntity['fnfid']));

            if(is_object($fnfUser)){
                $fnfUser->removeGroup($group);
                $this->em->persist( $fnfUser );
                $this->em->flush();
            }

            // $this->container->get('fnfgroup.helper.fnfgroup')->removeFNFUsers( $group, $fnfUser);
        }

        return;
    }
}