<?php

namespace LoveThatFit\AdminBundle\Entity;

class ProductImageGenrateHelper {

    /**
     * @param $path
     * return total directoies
     */
    public function getTotalDirectories($path)
    {
        return scandir($path);
    }

    /**
     * @param $path
     * @return int total count files
     *
     */
    public function getCountFiles($path)
    {
        return count(glob($path . "/" . '*', GLOB_MARK));
    }

    /**
     * @param $imageExtention
     * @param $imageFile
     * @return resource
     * returm source of image
     */
    public function getImageSource($imageExtention, $imageFile)
    {
        switch ($imageExtention) {
            case "jpg":
                $source = imagecreatefromjpeg($imageFile);
                break;
            case "gif":
                $source = imagecreatefromgif($imageFile);
                break;
            case "png":
                $source = imagecreatefrompng($imageFile);
                break;
        }
        return $source;
    }

    /**
     * @param $imageExtention
     * @param $srcImage
     * @param $destImage
     * image move to destination Folder
     */
    public function setImagesMovePath($imageExtention, $srcImage, $destImage)
    {
        switch ($imageExtention) {
            case "jpg":
                imagejpeg($srcImage, $destImage);
                break;
            case "gif":
                imagegif($srcImage, $destImage);
                break;
            case "png":
                imagepng($srcImage, $destImage);
                break;
        }
    }

    /**
     * @param $path
     * @return array
     * retun total files into Directory
     */
    public function getImages($path)
    {
        $filesPath = opendir($path);
        while ($readFile = readdir($filesPath)) {
            $contents[] = $readFile;
        }
        closedir($filesPath);
        return $contents;
    }

    /**
     * @param $src_path
     * @param $dest_path
     * @param $extension
     * @param $width
     * @param $height
     * this function Resize the Image and move into define folder
     */
    public function setPathResizeDimentions($src_path, $dest_path, $extension, $width, $height) {
        list($orignalWidth, $orignalHeight) = getimagesize($src_path);
        $source = $this->getImageSource($extension,$src_path);
        $percent = $orignalWidth > $orignalHeight ? $width / $orignalWidth : $height / $orignalHeight;
        $calculateWidth = round($orignalWidth * $percent);
        $calculateHeight = round($orignalHeight * $percent);
        $img_new = imagecreatetruecolor($calculateWidth, $calculateHeight);
        imagealphablending($img_new, false);
        imagesavealpha($img_new,true);
        if($extension=="png"){
            $transparent = imagecolorallocatealpha($img_new, 255, 255, 255, 127);
            imagefilledrectangle($img_new, 0, 0, $calculateWidth, $calculateHeight, $transparent);
        }
        imagecopyresampled($img_new, $source, 0, 0, 0, 0, $calculateWidth, $calculateHeight, imagesx($source), imagesy($source));
        $this->setImagesMovePath($extension,$img_new,$dest_path );
    }
}