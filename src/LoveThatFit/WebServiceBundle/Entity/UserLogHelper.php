<?php
/**
 * Created by PhpStorm.
 * User: haris.khalique
 * Date: 4/19/2017
 * Time: 9:19 PM
 */

namespace LoveThatFit\WebServiceBundle\Entity;


use LoveThatFit\UserBundle\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class UserLogHelper
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
        $userLog = new $class();
        return $userLog;
    }

    public function save($entity) {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function logUserLoginTime( User $user, $request = array()){
        if(is_object($user)){
            $userLog = $this->createNew();
            $sessionId = md5(uniqid('php_').$user->getAuthToken().time());
            $appName = (isset($request['appname']) ? strtolower($request['appname']) : "");

            $userLog->setAppName( $appName );
            $userLog->setLoginAt(new \DateTime('now'));
            $userLog->setLogoutAt(new \DateTime("0000-00-00 00:00:00"));
            $userLog->setIsLogout(0);
            $userLog->setUsers( $user );
            $userLog->setSessionId( $sessionId );

            $this->save( $userLog );

            return $userLog;
        }

        return false;
    }

    public function findUserBySessionId( User $user, $request = array())
    {
        $userLogObject = $this->repo->findOneBy(
                array("users" => $user->getId(),
                     "sessionId" => $request['session_id'],
                     "appName" => strtolower($request['appname'])
                ));

        if(is_object($userLogObject)){
            $userLogObject->setLogoutAt(new \DateTime('now'));
            $userLogObject->setIsLogout(1);

            $this->save($userLogObject);
            return $userLogObject;
        }

        return false;
    }

    public function userBySessionId(array $request)
    {
        $userLogObject = $this->repo->findOneBy(
            array(
                "sessionId" => $request['session_id']
            ));

        if(is_object($userLogObject)){
            return $userLogObject->getUsers();
        }

        return 0;
    }
    public function checkForUserSession(array $request)
    {
        $userLogObject = $this->repo->findOneBy(
            array(
                "sessionId" => $request['session_id']
            ));

        if(is_object($userLogObject)){
            $logoutDate = strtotime($userLogObject->getLogoutAt()->format('Y-m-d H:i:s'));
            $isUserLogout = $userLogObject->getIsLogout();
            if($isUserLogout == 0){
                return true;
            }
        }

        return false;
    }

    public function findUserLogsByUserId( User $user)
    {
        $userLogObject = $this->repo->findBy(
            array("users" => $user->getId()) /*, array('price' => 'ASC')*/
        );
        return $userLogObject;
    }

    public function findUserIsLoginBefore( User $user, $decode)
    {
        $userLogObject = $this->repo->findBy(
            array(
                "users" => $user->getId(),
                "appName" => array($decode['appname'], strtolower($decode['appname']))
            ) /*, array('price' => 'ASC')*/
        );
        return $userLogObject;
    }
}