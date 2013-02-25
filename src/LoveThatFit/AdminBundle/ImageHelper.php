<?php

namespace LoveThatFit\AdminBundle;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ImageHelper {

    

    public function __construct($category, $entity)
    {
        $yaml = new Parser();
        $conf = $yaml->parse(file_get_contents('../app/config/image_helper.yml'));
        $this->category=$category;
        $this->entity=$entity;
    }

    
    public function getImageConfiguration(){
        return $this->conf ;
    } 
    
    
    public function save()
    {
        switch ($this->category)
        {
            case 'brand':
                break;
            case 'product':
                break;
            case 'user':
                break;
            default:
                break;
        }
    }
    
   public function getImagePaths() {
        $n[]=null;
         foreach ($this->conf as $key => $value) {
             if ($key!='original'){
                 $value=$this->validateConf($value);
                 $n[$key] = $value['dir'] . '/' . $this->getUniqueCode() . $value['prefix'] . $key  . '.' . $this->getImageExtention();
             }
            }
        return $n;        
}

  
    //---------------------------------------------------------------------
    protected function getImageExtention(){
        return pathinfo($this->entity->getImage(), PATHINFO_EXTENSION);
    }

//---------------------------------------------------------------------
        protected function getUniqueCode(){
        return str_replace('.'.$this->getImageExtention(), '', $this->entity->getImage());
    }

//---------------------------------------------------------------------   
    
     
function validateConf ($value)
{
   $value['prefix']=strlen($value['prefix'])==0?'':'_'.$value['prefix'].'_';
   return $value;
}

}
