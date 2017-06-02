<?php

namespace LoveThatFit\PodioBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="LoveThatFit\PodioBundle\Entity\PodioOrdersDetailRepository")
 * @ORM\Table(name="podio_orders_detail")
 * @ORM\HasLifecycleCallbacks()
 */
class PodioOrdersDetail {

    /**
     * @ORM\ManyToOne(targetEntity="LoveThatFit\PodioBundle\Entity\PodioOrders", inversedBy="podio_orders_detail")
     * @ORM\JoinColumn(name="podio_order_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $podio_order_detail;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $podio_id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set podio_id
     *
     * @param string $podio_id
     * @return podio_id
     */
    public function setPodioId($podio_id) {
        $this->podio_id = $podio_id;

        return $this;
    }

    /**
     * Get podio_id
     *
     * @return string 
     */
    public function getPodioId() {
        return $this->podio_id;
    }

    /**
     * Set podio_order_detail
     *
     * @param LoveThatFit\PodioBundle\Entity\PodioOrders $podio_order_detail
     * @return podio_order_id
     */
    public function setUserOrderDetail(\LoveThatFit\PodioBundle\Entity\PodioOrders $podio_order_detail = null) {
        $this->podio_order_detail = $podio_order_detail;

        return $this;
    }

    /**
     * Get podio_order_detail
     *
     * @return LoveThatFit\PodioBundle\Entity\PodioOrders 
     */
    public function getUserOrderDetail() {
        return $this->podio_order_detail;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Brand
     */
    public function setCreatedAt($createdAt) {
        $this->created_at = $createdAt;
        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt() {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Brand
     */
    public function setUpdatedAt($updatedAt) {
        $this->updated_at = $updatedAt;
        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt() {
        return $this->updated_at;
    }
}