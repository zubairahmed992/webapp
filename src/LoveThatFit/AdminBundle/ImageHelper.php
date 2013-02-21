<?php

namespace LoveThatFit\AdminBundle;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ImageHelper {

    
var $conf;
    public function __construct($entity, $category)
    {
        $yaml = new Parser();
        $this->conf = $yaml->parse(file_get_contents('../app/config/image_helper.yml'));
        $this->entity=$entity;
        $this->category=$category;
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
    
    private function saveProduct()
    {
        $small = imagecreatetruecolor(110, 85);
        $source = imagecreatefromjpeg($this->entity->image);
        imagecopyresampled($small, $source, 0, 0, 0, 0, 110, 85, imagesx($source), imagesx($source));
        imagejpeg($small,$this->getUploadRootDir().'/led.jpg',75);
    }


}
