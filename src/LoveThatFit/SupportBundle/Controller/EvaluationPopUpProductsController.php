<?php

namespace LoveThatFit\SupportBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LoveThatFit\SupportBundle\Entity\EvaluationPopUpProducts;
use LoveThatFit\SupportBundle\Form\EvaluationPopUpProductsType;
use Symfony\Component\Form\Forms;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use LoveThatFit\AdminBundle\Entity\Product;

/**
 * EvaluationPopUpProducts controller.
 *
 * @Route("/evaluationpopupproducts")
 */
class EvaluationPopUpProductsController extends Controller
{
    /**
     * Lists all EvaluationPopUpProducts entities.
     *
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();


        $entities = $em->getRepository('LoveThatFitSupportBundle:EvaluationPopUpProducts')->findAll();
        $products = array();
        $productSizes = array();
        if ($entities) {
            $productId = array();
            foreach ($entities as $entity) {
                $productId[] = $entity->getProductId();
            }

            $productsList = $this->get('doctrine')
                ->getRepository('LoveThatFitAdminBundle:Product')
                ->findById($productId, array('name' => 'ASC'));



            foreach ($productsList as $product) {
                $products[$product->getID()] = $product->getName();
                $productSize = $product->getProductSizes();
                if ($productSize) {
                    foreach ($productSize as $size) {
                        $productSizes[$size->getID()] = $size->getTitle();
                    }
                }

            }

        }



        return $this->render('LoveThatFitSupportBundle:EvaluationPopUpProducts:index.html.twig',
            array(
                'entities' => $entities,
                'products' => $products,
                'product_sizes' => $productSizes
            )
        );

    }

    /**
     * Finds and displays a EvaluationPopUpProducts entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LoveThatFitSupportBundle:EvaluationPopUpProducts')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EvaluationPopUpProducts entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LoveThatFitSupportBundle:EvaluationPopUpProducts:show.html.twig',
            array(
                'entity' => $entity,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to create a new EvaluationPopUpProducts entity.
     *
     */
    public function newAction()
    {

        $entity = new EvaluationPopUpProducts();
        $form = $this->createForm(new EvaluationPopUpProductsType(), $entity);


        //Current Product
        $em = $this->getDoctrine()->getManager();
        $existingProductsList = $em->getRepository('LoveThatFitSupportBundle:EvaluationPopUpProducts')->findAll();
        $existProducts = array();
        if ($existingProductsList) {
            foreach ($existingProductsList as $key=>$product) {
                $existProducts[$key] = $product->getProductID();
            }

        }


        return $this->render('LoveThatFitSupportBundle:EvaluationPopUpProducts:new.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
                'exists_products' => $existProducts,
            )
        );

    }

    /**
     * Creates a new EvaluationPopUpProducts entity.
     */
    public function createAction(Request $request)
    {
        $entity = new EvaluationPopUpProducts();
        $form = $this->createForm(new EvaluationPopUpProductsType(), $entity);
        $form->bind($request);

        $productInfo = $request->request->all();
        if ($productInfo['lovethatfit_supportbundle_evaluationpopupproductstype']['product_id'] == "" || $productInfo['lovethatfit_supportbundle_evaluationpopupproductstype']['product_sizes'] == "") {
            $form->isValid();
        } else {

            $productID = $productInfo['lovethatfit_supportbundle_evaluationpopupproductstype']['product_id'];
            $productSizes = $productInfo['lovethatfit_supportbundle_evaluationpopupproductstype']['product_sizes'];


            $entity->setProductId($productID);
            $entity->setProductSizes(implode(',', $productSizes));

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('evaluationpopupproducts_edit', array('id' => $entity->getId())));
        }
        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing EvaluationPopUpProducts entity.
     *
     */
    public function editAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LoveThatFitSupportBundle:EvaluationPopUpProducts')->find($id);


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EvaluationPopUpProducts entity.');
        }

        //All exists Product
        $em = $this->getDoctrine()->getManager();
        $existingProductsList = $em->getRepository('LoveThatFitSupportBundle:EvaluationPopUpProducts')->findAll();
        $existProducts = array();
        if ($existingProductsList) {
            foreach ($existingProductsList as $key=>$product) {
                $existProducts[$key] = $product->getProductID();
            }

        }


        $editForm = $this->createForm(new EvaluationPopUpProductsType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $productsInfo = $this->get('doctrine')
            ->getRepository('LoveThatFitAdminBundle:Product')
            ->find($entity->getProductID());

        if (!$productsInfo) {
            throw $this->createNotFoundException('Unable to find evaluation default products entity.');
        }

        $options = array();
        if ($productsInfo->getProductSizes()) {
            $productSizes = $productsInfo->getProductSizes();
            foreach ($productSizes as $size) {
                $options[$size->getID()] = $size->getTitle();
            }
        }

        $SelectedSizes = explode(',', $entity->getProductSizes());


        return $this->render('LoveThatFitSupportBundle:EvaluationPopUpProducts:edit.html.twig',
            array(
                'exists_products' => $existProducts,
                'product_info' => $productsInfo,
                'product_sizes' => $options,
                'selected_sizes' => $SelectedSizes,
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Edits an existing EvaluationPopUpProducts entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LoveThatFitSupportBundle:EvaluationPopUpProducts')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EvaluationPopUpProducts entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new EvaluationPopUpProductsType(), $entity);
        $editForm->bind($request);

        $productInfo = $request->request->all();

        if ($productInfo['lovethatfit_supportbundle_evaluationpopupproductstype']['product_id'] == "" || $productInfo['lovethatfit_supportbundle_evaluationpopupproductstype']['product_sizes'] == "") {
            $editForm->isValid();
        } else {

            $productID = $productInfo['lovethatfit_supportbundle_evaluationpopupproductstype']['product_id'];
            $productSizes = $productInfo['lovethatfit_supportbundle_evaluationpopupproductstype']['product_sizes'];


            $entity->setProductId($productID);
            $entity->setProductSizes(implode(',', $productSizes));

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('evaluationpopupproducts_edit', array('id' => $id)));
        }

        $productsInfo = $this->get('doctrine')
            ->getRepository('LoveThatFitAdminBundle:Product')
            ->find($entity->getProductID());

        if (!$productsInfo) {
            throw $this->createNotFoundException('Unable to find evaluation default products entity.');
        }

        $options = array();
        if ($productsInfo->getProductSizes()) {
            $productSizes = $productsInfo->getProductSizes();
            foreach ($productSizes as $size) {
                $options[$size->getID()] = $size->getTitle();
            }
        }

        $SelectedSizes = explode(',', $entity->getProductSizes());

        return array(
            'product_info' => $productsInfo,
            'product_sizes' => $options,
            'selected_sizes' => $SelectedSizes,
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a EvaluationPopUpProducts entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LoveThatFitSupportBundle:EvaluationPopUpProducts')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find EvaluationPopUpProducts entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('evaluationpopupproducts'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
    }
    
}
