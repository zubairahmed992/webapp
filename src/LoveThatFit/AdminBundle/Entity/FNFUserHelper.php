<?php
/**
 * Created by PhpStorm.
 * User: haris.khalique
 * Date: 2/21/2017
 * Time: 6:04 PM
 */

namespace LoveThatFit\AdminBundle\Entity;

use LoveThatFit\UserBundle\Entity\User;
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

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class) {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
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
            ))[0];

            return $fnfEntity;
        }

        return false;
    }

    public function setIsAvailable( FNFUser $fnfEntity ){
        $fnfEntity->setIsAvailable(false);
        $this->save( $fnfEntity );

        return $fnfEntity;
    }
}