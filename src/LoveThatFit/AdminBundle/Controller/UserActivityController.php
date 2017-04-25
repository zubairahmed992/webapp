<?php

namespace LoveThatFit\AdminBundle\Controller;

use LoveThatFit\AdminBundle\Form\Type\MannequinTestType;
use LoveThatFit\AdminBundle\Form\Type\RetailerSiteUserType;
use LoveThatFit\AdminBundle\Form\Type\UserMeasurementType;
use LoveThatFit\AdminBundle\Form\Type\UserProfileSettingsType;
use LoveThatFit\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserActivityController extends Controller
{
    public function indexAction()
    {
        $user_list = $this->get('user.helper.user')->getListWithPagination(0, 'email');
        return $this->render('LoveThatFitAdminBundle:UserActivity:index.html.twig', array(
            'users' => $user_list['users'],
        ));
    }

    public function showAction(Request $request, $user_id)
    {
        $decoded = $this->get('webservice.helper')->processRequest($this->getRequest());
        $user = $this->get('user.helper.user')->findUserById($user_id);
        if ($user) {
            $user_logs = $this->get('userlog.helper.userlog')->findUserLogsByUserId($user);
            $log = array();
            $count = 0;
            foreach ($user_logs as $logs) {
                $log[$count]["id"] = $logs->getId();
                $log[$count]["app"] = $logs->getAppName();
                if ($logs->getLoginAt()->format('Y') > "1800") {
                    $log[$count]["login_at"] = $logs->getLoginAt()->format('Y-m-d H:i:s');
                } else {
                    $log[$count]["login_at"] = "-";
                }

                if ($logs->getLogoutAt()->format('Y') > "1800") {
                    $log[$count]["logout_at"] = $logs->getLogoutAt()->format('Y-m-d H:i:s');
                } else {
                    $log[$count]["logout_at"] = "-";
                }
                $count++;
            }
            $res = $this->get('webservice.helper')->response_array(true, 'success', true, $log);
        } else {
            $res = $this->get('webservice.helper')->response_array(false, 'User not found.', true, []);
        }
        return new Response($res);
    }
}
