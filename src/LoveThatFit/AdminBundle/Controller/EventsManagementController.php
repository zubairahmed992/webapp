<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\AdminBundle\Form\Type\EventsManagementType;

class EventsManagementController extends Controller {

    public function indexAction()
    {
        return $this->render('LoveThatFitAdminBundle:EventsManagement:index.html.twig');
    }

    public function paginateAction(Request $request)
    {
        $requestData = $this->get('request')->request->all();
        $output = $this->get('admin.helper.eventsManagement')->search($requestData);
        
        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']); 
    }

    public function newAction() {
        $form = $this->createForm(new EventsManagementType('add'));
        return $this->render('LoveThatFitAdminBundle:EventsManagement:new.html.twig', array(
                'form' => $form->createView()
            ));
    }

    public function createAction(Request $request)
    {
        $decoded = $request->request->all();
        $form    = $this->createForm(new EventsManagementType('add'));
        $form->bind($request);
        if (count($this->get('admin.helper.support')->findOneBy($decoded["support_user"]["email"])) > 0) {
            $this->get('session')->setFlash('warning', 'Support User email already exists.');
            return $this->render('LoveThatFitAdminBundle:EventsManagement:user_new.html.twig', array(
                    'form' => $form->createView()
                )
            );
        }
        if (count($this->get('admin.helper.support')->findOneByUserName($decoded["support_user"]["user_name"])) > 0) {
            $this->get('session')->setFlash('warning', 'Support Username already exists.');
            return $this->render('LoveThatFitAdminBundle:EventsManagement:user_new.html.twig', array(
                    'form' => $form->createView()
                )
            );
        }else{
            $supportUser = $this->get('admin.helper.support')->createNew();
            $this->get('admin.helper.support')->saveSupportUsers($supportUser,$decoded["support_user"]);
            $this->get('session')->setFlash('success', 'Support User has been created Successfully.');
            return $this->redirect($this->generateUrl('admin_support_user'));
        }

    }

    public function editAction($id)
    {
        die("in edit method");
        $yaml = new Parser();
        $conf = $yaml->parse(
                file_get_contents('../src/LoveThatFit/AdminBundle/Resources/config/users_roles.yml')
            );
        
        $roles = [];
        if (!empty($conf)) {
            foreach ($conf as $key => $value) {
                foreach ($value as $k => $v) {
                    $roles[] = $k;
                }
            }
        }
        $entity = $this->get('admin.helper.support')->find($id);
        $form = $this->createForm(new SupportUserType('edit'), $entity);
        return $this->render('LoveThatFitAdminBundle:EventsManagement:user_edit.html.twig', array(
                'form'   => $form->createView(),
                'entity' => $entity,
                'roles'  => $roles
            )
        );
    }

    public function updateAction(Request $request, $id)
    {
        $decoded  = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity =  $this->get('admin.helper.support')->find($id);
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Unable to find Support User.');
        }
        $form = $this->createForm(new SupportUserType('edit'), $entity);
        $form->bind($request);
        $message_array =  $this->get('admin.helper.support')->update($entity,$decoded["support_user"]);
        if($message_array["message_type"] == 'warning'){
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            return $this->render('LoveThatFitAdminBundle:EventsManagement:user_edit.html.twig', array(
                'form' => $form->createView(),
                'entity' => $entity,
                )
            );
        }else{
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            return $this->redirect($this->generateUrl('admin_support_user', array('id' => $entity->getId())));
        }
    }

    public function deleteAction($id)
    {
        die("in delete method");
        try {
            $message_array = $this->get('admin.helper.support')->delete($id);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            return $this->redirect($this->generateUrl('admin_support_user'));
        } catch (\Doctrine\DBAL\DBALException $e) {

            $this->get('session')->setFlash('warning', 'This Support user cannot be deleted!');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }

}
