<?php
/**
 * Created by PhpStorm.
 * User: haris.khalique
 * Date: 2/20/2017
 * Time: 6:12 PM
 */

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class FNFUserController extends Controller
{
    public function indexAction()
    {
        $user = $this->get('user.helper.user')->find('1075');
        $fnfUser = $this->get('fnfuser.helper.fnfuser')->getFNFUserById($user);
        $fnfUser = $this->get('fnfuser.helper.fnfuser')->setIsAvailable($fnfUser);

        var_dump( $fnfUser ); die;
    }

    public function getApplicableFNFUserAction()
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = array_key_exists('auth_token', $decoded) ? $this->get('webservice.helper')->findUserByAuthToken($decoded['auth_token']) : null;

        if ($user) {
            $fnfUser = $this->get('fnfuser.helper.fnfuser')->getApplicableFNFUser($user);
            if(is_object($fnfUser)){
                $res = $this->get('webservice.helper')->response_array(true, 'applicable for discount', true, array(
                    'discount_amount' => $fnfUser->getDiscount()
                ));
            }else{
                $res = $this->get('webservice.helper')->response_array(false, 'user in not applicable for discount.');
            }
        }
        else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not authenticated.');
        }

        return new Response( $res );
    }
}