<?php

namespace LoveThatFit\AdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\AdminBundle\Form\Type\DeleteType;
use LoveThatFit\AdminBundle\Form\Type\SizeChartType;
use Symfony\Component\HttpFoundation\Response;


class SizeChartController extends Controller {
//---------------------------------------------------------------------
    
    public function indexAction($page_number, $sort = 'id') {
        $size_with_pagination = $this->get('admin.helper.sizechart')->getListWithPagination($page_number, $sort);
        return $this->render('LoveThatFitAdminBundle:SizeChart:index.html.twig', $size_with_pagination);
    }
    
    
    
    public function showAction($id) {

        $specs = $this->get('admin.helper.sizechart')->findWithSpecs($id);
        $entity = $specs['entity'];
        
        $sizechart_limit = $this->get('admin.helper.sizechart')->getRecordsCountWithCurrentSizeChartLimit($id);
     
      
        $page_number=ceil($this->get('admin.helper.utility')->getPageNumber( $sizechart_limit[0]['id']));
        if($page_number==0){
       $page_number=1;
     }

        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
        }
        return $this->render('LoveThatFitAdminBundle:SizeChart:show.html.twig', array(
                    'sizechart' => $entity,
                    'page_number'=>$page_number,
                ));
    }    
    
   
            


    
     public function newAction() {        
        $entity = $this->get('admin.helper.sizechart')->createNew();
        $form = $this->createForm(new SizeChartType('add'), $entity);      
      return $this->render('LoveThatFitAdminBundle:SizeChart:new.html.twig', array(
                    'form' => $form->createView(),'allMixSizeTitles'=>json_encode($this->get('admin.helper.sizechart')->getMixSizeTitle())));
    }
    
    public function createAction(Request $request)
    {       
        $entity = $this->get('admin.helper.sizechart')->createNew();
        $form = $this->createForm(new SizeChartType('add'), $entity);
        $form->bindRequest($request);
        return new response(json_encode($entity->getTitle()));
        if($entity->getTarget()=='Dress' and $entity->getGender()=='m' )
        {
            $this->get('session')->setFlash('warning', 'Dresses can not be selected  for Male');
        }else
        {
        if ($form->isValid()) {

            $message_array = $this->get('admin.helper.sizechart')->save($entity);
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
         
            if ($message_array['success']) {
                return $this->redirect($this->generateUrl('admin_size_chart_show', array('id' => $entity->getId())));
            }
        } else {
            $this->get('session')->setFlash('warning', 'The Size chart can not be Created!');
        }
        }

        return $this->render('LoveThatFitAdminBundle:SizeChart:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
            'allMixSizeTitles'=>json_encode($this->get('admin.helper.sizechart')->getMixSizeTitle()),
            
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
    
    
    
    
    
    
    
    public function editAction($id)
    {
        $specs = $this->get('admin.helper.sizechart')->findWithSpecs($id);
        $entity = $specs['entity'];
        if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
        }
        $form = $this->createForm(new SizeChartType('edit'), $entity);
        return $this->render('LoveThatFitAdminBundle:SizeChart:edit.html.twig', array(
                    'form' => $form->createView(),                   
                    'entity' => $entity,
            'allMixSizeTitles'=>json_encode($this->get('admin.helper.sizechart')->getMixSizeTitle()),
            'womanSizeTitle'=>$this->get('admin.helper.sizechart')->getMixSizeTitleForWoman(),
            'manTopSizeTitle'=>$this->get('admin.helper.sizechart')->getMixSizeTitleForMenTop(),
            'manBottomSizeTitle'=>$this->get('admin.helper.sizechart')->getMixSizeTitleForMenBottom(),
            
            ));
    }
    
    
    public function updateAction(Request $request, $id)
    {
       $specs = $this->get('admin.helper.sizechart')->findWithSpecs($id);
       $entity = $specs['entity'];   
       $form = $this->createForm(new SizeChartType('edit'), $entity);
        $form->bindRequest($request);
      if ($specs['success'] == false) {
            $this->get('session')->setFlash($specs['message_type'], $specs['message']);
            return $this->redirect($this->generateUrl('admin_size_charts'));
        }
        if($entity->getTarget()=='Dress' and $entity->getGender()=='m' )
        {
            $this->get('session')->setFlash('warning', 'Dresses can not be selected  for Male');
        }else
        {        
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
                    'entity' => $entity));
    }
  public function searchSizeChartFormAction(){
      $brandList=$this->get('admin.helper.brand')->findAll();
      $genders=$this->get('admin.helper.utility')->getGenders();
      $target=$this->get('admin.helper.utility')->getTargets();
      $bodyType=$this->get('admin.helper.utility')->getBodyTypesSearching();
      return $this->render('LoveThatFitAdminBundle:SizeChart:sizeChartSearchForm.html.twig',array('brandList'=>$brandList,'genders'=>$genders,'target'=>$target,'bodyType'=>$bodyType));
  }  
 public function searchSizeChartResultAction(Request $request){
  $data = $request->request->all();
  $brand_id=$data['brand'];
  $target=$data['target'];
  $genders=$data['genders'];
 // $male=$genders['m'];
  if(isset($genders['0'])){
  $male=$genders['0'];}else{
      $male=null;
  }
  if (isset($genders['1'])){
  $female=$genders['1'];}else{
      $female=null;
  }
  $bodyType=$data['bodytype'];
  
#-----Pagination-----------------------------------#  
$page =$data['page'];

$searchResult=$this->get('admin.helper.sizechart')->searchSizeChartPagination($brand_id,$male,$female,$bodyType,$target,$page);
return $this->render('LoveThatFitAdminBundle:SizeChart:sizeChartSearchResult.html.twig',$searchResult);    
 }

}

