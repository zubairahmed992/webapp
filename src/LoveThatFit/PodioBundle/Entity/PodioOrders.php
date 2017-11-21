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
     * @ORM\OneToMany(targetEntity="LoveThatFit\PodioBundle\Entity\PodioOrdersDetail", mappedBy="podio_order_detail")
     */
    protected $user_podio_order_detail;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="order_number", type="integer", length=20, nullable=true)
     */
    private $order_number;

    /**
     * @ORM\Column(type="integer", length=4, nullable=false)
     */
    protected $status;

    /**
     * @ORM\Column(type="integer", length=4, nullable=false)
     */
    protected $tracking_number_status;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $tracking_number_updated_at;

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
     * @param integer $orderNumber
     * @return PodioOrders
     */
    public function setOrderNumber($orderNumber) {
        $this->order_number = $orderNumber;

        return $this;
    }

    /**
     * Get order_number
     *
     * @return integer 
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
     * Set tracking_number_status
     *
     * @param integer $tracking_number_status
     * @return tracking_number_status
     */
    public function setTrackingNumberStatus($tracking_number_status) {
        $this->tracking_number_status = $tracking_number_status;

        return $this;
    }

    /**
     * Get tracking_number_status
     *
     * @return integer 
     */
    public function getTrackingNumberStatus() {
        return $this->tracking_number_status;
    }


    /**
     * Set tracking_number_updated_at
     *
     * @param \DateTime $tracking_number_updated_at
     * @return tracking_number_updated_at
     */
    public function setTrackingNumberUpdatedAt($tracking_number_updated_at) {
        $this->tracking_number_updated_at = $tracking_number_updated_at;
        return $this;
    }

    /**
     * Get tracking_number_updated_at
     *
     * @return \DateTime 
     */
    public function getTrackingNumberUpdatedAt() {
        return $this->tracking_number_updated_at;
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

    /**
     * Add user_podio_order_detail
     *
     * @param \LoveThatFit\PodioBundle\Entity\PodioOrdersDetail $user_podio_order_detail
     * @return PodioOrders
     */
    public function addUserPodioOrderDetail(\LoveThatFit\PodioBundle\Entity\PodioOrdersDetail $user_podio_order_detail)
    {
        $this->user_podio_order_detail[] = $user_podio_order_detail;
    
        return $this;
    }

    /**
     * Remove user_podio_order_detail
     *
     * @param \LoveThatFit\PodioBundle\Entity\PodioOrdersDetail $userOrder
     */
    public function removeUserPodioOrderDetail(\LoveThatFit\PodioBundle\Entity\PodioOrdersDetail $user_podio_order_detail)
    {
        $this->user_podio_order_detail->removeElement($user_podio_order_detail);
    }

    /**
     * Get user_podio_order_detail
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserPodioOrderDetail()
    {
        return $this->user_podio_order_detail;
    }
}