<?php

namespace LoveThatFit\UserBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use LoveThatFit\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;

class UserAppAccessLogHelper {

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

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class,  Container $container) {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;        
        $this->repo = $em->getRepository($class);
    }

	public function saveLogs($user=null) {
	  $result = $this->findLogsCountByUserId($user);
	  if($result["valCount"] == 0){
		$user_app_access_log = $this->createNew();
		$user_app_access_log->setCreatedAt(new \DateTime('now'));
		$user_app_access_log->setUpdatedAt(new \DateTime('now'));
		$user_app_access_log->setUser($user);
		$user_app_access_log->setUpdatedCount(1);
		return $this->save($user_app_access_log);
	  }else{
		$id = $result["id"];
		$user_app_access_log=$this->find($id);
		$setUpdatedCount = $result["updated_count"]+1;
		$user_app_access_log->setUpdatedCount($setUpdatedCount);
		return $this->save($user_app_access_log);
	  }
	}
  //-------------------------
  public function getAppAccessLogCount($user) {
	$result = $this->findLogsCountByUserId($user);
	return $result["valCount"];
  }
  //-------------------------
  public function save($user_app_access_log) {
	$class = $this->class;
	$this->em->persist($user_app_access_log);
	$this->em->flush();
	return $user_app_access_log;
  }
  //-------------------------Create New Logs --------------------------------------------

  public function createNew() {
	$class = $this->class;
	$user_app_access_log = new $class();
	return $user_app_access_log;
  }
  #------------------------------Find Logs count by user id--------------------------------#
	public function findLogsCountByUserId($user){
	  return $this->repo->findOneByUserId($user);
	}
    #-------------------------------------------------------------------------
    
    public function find($id) {
        return $this->repo->find($id);
    }

    #-------------------------------------------------------------------------
    public function findOneByName($name) {
        return $this->repo->findOneByName($name);
    }
#-------------------------------------------------------------------------
    public function findMaxUserId() {
        return $this->repo->findMaxUserId();
    }
#-------------------------------------------------------------------------


}