<?php

namespace LoveThatFit\SupportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="evaluation_default_products")
 */
class EvaluationDefaultProducts
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var string $product_id
     * 
     * @ORM\Column(name="product_id", type="integer" , nullable=false , unique=true)
     * @Assert\NotBlank(groups={"add", "edit"}, message = "Please select product!")
     */
    protected $product_id;


    /**
     * @var string $product_sizes
     *
     * @ORM\Column(name="product_sizes", type="string", length=100 , nullable=false)
     * @Assert\NotBlank(groups={"add", "edit"}, message = "Please select sizes!")
     */
    private $product_sizes;


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
     * Set product_id
     *
     * @param integer $productId
     * @return EvaluationDefaultProducts
     */
    public function setProductId($productId)
    {
        $this->product_id = $productId;
    
        return $this;
    }

    /**
     * Get product_id
     *
     * @return integer 
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * Set product_sizes
     *
     * @param string $productSizes
     * @return EvaluationDefaultProducts
     */
    public function setProductSizes($productSizes)
    {
        $this->product_sizes = $productSizes;
    
        return $this;
    }

    /**
     * Get product_sizes
     *
     * @return string 
     */
    public function getProductSizes()
    {
        return $this->product_sizes;
    }


}