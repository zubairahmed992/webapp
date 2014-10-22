<?php

namespace LoveThatFit\UserBundle\Entity;

use LoveThatFit\AdminBundle\ImageHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * LoveThatFit\UserBundle\Entity\UserMarker
 *  
 * @ORM\Table(name="user_marker")
 * @ORM\Entity(repositoryClass="LoveThatFit\UserBundle\Entity\UserMarkerRepository")
 */
class UserMarker
{   
    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="user_marker")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     * */   
   
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
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;
    
    
    /**
     * @var string $svg_paths
     *
     * @ORM\Column(name="svg_paths", type="text", nullable=true)     
     */
    private $svg_paths;
    
    /**
     * @var float $mask_x
     *
     * @ORM\Column(name="mask_x", type="float", nullable=true)     
     */
    private $mask_x;
    
    
    /**
     * @var float $mask_y
     *
     * @ORM\Column(name="mask_y", type="float", nullable=true)     
     */
    private $mask_y;
    
    
    /**
     * @var string $rect_x
     *
     * @ORM\Column(name="rect_x", type="float", nullable=true)     
     */
    private $rect_x;
    
    
    /**
     * @var string $rect_y
     *
     * @ORM\Column(name="rect_y", type="float", nullable=true)     
     */
    private $rect_y;
    
    
    /**
     * @var string $rect_height 
     *
     * @ORM\Column(name="rect_height ", type="float", nullable=true)     
     */
    private $rect_height;
    
    
    /**
     * @var string $rect_width
     *
     * @ORM\Column(name="rect_width ", type="float", nullable=true)     
     */
    private $rect_width;
    
    /**
     * @var string $marker_json
     *
     * @ORM\Column(name="marker_json", type="text", nullable=true)
     */
    private $marker_json;

    
    /**
     * @var string $default_marker_json
     *
     * @ORM\Column(name="default_marker_json", type="text", nullable=true)
     */
    private $default_marker_json;
    
    
    /**
     * @var string $default_marker_svg
     *
     * @ORM\Column(name="default_marker_svg", type="text", nullable=true)
     */
    private $default_marker_svg;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

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

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return UserMarker
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    
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
    
     /**
     * Set mask_x
     *
     * @param float $maskX
     * @return UserMarker
     */
    public function setMaskX($maskX)
    {
        $this->mask_x = $maskX;
    
        return $this;
    }

    /**
     * Get mask_x
     *
     * @return float 
     */
    public function getMaskX()
    {
        return $this->mask_x;
    }

    /**
     * Set mask_y
     *
     * @param float $maskY
     * @return UserMarker
     */
    public function setMaskY($maskY)
    {
        $this->mask_y = $maskY;
    
        return $this;
    }

    /**
     * Get mask_y
     *
     * @return float 
     */
    public function getMaskY()
    {
        return $this->mask_y;
    }


    /**
     * Set rect_x
     *
     * @param float $rectX
     * @return UserMarker
     */
    public function setRectX($rectX)
    {
        $this->rect_x = $rectX;
    
        return $this;
    }

    /**
     * Get rect_x
     *
     * @return float 
     */
    public function getRectX()
    {
        return $this->rect_x;
    }

    /**
     * Set rect_y
     *
     * @param float $rectY
     * @return UserMarker
     */
    public function setRectY($rectY)
    {
        $this->rect_y = $rectY;
    
        return $this;
    }

    /**
     * Get rect_y
     *
     * @return float 
     */
    public function getRectY()
    {
        return $this->rect_y;
    }

    /**
     * Set rect_height
     *
     * @param float $rectHeight
     * @return UserMarker
     */
    public function setRectHeight($rectHeight)
    {
        $this->rect_height = $rectHeight;
    
        return $this;
    }

    /**
     * Get rect_height
     *
     * @return float 
     */
    public function getRectHeight()
    {
        return $this->rect_height;
    }

    /**
     * Set rect_width
     *
     * @param float $rectWidth
     * @return UserMarker
     */
    public function setRectWidth($rectWidth)
    {
        $this->rect_width = $rectWidth;
    
        return $this;
    }

    /**
     * Get rect_width
     *
     * @return float 
     */
    public function getRectWidth()
    {
        return $this->rect_width;
    }

    /**
     * Set marker_json
     *
     * @param string $markerJson
     * @return UserMarker
     */
    public function setMarkerJson($markerJson)
    {
        $this->marker_json = $markerJson;
    
        return $this;
    }

    /**
     * Get marker_json
     *
     * @return string 
     */
    public function getMarkerJson()
    {
        return $this->marker_json;
    }

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

    /**
     * Set default_marker_json
     *
     * @param string $defaultMarkerJson
     * @return UserMarker
     */
    public function setDefaultMarkerJson($defaultMarkerJson)
    {
        $this->default_marker_json = $defaultMarkerJson;
    
        return $this;
    }

    /**
     * Get default_marker_json
     *
     * @return string 
     */
    public function getDefaultMarkerJson()
    {
        return $this->default_marker_json;
    }

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
}