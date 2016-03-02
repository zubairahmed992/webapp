<?php

namespace LoveThatFit\UserBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\UserBundle\Event\UserEvent;
use LoveThatFit\UserBundle\Entity\User;
use LoveThatFit\AdminBundle\Entity\SizeChart;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;

class UserArchivesHelper {

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

    #-------------------------------------------------------------------------

	public function saveArchives($user_archives,$data) {
	  if (array_key_exists('measurement', $data)){
	 	 $user_archives->setMeasurementJson($data['measurement']);
	  }
	  if (array_key_exists('image_actions', $data)){
		$user_archives->setImageActions($data['image_actions']);
	  }
	  if (array_key_exists('marker_params', $data)){
		$user_archives->setMarkerParams($data['marker_params']);
	  }
	  if (array_key_exists('svg_paths', $data)){
		$user_archives->setSvgPaths($data['svg_paths']);
	  }
	  if (array_key_exists('marker_json', $data)){
		$user_archives->setMarkerJson($data['marker_json']);
	  }
	  if (array_key_exists('default_marker_svg', $data)){
		$user_archives->setDefaultMarkerSvg($data['default_marker_svg']);
	  }
		return $this->save($user_archives);
	}

    public function find($id) {
        return $this->repo->find($id);
    }
#-------------------------------------------------------------------------
   public function createNew($user) {
	 $user_archives = new $this->class();
	 $user_archives->setUser($user);
	 $user_archives->setStatus('-1');
	 $user_archives->setCreatedAt(new \DateTime('now'));
	 $user_archives->setUpdatedAt(new \DateTime('now'));
     return $user_archives;
    }

   public function save(UserArchives $user_archives) {
	 $user_archives->setUpdatedAt(new \DateTime('now'));
	 $this->em->persist($user_archives);
	 $this->em->flush();
	 return $user_archives;
 }

    
}