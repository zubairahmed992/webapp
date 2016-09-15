<?php

namespace LoveThatFit\SupportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\SupportBundle\Form\Type\AlgoritumTestlType;
use LoveThatFit\SupportBundle\Form\Type\AlgoritumProductTestlType;

class TaskLogController extends Controller {

	public function indexAction()
    {
        $entity = $this->get('support.helper.supporttasklog')->findSupprtUsers();
        if(!$entity){        
            $this->get('session')->setFlash('warning', 'No User not found!');
            return $this->redirect($this->generateUrl('support_users_task_log'));
            exit;
        }

        return $this->render('LoveThatFitSupportBundle:TaskLog:index.html.twig',
            array(
                "supportData"  => $entity,
            )
        );
    }

    public function paginateAction(Request $request)
    {
        $requestData = $this->get('request')->request->all();
        $output = $this->get('support.helper.supporttasklog')->search($requestData);

        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']); 
    }

    public function showAction($id)
    {
        $entity       = $this->get('support.helper.supporttasklog')->findSupprtUserByID($id);
        $supportUsers = $this->get('admin.helper.support')->findAll();

        if(!$entity){        
            $this->get('session')->setFlash('warning', 'No User not found!');
            return $this->redirect($this->generateUrl('support_users_task_log'));
            exit;
        }

        return $this->render('LoveThatFitSupportBundle:TaskLog:show.html.twig',
            array(
                "id"           => $id,
                "supportData"  => $entity,
                "supportUsers" => $supportUsers
            )
        );
    }

    public function showPaginateAction()
    {
        $requestData = $this->get('request')->request->all();
        $output = $this->get('support.helper.supporttasklog')->showSearch($requestData);

        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']); 
    }
}