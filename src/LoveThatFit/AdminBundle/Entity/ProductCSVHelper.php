<?php


class ProductCSVHelper {
        //--------------------------------------------------------------------
    public function __construct() {
        
    }
   //------------------------------------------------------
   private $product;
   
    public function read($path){
        return 'foo bar';
        $row = 0;
        $previous_row='';
    if (($handle = fopen($path, "r")) !== FALSE) {
    
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);        
       
         if ($row==0){
            $product['garment_name']=$data[1];
            $product['retailer_name']= $data[4]; #~~~~~ Retailer
            $product['style']= $data[7]; #~~~~~ Style
            
        }
        
        if ($row==11){
            $product['stretch_type']=$data[1];
            $product['horizontal_stretch']= $data[3]; 
            $product['vertical_stretch']= $data[5]; 
            
        }
        
        if ($row==13){
            $product['fabric_weight']=$data[1];
            $product['structural_detail']= $data[4]; 
            $product['styling_detail']= $data[7]; 
            }
        if ($row==15){
            $product['fit_type']=$data[1];
            $product['layring']= $data[4]; 
            }        
        #~~~~~ Fit Priority
        if ($row==18){    
            $product['fit_priority']=array();
            
            //echo "<b>Fit Priority :</b>" ;
            //echo $data[1]. $previous_row[2]. ' : ' . $data[2]. $previous_row[3]. ' : ' . $data[3]. $previous_row[4]. ' : ' . $data[4]. $previous_row[5]. ' : ' . $data[5]. $previous_row[6]. ' : ' . $data[6]. $previous_row[7]."<br />\n";            
        } 
        #~~~~~ Colors
        if ($row==25){    
            $product['product_color']=  array($data[1], $data[2], $data[3],  $data[4],  $data[5],  $data[6] ,  $data[7],  $data[8],  $data[9],  $data[10],  $data[11]);
            //echo "<b>Colors :</b>" ;
            //echo $data[1] .', '. $data[2].', '. $data[3] .', '. $data[4].', '. $data[5].', '. $data[6] .', '. $data[7].', '. $data[8].', '. $data[9].', '. $data[10].', '. $data[11]."<br />\n";            
        }
        /*
        #---------- Fabric Content
        if ($row==28){    
            $product['']=$data[];
            $product['']= $data[]; 
            $product['']= $data[]; 
            
            echo "<b>Fabric Content </b><br/>";            
            echo $data[1] .' : '. $data[0] . "<br />\n";
            echo $data[3] .' : '. $data[2] . "<br />\n";
            echo $data[5] .' : '. $data[4] . "<br />\n";
            echo $data[7] .' : '. $data[6] . "<br />\n";
            echo $data[9] .' : '. $data[8] . "<br />\n";
            echo $previous_row[1] .' : '. $previous_row[0] . "<br />\n";
            echo $previous_row[3] .' : '. $previous_row[2] . "<br />\n";
            echo $previous_row[5] .' : '. $previous_row[4] . "<br />\n";
            echo $previous_row[7] .' : '. $previous_row[6] . "<br />\n";
            echo $previous_row[9] .' : '. $previous_row[8] . "<br />\n";            
          } 
        $this->getCSVSizeDetail($data, $row);
        */
        $previous_row=$data;
        
        $row++;
        /*
        for ($c=0; $c < $num; $c++) {
            echo $data[$c] . "<br />\n";
        }*/
    }
    fclose($handle);
    //return new Response(json_encode($data[$c]));
    return new Response('true');
}
      
    
    }
    
    private function getCSVSizeDetail($data, $row){
       if ($row>=5 && $row<=22){
        $this->getCSVFields($data, $row);
       }
    }
    private function getCSVFields($data, $row){
       
        echo $data[24]." ".$data[32]." ".$data[47]." ".$data[54]." ".$data[63]." ".$data[71]." ".$data[79]." ".$data[87]." ".$data[95]."<br>";
       
    }

    //------------------------------------------------------
   
    public function readProductCsvAction() {
        $row = 0;
        $previous_row = '';
        if (($handle = fopen("../app/config/LaceBlouse.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {                
               if ($row >= 5 && $row <= 22) {
                        #garment_measurement_flat	stretch_type_percentage	garment_measurement_stretch_fit	maximum_body_measurement ideal_body_size_high | ideal_body_size_low			
                        echo "00  |" . $data[23].":".$data[25].":".$data[26].":".$data[27].":".$data[28].":".$data[29].":".$data[30]."<br>";
                        echo "0   |" . $data[31].":".$data[32].":".$data[33].":".$data[34].":".$data[35].":".$data[36].":".$data[37]."<br>";
                        echo "2   |" . $data[39].":".$data[33].":".$data[34].":".$data[35].":".$data[36].":".$data[37].":".$data[38]."<br>";
                        echo "4   |" . $data[47].":".$data[48].":".$data[49].":".$data[50].":".$data[51].":".$data[52].":".$data[53]."<br>";
                        echo "6   |" . $data[55].":".$data[56].":".$data[57].":".$data[58].":".$data[59].":".$data[60].":".$data[61]."<br>";
                        echo "8   |" . $data[63].":".$data[64].":".$data[65].":".$data[66].":".$data[67].":".$data[68].":".$data[69]."<br>";
                        echo "10  |" . $data[71].":".$data[72].":".$data[73].":".$data[74].":".$data[75].":".$data[76].":".$data[77]."<br>";
                        echo "12  |" . $data[79].":".$data[80].":".$data[81].":".$data[82].":".$data[83].":".$data[84].":".$data[85]."<br>";
                        echo "14  |" . $data[87].":".$data[88].":".$data[89].":".$data[90].":".$data[91].":".$data[92].":".$data[93]."<br>";
                        echo "16  |" . $data[95].":".$data[96].":".$data[97].":".$data[98].":".$data[99].":".$data[100].":".$data[101]."<br>";

                }
                echo "<br>";
                $previous_row = $data;
                $row++;
            }
            fclose($handle);
            return new Response('true');
        }
      
}
    
}

?>
