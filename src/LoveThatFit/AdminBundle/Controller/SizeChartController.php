<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Form\Type\DeleteType;
use LoveThatFit\AdminBundle\Form\Type\SizeChartType;
use LoveThatFit\AdminBundle\Form\Type\SizeChartDynamicType;
use LoveThatFit\AdminBundle\Form\Type\BrandSizeChartType;
use Symfony\Component\HttpFoundation\Response;

class SizeChartController extends Controller {

//---------------------------------------------------------------------


    public function indexAction() {
        $form = $this->createForm(new BrandSizeChartType());
        return $this->render('LoveThatFitAdminBundle:SizeChart:index.html.twig', array('form' => $form->createView(),
                    'rec_count' => $this->get('admin.helper.sizechart')->countAllSizeChartRecord(),
                    'maleSizeChart' => $this->get('admin.helper.sizechart')->getSizeChartByGender('m'),
                    'femaleSizeChart' => $this->get('admin.helper.sizechart')->getSizeChartByGender('f'),
                    'topSizeChart' => $this->get('admin.helper.sizechart')->getSizeChartByTarget('Top'),
                    'bottomSizeChart' => $this->get('admin.helper.sizechart')->getSizeChartByTarget('Bottom'),
                    'dressSizeChart' => $this->get('admin.helper.sizechart')->getSizeChartByTarget('Dress'),
                ));
    }
#------------------------------------------------------------
    public function getBrandSizeChartListAction($brand_id) {
        $brand = $this->get('admin.helper.brand')->find($brand_id);
        $sizechart = $this->get('admin.helper.sizechart')->getSizeChartByBrand($brand);
        if ($sizechart) {
            return $this->render('LoveThatFitAdminBundle:SizeChart:brand_sizechart_list.html.twig', array('sizechart' => $sizechart, 'brand' => $brand->getName()));
        } else {
            return new Response('Unable to find size chart');
        }
    }
#------------------------------------------------------------
    public function _indexAction($page_number, $sort = 'id') {
        $size_with_pagination = $this->get('admin.helper.sizechart')->getListWithPagination($page_number, $sort);
        return $this->render('LoveThatFitAdminBundle:SizeChart:index.html.twig', $size_with_pagination);
    }
#------------------------------------------------------------
    public function showAction($id) {

        $specs = $this->get('admin.helper.sizechart')->findWithSpecs($id);
        $entity = $specs['entity'];

        $sizechart_limit = $this->get('admin.helper.sizechart')->getRecordsCountWithCurrentSizeChartLimit($id);


        $page_number = ceil($this->get('admin.helper.utility')->getPageNumber($sizechart_limit[0]['id']));
        if ($page_number == 0) {
            $page_number = 1;
        }

        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
        }
        return $this->render('LoveThatFitAdminBundle:SizeChart:show.html.twig', array(
                    'sizechart' => $entity,
                    'page_number' => $page_number,
                ));
    }
#------------------------------------------------------------
    public function newAction() {
        $entity = $this->get('admin.helper.sizechart')->createNew();
        $getDefaultArray = $this->get('admin.helper.size')->getDefaultArray();
        $form = $this->createForm(new SizeChartType($this->get('admin.helper.size')->getAllSizeTitleType()), $entity);
        return $this->render('LoveThatFitAdminBundle:SizeChart:new.html.twig', array('form' => $form->createView(), 'allSizes' => json_encode($this->get('admin.helper.size')->getAllSizes()), 'allMixSizeTitles' => json_encode($this->get('admin.helper.sizechart')->getMixSizeTitle())));
    }
#------------------------------------------------------------
    public function dynamicNewAction() {
        $size_specs = $this->get('admin.helper.size')->getDefaultArray();
        #return new Response(json_encode($size_specs));
        $entity = $this->get('admin.helper.sizechart')->createNew();
        $form = $this->createForm(new SizeChartDynamicType($size_specs), $entity);
        return $this->render('LoveThatFitAdminBundle:SizeChart:dynamic_new.html.twig', array('form' => $form->createView(),
                    'size_specs' => $size_specs,
                ));
    }
#------------------------------------------------------------
    public function createAction(Request $request) {
        
        $data = $request->request->all();
        $new_size_chart = $this->get('admin.helper.sizechart')->fillInRequest($data['sizechart']);
        #return new Response(json_encode($new_size_chart->toArray()));
        #return new Response(json_encode($data['sizechart']));
        
        if ($new_size_chart->getTarget() == 'Dress' and $new_size_chart->getGender() == 'm') {
            $this->get('session')->setFlash('warning', 'Dresses can not be selected  for Male');
        } else {
            $message_array = $this->get('admin.helper.sizechart')->save($new_size_chart);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            if ($message_array['success']) {
                return $this->redirect($this->generateUrl('admin_size_chart_show', array('id' => $new_size_chart->getId())));
            }            
        }
        
        $form = $this->createForm(new SizeChartType($this->get('admin.helper.size')->getAllSizeTitleType()), $entity);
        $form->bindRequest($request);
        return $this->render('LoveThatFitAdminBundle:SizeChart:new.html.twig', array(
                    'entity' => $new_size_chart,
                    'form' => $form->createView(),
                    'allMixSizeTitles' => json_encode($this->get('admin.helper.sizechart')->getMixSizeTitle()),
                    'allSizes' => json_encode($this->get('admin.helper.size')->getAllSizes()),
                ));
    }

    
    public function _createAction(Request $request) {
        $entity = $this->get('admin.helper.sizechart')->createNew();
        $getDefaultArray = $this->get('admin.helper.size')->getDefaultArray();
        $form = $this->createForm(new SizeChartType($this->get('admin.helper.size')->getAllSizeTitleType()), $entity);
        $form->bindRequest($request);
        // return new response(json_encode($form->getErrorsAsString()));
        // var_dump($entity);
        if ($entity->getTarget() == 'Dress' and $entity->getGender() == 'm') {
            $this->get('session')->setFlash('warning', 'Dresses can not be selected  for Male');
        } else {

            //  if ($form->isValid()) {

            $message_array = $this->get('admin.helper.sizechart')->save($entity);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);

            if ($message_array['success']) {
                return $this->redirect($this->generateUrl('admin_size_chart_show', array('id' => $entity->getId())));
            }
            //   } else {
            $this->get('session')->setFlash('warning', 'The Size chart can not be Created!');
            //    }
        }

        return $this->render('LoveThatFitAdminBundle:SizeChart:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'allMixSizeTitles' => json_encode($this->get('admin.helper.sizechart')->getMixSizeTitle()),
                    'allSizes' => json_encode($this->get('admin.helper.size')->getAllSizes()),
                ));
    }
    //--------------------------Delete Size Chart-------------------
    public function deleteAction($id) {
        try {

            $message_array = $this->get('admin.helper.sizechart')->delete($id);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);

            return $this->redirect($this->generateUrl('admin_size_charts'));
        } catch (\Doctrine\DBAL\DBALException $e) {

            $this->get('session')->setFlash('warning', 'This Size cannot be deleted!');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }
#------------------------------------------------------------
    public function editAction($id) {
        $specs = $this->get('admin.helper.sizechart')->findWithSpecs($id);
        $entity = $specs['entity'];
        $getDefaultArray = $this->get('admin.helper.size')->getDefaultArray();
        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
        }
        $form = $this->createForm(new SizeChartType($this->get('admin.helper.size')->getAllSizeTitleType()), $entity);
        return $this->render('LoveThatFitAdminBundle:SizeChart:edit.html.twig', array(
                    'form' => $form->createView(),
                    'entity' => $entity,
                    'allSizes' => json_encode($this->get('admin.helper.size')->getAllSizes()),
                ));
    }
#------------------------------------------------------------
    public function updateAction(Request $request, $id) {
        $specs = $this->get('admin.helper.sizechart')->findWithSpecs($id);
        $entity = $specs['entity'];
        $getDefaultArray = $this->get('admin.helper.size')->getDefaultArray();
        $form = $this->createForm(new SizeChartType($this->get('admin.helper.size')->getAllSizeTitleType()), $entity);
        $form->bindRequest($request);
        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
            return $this->redirect($this->generateUrl('admin_size_charts'));
        }
        if ($entity->getTarget() == 'Dress' and $entity->getGender() == 'm') {
            $this->get('session')->setFlash('warning', 'Dresses can not be selected  for Male');
        } else {
            if ($form->isValid()) {
                $message_array = $this->get('admin.helper.sizechart')->update($entity);
                $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
                if ($message_array['success'] == true) {
                    return $this->redirect($this->generateUrl('admin_size_chart_show', array('id' => $entity->getId())));
                }
            } else {
                $this->get('session')->setFlash('warning', 'Unable to update Size Chart!');
            }
        }
        $deleteForm = $this->createForm(new DeleteType(), $entity);
        return $this->render('LoveThatFitAdminBundle:SizeChart:edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $entity,
                    'allSizes' => json_encode($this->get('admin.helper.size')->getAllSizes()),
                ));
    }
#------------------------------------------------------------
    public function searchSizeChartFormAction() {
        $brandList = $this->get('admin.helper.brand')->findAll();
        $genders = $this->get('admin.helper.utility')->getGenders();
        $target = $this->get('admin.helper.utility')->getTargets();
        $bodyType = $this->get('admin.helper.utility')->getBodyTypesSearching();
        return $this->render('LoveThatFitAdminBundle:SizeChart:sizeChartSearchForm.html.twig', array('brandList' => $brandList, 'genders' => $genders, 'target' => $target, 'bodyType' => $bodyType));
    }
#------------------------------------------------------------
    public function searchSizeChartResultAction(Request $request) {
        $data = $request->request->all();
        $brand_id = $data['brand'];
        $target = $data['target'];
        $genders = $data['genders'];
        // $male=$genders['m'];
        if (isset($genders['0'])) {
            $male = $genders['0'];
        } else {
            $male = null;
        }
        if (isset($genders['1'])) {
            $female = $genders['1'];
        } else {
            $female = null;
        }
        $bodyType = $data['bodytype'];

#-----Pagination-----------------------------------#  
        $page = $data['page'];

        $searchResult = $this->get('admin.helper.sizechart')->searchSizeChartPagination($brand_id, $male, $female, $bodyType, $target, $page);
        return $this->render('LoveThatFitAdminBundle:SizeChart:sizeChartSearchResult.html.twig', $searchResult);
    }

//------------------Brand List Of Size Charts----------------------------------------------- 

    public function getBrandSizeChartAction($page_number, $sort = 'id') {
        $size_with_pagination = $this->get('admin.helper.sizechart')->getListWithPagination($page_number, $sort);
        return $this->render('LoveThatFitAdminBundle:SizeChart:brand_sizechart.html.twig', $size_with_pagination);
    }

}

