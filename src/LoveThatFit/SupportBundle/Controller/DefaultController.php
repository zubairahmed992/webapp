<?php

namespace LoveThatFit\SupportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Yaml\Parser;

class DefaultController extends Controller
{
    public function indexAction()
    {
    	$yaml = new Parser();
    	$role = $this->getUser()->getRoleName();
		$conf = $yaml->parse(
        		file_get_contents('../src/LoveThatFit/AdminBundle/Resources/config/users_roles.yml')
            );

        $permissions = [];
        if (!empty($conf)) {
            foreach ($conf as $key => $value) {
            	foreach ($value as $k => $v) {
            		if ($k == $role) {
						$permissions = $v;
                    }
                }
            }
        }
        if (!empty($permissions)) {
            $this->get('session')->set('Permissions', $permissions);
            return $this->render('LoveThatFitSupportBundle:Default:index.html.twig');
        } else {
            return $this->redirect($this->generateUrl('support_logout'));
            die();
        }
    }
}
