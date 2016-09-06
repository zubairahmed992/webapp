<?php

namespace LoveThatFit\UserBundle\Entity;

use LoveThatFit\AdminBundle\ImageHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * LoveThatFit\UserBundle\Entity\UserArchives
 *  
 * @ORM\Table(name="user_archives")
 * @ORM\Entity(repositoryClass="LoveThatFit\UserBundle\Entity\UserArchivesRepository")
 */
class UserArchives
{   
    
     /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="user_archives" , cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE" )
     *  */
    private $user;
    
    /**
     * @ORM\OneToMany(targetEntity="LoveThatFit\SupportBundle\Entity\SupportTaskLog", mappedBy="user_archives")
     */
    protected $archives;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string $measurement_json
     *
     * @ORM\Column(name="measurement_json", type="text", nullable=true)
     */
    private $measurement_json;    
    
    /**
     * @var string $image_actions
     *
     * @ORM\Column(name="image_actions", type="text", nullable=true)     
     */
    private $image_actions;
    
    /**
     * @var string $marker_params
     *
     * @ORM\Column(name="marker_params", type="text", nullable=true)     
     */
    private $marker_params;
    
    /**
     * @var string $svg_paths
     *
     * @ORM\Column(name="svg_paths", type="text", nullable=true)     
     */
    private $svg_paths;
    
    
    /**
     * @var string $marker_json
     *
     * @ORM\Column(name="marker_json", type="text", nullable=true)
     */
    private $marker_json;
    
    
    /**
     * @var string $default_marker_svg
     *
     * @ORM\Column(name="default_marker_svg", type="text", nullable=true)
     */
    private $default_marker_svg;
   
    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $created_at;
        /**
     * @var dateTime $updatedAt
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updated_at;

	/**
	 * @var integer $status
	 * @ORM\Column(name="status", type="integer", nullable=true, options={"default":"-1"})
	 */
	private $status;
        
    /**
     * @var string $image
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     * )
     */
    private $image;        
    
/**
     * @Assert\File()
     */
    public $file;
    #----------------------------------------
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
#----------------------------------------
        
    /**
     * Set measurement_json
     *
     * @param string $measurementJson
     * @return UserArchives
     */
    public function setMeasurementJson($measurementJson){
        $this->measurement_json= $measurementJson;    
        return $this;
    }

    /**
     * Get measurement_json
     *
     * @return string 
     */
    public function getMeasurementJson(){
        return $this->measurement_json;
    }

#----------------------------------------
    
  /**
     * Set image_actions
     *
     * @param string $image_actions
     * @return UserMarker
     */
    public function setImageActions($image_actions){
        $this->image_actions = $image_actions;    
        return $this;
    }

    /**
     * Get image_actions
     *
     * @return string 
     */
    public function getImageActions(){
        return $this->image_actions;
    }
    
    
#----------------------------------------
    
  /**
     * Set marker_params
     *
     * @param string $marker_params
     * @return MarkerParams
     */
    public function setMarkerParams($marker_params){
        $this->marker_params = $marker_params;    
        return $this;
    }

    /**
     * Get marker_params
     *
     * @return string 
     */
    public function getMarkerParams(){
        return $this->marker_params;
    }
    
    

    #----------------------------------------
    /**
     * Set svg_paths
     *
     * @param string $svgPaths
     * @return UserMarker
     */
    public function setSvgPaths($svgPaths)
    {
        $this->svg_paths = $svgPaths;
    
        return $this;
    }

    /**
     * Get svg_paths
     *
     * @return string 
     */
    public function getSvgPaths()
    {
        return $this->svg_paths;
    }
#----------------------------------------
    
    /**
     * Set marker_json
     *
     * @param string $markerJson
     * @return UserMarker
     */
    public function setMarkerJson($markerJson){
        $this->marker_json = $markerJson;    
        return $this;
    }

    /**
     * Get marker_json
     *
     * @return string 
     */
    public function getMarkerJson(){
        return $this->marker_json;
    }


#----------------------------------------
    
    
    /**
     * Set default_marker_svg
     *
     * @param string $defaultMarkerSvg
     * @return UserMarker
     */
    public function setDefaultMarkerSvg($defaultMarkerSvg)
    {
        $this->default_marker_svg = $defaultMarkerSvg;
    
        return $this;
    }

    /**
     * Get default_marker_svg
     *
     * @return string 
     */
    public function getDefaultMarkerSvg()
    {
        return $this->default_marker_svg;
    }
    
#----------------------------------------
    
    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return UserMarker
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    
        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    #----------------------------------------
      /**
     * Set updated_at
     *
     * @param \DateTime $updated_at
     * @return UserDevices
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    
        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }
    #----------------------------------------

	/**
	 * Set status
	 *
	 * @param integer $status
	 * @return UserArchives
	 */
	public function setStatus($status)
	{
	  $this->status = $status;

	  return $this;
	}

	/**
	 * Get status
	 *
	 * @return integer
	 */
	public function getStatus()
	{
	  return $this->status;
	}

#----------------------------------------
        
          /**
     * Set image
     *
     * @param string $image
     * @return User
     */
    public function setImage($image) {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage() {
        return $this->image;
    }
    #----------------------------------------
	  /**
     * Set user
     *
     * @param \LoveThatFit\UserBundle\Entity\User $user
     * @return UserMarker
     */
    public function setUser(\LoveThatFit\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \LoveThatFit\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
    #----------------------------------------

    /**
     * Add archives
     *
     * @param \LoveThatFit\SupportBundle\Entity\SupportTaskLog $archives
     * @return Brand
     */
    public function addArchives(\LoveThatFit\SupportBundle\Entity\SupportTaskLog $archives)
    {
        $this->archives[] = $archives;
    
        return $this;
    }
    /**
     * Remove archives
     *
     * @param \LoveThatFit\SupportBundle\Entity\SupportTaskLog $archives
     */
    public function removeArchives(\LoveThatFit\SupportBundle\Entity\SupportTaskLog $archives)
    {
        $this->archives->removeElement($archives);
    }

    /**
     * Get archives
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArchives()
    {
        return $this->archives;
    }
    #----------------------------------------
    public function getMarkerArray() {

        $ar = array();
        $ar['svg_paths'] = $this->svg_paths;
        $ar['marker_json'] = $this->marker_json;
        $ar['image_actions'] = $this->image_actions;
        $ar['default_svg_paths'] = $this->default_marker_svg;
        
        
        $mp = json_decode($this->marker_params,true);
        if (is_array($mp)) {
            array_key_exists('mask_x', $mp) ? $ar['mask_x'] = $mp['mask_x'] : '';
            array_key_exists('mask_y', $mp) ? $ar['mask_y'] = $mp['mask_y'] : '';
            array_key_exists('rect_x', $mp) ? $ar['rect_x'] = $mp['rect_x'] : '';
            array_key_exists('rect_y', $mp) ? $ar['rect_y'] = $mp['rect_y'] : '';
            array_key_exists('rect_height', $mp) ? $ar['rect_height'] = $mp['rect_height'] : '';
            array_key_exists('rect_width', $mp) ? $ar['rect_width'] = $mp['rect_width'] : '';
        }
        return $ar;
    }
    
        #----------------------------------------
    public function getOriginalAbsolutePath() {
        return $this->getAbsolutePath('original');
    }
//----------------------------------------------------------
    public function getOriginalWebPath() {     
           return $this->getWebPath('original');
    }
    //----------------------------------------------------------    
    public function getCroppedAbsolutePath() {
        return $this->getAbsolutePath('cropped');
    }
//----------------------------------------------------------
    public function getCroppedWebPath() {     
           return $this->getWebPath('cropped');
    }
    
    #----------------------------------------
    public function getImageName($image_type){
        switch ($image_type) {
            case 'original':
                return 'original_'.$this->image;
                break;
            case 'cropped':
                return 'cropped_'.$this->image;
                break;
            case '-original':
                $ar=explode('.', $this->image);
                return 'original.'.$ar[1];                
                break;
            case '-cropped':
                $ar=explode('.', $this->image);
                return 'cropped.'.$ar[1];                
                break;
            default:
                return $this->image;
                break;
        }
        
        
    }
    
    #----------------------------------------

    public function getAbsolutePath($image_type) {
        return null === $this->image ? null : $this->getUploadRootDir() . '/' . $this->getImageName($image_type);
    }

//----------------------------------------------------------
    public function getWebPath($image_type) {
            return null === $this->image ? null : $this->getUploadDir() . '/' . $this->getImageName($image_type) . '?rand=' . uniqid();
    }

    //----------------------------------------------------------
    public function getDirWebPath() {
        return $this->getUploadDir() . '/';
    }

//----------------------------------------------------------
    public function getUploadRootDir() {
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

//----------------------------------------------------------
    public function getUploadDir() {
        return 'uploads/ltf/users/' . $this->getUser()->getId();
    }
//----------------------------------------------------------
    public function copyImagesToUser() {     
        @copy($this->getAbsolutePath('original'),$this->getAbsolutePath('-original'));
        @copy($this->getAbsolutePath('cropped'),$this->getAbsolutePath('-cropped'));
    }
//----------------------------------------------------------
    public function upload() {

        if (null === $this->file) {
            return;
        }

        $ext = pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION);
        $this->image = $this->id.'_original.' . $ext;
        
        $this->file->move(
                $this->getUploadRootDir(), $this->image
        );
        $this->file = null;
        return $this->$this->image;
    }
    //----------------------------------------------------
    public function writeImageFromCanvas($raw_data) {
        $data = substr($raw_data, strpos($raw_data, ",") + 1);
        $decodedData = base64_decode($data);
        $fp = fopen($this->getAbsolutePath('cropped'), 'wb');
        @fwrite($fp, $decodedData);
        @fclose($fp);
        $cropped_image_url=$this->getWebPath('cropped');        
       return json_encode(array("status"=>"check", "url"=>$cropped_image_url));
        
    }
    #-------------------------------------------
    
     public function resizeImage($device_type='') {

        $filename = $this->getAbsolutePath('cropped');
        $image_info = @getimagesize($filename);
        $image_type = $image_info[2];

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
        #------------ Need dimensions
        
        //$width = $image_info[0] * 0.50;
        //$height = $image_info [1] * 0.50;

         //$width = $image_info[0];
         //$height = $image_info [1];
        if($device_type == 'iphone6'){
            $width = 375;
            $height = 667;
        }else{
            $width = 320;
            $height = 568;
        }
        $img_new = imagecreatetruecolor($width, $height);
        imagealphablending($img_new, false);
        imagesavealpha($img_new,true);
        $transparent = imagecolorallocatealpha($img_new, 255, 255, 255, 127);
        imagefilledrectangle($img_new, 0, 0, $width, $height, $transparent);
        imagecopyresampled($img_new, $source, 0, 0, 0, 0, $width, $height, imagesx($source), imagesy($source));

        switch ($image_type) {
            case IMAGETYPE_JPEG:
                imagejpeg($img_new, $filename, 75);
                break;
            case IMAGETYPE_GIF:
                imagegif($img_new, $filename);
                break;
            case IMAGETYPE_PNG:
                imagepng($img_new, $filename);
                break;
        }
      
        
    }
}