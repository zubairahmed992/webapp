<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="app_store_link")
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\AppStoreLinkRepository")
 */

class AppStoreLink
{ 
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $app_link
     *
     * @ORM\Column(name="app_link", type="text", nullable=true)
     */
    private $app_link;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;

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
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Categories
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
     * Set app_link
     *
     * @param string $app_link
     * @return AppStoreLink
     */
    public function setAppLink($app_link) {
        $this->app_link = $app_link;

        return $this;
    }

    /**
     * Get app_link
     *
     * @return string 
     */
    public function getAppLink() {
        return $this->app_link;
    }
}