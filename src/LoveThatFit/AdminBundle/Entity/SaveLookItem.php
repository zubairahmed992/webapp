<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SaveLookItem
 *
 * @ORM\Table("save_look_item")
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\SaveLookItemRepository")
 */
class SaveLookItem
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
     * @ORM\ManyToOne(targetEntity="SaveLook", inversedBy="save_look_item")
     * @ORM\JoinColumn(name="save_look_id", referencedColumnName="id", onDelete="CASCADE")
     */

    protected $savelook;

    /**
     * @ORM\ManyToOne(targetEntity="LoveThatFit\AdminBundle\Entity\ProductItem", inversedBy="save_look_item")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id", onDelete="CASCADE")
     */

    protected $items;

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
     * Set savelook
     *
     * @param \LoveThatFit\AdminBundle\Entity\SaveLook $savelook
     * @return SaveLookItem
     */
    public function setSavelook(\LoveThatFit\AdminBundle\Entity\SaveLook $savelook = null)
    {
        $this->savelook = $savelook;
    
        return $this;
    }

    /**
     * Get savelook
     *
     * @return \LoveThatFit\AdminBundle\Entity\SaveLook
     */
    public function getSavelook()
    {
        return $this->savelook;
    }

    /**
     * Set items
     *
     * @param \LoveThatFit\AdminBundle\Entity\ProductItem $items
     * @return SaveLookItem
     */
    public function setItems(\LoveThatFit\AdminBundle\Entity\ProductItem $items = null)
    {
        $this->items = $items;
    
        return $this;
    }

    /**
     * Get items
     *
     * @return \LoveThatFit\AdminBundle\Entity\ProductItem 
     */
    public function getItems()
    {
        return $this->items;
    }
}