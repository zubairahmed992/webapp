<?php

namespace LoveThatFit\AdminBundle;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ImageHelper {

    protected $category;
    protected $entity;
    protected $conf;
    protected $image;
    //--------------------------------------------------------------------
    public function __construct($category, $entity) {
        $yaml = new Parser();
        $conf = $yaml->parse(file_get_contents('../app/config/image_helper.yml'));
        
        $this->category = $category;
        $this->entity = $entity;        
        $this->conf = $conf['image_category'][$category];        
        $this->image=$entity->getImage();
        
    }
    //--------------------------------------------------------------------
    public function getImageConfiguration() {
        return $this->conf;
    }
//--------------------------------------------------------------------
  
    
     public function upload() {
        
        if (null === $this->entity->file) {
            return;
        }
        
        $previous_image=$this->image;
        $ext = pathinfo($this->entity->file->getClientOriginalName(), PATHINFO_EXTENSION);
        
        $this->image=uniqid() .'.'. $ext;
        $this->entity->setImage($this->image);        
        $this->entity->file->move(
                $this->getUploadRootDir(), $this->image
        );
        
        $this->resize_image();
        //if record is being updated, then delete previous images
        if ($this->entity->getId())
            $this->deleteImages($previous_image); 
        
        $this->entity->file = null;
    }
//--------------------------------------------------------------------
    public function uploadProductTempImage()
    {
        if ($this->category=='product'){
        $ext = pathinfo($this->entity->getAbsoluteTempPath(), PATHINFO_EXTENSION);
        
        $new_name = uniqid() .'.'. $ext;
        $previous_image=$this->image;
        
        $dest=$this->getUploadRootDir().'/'. $new_name;
        rename($this->entity->getAbsoluteTempPath(),$dest);
        
        $this->image=$new_name;
        $this->entity->setImage($this->image);        
        $this->resize_image();        
        
        if ($this->entity->getId())
            $this->deleteImages($previous_image); 
        
        $this->entity->file = null;
        }
    }
    //--------------------------------------------------------------------

    private function resize_image() {

        $filename = $this->getAbsolutePath();
        $image_info = getimagesize($filename);
        $image_type = $image_info[2];
        
        $conf = $this->getImageConfiguration(); //read yml to conf variable

        switch ($image_type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($filename);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($filename);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($filename);
                break;
        }

        //scal down dimentions of the image, nearest possible to the given standard dimentions
        $resize_dimention=$this->getResizeDimentions();

        foreach ($conf as $key => $value) {
            if ($key != 'original') {
                $value = $this->validateConf($value);
               
                $img_new = imagecreatetruecolor($resize_dimention[$key]['width'], $resize_dimention[$key]['height']);
                // To make the image pixel tranparent //
                imagealphablending($img_new, false);
                imagesavealpha($img_new,true);
                $transparent = imagecolorallocatealpha($img_new, 255, 255, 255, 127);
                imagefilledrectangle($img_new, 0, 0, $resize_dimention[$key]['width'],$resize_dimention[$key]['height'], $transparent);
               
                
                imagecopyresampled($img_new, $source, 0, 0, 0, 0, $resize_dimention[$key]['width'], $resize_dimention[$key]['height'], imagesx($source), imagesy($source));

                if (!is_dir($value['dir'])) {
                    mkdir($value['dir'], 0700);
                }

                switch ($image_type) {
                    case IMAGETYPE_JPEG:
                        imagejpeg($img_new, $this->generateThisImagePath($key, $value), 75);
                        break;
                    case IMAGETYPE_GIF:
                        imagegif($img_new, $this->generateThisImagePath($key, $value));
                        break;
                    case IMAGETYPE_PNG:
                        imagepng($img_new, $this->generateThisImagePath($key, $value));
                        break;
                }
         
            

            }
        }
    }
//---------------------------------------------------------------------

    public function getResizeDimentions() {
        
        $image_info = getimagesize($this->getAbsolutePath());
        $iw = $image_info[0] ;
        $ih = $image_info [1];

        foreach ($this->conf as $key => $value) {
            if ($key != 'original') {
                $percent = $iw > $ih ? $value['width'] / $iw : $value['height'] / $ih;
                $n[$key]['width'] = round($iw * $percent);
                $n[$key]['height'] = round($ih * $percent);
            }
        }
        return $n;
    }
    
    //------------------------------------------------------------------
    public function getImagePaths() {
        $n[] = null;
        foreach ($this->conf as $key => $value) {
            $value = $this->validateConf($value);
            $n[$key] = $this->generateImagePath($key, $value, $this->image);
        }
        return $n;
    }
    //-------------------------------------------------------
    
    private function generateThisImagePath($key, $value)
    {
        return $this->generateImagePath($key, $value, $this->image);
    }
    //--------------------------------------------------------------------
    private function generateImagePath($key, $value, $filename)
    {
        if ($key == 'original') {
                return $value['dir'] . '/' . $this->stripFileName($filename) . '.' . $this->stripImageExtention($filename);
            }else{
                //return $value['dir'] . '/' . $this->stripFileName($filename) . $value['prefix'] . $key . '.' . $this->stripImageExtention($filename);
                return $value['dir'] . '/' . $this->stripFileName($filename) . '.' . $this->stripImageExtention($filename);
            }         
    }

    //---------------------------------------------------------------------
    
    private function stripImageExtention($file_name) {
        return pathinfo($file_name, PATHINFO_EXTENSION);
    }
//---------------------------------------------------------------------
    
    private function stripFileName($file_name) {
        return str_replace('.' . $this->stripImageExtention($file_name), '', $file_name);
    }
    
//---------------------------------------------------------------------   

    private function validateConf($value) {
        $value['prefix'] = strlen($value['prefix']) == 0 ? '' : $value['prefix'];
        return $value;
    }

//-------------------------------------------------------
    private function getAbsolutePath() {
        return null === $this->image? null : $this->getUploadRootDir() . '/' . $this->image;
    }

//-------------------------------------------------------
    private function getUploadRootDir() {
        return __DIR__ . '/../../../web/' . $this->getUploadDir();
    }

//-------------------------------------------------------
    private function getUploadDir() {
        return $this->conf['original']['dir'];
    }
//-----------------------------------------------
    
    public function deleteImages($old_filename)
    {
        foreach ($this->conf as $key => $value) {
            $value = $this->validateConf($value);
            $generated_file_name =  $this->generateImagePath($key, $value, $old_filename);
            
            if (is_readable($generated_file_name )){
                @unlink($generated_file_name );    
            }
        }
        
    }
    
    
}

