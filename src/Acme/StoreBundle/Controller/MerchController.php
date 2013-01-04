<?php

namespace Acme\StoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
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
    public function _createAction(Request $request)
    {
        $entity  = new Merch();
        $form = $this->createForm(new MerchType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('acme_merch_show', array('id' => $entity->getId())));
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

            return $this->redirect($this->generateUrl('acme_merch_edit', array('id' => $id)));
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

        return $this->redirect($this->generateUrl('acme_merchs'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    #----------------------------------------------------------
 public function ajaxFormAction()
    {
        $entity  =  $this->getNewEntity();
        $form =  $form = $this->createForm(new MerchType(), $entity);
        $form->bind($request);
        return    $response= new Response("p");

    }
    
    
       /**
     * Lists all Merch entities.
     *
     * @Template()
     */
   
      public function ajaxAction()
    {
        return array(
            'entities' => $this->getList(),
              'entity' => $this->getNewEntity(),
            'form'   => $this->getNewForm(),
        );
    }
 

 public function createAction(Request $request)
    {
        $entity  =  $this->getNewEntity();
        $form =  $form = $this->createForm(new MerchType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

        //return new Response(json_encode("howdee ajax!"));
            $entities=$this->getList();
            $fr = $this->container->get('templating')->render('AcmeStoreBundle:Merch:new.html.twig', array(
            'form'=> $form->createView(),
            'entity'=> $entity ));
            
            $response= new Response(json_encode(array(
            'entities' => $entities,
            'entity' => $entity,
            'form'   => $fr,
            'message' => "Entity succesfully  Inserted"    
                
        )));
            
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
else
{
    return new Response("ajax we have a problem!");
    
}
        
    }
    
    
    private function getNewForm()
    {
         return $this->createForm(new MerchType(), new Merch())->createView();

    }
    private function getForm($entity)
    {
         return $this->createForm(new MerchType(), $entity)->createView();
    }
    private function getList()
    {
          return $this->getDoctrine()->getRepository('AcmeStoreBundle:Merch')->findAll();
    }
      private function getNewEntity()
    {
        return new Merch();

    }
}
