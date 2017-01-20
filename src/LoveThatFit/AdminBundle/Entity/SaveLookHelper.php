<?php
/**
 * Created by PhpStorm.
 * User: haris.khalique
 * Date: 1/16/2017
 * Time: 7:16 PM
 */

namespace LoveThatFit\AdminBundle\Entity;

use LoveThatFit\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use LoveThatFit\AdminBundle\Entity\SaveLook;

class SaveLookHelper
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
        $saveLook = new $class();
        return $saveLook;
    }

    public function save($entity) {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function uploadUserLook( $user )
    {
        $saveLook =  $this->createNew();
        $saveLook->setUsers( $user );

        if (array_key_exists("image", $_FILES)) {
            $saveLook->file = $_FILES["image"];
        } else {
            return array(
                'isFileExists' => false,
                'image'         => ""
            );
        }

        return array(
            'isFileExists' => true,
            'image'         => $saveLook->upload()
        );
    }

    public function addItem($saveLookImage = null, User $users) {
        $saveLookObj = new SaveLook();
        $saveLookObj->setUsers($users);
        $saveLookObj->setUserLookImage($saveLookImage);
        $this->save($saveLookObj);

        return $saveLookObj;
    }

    public function getLooksByUserId( $user_id)
    {
        return $this->repo->getAllUserSaveLooks( $user_id );
    }

    public function findByLookId( $look_id )
    {
        return $this->repo->find( $look_id );
    }

    public function removeUserLook( $saveLookEntity, $user = null )
    {
        $saveLookObj = new SaveLook();
        $saveLookObj->setUsers($user);

        $saveLookObj->deleteImages( $saveLookEntity->getUserLookImage() );

        $this->em->remove( $saveLookEntity );
        $this->em->flush();
    }
}