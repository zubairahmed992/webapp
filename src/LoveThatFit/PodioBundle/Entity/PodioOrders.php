<?php

namespace LoveThatFit\PodioBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="LoveThatFit\PodioBundle\Entity\PodioOrdersRepository")
 * @ORM\Table(name="podio_orders")
 * @ORM\HasLifecycleCallbacks()
 */
class PodioOrders {

    /**
     * @ORM\OneToOne(targetEntity="LoveThatFit\CartBundle\Entity\UserOrder", inversedBy="podio_orders")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user_podio_order;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $order_number;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $podio_id;

    /**
     * @ORM\Column(type="integer", length=4, nullable=false)
     */
    protected $status;

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
     * Set user_podio_order
     *
     * @param LoveThatFit\CartBundle\Entity\UserOrder $user_podio_order
     * @return order_id
     */
    public function setUserOrder(\LoveThatFit\CartBundle\Entity\UserOrder $user_podio_order = null) {
        $this->user_podio_order = $user_podio_order;

        return $this;
    }

    /**
     * Get user_podio_order
     *
     * @return LoveThatFit\CartBundle\Entity\UserOrder 
     */
    public function getUserOrder() {
        return $this->user_podio_order;
    }

    /**
     * Set order_number
     *
     * @param string $order_number
     * @return order_number
     */
    public function setOrderNumber($order_number) {
        $this->order_number = $order_number;

        return $this;
    }

    /**
     * Get order_number
     *
     * @return string 
     */
    public function getOrderNumber() {
        return $this->order_number;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return status
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus() {
        return $this->status;
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