<?php

namespace LoveThatFit\AdminBundle\Entity;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

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

    //-----------------------------------------------------------
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

    public function delete($id) {

        $entity = $this->repo->find($id);
        $entity_name = $entity->getName();
        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();

            return array('retailer' => $entity,
                'message' => 'The Retailer User ' . $entity_name . ' has been Deleted!',
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
// FIND    
//-------------------------------------------------------

    public function find($id) {
        return $this->repo->find($id);
    }

    #-----------------------------------------------------

    public function findAll() {
        return $this->repo->findAll();
    }

//-------------------------------------------------------
    public function findOneByName($name) {
        return $this->repo->findOneByName($name);
    }

    //-----------------------------------------------------------

    public function findOneBy($email) {
        return $this->repo->findOneBy(array('email' => $email));
    }

    //-----------------------------------------------------------

    public function getRetailerNameByRetailerUser($retailer) {
        return $this->repo->getRetailerNameByRetailerUser($retailer);
    }

//-----------------------------------------------------------
    public function getRecordsCountWithCurrentRetailerLimit($retailer_id) {

        return $this->repo->getRecordsCountWithCurrentRetailerLimit($retailer_id);
    }

//-----------------------------------------------------------

    public function getRetaielerUserByRetailer($retailer) {
        return $this->repo->getRetaielerUserByRetailer($retailer);
    }

    #---------------------------------------------------------------------------------

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

}