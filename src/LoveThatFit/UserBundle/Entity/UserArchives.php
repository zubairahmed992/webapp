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
    public function getImageName($image_type='original'){
        $file_array = explode('.',$this->image);
        $ext = $file_array[1];
        switch ($image_type) {
            case 'original':
                return $this->id .'_original.'.$ext;
                break;
            case 'cropped':
                return $this->id .'_cropped.'.$ext;
                break;
            default:
                return $this->id .'_cropped.'.$ext;
                break;
        }
        
        
    }
    
    #----------------------------------------

    public function getAbsolutePath($image_type='original') {
        return null === $this->image ? null : $this->getUploadRootDir() . '/' . $this->getImageName($image_type);
    }

//----------------------------------------------------------
    public function getWebPath($image_type='original') {
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
    
}