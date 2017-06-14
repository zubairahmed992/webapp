<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LoveThatFit\AdminBundle\Form\Type\DeleteType;
use LoveThatFit\AdminBundle\Form\Type\MarketingTilesTypes;


class MarketingTilesController extends Controller {

//-----------------------------Banner List-------------------------------------------------------------

    public function indexAction($page_number, $sort = 'id') {
        $marketing_tiles = $this->get('admin.helper.marketingtiles')->findAll();
        return $this->render('LoveThatFitAdminBundle:MarketingTiles:index.html.twig', array('marketing_tiles' => $marketing_tiles));
    }

//-------------------------------Banner display-----------------------------------------------------------
    
    public function showAction($id) {
        $marketing_tiles = $this->get('admin.helper.marketingtiles')->findWithMarketingTilesId($id);
        //echo "<pre>"; print_r($marketing_tiles); die();
        $marketing_tiles_limit = $this->get('admin.helper.marketingtiles')->getRecordsCountWithCurrentBannerLimit($id);
        $page_number = ceil($this->get('admin.helper.utility')->getPageNumber($marketing_tiles_limit[0]['id']));
        $page_number = ($page_number == 0) ? 1 : $page_number;
        if (!$marketing_tiles) {
            $this->get('session')->setFlash('warning', 'Marketing Tiles not found!');
        }
        return $this->render('LoveThatFitAdminBundle:MarketingTiles:show.html.twig', array(
                    'marketing_tiles' => $marketing_tiles[0],
                    'page_number' => $page_number,
        ));
    }

    //------------------------------Create New Banner------------------------------------------------------------
    public function newAction() {
        $entity = $this->get('admin.helper.marketingtiles')->createNew();
        $max_sorting_number = $this->get('admin.helper.marketingtiles')->maxSortingNumber();
        $entity->setSorting($max_sorting_number[0]['max_sort'] + 1) ;
        $sorting = $entity->getSorting();
        $button_action = $entity->getButtonAction();
        $form = $this->createForm(new MarketingTilesTypes('add',$entity,$button_action,$sorting), $entity);
        return $this->render('LoveThatFitAdminBundle:MarketingTiles:new.html.twig', array(
                    'form' => $form->createView()
        ));
    }

    //-------------------------------Save Banner in database-----------------------------------------------------------
    public function createAction(Request $request) {
        $entity = $this->get('admin.helper.marketingtiles')->createNew();
        $sorting = $entity->getSorting();
        $button_action = $entity->getButtonAction();
        $form = $this->createForm(new MarketingTilesTypes('add',$entity,$button_action,$sorting), $entity);
        $form->bind($request);

            /*Conditions for handling Banner sorting*/
            $form_sorting_value = $entity->getSorting();

            /*If User added random sort number which is greater than max sort number then max sort will be set*/
            $max_sorting_number = $this->get('admin.helper.marketingtiles')->maxSortingNumber();
            
            if( isset($max_sorting_number[0]['max_sort']) && $form_sorting_value > $max_sorting_number[0]['max_sort']) {
                $entity->setSorting($max_sorting_number[0]['max_sort'] + 1) ;
                $form_sorting_value = $entity->getSorting();
            }

            $this->get('admin.helper.marketingtiles')->editBannerSorting($form_sorting_value, 'add');
            /*Conditions for handling Banner sorting*/

            $message_array = $this->get('admin.helper.marketingtiles')->save($entity);
            
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            if ($message_array['success']) {
                return $this->redirect($this->generateUrl('admin_marketing_tiles_show', array('id' => $entity->getId())));
            }

        return $this->render('LoveThatFitAdminBundle:MarketingTiles:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

//----------------------------------------Edit Banner--------------------------------------------------
    public function editAction($id) {
        $entity = $this->get('admin.helper.marketingtiles')->find($id);
        $sorting = $entity->getSorting();
        $button_action = $entity->getButtonAction();
        if(!$entity){       
            $this->get('session')->setFlash('warning', 'The Marketing Tiles can not be Created!');
        }else{
            $form = $this->createForm(new MarketingTilesTypes('edit',$entity,$button_action,$sorting), $entity);
            $deleteForm = $this->createForm(new DeleteType(), $entity);
        }
        return $this->render('LoveThatFitAdminBundle:MarketingTiles:edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity
                    ));
    }

//------------------------------------Update Banner------------------------------------------------------
    public function updateAction(Request $request, $id) {

        $entity = $this->get('admin.helper.marketingtiles')->find($id);
        $sorting = $entity->getSorting();
        $button_action = $entity->getButtonAction();
        if(!$entity){
            $this->get('session')->setFlash('warning', 'The Marketing Tiles not found!');
        }else{
            $form = $this->createForm(new MarketingTilesTypes('edit',$entity,$button_action,$sorting), $entity);
            $form->bind($request);

            if ($form->isValid()) {

                /*Conditions for handling Marketing Tiles sorting, if sorting value will be change then
                it will update sorting*/
                $db_banner_sorting =  (int) $request->request->get('sorting_db_value');
                $form_sorting_value = $entity->getSorting();               

                if($db_banner_sorting !== $form_sorting_value){
                    /*If User added random sort number which is greater than max sort number then max sort will be set*/
                    $max_sorting_number = $this->get('admin.helper.marketingtiles')->maxSortingNumber(); 
                    if($form_sorting_value > $max_sorting_number[0]['max_sort']) {
                        $entity->setSorting($max_sorting_number[0]['max_sort']);
                        $form_sorting_value = $entity->getSorting();
                    }
                    $this->get('admin.helper.marketingtiles')->editBannerSorting($form_sorting_value, 'update',$db_banner_sorting);
                    /*Conditions for handling Banner sorting*/
                }

                $message_array = $this->get('admin.helper.marketingtiles')->update($entity);

                $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
                if ($message_array['success'] == true) {
                    return $this->redirect($this->generateUrl('admin_marketingtiles'));
                }
            } else {
                $this->get('session')->setFlash('warning', 'Unable to update Marketing Tiles!');
            }
            $deleteForm = $this->createForm(new DeleteType(), $entity);
        }
        return $this->render('LoveThatFitAdminBundle:MarketingTiles:edit.html.twig', array(
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
            'entity' => $entity
        ));
    }

    //----------------------------------------Delete Marketing Tiles----------------------------------------------

    public function deleteAction($id) {
        $entity = $this->get('admin.helper.marketingtiles')->find($id);
        /*Conditions for handling Marketing Tiles sorting*/
        $selectedsortingcondition = $entity->getSorting();
        $this->get('admin.helper.marketingtiles')->editBannerSorting($selectedsortingcondition, 'delete');
        /*Conditions for handling Marketing Tiles sorting*/
        try {
            $message_array = $this->get('admin.helper.marketingtiles')->delete($id);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            return $this->redirect($this->generateUrl('admin_marketingtiles'));
        } catch (\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->setFlash('warning', 'This Marketing Tiles cannot be deleted!');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }

    //------------------------------------------------------------------------------------------
    public function standardsAction(){
       $standards = $this->get('admin.helper.size')->getDefaultArray();       
       return $this->render('LoveThatFitAdminBundle:Banner:standards.html.twig', array(
                    'specs' => $standards,
                    ));
       
       return new \Symfony\Component\HttpFoundation\Response (json_encode($standards));
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
        $output = $this->get('admin.helper.marketingtiles')->search($requestData);

        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
    }
}