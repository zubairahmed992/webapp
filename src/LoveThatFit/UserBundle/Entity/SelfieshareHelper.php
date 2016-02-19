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
use LoveThatFit\UserBundle\Entity\MaskMarker;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use LoveThatFit\UserBundle\Event\UserEvent;
use Symfony\Component\HttpFoundation\Request;

class SelfieshareHelper {

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
   #----------------------------------------------------------------------------
    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, Container $container) {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }
       #----------------------------------------------------------------------------
     public function createNew($user=null){
       $class = $this->class;
       $selfieshare = new $class();
       $selfieshare->setUser($user);       
       $selfieshare->setCreatedAt(new \DateTime('now'));    
       return $selfieshare;
     }
     
   #----------------------------------------------------------------------------
     public function createWithParam($ra, $user) {
        $selfieshare=  $this->createNew($user);
    
        if (array_key_exists("image", $_FILES)) {
            $selfieshare->file = $_FILES["image"];
        } else {
            return 'image not provided';
        }
        
        $selfieshare->upload();
        
        if(array_key_exists('device_type', $ra) && $ra['device_type']){$selfieshare->setDeviceType($ra['device_type']);}
        if(array_key_exists('image', $ra) && $ra['image']){$selfieshare->setImage($ra['image']);}  
        if(array_key_exists('message', $ra) && $ra['message']){$selfieshare->setMessage($ra['message']);}
        if(array_key_exists('friend_name', $ra) && $ra['friend_name']){$selfieshare->setFriendName($ra['friend_name']);}
        if(array_key_exists('friend_email', $ra) && $ra['friend_email']){$selfieshare->setFriendEmail($ra['friend_email']);}
        if(array_key_exists('friend_phone', $ra) && $ra['friend_phone']){$selfieshare->setFriendPhone($ra['friend_phone']);}
        $selfieshare->setRef(uniqid());
        $this->save($selfieshare);          
        return $selfieshare;
    }  
     #----------------------------------------------------------------------------
     public function updateFeedback($ra) {
        $selfieshare=$this->repo->findOneBy(array('ref' => $ra['ref']));
        if(array_key_exists('rating', $ra) && $ra['rating']){$selfieshare->setRating($ra['rating']);}
        if(array_key_exists('favourite', $ra) && $ra['favourite']){$selfieshare->setFavourite($ra['favourite']=='false'?false:true);}
        if(array_key_exists('comments', $ra) && $ra['comments']){$selfieshare->setComments($ra['comments']);}
        $current = new \DateTime('now');        
        $updated_at = $selfieshare->getUpdatedAt();        
        $updated_at = $updated_at->format('Y-m-d H:i:s');
        $current = $current->format('Y-m-d H:i:s');
        $to_time = strtotime($current);
        $from_time = strtotime($updated_at);
        $interval  =  round(abs($to_time - $from_time) / 60,2);
        if ($interval>10){
            $user=$selfieshare->getUser();
            $ss_ar['to_email'] = $user->getEmail();
            $ss_ar['template']='LoveThatFitAdminBundle::email/selfieshare.html.twig';
            $ss_ar['template_array']=array('user'=>$user, 'selfieshare'=>$selfieshare, 'link_type'=>'show');
            $ss_ar['subject']='SelfieStyler friend share';
            $this->container->get('mail_helper')->sendEmailWithTemplate($ss_ar);            
        }
        
        $this->save($selfieshare);         
        return $selfieshare;
    }  
    #----------------------------------------------------------------------------
    public function save($selfieshare) {
       $selfieshare->setUpdatedAt(new \DateTime('now'));
        $this->em->persist($selfieshare);
        $this->em->flush();      
    }
       
   
       #----------------------------------------------------------------------------
    public function find($id) {
        return $this->repo->find($id);
    } 
    #----------------------------
    public function findByRef($ref) {
        return $this->repo->findOneBy(array('ref' => $ref));
    } 
    
}
    
?>