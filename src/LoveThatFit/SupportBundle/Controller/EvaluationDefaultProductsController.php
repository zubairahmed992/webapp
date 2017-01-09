<?php

namespace LoveThatFit\SupportBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LoveThatFit\SupportBundle\Entity\EvaluationDefaultProducts;
use LoveThatFit\SupportBundle\Form\EvaluationDefaultProductsType;
use Symfony\Component\Form\Forms;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use LoveThatFit\AdminBundle\Entity\Product;

/**
 * EvaluationDefaultProducts controller.
 *
 * @Route("/evaluationdefaultproducts")
 */
class EvaluationDefaultProductsController extends Controller
{
    /**
     * Lists all EvaluationDefaultProducts entities.
     *
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();


        $entities = $em->getRepository('LoveThatFitSupportBundle:EvaluationDefaultProducts')->findAll();
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
                
                $products[$product->getID()]['title'] = $product->getName();
                $products[$product->getID()]['brand'] = $product->getBrand()->getName();

                $productSize = $product->getProductSizes();
                if ($productSize) {
                    foreach ($productSize as $size) {
                        $productSizes[$size->getID()] = $size->getTitle();
                    }
                }

            }

        }



        return $this->render('LoveThatFitSupportBundle:EvaluationDefaultProducts:index.html.twig',
            array(
                'entities' => $entities,
                'products' => $products,
                'product_sizes' => $productSizes
            )
        );

    }

    /**
     * Finds and displays a EvaluationDefaultProducts entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LoveThatFitSupportBundle:EvaluationDefaultProducts')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EvaluationDefaultProducts entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LoveThatFitSupportBundle:EvaluationDefaultProducts:show.html.twig',
            array(
                'entity' => $entity,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to create a new EvaluationDefaultProducts entity.
     *
     */
    public function newAction()
    {

        $entity = new EvaluationDefaultProducts();
        $form = $this->createForm(new EvaluationDefaultProductsType(), $entity);


        //Current Product
        $em = $this->getDoctrine()->getManager();
        $existingProductsList = $em->getRepository('LoveThatFitSupportBundle:EvaluationDefaultProducts')->findAll();
        $existProducts = array();
        if ($existingProductsList) {
            foreach ($existingProductsList as $key=>$product) {
                $existProducts[$key] = $product->getProductID();
            }

        }


        return $this->render('LoveThatFitSupportBundle:EvaluationDefaultProducts:new.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
                'exists_products' => $existProducts,
            )
        );

    }

    /**
     * Creates a new EvaluationDefaultProducts entity.
     */
    public function createAction(Request $request)
    {
        $entity = new EvaluationDefaultProducts();
        $form = $this->createForm(new EvaluationDefaultProductsType(), $entity);
        $form->bind($request);

        $productInfo = $request->request->all();
        if ($productInfo['lovethatfit_supportbundle_evaluationdefaultproductstype']['product_id'] == "" || $productInfo['lovethatfit_supportbundle_evaluationdefaultproductstype']['product_sizes'] == "") {
            $form->isValid();
        } else {

            $productID = $productInfo['lovethatfit_supportbundle_evaluationdefaultproductstype']['product_id'];
            $productSizes = $productInfo['lovethatfit_supportbundle_evaluationdefaultproductstype']['product_sizes'];


            $entity->setProductId($productID);
            $entity->setProductSizes(implode(',', $productSizes));

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('evaluationdefaultproducts'));
        }
        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing EvaluationDefaultProducts entity.
     *
     */
    public function editAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LoveThatFitSupportBundle:EvaluationDefaultProducts')->find($id);


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EvaluationDefaultProducts entity.');
        }

        //All exists Product
        $em = $this->getDoctrine()->getManager();
        $existingProductsList = $em->getRepository('LoveThatFitSupportBundle:EvaluationDefaultProducts')->findAll();
        $existProducts = array();
        if ($existingProductsList) {
            foreach ($existingProductsList as $key=>$product) {
                $existProducts[$key] = $product->getProductID();
            }

        }


        $editForm = $this->createForm(new EvaluationDefaultProductsType(), $entity);
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
                if($size->getDisabled() == 0) {
                    $options[$size->getID()] = $size->getTitle();
                }
            }
        }

        $SelectedSizes = explode(',', $entity->getProductSizes());


        return $this->render('LoveThatFitSupportBundle:EvaluationDefaultProducts:edit.html.twig',
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
     * Edits an existing EvaluationDefaultProducts entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LoveThatFitSupportBundle:EvaluationDefaultProducts')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EvaluationDefaultProducts entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new EvaluationDefaultProductsType(), $entity);
        $editForm->bind($request);

        $productInfo = $request->request->all();

        if ($productInfo['lovethatfit_supportbundle_evaluationdefaultproductstype']['product_id'] == "" || $productInfo['lovethatfit_supportbundle_evaluationdefaultproductstype']['product_sizes'] == "") {
            $editForm->isValid();
        } else {

            $productID = $productInfo['lovethatfit_supportbundle_evaluationdefaultproductstype']['product_id'];
            $productSizes = $productInfo['lovethatfit_supportbundle_evaluationdefaultproductstype']['product_sizes'];


            $entity->setProductId($productID);
            $entity->setProductSizes(implode(',', $productSizes));

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('evaluationdefaultproducts_edit', array('id' => $id)));
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
     * Deletes a EvaluationDefaultProducts entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LoveThatFitSupportBundle:EvaluationDefaultProducts')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find EvaluationDefaultProducts entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('evaluationdefaultproducts'));
    }


    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
    }

    /**
     * This will delete entry from the database base on the URL
     *
     */
    public function createDeleteUrlBaseAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LoveThatFitSupportBundle:EvaluationDefaultProducts')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find product.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('evaluationdefaultproducts'));
    }


    /**
     * Deletes a EvaluationDefaultProducts entity.
     *
     */
    public function sizesAction(Request $request)
    {

        // is it an Ajax request
        if (!$request->isXmlHttpRequest()) {
            echo '';
            exit;
        }

        $productID = false;
        $product = $request->request->all();

        if (isset($product['id']) && intval($product['id'])) {
            $productID = intval($product['id']);
        }

        if (!$productID) {
            echo '';
            exit;
        }

        $productsInfo = $this->get('doctrine')
            ->getRepository('LoveThatFitAdminBundle:Product')
            ->find($productID);

        $options = '';
        if ($productsInfo->getProductSizes()) {
            $productSizes = $productsInfo->getProductSizes();
            foreach ($productSizes as $size) {
                if($size->getDisabled() == 0 ) {
                    $options .= '<option value="' . $size->getID() . '">' . $size->getTitle() . '</option>';
                }

            }
        }

        echo $options;
        exit;
    }


}
