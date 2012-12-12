<?php

namespace Acme\StoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Acme\StoreBundle\Entity\Merch;
use Acme\StoreBundle\Form\MerchType;

/**
 * Merch controller.
 *
 * @Route("/merch")
 */
class MerchController extends Controller
{
    /**
     * Lists all Merch entities.
     *
     * @Route("/", name="merch")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AcmeStoreBundle:Merch')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a Merch entity.
     *
     * @Route("/{id}/show", name="merch_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AcmeStoreBundle:Merch')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Merch entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Merch entity.
     *
     * @Route("/new", name="merch_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Merch();
        $form   = $this->createForm(new MerchType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Merch entity.
     *
     * @Route("/create", name="merch_create")
     * @Method("POST")
     * @Template("AcmeStoreBundle:Merch:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Merch();
        $form = $this->createForm(new MerchType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('merch_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Merch entity.
     *
     * @Route("/{id}/edit", name="merch_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AcmeStoreBundle:Merch')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Merch entity.');
        }

        $editForm = $this->createForm(new MerchType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Merch entity.
     *
     * @Route("/{id}/update", name="merch_update")
     * @Method("POST")
     * @Template("AcmeStoreBundle:Merch:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AcmeStoreBundle:Merch')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Merch entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new MerchType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('merch_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Merch entity.
     *
     * @Route("/{id}/delete", name="merch_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AcmeStoreBundle:Merch')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Merch entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('merch'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
