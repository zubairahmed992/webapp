<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdminConfig
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\AdminConfigRepository")
 */
class AdminConfig
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="config_key", type="string", length=150)
     */
    private $config_key;

    /**
     * @var string
     *
     * @ORM\Column(name="config_value", type="string", length=150)
     */
    private $config_value;

    /**
     * @var string
     *
     * @ORM\Column(name="config_title", type="string", length=150)
     */
    private $config_title;

    /**
     * @ORM\OneToMany(targetEntity="AdminConfig", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="AdminConfig", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;


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
     * Set config_key
     *
     * @param string $configKey
     * @return AdminConfig
     */
    public function setConfigKey($configKey)
    {
        $this->config_key = $configKey;
    
        return $this;
    }

    /**
     * Get config_key
     *
     * @return string 
     */
    public function getConfigKey()
    {
        return $this->config_key;
    }

    /**
     * Set config_value
     *
     * @param string $configValue
     * @return AdminConfig
     */
    public function setConfigValue($configValue)
    {
        $this->config_value = $configValue;
    
        return $this;
    }

    /**
     * Get config_value
     *
     * @return string 
     */
    public function getConfigValue()
    {
        return $this->config_value;
    }

    /**
     * Set config_title
     *
     * @param string $configTitle
     * @return AdminConfig
     */
    public function setConfigTitle($configTitle)
    {
        $this->config_title = $configTitle;
    
        return $this;
    }

    /**
     * Get config_title
     *
     * @return string 
     */
    public function getConfigTitle()
    {
        return $this->config_title;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add children
     *
     * @param \LoveThatFit\AdminBundle\Entity\AdminConfig $children
     * @return AdminConfig
     */
    public function addChildren(\LoveThatFit\AdminBundle\Entity\AdminConfig $children)
    {
        $this->children[] = $children;
    
        return $this;
    }

    /**
     * Remove children
     *
     * @param \LoveThatFit\AdminBundle\Entity\AdminConfig $children
     */
    public function removeChildren(\LoveThatFit\AdminBundle\Entity\AdminConfig $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \LoveThatFit\AdminBundle\Entity\AdminConfig $parent
     * @return AdminConfig
     */
    public function setParent(\LoveThatFit\AdminBundle\Entity\AdminConfig $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return \LoveThatFit\AdminBundle\Entity\AdminConfig 
     */
    public function getParent()
    {
        return $this->parent;
    }
}