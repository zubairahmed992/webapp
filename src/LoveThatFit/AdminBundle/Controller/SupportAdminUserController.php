<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
//use LoveThatFit\AdminBundle\Form\Type\DeleteType;
use LoveThatFit\AdminBundle\Form\Type\SupportUserType;
use LoveThatFit\AdminBundle\Entity\SupportAdminUser;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SupportAdminUserController extends Controller {

    public function indexAction($page_number, $sort = 'id') {
        $support_with_pagination = $this->get('admin.helper.support')->getListWithPagination($page_number, $sort);
        return $this->render('LoveThatFitAdminBundle:UserSupport:index.html.twig', $support_with_pagination);
    }



    public function newAction() {
	  $form = $this->createForm(new SupportUserType('add'));
      return $this->render('LoveThatFitAdminBundle:UserSupport:user_new.html.twig', array(
             'form' => $form->createView(),
             ));
    }
    public function createAction(Request $request) {
        $decoded  = $request->request->all();
        $form = $this->createForm(new SupportUserType('add'));
        $form->bind($request);
        if (count($this->get('admin.helper.support')->findOneBy($decoded["support_user"]["email"])) > 0) {
            $this->get('session')->setFlash('warning', 'Support User email already exists.');
            return $this->render('LoveThatFitAdminBundle:UserSupport:user_new.html.twig', array(
                    'form' => $form->createView()
                )
            );
        }
        if (count($this->get('admin.helper.support')->findOneByUserName($decoded["support_user"]["user_name"])) > 0) {
            $this->get('session')->setFlash('warning', 'Support Username already exists.');
            return $this->render('LoveThatFitAdminBundle:UserSupport:user_new.html.twig', array(
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

    public function editAction($id) {

        $entity = $this->get('admin.helper.support')->find($id);
        $form = $this->createForm(new SupportUserType('edit'), $entity);
        return $this->render('LoveThatFitAdminBundle:UserSupport:user_edit.html.twig', array(
                'form' => $form->createView(),
                'entity' => $entity,
            )
        );
    }

    public function updateAction(Request $request, $id) {
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
            return $this->render('LoveThatFitAdminBundle:UserSupport:user_edit.html.twig', array(
                    'form' => $form->createView(),
                    'entity' => $entity,
                )
            );
        }else{
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
             return $this->redirect($this->generateUrl('admin_support_user', array('id' => $entity->getId())));
            }
        }
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    public function changePasswordAction(Request $request, $id) {
        $decoded  = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity =  $this->get('admin.helper.support')->find($id);
        $message_array =  $this->get('admin.helper.support')->changePassword($entity,$decoded["password"]);
        $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
        return $this->redirect($this->generateUrl('admin_support_user', array('id' => $entity->getId())));
    }

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    public function deleteAction($id) {
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
