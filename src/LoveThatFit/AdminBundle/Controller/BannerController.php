<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\AdminBundle\Form\Type\DeleteType;
use LoveThatFit\AdminBundle\Form\Type\BannerTypes;


class BannerController extends Controller
{

//-----------------------------Banner List-------------------------------------------------------------

    public function indexAction($page_number, $sort = 'id')
    {
        $banner = $this->get('admin.helper.banner')->findAll();
        return $this->render('LoveThatFitAdminBundle:Banner:index.html.twig', array('banner' => $banner));
    }

//-------------------------------Banner display-----------------------------------------------------------

    public function showAction($id)
    {
        $entity = $this->get('admin.helper.banner')->findWithCategoryName($id);
        $banner_limit = $this->get('admin.helper.banner')->getRecordsCountWithCurrentBannerLimit($id);
        $page_number = ceil($this->get('admin.helper.utility')->getPageNumber($banner_limit[0]['id']));

        $page_number = $page_number == 0 ? 1 : $page_number;
        if (!$entity) {
            $this->get('session')->setFlash('warning', 'Banner not found!');
        }
        return $this->render('LoveThatFitAdminBundle:Banner:show.html.twig', array(
            'banner' => $entity[0],
            'page_number' => $page_number,
        ));
    }

    //------------------------------Create New Banner------------------------------------------------------------
    public function newAction()
    {
        $entity = $this->get('admin.helper.banner')->createNew();
        $getcategoriestreeview = $this->get('admin.helper.Categories')->getCategoriesTreeViewNew();
        $getbannerlist = $this->get('admin.helper.banner')->getBannerlist();
        $shopLook = $this->get('admin.helper.shoplook')->findByParams(array('app_version' => '3'));
        $product_list = $this->get('admin.helper.product')->idNameListEnabledProduct();
        $brand_list = $this->get('admin.helper.brand')->getBrandNameId();
        $colors = $this->get('admin.helper.productcolor')->getDistinctColors();

        $form = $this->createForm(new BannerTypes('add', $entity), $entity);

        return $this->render('LoveThatFitAdminBundle:Banner:new.html.twig', array(
            'form' => $form->createView(),
            'getcategoriestreeview' => $getcategoriestreeview,
            'getbannerlist' => $getbannerlist,
            'shop_look' => $shopLook,
            'product_list' => $product_list,
            'brand_list' => $brand_list,
            'colors' => $colors,
        ));
    }

    //-------------------------------Save Banner in database-----------------------------------------------------------
    public function createAction(Request $request)
    {
        $entity = $this->get('admin.helper.Banner')->createNew();
        $getcategoriestreeview = $this->get('admin.helper.Categories')->getCategoriesTreeViewNew();
        $getbannerlist = $this->get('admin.helper.banner')->getBannerlist();

        $form = $this->createForm(new BannerTypes('add', $entity), $entity);
        $form->bind($request);

        if ($entity->getBannerType() != null) {
            $selected_banner_id = $request->request->get('banner_list_id');
            $shoplook           = $request->request->get('shop_look');
            $product_id         = $request->request->get('product_id');

            $banner_filter = '';
            if ($entity->getBannerType() == 8) {

                if ($request->request->get('category_id_multi') != null) {
                    $banner_filter['category'] = $request->request->get('category_id_multi');
                }
                if (!empty($request->request->get('brand_id'))) {
                    $banner_filter['brand'] = $request->request->get('brand_id');
                }
                if (!empty($entity->getPriceMin())) {
                    $banner_filter['min_price'] = $entity->getPriceMin();
                    if (empty($entity->getPriceMax())) {
                        $banner_filter['max_price'] = $entity->getPriceMin();
                    }
                }
                if (!empty($entity->getPriceMax())) {
                    $banner_filter['max_price'] = $entity->getPriceMax();
                    if (empty($entity->getPriceMin())) {
                        $banner_filter['min_price'] = $entity->getPriceMax();
                    }
                }
                $entity->setPriceMin(null);
                $entity->setPriceMax(null);
                if (!empty($request->request->get('color_id'))) {
                    $banner_filter['color'] = $request->request->get('color_id');
                }

                $banner_filter = json_encode($banner_filter);
            }

            /*Conditions for handling Banner sorting*/
            $selectedbannercondition = null;
            $form_displayscreen_value = $entity->getDisplayScreen();
            $form_sorting_value = $entity->getSorting();

            if ($selected_banner_id == '0') {
                $selectedbannercondition = null;
            } else {
                $selectedbannercondition = $selected_banner_id;
            }

            /*If User added random sort number which is greater than max sort number then max sort will be set*/
            $max_sorting_number = $this->get('admin.helper.Banner')->maxSortingNumber($form_sorting_value, $selectedbannercondition, $form_displayscreen_value);
            if ($form_sorting_value > $max_sorting_number[0]['max_sort']) {
                $entity->setSorting($max_sorting_number[0]['max_sort'] + 1);
                $form_sorting_value = $entity->getSorting();
            }

            $this->get('admin.helper.Banner')->editBannerSorting($form_sorting_value, 'add', $selectedbannercondition, $form_displayscreen_value);
            /*Conditions for handling Banner sorting*/

            if($shoplook > 0){
                $shoplook_entity = $this->get('admin.helper.shoplook')->find($shoplook);
                $entity->setBannerShoplook($shoplook_entity);
            }

            if($product_id > 0){
                $product_entity = $this->get('admin.helper.product')->find($product_id);
                $entity->setBannerProduct($product_entity);
            }

            if (!empty($banner_filter)) {
                $banner_filter = json_encode($banner_filter);
            }
            $entity->setBannerFilter($banner_filter);

            $message_array = $this->get('admin.helper.Banner')->save($entity);
            if ($selected_banner_id == '0') {
                $entity->setParentId(null);
            } else {
                $entity->setParentId($selected_banner_id);
            }
            $this->get('admin.helper.Banner')->updateParent($entity->getId(), $entity->getParentId());

            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            if ($message_array['success']) {
                return $this->redirect($this->generateUrl('admin_banner_show', array('id' => $entity->getId())));
            }
        } else {
            $this->get('session')->setFlash('warning', 'The Banner can not be Created!');
        }

        return $this->render('LoveThatFitAdminBundle:Banner:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'getcategoriestreeview' => $getcategoriestreeview,
            'getbannerlist' => $getbannerlist,
        ));
    }

//----------------------------------------Edit Banner--------------------------------------------------
    public function editAction($id)
    {
        $shopLookId = 0;
        $productId = 0;
        $selectedbrand = 0;
        $selectedcolor = '';
        $selectedcategory = '';
        $entity = $this->get('admin.helper.Banner')->find($id);
        $getcategoriestreeview = $this->get('admin.helper.Categories')->getCategoriesTreeViewNew();
        $getbannerlist = $this->get('admin.helper.banner')->getBannerlist();
        $getallcategories = $this->get('admin.helper.Categories')->findAllCategories();
        $shopLook = $this->get('admin.helper.shoplook')->findByParams(array('app_version' => '3'));
        $product_list = $this->get('admin.helper.product')->idNameListEnabledProduct();
        $brand_list = $this->get('admin.helper.brand')->getBrandNameId();
        $colors = $this->get('admin.helper.productcolor')->getDistinctColors();

         $filter = json_decode(json_decode($entity->getBannerFilter(), true), true);
         if(isset($filter['category'])) {
             $selectedcategory = $filter['category'];
         }

        if(isset($filter['brand'])) {
            $selectedbrand = $filter['brand'];
        }

        if(isset($filter['color'])) {
            $selectedcolor = $filter['color'];
        }

        $min_price = '';
        $max_price = '';
        if(isset($filter['min_price'])) {
            $min_price = $filter['min_price'];
            $max_price = $filter['max_price'];
        }

        if($entity->getBannerShoplook() != null){
            $shopLookId = $entity->getBannerShoplook()->getId();
        }

        if($entity->getBannerProduct() != null){
            $productId = $entity->getBannerProduct()->getId();
        }

        if (!$entity) {
            $this->get('session')->setFlash('warning', 'The Banner can not be Created!');
        } else {
            $form = $this->createForm(new BannerTypes('edit', $entity), $entity);
            $deleteForm = $this->createForm(new DeleteType(), $entity);
        }
        return $this->render('LoveThatFitAdminBundle:Banner:edit.html.twig', array(
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
            'entity' => $entity,
            'getallcategories' => $getallcategories,
            'getcategoriestreeview' => $getcategoriestreeview,
            'getbannerlist' => $getbannerlist,
            'shop_look' => $shopLook,
            'shoplookId' => $shopLookId,
            'product_list' => $product_list,
            'productId' => $productId,
            'brand_list' => $brand_list,
            'colors' => $colors,
            'selectedbrand' => $selectedbrand,
            'selectedcolor' => $selectedcolor,
            'selectedcategory' => $selectedcategory,
            'min_price' => $min_price,
            'max_price' => $max_price,
        ));
    }

//------------------------------------Update Banner------------------------------------------------------
    public function updateAction(Request $request, $id)
    {

        $entity = $this->get('admin.helper.Banner')->find($id);
        $getcategoriestreeview = $this->get('admin.helper.Categories')->getCategoriesTreeViewNew($id);
        $getbannerlist = $this->get('admin.helper.banner')->getBannerlist();
        $getallcategories = $this->get('admin.helper.Categories')->findAllCategories();

        if (!$entity) {
            $this->get('session')->setFlash('warning', 'The Banner not found!');
        } else {
            $form = $this->createForm(new BannerTypes('edit', $entity), $entity);
            $form->bind($request);

            if ($form->isValid()) {

                $selected_banner_id = $request->request->get('banner_list_id');
                $shoplook           = $request->request->get('shop_look');
                $product_id         = $request->request->get('product_id');

                $banner_filter = '';
                if ($entity->getBannerType() == 8) {

                    if (!empty($request->request->get('category_id_multi'))) {
                        $banner_filter['category'] = $request->request->get('category_id_multi');
                    }
                    if (!empty($request->request->get('brand_id'))) {
                        $banner_filter['brand'] = $request->request->get('brand_id');
                    }
                    if (!empty($entity->getPriceMin())) {
                        $banner_filter['min_price'] = $entity->getPriceMin();
                        if (empty($entity->getPriceMax())) {
                            $banner_filter['max_price'] = $entity->getPriceMin();
                        }
                    }
                    if (!empty($entity->getPriceMax())) {
                        $banner_filter['max_price'] = $entity->getPriceMax();
                        if (empty($entity->getPriceMin())) {
                            $banner_filter['min_price'] = $entity->getPriceMax();
                        }
                    }
                    $entity->setPriceMin(null);
                    $entity->setPriceMax(null);
                    if (!empty($request->request->get('color_id'))) {
                        $banner_filter['color'] = $request->request->get('color_id');
                    }

                    $banner_filter = json_encode($banner_filter);
                }


                /*Conditions for handling Banner sorting, if sorting value will be change then
                it will update sorting*/
                $db_banner_sorting = (int)$request->request->get('sorting_db_value');

                $db_banner_displayscreen = $request->request->get('displayscreen_db_value');
                $db_banner_parent = (int)$request->request->get('parent_db_value');
                if ($db_banner_parent == 0) {
                    $db_banner_parent = '';
                }

                $form_sorting_value = $entity->getSorting();

                /*var_dump($form_sorting_value); die;*/

                $selectedbannercondition = $entity->getParentId();
                $form_displayscreen_value = $entity->getDisplayScreen();

                if (($db_banner_sorting !== $form_sorting_value) || ($db_banner_displayscreen !== $form_displayscreen_value) || ($db_banner_parent != $selectedbannercondition)) {

                    /*If User added random sort number which is greater than max sort number then max sort will be set*/
                    $max_sorting_number = $this->get('admin.helper.Banner')->maxSortingNumber($form_sorting_value, $selectedbannercondition, $form_displayscreen_value);
                    if ($form_sorting_value > $max_sorting_number[0]['max_sort']) {
                        $entity->setSorting($max_sorting_number[0]['max_sort']);
                        $form_sorting_value = $entity->getSorting();
                    }

                    $this->get('admin.helper.Banner')->editBannerSorting($form_sorting_value, 'update', $selectedbannercondition, $form_displayscreen_value, $db_banner_sorting);
                    /*Conditions for handling Banner sorting*/
                }


                if ($selected_banner_id == '0' || $selected_banner_id == $entity->getId()) {
                    $entity->setParentId(null);
                } else {
                    $entity->setParentId($selected_banner_id);
                }

                if($shoplook > 0){
                    $shoplook_entity = $this->get('admin.helper.shoplook')->find($shoplook);
                    $entity->setBannerShoplook($shoplook_entity);
                }

                if ($product_id > 0) {
                    $product_entity = $this->get('admin.helper.product')->find($product_id);
                    $entity->setBannerProduct($product_entity);
                }

                if (!empty($banner_filter)) {
                    $banner_filter = json_encode($banner_filter);
                }
                $entity->setBannerFilter($banner_filter);

                $message_array = $this->get('admin.helper.Banner')->update($entity);

                $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
                if ($message_array['success'] == true) {
                    return $this->redirect($this->generateUrl('admin_banners'));
                }
            } else {
                $this->get('session')->setFlash('warning', 'Unable to update Banner!');
            }
            $deleteForm = $this->createForm(new DeleteType(), $entity);
        }
        return $this->render('LoveThatFitAdminBundle:Banner:edit.html.twig', array(
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
            'entity' => $entity,
            'getallcategories' => $getallcategories,
            'getcategoriestreeview' => $getcategoriestreeview,
            'getbannerlist' => $getbannerlist,
        ));
    }

    //----------------------------------------Delete Banner--------------------------------------------------

    public function deleteAction($id)
    {
        $entity = $this->get('admin.helper.Banner')->find($id);

        /*Conditions for handling Banner sorting*/
        $selectedbannercondition = $entity->getParentId();
        $displayscreencondition = $entity->getDisplayScreen();
        $selectedsortingcondition = $entity->getSorting();
        $this->get('admin.helper.Banner')->editBannerSorting($selectedsortingcondition, 'delete', $selectedbannercondition, $displayscreencondition);
        /*Conditions for handling Banner sorting*/

        try {
            $message_array = $this->get('admin.helper.Banner')->delete($id);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            return $this->redirect($this->generateUrl('admin_banners'));
        } catch (\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->setFlash('warning', 'This Banner cannot be deleted!');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }

    //------------------------------------------------------------------------------------------
    public function standardsAction()
    {
        $standards = $this->get('admin.helper.size')->getDefaultArray();
        return $this->render('LoveThatFitAdminBundle:Banner:standards.html.twig', array(
            'specs' => $standards,
        ));

        return new \Symfony\Component\HttpFoundation\Response (json_encode($standards));
    }

    public function fetchCategoryTree($result, $parent = 0, $spacing = '', $user_tree_array = '')
    {
        if ($result->num_rows > 0) {
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
        $output = $this->get('admin.helper.banner')->search($requestData);

        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
    }
}