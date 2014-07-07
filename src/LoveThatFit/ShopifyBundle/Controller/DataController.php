<?php

namespace LoveThatFit\ShopifyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use LoveThatFit\ShopifyBundle\DependencyInjection\ShopifyCSVHelper;


class DataController extends Controller
{
    
    
    public function indexAction() {
        #$variants=array('product_name'=>'Gingham Blouse', 'brand_name'=>'New York and Co');
        $variants=array('product_name'=>'purple Top', 'brand_name'=>'Gap');
        
        #$data=  $this->get('admin.helper.productitem')->findDetailsByVariants($variants);
        #return new Response(json_encode($data));
        $form = $this->createFormBuilder()
                ->add('csvfile', 'file')
                ->getForm();
        return $this->render('LoveThatFitShopifyBundle:Data:import_csv.html.twig', array('form' => $form->createView())
        );
    }
    
    public function skuUploadAction(Request $request) {
        
        $form = $this->createFormBuilder()
                ->add('csvfile', 'file')
                ->getForm();
        $form->bindRequest($request);        
        $file = $form->get('csvfile');
        $filename = $file->getData();
        $pcsv = new ShopifyCSVHelper($filename);        
        $csv_data = $pcsv->convertToArray();
        $pp=$this->fillMatched($csv_data);
        return $this->render('LoveThatFitShopifyBundle:Data:_summary.html.twig', array('data' => $pp));
        return new Response(json_encode($pp));
        
     }
     private function fillMatched($csv_data){
         $tt=array();
         foreach ($csv_data as $key=>$value){
            $variants=array('product_name'=>$value['title'], 'brand_name'=>$value['vendor']);
            $dbp=  $this->get('admin.helper.productitem')->findDetailsByVariants($variants);
            array_push($tt, $this->match($value, $dbp));    
            #$tt[$value['title']]=$this->match($value, $dbp);
         }
         return $tt;
     }
     private function match($csvp, $dbp){
         $tt=array();
         foreach ($csvp['variant'] as $vk=>$vv){
             $item_id=$this->findItemByVariant($vv, $dbp);
             if($item_id){
                 $tt[$csvp['vendor']." -> ".$csvp['title']." ->Color:(" . $vv['Color'] .") ->Size:(". $vv['Size'] .") ->(". $vv['variant_sku'].")"] = 'Updated';
             }else{
                 $tt[$csvp['vendor']." -> ".$csvp['title']." ->Color:(".$vv['Color'] .") ->Size:(". $vv['Size'] .") ->(". $vv['variant_sku'].")"] = 'not matched';                 
             }
         }
         return $tt;
     }
     
     private function findItemByVariant($csv_v, $dbp){
         foreach ($dbp as $v){
             if ($csv_v['Size'] == $v['size_title'] && $csv_v['Color']==$v['color']){
         
                 $item = $this->get('admin.helper.productitem')->find($v['item_id']);
                 $item->setSKU($csv_v['variant_sku']);
                 $this->get('admin.helper.productitem')->save($item);
                 
                 return $v['item_id'];
             }
         }
         return null;
     }
     
}
