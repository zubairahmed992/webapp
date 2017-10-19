<?php

namespace LoveThatFit\UserBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="LoveThatFit\UserBundle\Entity\PodioUsersRepository")
 * @ORM\Table(name="podio_users")
 * @ORM\HasLifecycleCallbacks()
 */
class PodioUsers {

    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="podio_users")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;
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
     * @ORM\Column(type="integer", length=4, nullable=false)
     */
    protected $is_podio_updated;

     /**
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    protected $is_primary_email_updated;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $primary_email_updated_at;

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
     * Set user
     *
     * @param LoveThatFit\UserBundle\Entity\User $user
     * @return user_id
     */
    public function setUser(\LoveThatFit\UserBundle\Entity\User $user = null) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return LoveThatFit\UserBundle\Entity\User 
     */
    public function getUser() {
        return $this->user;
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

    /**
     * Set is_podio_updated
     *
     * @param integer $is_podio_updated
     * @return is_podio_updated
     */
    public function setPodioUpdated($is_podio_updated) {
        $this->is_podio_updated = $is_podio_updated;

        return $this;
    }

    /**
     * Get is_podio_updated
     *
     * @return integer 
     */
    public function getPodioUpdated() {
        return $this->is_podio_updated;
    }

    /**
     * Set is_primary_email_updated
     *
     * @param integer $is_primary_email_updated
     * @return is_primary_email_updated
     */
    public function setPrimaryEmailUpdated($is_primary_email_updated) {
        $this->is_primary_email_updated = $is_primary_email_updated;

        return $this;
    }

    /**
     * Get is_primary_email_updated
     *
     * @return integer 
     */
    public function getPrimaryEmailUpdated() {
        return $this->is_primary_email_updated;
    }


     /**
     * Set primary_email_updated_at
     *
     * @param \DateTime $updatedAt
     * @return Brand
     */
    public function setPrimaryEmailUpdatedAt($PrimaryEmailUpdatedAt) {
        $this->primary_email_updated_at = $PrimaryEmailUpdatedAt;
        return $this;
    }

    /**
     * Get primary_email_updated_at
     *
     * @return \DateTime 
     */
    public function getPrimaryEmailUpdatedAt() {
        return $this->primary_email_updated_at;
    }
}