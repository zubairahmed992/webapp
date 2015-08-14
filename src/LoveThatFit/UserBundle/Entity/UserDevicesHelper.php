<?php

namespace LoveThatFit\UserBundle\Entity;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;
use LoveThatFit\UserBundle\Entity\User;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\UserBundle\Event\UserEvent;
use LoveThatFit\UserBundle\Entity\Measurement;
use LoveThatFit\AdminBundle\Entity\SizeChart;
use Symfony\Component\HttpFoundation\Request;

class UserDevicesHelper {

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

    public function saveUserDevices(UserDevices $user_device) {
        $user_device->setUpdatedAt(new \DateTime('now'));            
       $this->em->persist($user_device);
        $this->em->flush();
    }
//-------------------------------------------------------

    public function createNew($user=null) {      
        $class = $this->class;
        $user_device = new $class();
        $user_device->setUser($user);
        $user_device->setCreatedAt(new \DateTime('now'));
        $user_device->setUpdatedAt(new \DateTime('now'));            
        return $user_device;
    }

#-------------- Find Heightper inch base on user id and device type------------#
    public function findHeightPerInchRatio($deviceType,$userId){
        return $this->repo->findHeightPerInchRatio($deviceType,$userId);
    }
 #--------------- Find Device Type base on user id ---------------------------#   
    public function findDeviceTypeBaseOnUserId($userId){
    return $this->repo->findDeviceTypeBaseOnUserId($userId);
    }
    #------------------------------------
    public function findOneByDeviceTypeAndUser($user_id, $device_type){
        return $this->repo->findOneBy(array('user' => $user_id, 'deviceType' => $device_type));
    }
 
    #-------------------------------------------------------------------------
    
    public function updateDeviceDetails($user, $device_type, $height_per_inch){
        $device = $this->repo->findOneBy(array('user' => $user->getId(), 'deviceType' => $device_type));
        if(!$device){
            $device=$this->createNew($user);
            $device->setDeviceType($device_type);
        }
        $device->setDeviceUserPerInchPixelHeight($height_per_inch);
        $this->saveUserDevices($device);
        return $device;
   } 


}