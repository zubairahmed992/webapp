<?php

namespace LoveThatFit\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;




class ProductImagesController extends Controller {
    private $product_image_path;
    
    public function __construct(){
        $yaml = new Parser();
        $productImageModelPath =  $yaml->parse(file_get_contents('../app/config/image_helper.yml'));
        $this->product_image_path = $productImageModelPath['image_category']['product_models']['original']['dir'];
       
    }

    //-----------------------------Product Model Image -----------------------------------------------------------
    public function indexAction($product_id) {     
        $image_path = $this->product_image_path;
        $path = $this->getRequest()->getBasePath().'/';
        $productImageRecord = $this->get('admin.helper.productimage')->findByProductId($product_id);
    return $this->render('LoveThatFitAdminBundle:ProductImages:index.html.twig', array('product_image'=>$productImageRecord, 'image_path'=>$path.$image_path));
    }
   
//------------------------------Save Categories in database-----------------------------------------------------------
    public function createAction(Request $request) 
    {
        $decoded = $request->request->all();  
        $image_path = $this->product_image_path;
        if (!file_exists($image_path)) {
            mkdir($image_path, 0777, true);
        }
        $product = $this->get('admin.helper.product')->find($decoded['product_id']);
        $file = $_FILES["productimages"];
        foreach ($file['name'] as $key => $value) {
              $entity = $this->get('admin.helper.productimage')->createNew();
            $this->get('admin.helper.productimage')->save($entity,$file,$product,$decoded);
        } 
        return $this->indexAction($decoded['product_id']);
    }

    //------------------------------------Update Product Model Images------------------------------------------------------
    public function updateAction(Request $request, $id, $product_id) {
     $decoded = $request->request->all();  
     $entity = $this->get('admin.helper.productimage')->find($id);
        if(!$entity)
        {
            $this->get('session')->setFlash('warning', 'The product image not found!');
        }else{
            $entity->setImageTitle($decoded['image_title']);
            $entity->setImageSort($decoded['image_sort']);
            $this->get('admin.helper.productimage')->update($entity); 
            
        }
         return $this->indexAction($product_id);     
        
    }

    //----------------------------------------Delete ProductModelImages--------------------------------------------------

    public function deleteAction($id,$product_id=null) {
        try {     
            $message_array = $this->get('admin.helper.productimage')->delete($id);            
            $this->get('session')->setFlash($message_array['message_type'], $message_array['message']);
            return $this->indexAction($product_id);          
        } catch (\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->setFlash('warning', 'This Categories cannot be deleted!');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }
    
}