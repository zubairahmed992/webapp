<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\AdminBundle\Form\Type\DeleteType;
use LoveThatFit\AdminBundle\Form\Type\AppStoreLinkTypes;

class AppStoreLinkController extends Controller {

    public function addAction() {
        $app_link = "https://www.selfiestyler.com/";
        $this->get('admin.helper.appstorelink')->save($app_link);
        return $this->redirect('/admin/appstorelink/1/edit');
    }

    public function editAction($id) {

        $entity = $this->get('admin.helper.appstorelink')->find($id);

        if(!$entity){       
            $app_link = "https://www.selfiestyler.com/";
            $this->get('admin.helper.appstorelink')->save($app_link);
            $entity = $this->get('admin.helper.appstorelink')->find($id);
        }
            
        $form = $this->createForm(new AppStoreLinkTypes('edit',$entity), $entity);
        $deleteForm = $this->createForm(new DeleteType(), $entity);
        
        return $this->render('LoveThatFitAdminBundle:AppStoreLink:edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity
                    ));
    }

    public function updateAction(Request $request, $id) {

        $entity = $this->get('admin.helper.appstorelink')->find($id);
        
        $form = $this->createForm(new AppStoreLinkTypes('edit',$entity), $entity);

        $form->bind($request);

        $app_store_link = $request->request->get('app_store_link');
        
        $app_link = $app_store_link['app_link'];

        $this->get('admin.helper.appstorelink')->update($id, $app_link);
        
        $deleteForm = $this->createForm(new DeleteType(), $entity);        

        return $this->render('LoveThatFitAdminBundle:AppStoreLink:edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity
            ));
    }
}