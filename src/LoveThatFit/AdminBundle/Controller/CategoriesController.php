<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\AdminBundle\Form\Type\DeleteType;
use LoveThatFit\AdminBundle\Form\Type\CategoriesTypes;


class CategoriesController extends Controller {

//-----------------------------Categories List-------------------------------------------------------------

    public function indexAction($page_number, $sort = 'id') {
        $categories = $this->get('admin.helper.categories')->findAll();
        return $this->render('LoveThatFitAdminBundle:Categories:index.html.twig', array('categories' => $categories));
    }

//-------------------------------Categories display-----------------------------------------------------------
    
    public function showAction($id) {
        $entity = $this->get('admin.helper.categories')->find($id);
        $categories_limit = $this->get('admin.helper.categories')->getRecordsCountWithCurrentCategoriesLimit($id);
        $page_number = ceil($this->get('admin.helper.utility')->getPageNumber($categories_limit[0]['id']));
        $page_number = $page_number == 0?1:$page_number;
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Categories not found!');
        }
        return $this->render('LoveThatFitAdminBundle:Categories:show.html.twig', array(
                    'categories' => $entity,
                    'page_number' => $page_number,
        ));
    }

    //------------------------------Create New Categories------------------------------------------------------------
    public function newAction() {
        $entity = $this->get('admin.helper.categories')->createNew();
        $getcategoriestreeview = $this->get('admin.helper.Categories')->getCategoriesTreeViewNew();

        $form = $this->createForm(new CategoriesTypes('add',$entity), $entity);

        return $this->render('LoveThatFitAdminBundle:Categories:new.html.twig', array(
                    'form' => $form->createView(),
                    'getcategoriestreeview' => $getcategoriestreeview));
    }

    //-------------------------------Save Categories in database-----------------------------------------------------------
    public function createAction(Request $request) {
        $entity = $this->get('admin.helper.Categories')->createNew();
        $getcategoriestreeview = $this->get('admin.helper.Categories')->getCategoriesTreeViewNew();

        $form = $this->createForm(new CategoriesTypes('add',$entity), $entity);
        $form->bind($request);

        if ($entity->getName() != null) {

            /*Set Parent Id*/
            $selected_category_id = $request->request->get('category_id');
            $selected_category_gender = $this->get('admin.helper.Categories')->findById($selected_category_id);

            if($selected_category_id != 0)
            $entity->setGender($selected_category_gender['gender']);

            $message_array = $this->get('admin.helper.Categories')->save($entity);

            /* Get Id for fetch top level id*/
            $catid = $entity->getId();

            /* Update Parent ID */
            if ($selected_category_id != 0 && $selected_category_id != $entity->getId()) {
                $this->get('admin.helper.Categories')->updateParent($entity->getId(), $selected_category_id);
            }

            /* Update TopLevelCategory */
            $top_level_category = $this->get('admin.helper.Categories')->getTopLevelCategory($catid);
            $this->get('admin.helper.Categories')->updateTopLevelCategory($catid, $top_level_category);


            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            if ($message_array['success']) {
                return $this->redirect($this->generateUrl('admin_category_show', array('id' => $entity->getId())));
            }
        } else {
            $this->get('session')->setFlash('warning', 'The Categories can not be Created!');
        }

        return $this->render('LoveThatFitAdminBundle:Categories:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'getcategoriestreeview' => $getcategoriestreeview
        ));
    }

//----------------------------------------Edit Categories--------------------------------------------------
    public function editAction($id) {

        $entity = $this->get('admin.helper.Categories')->find($id);
        $getcategoriestreeview = $this->get('admin.helper.Categories')->getCategoriesTreeViewNew();
        $getallcategories = $this->get('admin.helper.Categories')->findAllCategories();


        if(!$entity){       
        $this->get('session')->setFlash('warning', 'The Categories can not be Created!');
        }else{
        $form = $this->createForm(new CategoriesTypes('edit',$entity), $entity);
        $deleteForm = $this->createForm(new DeleteType(), $entity);
        }
        return $this->render('LoveThatFitAdminBundle:Categories:edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity,
                    'getallcategories' => $getallcategories,
                    'getcategoriestreeview' => $getcategoriestreeview,
                    ));
    }

//------------------------------------Update Categories------------------------------------------------------
    public function updateAction(Request $request, $id) {

        $entity = $this->get('admin.helper.Categories')->find($id);
        $getcategoriestreeview = $this->get('admin.helper.Categories')->getCategoriesTreeViewNew($id);
        $getallcategories = $this->get('admin.helper.Categories')->findAllCategories();
        if(!$entity)
        {
            $this->get('session')->setFlash('warning', 'The Categories not found!');
        }else{
        $form = $this->createForm(new CategoriesTypes('edit',$entity), $entity);
        $form->bind($request);

        if ($form->isValid()) {

            $selected_category_gender = $this->get('admin.helper.Categories')->findById($id);
            $entity->setGender($selected_category_gender['gender']);

            $message_array = $this->get('admin.helper.Categories')->update($entity);

            /* Get Id for fetch top level id*/
            $catid = $entity->getId();

            /* Add Parent Category Id into Child Category */
            $default_category_id = $request->request->get('selected_id');
            $selected_category_id = $request->request->get('category_id');
            $category_option = $request->request->get('category_option');

            if (($selected_category_id != 0) && $selected_category_id != $id) {
                    $this->get('admin.helper.Categories')->updateParent($id, $selected_category_id);
            }

            /* Update TopLevelCategory */
            $top_level_category = $this->get('admin.helper.Categories')->getTopLevelCategory($catid);
            $this->get('admin.helper.Categories')->updateTopLevelCategory($catid, $top_level_category);

            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            if ($message_array['success'] == true) {              
                return $this->redirect($this->generateUrl('admin_categories'));
            }
        } else {
            $this->get('session')->setFlash('warning', 'Unable to update Categories!');
        }
        $deleteForm = $this->createForm(new DeleteType(), $entity);        
        }
        return $this->render('LoveThatFitAdminBundle:Categories:edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity,
                    'getallcategories' => $getallcategories,
                    'getcategoriestreeview' => $getcategoriestreeview,
            ));
    }

    //----------------------------------------Delete Categories--------------------------------------------------

    public function deleteAction($id) {
        try {
            $message_array = $this->get('admin.helper.Categories')->delete($id);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            return $this->redirect($this->generateUrl('admin_categories'));
        } catch (\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->setFlash('warning', 'This Categories cannot be deleted!');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }

    //------------------------------------------------------------------------------------------
    public function standardsAction(){
       $standards = $this->get('admin.helper.size')->getDefaultArray();       
       return $this->render('LoveThatFitAdminBundle:Categories:standards.html.twig', array(
                    'specs' => $standards,
                    ));
       
       return new \Symfony\Component\HttpFoundation\Response (json_encode($standards));
    }

    public function sendNotificationsAction()
    {
        $decoded = $this->get('user.helper.user')->findAllUsersAuthDeviceToken();
        foreach ($decoded as $user) {
            $tokens = json_decode($user['device_tokens'], true);
            if (!empty($tokens['iphone'][0])) {
                $device_token =  $tokens['iphone'][0];
                if ($device_token != "") {
                    $push_response = $this->get('pushnotification.helper')
                        ->sendNotifyClothingType($device_token);
                }
            }
        }
        
        $entity = $this->get('admin.helper.cronNotification')->findByCronType("categories");
        $this->get('admin.helper.cronNotification')->update($entity, 0);

        $this->get('session')->setFlash('success', 'Push Notifications Send To All Users!');
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    public function addCronNotificationsAction()
    {
        $entity = $this->get('admin.helper.cronNotification')->findByCronType("categories");
        if(!$entity) {
            $this->get('session')->setFlash('warning', 'The Cron Type not found!');
        } else {
            $this->get('admin.helper.cronNotification')->update($entity, 1);
            $this->get('session')->setFlash('success', 'Cron has been set to send notifications!');
        }

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    public function fetchCategoryTree($result, $parent = 0, $spacing = '', $user_tree_array = '') {
        if ($result->num_rows >  0) {
            while ($row = $result->fetch_assoc()) {
                $user_tree_array[] = array("id" => $row['id'], "name" => $spacing . $row['name']);
                $user_tree_array = fetchCategoryTree($row['id'], $spacing . '&nbsp;&nbsp;', $user_tree_array);
            }
        }
        return $user_tree_array;
    }

    public function paginateAction(Request $request)
    {
        $requestData = $this->get('request')->request->all();
        $requestData['base_path'] = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getBasePath() . '/';
        $output = $this->get('admin.helper.categories')->search($requestData);

        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
    }


    //-------------------------------This Controller are using in Product Listing-------------------------------------------

    public function loadcategoriesAction($id) {
        $getallcategorieslist = $this->get('admin.helper.Categories')->getCategoriesTreeViewNew();
        $getselectedcategories = $this->get('admin.helper.Categories')->getSelectedCategories($id);
        $selectedcategories = array_column($getselectedcategories, 'id');

        return $this->render('LoveThatFitAdminBundle:CategoriesProduct:index.html.twig', array(
            'getallcategorieslist' => $getallcategorieslist,
            'selectedcategories' => $selectedcategories,
            'productId' => $id,
        ));
    }

    //Treat for Create
    public function savecategoriesAction(Request $request) {
        $requestData = $this->get('request')->request->all();

        $category_array = array();
        if(array_key_exists('category',$requestData)) {
            $category_array = $requestData['category'];
        }

        $productId = $requestData['product_id'];
        $message_array = $this->get('admin.helper.Categories')->saveProductCategories($productId, $category_array);

        $this->get('session')->setFlash('success', "Categories have been assorted with Product");
        return $this->redirect($this->generateUrl('admin_product_categories', array('id' => $productId)));

    }



}