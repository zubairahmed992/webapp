<?php

namespace LoveThatFit\ShopifyBundle\DependencyInjection;
use LoveThatFit\AdminBundle\Entity\Product;
use LoveThatFit\AdminBundle\Entity\ProductColor;
use LoveThatFit\AdminBundle\Entity\ProductSize;
use LoveThatFit\AdminBundle\Entity\ProductSizeMeasurement;
use LoveThatFit\AdminBundle\Entity\ProductItem;


class ShopifyCSVHelper {

    private $product;
    private $row;
    private $previous_row;
    private $path;
    
//--------------------------------------------------------------------
    public function __construct($path) {
        $this->path = $path;
    }

    //------------------------------------------------------

    public function read($row_length=100) {
        if($row_length==null)$row_length=100;
        $this->row = 0;
        $this->previous_row = '';

        if (($handle = fopen($this->path, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, $row_length, ",")) !== FALSE) {
                $this->readProduct($data);
                $this->previous_row = $data;
                $this->row++;
            }
            fclose($handle);
            
            return $this->product;
        }
        return;
    }

//------------------------------------------------------

    private function readProduct($data) {
        $this->product['handle'] = $data[0];
        $this->product['title'] = $data[1];
        $this->product['body_html'] = $data[2];
        $this->product['vendor'] = $data[3];
        $this->product['type'] = $data[4];
        $this->product['tags'] = $data[5];
        $this->product['published'] = $data[6];
        $this->product['option1_name'] = $data[7];
        $this->product['option1_value'] = $data[8];
        $this->product['option2_name'] = $data[9];
        $this->product['option2_value'] = $data[10];
        $this->product['option3_name'] = $data[11];
        $this->product['option3_value'] = $data[12];
        $this->product['variant_sku'] = $data[13];
        $this->product['variant_grams'] = $data[14];
        $this->product['variant_inventory_tracker'] = $data[15];
        $this->product['variant_inventory_qty'] = $data[16];
        $this->product['variant_inventory_policy'] = $data[15];
        $this->product['variant_fulfillment_service'] = $data[18];
        return;
        $this->product['variant_price'] = $data[19];
        $this->product['variant_compare_at_price'] = $data[20];
        $this->product['variant_requires_shipping'] = $data[21];
        $this->product['variant_taxable'] = $data[22];
        $this->product['variant_barcode'] = $data[23];
        $this->product['image_src'] = $data[24];
        $this->product['image_alt_text'] = $data[25];
        $this->product['gift_card'] = $data[26];
        $this->product['seo_title'] = $data[27];
        $this->product['seo_description'] = $data[28];
        $this->product['gs_product_category'] = $data[29];
        $this->product['gs_gender'] = $data[30];
        $this->product['gs_age_group'] = $data[31];
        $this->product['gs_mpn'] = $data[32];
        $this->product['gs_adwords_grouping'] = $data[33];
        $this->product['gs_adwords_labels'] = $data[34];
        $this->product['gs_condition'] = $data[35];
        $this->product['gs_custom_product'] = $data[36];
    }

#---------------------------------------------------------------
    public function map($row_length=1000, $col=20) {

        $this->row = 0;
        $this->previous_row = '';

        if (($handle = fopen($this->path, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 300, ",")) !== FALSE) {
                //$this->readProduct($data);
                $str = $this->row . '  ';
                for ($i=0;$i<=$col;$i++){
                    $str.=$data[$i].', ';
                }
                $this->product[$this->row] = $str;
                $this->row++;
            }
            fclose($handle);
            
            return $this->product;
        }
        return;
    }


//-------------------------------------------------------
    private function initialCap($str){        
        return str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($str))));
    }
    
//-------------------------------------------------------
    private function makeSnake($str){                
        return str_replace(' ', '_', strtolower($str));
    }
    
    //-------------------------------------------------------
      private function removePercent($str){
        return str_replace('%', '', $str);
    }
}

?>
