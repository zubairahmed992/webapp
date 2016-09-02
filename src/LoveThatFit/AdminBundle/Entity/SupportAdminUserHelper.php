<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Security\Core\SecurityContext;
class SupportAdminUserHelper {

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

  public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class , Container $container) {
	$this->dispatcher = $dispatcher;
    $this->container = $container;
	$this->em = $em;
	$this->class = $class;
	$this->repo = $em->getRepository($class);
  }

  //---------------------------------------------------------------------

  public function createNew() {
	$class = $this->class;
	$support = new $class();
	return $support;
  }

//-------------------------------------------------------
	public function saveSupportUsers($supportUser,$data) {
		$password = $this->encodeThisPassword($supportUser,$data["password"]);
		$supportUser->setPassword($password);
		$supportUser->setUserName($data["user_name"]);
		$supportUser->setEmail($data["email"]);
		$supportUser->setRoleName($data["role_name"]);
		$this->save($supportUser);
	}
//-------------------------------------------------------




  public function save($entity) {
	$msg_array =null;
	if ($msg_array == null) {
	  $entity->setCreatedAt(new \DateTime('now'));
	  $entity->setUpdatedAt(new \DateTime('now'));
	  $this->em->persist($entity);
	  $this->em->flush();
	  return array('message' => 'Support User succesfully created.',
		'field' => 'all',
		'message_type' => 'success',
		'success' => true,
	  );
	} else {
	  return $msg_array;
	}
  }

  //-------------------------------------------------------

  public function update($entity,$data) {
	$msg_array = $this->validateForUpdate($entity);
	  if ($msg_array["success"] == "no") {
	  $entity->setUpdatedAt(new \DateTime('now'));
	  $entity->setEmail($data["email"]);
	  $entity->setRoleName($data["role_name"]);
	  $this->em->persist($entity);
	  $this->em->flush();

	  return array('message' => 'Support user ' . $entity->getUserName() . ' succesfully updated!',
		'field' => 'all',
		'message_type' => 'success',
		'success' => true,
	  );
	} else {
		  return $msg_array;
	}
  }

//-------------------------------------------------------

	public function changePassword($entity,$password) {

		$entity->setUpdatedAt(new \DateTime('now'));
		$password = $this->encodeThisPassword($entity,$password);
		$entity->setPassword($password);
		$this->em->persist($entity);
		$this->em->flush();

		return array('message' => 'Password changed succesfully!',
			'field' => 'all',
			'message_type' => 'success',
			'success' => true,
		);
	}


//-------------------------------------------------------



	public function delete($id) {

		$entity = $this->repo->find($id);
		//$entity_name = $entity->getName();
		if ($entity) {
			$this->em->remove($entity);
			$this->em->flush();
			return array('support' => $entity,
				'message' => 'The Support User has been Removed!',
				'message_type' => 'success',
				'success' => true,
			);
		} else {

			return array('support' => $entity,
				'message' => 'Support User not found!',
				'message_type' => 'warning',
				'success' => false,
			);
		}
	}




//-------------------------------------------------------

  public function find($id) {
	return $this->repo->find($id);
  }
  #-----------------------------------------------------
  public function findAll(){
	return $this->repo->findAll();
  }

//-------------------------------------------------------
  public function findOneByUserName($user_name) {
	return $this->repo->findOneByUserName($user_name);
  }


  public function findOneBy($email) {
	return $this->repo->findOneBy(array('email' => $email));
  }

  //-------------------------------------------------------

  public function getListWithPagination($page_number, $sort) {
	$yaml = new Parser();
	$pagination_constants = $yaml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
	$limit = $pagination_constants["constants"]["pagination"]["limit"];

	$entity = $this->repo->listAllSupportAdminUser($page_number, $limit, $sort);
	$rec_count = count($this->repo->countAllRecord());
	$cur_page = $page_number;

	if ($page_number == 0 || $limit == 0) {
	  $no_of_paginations = 0;
	} else {
	  $no_of_paginations = ceil($rec_count / $limit);
	}
	return array('products' => $entity,
	  'rec_count' => $rec_count,
	  'no_of_pagination' => $no_of_paginations,
	  'limit' => $cur_page,
	  'per_page_limit' => $limit,
	  'sort'=>$sort,
	  'retailers' => $entity,
	);
  }




//Private Methods
//----------------------------------------------------------
  private function validateForCreate($name) {
	if (count($this->findOneByName($name)) > 0) {
	  return array('message' => 'Support User already exists!',
		'field' => 'name',
		'message_type' => 'warning',
		'success' => false,
	  );
	}
	return;
  }

//----------------------------------------------------------
  private function validateForUpdate($entity) {
	$support = $this->findOneBy($entity->getEmail());
	if ($support && $support->getId() != $entity->getId()) {
	  return array('message' => 'Support User already exists!',
		'field' => 'email',
		'message_type' => 'warning',
		'success' => "yes",
	  );
	}
	  return array(
		  'success' => "no"
	  );
  }

	private function encodeThisPassword(SupportAdminUser $support_admin_user, $password) {
		$factory = $this->container->get('security.encoder_factory');
		$encoder = $factory->getEncoder($support_admin_user);
		$password = $encoder->encodePassword($password, $support_admin_user->getSalt());
		return $password;
	}


//-------------------------------------------------------
  public function matchPassword(SupportAdminUser $support_admin_user, $password) {
	$password = $this->encodeThisPassword($support_admin_user, $password);
	if ($support_admin_user->getPassword() == $password) {
	  return true;
	}
	return false;
  }



  public function emailCheck($email) {
	if ($this->isDuplicateEmail(Null, $email) == false) {
	  return array('Message' => 'Valid Email');
	} else {
	  return array('Message' => 'The Email already exists');
	}
  }
  #--------------------------------------------------------------------------#
  public function isDuplicateEmail($id, $email) {
	return $this->repo->isDuplicateEmail($id, $email);
  }


}