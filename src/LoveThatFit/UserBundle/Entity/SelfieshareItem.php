<?php

namespace LoveThatFit\UserBundle\Entity;

use LoveThatFit\AdminBundle\Entity\ProductItem;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * LoveThatFit\UserBundle\Entity\SelfieshareItem
 *  
 * @ORM\Table(name="selfieshare_item")
 * @ORM\Entity()
 */
class SelfieshareItem  {
    
     /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
        
    /**
     
     * @ORM\ManyToOne(targetEntity="LoveThatFit\AdminBundle\Entity\ProductItem", inversedBy="selfieshare_item")
     * @ORM\JoinColumn(name="product_item_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $product_item;
    
     /**
     * @ORM\ManyToOne(targetEntity="LoveThatFit\UserBundle\Entity\Selfieshare", inversedBy="selfieshare_item")
     * @ORM\JoinColumn(name="selfieshare_id", referencedColumnName="id", onDelete="CASCADE" )
     *  */
    private $selfieshare;
  
    /**
     * Set selfieshare
     *
     * @param \LoveThatFit\UserBundle\Entity\Selfieshare $selfieshare
     * @return SelfieshareItem
     */
    public function setSelfieshare(\LoveThatFit\UserBundle\Entity\Selfieshare $selfieshare = null)
    {
        $this->selfieshare = $selfieshare;    
        return $this;
    }

    /**
     * Get selfieshare
     *
     * @return \LoveThatFit\UserBundle\Entity\Selfieshare 
     */
    public function getSelfieshare()
    {
        return $this->selfieshare;
    }
    
    #------------------------------------------------   
    
     /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    #------------------------------------------------   
   
    /**
     * Set product_item
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductItem $product_item
     * @return SelfieshareItem
     */
    public function setProductitem(\LoveThatFit\AdminBundle\Entity\ProductItem $product_item = null){
        $this->product_item = $product_item;    
        return $this;
    }

    /**
     * Get product_item
     *
     * @return LoveThatFit\AdminBundle\Entity\ProductItem 
     */
    public function getProductitem(){
        return $this->product_item;
    }
    
}