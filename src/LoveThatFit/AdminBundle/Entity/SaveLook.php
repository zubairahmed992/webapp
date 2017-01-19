<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SaveLook
 *
 * @ORM\Table(name="save_look")
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\SaveLookRepository")
 */
class SaveLook
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
     * @ORM\Column(name="user_look_image", type="string", length=255)
     */
    private $user_look_image;

    /**
     * @ORM\OneToMany(targetEntity="SaveLookItem", mappedBy="savelook", orphanRemoval=true)
     */

    protected $save_look_item;

    /**
     * @ORM\ManyToOne(targetEntity="LoveThatFit\UserBundle\Entity\User", inversedBy="save_look")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */

    protected $users;
    public $file;


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
     * Set userLookImage
     *
     * @param string $userLookImage
     * @return SaveLook
     */
    public function setUserLookImage($userLookImage)
    {
        $this->user_look_image = $userLookImage;
    
        return $this;
    }

    /**
     * Get userLookImage
     *
     * @return string 
     */
    public function getUserLookImage()
    {
        return $this->user_look_image;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->save_look_item = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add save_look_item
     *
     * @param \LoveThatFit\AdminBundle\Entity\SaveLookItem $saveLookItem
     * @return SaveLook
     */
    public function addSaveLookItem(\LoveThatFit\AdminBundle\Entity\SaveLookItem $saveLookItem)
    {
        $this->save_look_item[] = $saveLookItem;
    
        return $this;
    }

    /**
     * Remove save_look_item
     *
     * @param \LoveThatFit\AdminBundle\Entity\SaveLookItem $saveLookItem
     */
    public function removeSaveLookItem(\LoveThatFit\AdminBundle\Entity\SaveLookItem $saveLookItem)
    {
        $this->save_look_item->removeElement($saveLookItem);
    }

    /**
     * Get save_look_item
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSaveLookItem()
    {
        return $this->save_look_item;
    }

    /**
     * Set users
     *
     * @param \LoveThatFit\UserBundle\Entity\User $users
     * @return SaveLook
     */
    public function setUsers(\LoveThatFit\UserBundle\Entity\User $users = null)
    {
        $this->users = $users;
    
        return $this;
    }

    /**
     * Get users
     *
     * @return \LoveThatFit\UserBundle\Entity\User 
     */
    public function getUsers()
    {
        return $this->users;
    }

    public function upload() {

        if (null === $this->file) {
            return;
        }

        $ext = pathinfo($this->file['name'], PATHINFO_EXTENSION);
        $this->user_look_image = 'save_user_look_'.substr(uniqid(),0,10) .'.'. $ext;

        if (!is_dir($this->getUploadRootDir())) {
            try {
                @mkdir($this->getUploadRootDir(), 0700);
            }catch (\Exception $e)
            { $e->getMessage();}
        }

        move_uploaded_file($this->file["tmp_name"], $this->getAbsolutePath());
        #$this->file->move($this->getUploadRootDir(), $this->image);

        $this->file = null;
        return $this->user_look_image;
    }

    public function getUploadRootDir() {
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    public function getUploadDir() {
        return 'uploads/ltf/users/'.$this->getUsers()->getId();
    }

    public function getAbsolutePath() {
        return null === $this->user_look_image ? null : $this->getUploadRootDir() . '/' . $this->user_look_image;
    }

    public function deleteImages( $image )
    {

        if ($image) {
            $generated_file_name = $this->getUploadRootDir() . '/' . $image;
            if (is_readable($generated_file_name)) {
                @unlink($generated_file_name);
            }

        }
    }
}