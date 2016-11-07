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

        $events = $this->get('admin.helper.eventsManagement')->createNew();
        $message_array = $this->get('admin.helper.eventsManagement')->save($events,$decoded["events_management"]);
        if($message_array["message_type"] == 'warning'){
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            return $this->render('LoveThatFitAdminBundle:EventsManagement:new.html.twig', array(
                'form' => $form->createView()
                )
            );
        }else{
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            return $this->redirect($this->generateUrl('admin_events'));
        }
    }

    public function editAction($id)
    {
        $entity = $this->get('admin.helper.eventsManagement')->find($id);
        $form = $this->createForm(new EventsManagementType('edit'), $entity);
        return $this->render('LoveThatFitAdminBundle:EventsManagement:edit.html.twig', array(
                'form'   => $form->createView(),
                'entity' => $entity
            )
        );
    }

    public function updateAction(Request $request, $id)
    {
        $decoded  = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity =  $this->get('admin.helper.eventsManagement')->find($id);
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Unable to find Event.');
        }
        $form = $this->createForm(new EventsManagementType('edit'), $entity);
        $form->bind($request);

        $message_array =  $this->get('admin.helper.eventsManagement')->update($entity,$decoded["events_management"]);
        if($message_array["message_type"] == 'warning'){
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            return $this->render('LoveThatFitAdminBundle:EventsManagement:edit.html.twig', array(
                'form' => $form->createView(),
                'entity' => $entity,
                )
            );
        }else{
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            return $this->redirect($this->generateUrl('admin_events', array('id' => $entity->getId())));
        }
    }

    public function deleteAction($id)
    {
        $message_array = $this->get('admin.helper.eventsManagement')->delete($id);
        $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
        return $this->redirect($this->generateUrl('admin_events'));
    }

}
