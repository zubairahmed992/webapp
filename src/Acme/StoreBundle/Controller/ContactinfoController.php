<?php

namespace Acme\StoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Acme\StoreBundle\Entity\Contactinfo;
use Acme\StoreBundle\Form\ContactinfoType;

/**
 * Contactinfo controller.
 *
 */
class ContactinfoController extends Controller
{
    /**
     * Lists all Contactinfo entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AcmeStoreBundle:Contactinfo')->findAll();

        return $this->render('AcmeStoreBundle:Contactinfo:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a Contactinfo entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AcmeStoreBundle:Contactinfo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contactinfo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AcmeStoreBundle:Contactinfo:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new Contactinfo entity.
     *
     */
    public function newAction()
    {
        $entity = new Contactinfo();
        $form   = $this->createForm(new ContactinfoType(), $entity);

        return $this->render('AcmeStoreBundle:Contactinfo:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Contactinfo entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new Contactinfo();
        $form = $this->createForm(new ContactinfoType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('contactinfo_show', array('id' => $entity->getId())));
        }

        return $this->render('AcmeStoreBundle:Contactinfo:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Contactinfo entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AcmeStoreBundle:Contactinfo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contactinfo entity.');
        }

        $editForm = $this->createForm(new ContactinfoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AcmeStoreBundle:Contactinfo:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Contactinfo entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AcmeStoreBundle:Contactinfo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contactinfo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ContactinfoType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('contactinfo_edit', array('id' => $id)));
        }

        return $this->render('AcmeStoreBundle:Contactinfo:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Contactinfo entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AcmeStoreBundle:Contactinfo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Contactinfo entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('contactinfo'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
