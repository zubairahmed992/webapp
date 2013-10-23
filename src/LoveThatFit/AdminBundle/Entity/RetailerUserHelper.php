<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;

class RetailerUserHelper {

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

    //---------------------------------------------------------------------   

    public function createNew() {
        $class = $this->class;
        $brand = new $class();
        return $brand;
    }

//-------------------------------------------------------

    public function save($entity) {
        $msg_array =null;
        //$msg_array = ;

        //$retailerTitle = $entity->getTitle();        
        //$msg_array = $this->validateForCreate($retailerTitle);
        if ($msg_array == null) {      
            $entity->setCreatedAt(new \DateTime('now'));
            $entity->setUpdatedAt(new \DateTime('now'));             
            $this->em->persist($entity);
            $this->em->flush();
            return array('message' => 'Retailer succesfully created.',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );
        } else {
            return $msg_array;
        }
    }

    //-------------------------------------------------------

    public function update($entity) {

        $msg_array = $this->validateForUpdate($entity);

        if ($msg_array == null) {            
            $this->em->persist($entity);
            $this->em->flush();

            return array('message' => 'Retailer ' . $entity->getName() . ' succesfully updated!',
                'field' => 'all',
                'message_type' => 'success',
                'success' => true,
            );
        } else {
            return $msg_array;
        }
    }

//-------------------------------------------------------

    public function delete($id) {

        $entity = $this->repo->find($id);
        $entity_name = $entity->getName();
        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();

            return array('retailer' => $entity,
                'message' => 'The Retailer ' . $entity_name . ' has been Deleted!',
                'message_type' => 'success',
                'success' => true,
            );
        } else {

            return array('retailers' => $entity,
                'message' => 'Retailer not found!',
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

    public function findWithSpecs($id) {
        $entity = $this->repo->find($id);

        if (!$entity) {
            $entity = $this->createNew();
            return array(
                'entity' => $entity,
                'message' => 'Retailer not found.',
                'message_type' => 'warning',
                'success' => false,
            );
        } else {
            return array(
                'entity' => $entity,
                'message' => 'Retailer found!',
                'message_type' => 'success',
                'success' => true,
            );
        }
    }

//-------------------------------------------------------
    public function findOneByName($name) {
        return $this->repo->findOneByName($name);
    }

    public function removeBrand() {
        return $this->repo->removeRetailer();
    }
    public function findOneBy($email) {
        return $this->repo->findOneBy(array('email' => $email));
    }

    //-------------------------------------------------------

    public function getListWithPagination($page_number, $sort) {
        $yaml = new Parser();
        $pagination_constants = $yaml->parse(file_get_contents('../app/config/config_ltf_app.yml'));
        $limit = $pagination_constants["constants"]["pagination"]["limit"];

        $entity = $this->repo->listAllRetailer($page_number, $limit, $sort);
        $rec_count = count($this->repo->countAllRecord());
        $cur_page = $page_number;

        if ($page_number == 0 || $limit == 0) {
            $no_of_paginations = 0;
        } else {
            $no_of_paginations = ceil($rec_count / $limit);
        }
        return array('retailers' => $entity,
            'rec_count' => $rec_count,
            'no_of_pagination' => $no_of_paginations,
            'limit' => $cur_page,
            'per_page_limit' => $limit,
            'sort'=>$sort,
        );
    }

    public function getRecordsCountWithCurrentRetailerLimit($retailer_id){
    
    return $this->repo->getRecordsCountWithCurrentRetailerLimit($retailer_id);
}

public function getRetaielerUserByRetailer($retailer)
{
    return $this->repo->getRetaielerUserByRetailer($retailer);
}
    
    
    
    
//Private Methods    
//----------------------------------------------------------
    private function validateForCreate($name) {
        if (count($this->findOneByName($name)) > 0) {
            return array('message' => 'Retailer Name already exists!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }
        return;
    }

//----------------------------------------------------------
    private function validateForUpdate($entity) {
        $brand = $this->findOneByName($entity->getName());

        if ($brand && $brand->getId() != $entity->getId()) {
            return array('message' => 'Retailer Name already exists!',
                'field' => 'name',
                'message_type' => 'warning',
                'success' => false,
            );
        }
        return;
    }
    
    //------------- Password encoding ------------------------------------------
    public function encodePassword(Retailer $retailer) {
        return $this->encodeThisPassword($retailer, $retailer->getPassword());
    }

//-------------------------------------------------------
    private function encodeThisPassword(Retailer $retailer, $password) {
        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($retailer);
        $password = $encoder->encodePassword($password, $retailer->getSalt());
        return $password;
    }

//-------------------------------------------------------
    public function matchPassword(Retailer $retailer, $password) {
        $password = $this->encodeThisPassword($retailer, $password);
        if ($retailer->getPassword() == $password) {
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
    
    
    public function getRegistrationSecurityContext($request) {        
        $session = $request->getSession();
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                    SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        return array('last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error' => $error,
        );
    }
    
    
    public function authenticateToken($token) {
        $entity = $this->repo->findOneBy(array('authToken' => $token));
        if (count($entity) > 0) {
            return array('status' => True, 'Message' => 'Authentication Success');
        } else {
            return array('status' => False, 'Message' => 'Authentication Failure');
        }
    }
    
    public function resetPassword($user, $request_array) {
        $oldPassword=$request_array->getOldpassword();
        $oldEncodedPassword = $this->encodeThisPassword($user, $oldPassword);
        $_user =  $this->find(1);
        return array('status' => true, 'header' => 'tester', 'message' => $oldEncodedPassword.' >~~> '.$_user->getPassword(), 'entity' => $user);
        if ($oldEncodedPassword == $user->getPassword()) {
            $user->getPassword($this->encodeThisPassword($user, $newPassword));
            $this->saveUser($user);
            return array('status' => true, 'header' => 'Success', 'message' => 'Password has been successfully changed', 'entity' => $user);
        } else {
            return array('status' => false, 'header' => 'Warning', 'message' => 'Old password Invalid', 'entity' => $user);
        }
    }
    
}