<?php

namespace LoveThatFit\UserBundle\Controller;

use LoveThatFit\UserBundle\Entity\PodioUsers;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;

class PodioUsersController extends Controller {

	private function process_request(){
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());        
        $decoded['base_path'] = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';
        return $decoded;        
    }

    public function indexAction()
    {
    	$decoded  = $this->process_request(); 
    	$status = [0,2]; //user podio status 0=pending , 2=failure
    	$podio_users = $this->get('user.helper.podio')->findUserByStatus($status); //get podio pending or failure users    	
        if($podio_users) {
            $total_podio_users = count($podio_users);
        	foreach ($podio_users as $users) {
                //echo "<pre>"; print_r($users); 
                $id = $users['id']; //podio user log id
        		$user_podio = array(
                    'id' => $users['member_id'],
                    'email' => ($users['email']) ? $users['email'] : '',
                    'gender' => ($users['gender']=='f') ? 'Female' : 'Male',
                    'birth_date' => ($users['birthDate']) ? $users['birthDate']->format('Y-m-d h:i:s') : '',
                    'created_at' => ($users['member_created']) ? $users['member_created']->format('Y-m-d h:i:s') : '',
                    'zipcode' => ($users['zipcode']) ? $users['zipcode'] : '',
                    'base_path' => ($decoded['base_path']) ? $decoded['base_path'] : ''
                );
        		//echo "<pre>"; print_r($user_podio); 

        		$podio_id = $this->container->get('user.helper.podioapi')->saveUserPodio($user_podio);
                //echo "podio_id:".$podio_id."<br>";
        		if($podio_id) {
                    $data = $this->get('user.helper.podio')->updatePodioUsers($id, $podio_id);
        		}
        	}
            die(''.$total_podio_users.' Podio users added by cron job service...');
        } else {
            die('No New Podio Users Found...');
        }
    }

    public function asyncCallAction($user_id)
    {
        $decoded  = $this->process_request(); 
        $user_id = ($user_id) ? $user_id : 0; //asyncronious call for podio user log from app registration
        $status = [0,2]; //user podio status 0=pending , 2=failure
        $podio_users = $this->get('user.helper.podio')->findUserByStatus($status,$user_id); //get podio pending or failure users        

        foreach ($podio_users as $users) {
            //echo "<pre>"; print_r($users); 
            $id = $users['id']; //podio user log id
            $user_podio = array(
                'id' => $users['member_id'],
                'email' => ($users['email']) ? $users['email'] : '',
                'gender' => ($users['gender']=='f') ? 'Female' : 'Male',
                'birth_date' => ($users['birthDate']) ? $users['birthDate']->format('Y-m-d h:i:s') : '',
                'created_at' => ($users['member_created']) ? $users['member_created']->format('Y-m-d h:i:s') : '',
                'zipcode' => ($users['zipcode']) ? $users['zipcode'] : '',
                'base_path' => ($decoded['base_path']) ? $decoded['base_path'] : ''
            );
            //echo "<pre>"; print_r($user_podio); 

            $podio_id = $this->container->get('user.helper.podioapi')->saveUserPodio($user_podio);
            //echo "podio_id:".$podio_id."<br>";
            if($podio_id) {
                $data = $this->get('user.helper.podio')->updatePodioUsers($id, $podio_id);
            }
        }
        die('Podio User Cron Job Service');
    }

}

?>