<?php

namespace LoveThatFit\AdminBundle\Entity;

use LoveThatFit\AdminBundle\ImageHelper;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="LoveThatFit\AdminBundle\Entity\ProductItemRepository")
 * @ORM\Table(name="product_item")
 * @ORM\HasLifecycleCallbacks()
 */
class ProductItem
{

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="product_items")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * @ORM\OneToMany(targetEntity="LoveThatFit\AdminBundle\Entity\SaveLookItem", mappedBy="items")
     */
    protected $save_look_item;

    /**
     * @ORM\ManyToOne(targetEntity="ProductSize", inversedBy="product_items")
     * @ORM\JoinColumn(name="product_size_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $product_size;

    /**
     * @ORM\ManyToOne(targetEntity="ProductColor", inversedBy="product_items")
     * @ORM\JoinColumn(name="product_color_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $product_color;

    /**
     * @ORM\OneToMany(targetEntity="LoveThatFit\SiteBundle\Entity\UserItemTryHistory", mappedBy="productitem")
     */
    private $user_item_try_history;

    /**
     * @ORM\OneToMany(targetEntity="LoveThatFit\CartBundle\Entity\Cart", mappedBy="productitem")
     */
    private $cart;

    /**
     * @ORM\OneToMany(targetEntity="LoveThatFit\CartBundle\Entity\Wishlist", mappedBy="productitem")
     */
    private $wishlist;

    /**
     * @ORM\OneToMany(targetEntity="LoveThatFit\CartBundle\Entity\UserOrderDetail", mappedBy="productitem")
     */
    private $user_order_detail;
	
	
    /**
     * @ORM\OneToMany(targetEntity="LoveThatFit\SiteBundle\Entity\UserItemFavHistory", mappedBy="productitem")
     */
    private $user_item_fav_history;

    /**
     * @ORM\OneToMany(targetEntity="LoveThatFit\SiteBundle\Entity\UserFittingRoomItem", mappedBy="productitem")
     */
    private $user_fitting_room_ittem;
    /**
     * @ORM\OneToMany(targetEntity="LoveThatFit\UserBundle\Entity\SelfieshareItem", mappedBy="product_item")
     */
    private $selfieshare_item;

    /**
     * @ORM\ManyToMany(targetEntity="LoveThatFit\UserBundle\Entity\User", mappedBy="product_items")
     **/
    private $users;


    /**
     * Bidirectional (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="ProductItemPiece", mappedBy="product_item")
     * */
    private $product_item_pieces;

    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->user_item_try_history = new \Doctrine\Common\Collections\ArrayCollection();
		$this->user_item_fav_history = new \Doctrine\Common\Collections\ArrayCollection();
        $this->cart = new \Doctrine\Common\Collections\ArrayCollection();
        $this->user_order_detail = new \Doctrine\Common\Collections\ArrayCollection();
    }



    /////////////////////////////////////////////////////////////


    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
#----------------------------------------------------------------------------------------
    /**
     * @var string $line_number
     *
     * @ORM\Column(name="line_number", type="string", nullable=true)
     */
    private $line_number;
#----------------------------------------------------------------------------------------
    /**
     * @var string $image
     *
     * @ORM\Column(name="image", type="string", nullable=true)
     */
    private $image;
    #----------------------------------------------------------------------------------------
    /**
     * @Assert\File(maxSize="6000000")
     */
    public $file;
    #----------------------------------------------------------------------------------------
    /**
     * @var string $raw_image
     *
     * @ORM\Column(name="raw_image", type="string", nullable=true)
     */
    private $raw_image;
#----------------------------------------------------------------------------------------
    /**
     * @var string $sku
     *
     * @ORM\Column(name="sku", type="string", nullable=true)
     */
    private $sku;
    #----------------------------------------------------------------------------------------
    /**
     * @var float $price
     * @ORM\Column(name="price", type="float", nullable=true,options={"default" = 0})
     */

    private $price = 0;


    #~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


    /**
     * Set price
     *
     * @param float $price
     * @return Measurement
     */
    public function setPrice($price)
    {
        if ($price != null) {
            $this->price = $price;
            return $this;
        } else {
            return $this->price = 0;
        }
    }
#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        if ($this->price != null) {
            return $this->price;
        } else {
            return $this->price = 0;
        }
    }
#----------------------------------------------------------------------------------------

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
     * Set line_number
     *
     * @param string $lineNumber
     * @return ProductItem
     */
    public function setLineNumber($lineNumber)
    {
        $this->line_number = $lineNumber;

        return $this;
    }

    /**
     * Get line_number
     *
     * @return string
     */
    public function getLineNumber()
    {
        return $this->line_number;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return ProductItem
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set product
     *
     * @param LoveThatFit\AdminBundle\Entity\Product $product
     * @return ProductItem
     */
    public function setProduct(\LoveThatFit\AdminBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return LoveThatFit\AdminBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set product_size
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductSize $productSize
     * @return ProductItem
     */
    public function setProductSize(\LoveThatFit\AdminBundle\Entity\ProductSize $productSize = null)
    {
        $this->product_size = $productSize;

        return $this;
    }

    /**
     * Get product_size
     *
     * @return LoveThatFit\AdminBundle\Entity\ProductSize
     */
    public function getProductSize()
    {
        return $this->product_size;
    }

    /**
     * Set product_color
     *
     * @param LoveThatFit\AdminBundle\Entity\ProductColor $productColor
     * @return ProductItem
     */
    public function setProductColor(\LoveThatFit\AdminBundle\Entity\ProductColor $productColor = null)
    {
        $this->product_color = $productColor;

        return $this;
    }

    /**
     * Get product_color
     *
     * @return LoveThatFit\AdminBundle\Entity\ProductColor
     */
    public function getProductColor()
    {
        return $this->product_color;
    }

    //---------------------------------------------------

    public function upload()
    {
        $ih = new ImageHelper('product_item', $this);
        $ih->upload(); // save & resize images
    }

    //---------------------------------------------------

    public function uploadRawImage()
    {
        if (null === $this->file) {
            return;
        }
        $old_image_path = Null;
        if ($this->raw_image) {
            $old_image_path = $this->getRawImageWebPath();
        }
        $ext = pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION);
        $this->raw_image = uniqid() . "." . $ext;
        $this->file->move(
            $this->getUploadRawImageRootDir(), $this->raw_image
        );

        if (is_readable($old_image_path)) {
            @unlink($old_image_path);
        }
        $this->file = null;

    }

    //-------------------------------------------------------


    public function getImagePaths()
    {
        $ih = new ImageHelper('product_item', $this);
        return $ih->getImagePaths();
    }

    //-------------------------------------------------------1
    public function getAbsolutePath()
    {
        return null === $this->image ? null : $this->getUploadRootDir() . '/' . $this->image;
    }

//-------------------------------------------------------2
    public function getWebPath()
    {
        return null === $this->image ? null : $this->getUploadDir() . '/' . $this->image;
    }

//-------------------------------------------------------3
    protected function getUploadRootDir()
    {
        return $this->getRootDir() . $this->getUploadDir();
    }

//-------------------------------------------------------4
    protected function getRootDir()
    {
        return __DIR__ . '/../../../../web/';
    }

//-------------------------------------------------------5
    protected function getUploadDir()
    {
        return 'uploads/ltf/products/fitting_room/web';
    }

    //-------------------------------------------------------1
    public function getRawImageAbsolutePath()
    {
        return null === $this->raw_image ? null : $this->getRootDir() . $this->getRawImageUploadDir() . '/' . $this->raw_image;
    }

//-------------------------------------------------------2
    public function getRawImageWebPath()
    {
        return null === $this->raw_image ? null : $this->getRawImageUploadDir() . '/' . $this->raw_image;
    }

//-------------------------------------------------------3
    protected function getUploadRawImageRootDir()
    {
        return $this->getRootDir() . $this->getRawImageUploadDir();
    }

//-------------------------------------------------------5
    protected function getRawImageUploadDir()
    {
        return 'uploads/ltf/products/fitting_room/raw';
    }

    //-------------------------------------------------------

    /**
     * @ORM\PostRemove
     */

    public function deleteImages()
    {
        if ($this->image) {
            $generated_file_name = $this->getUploadRootDir() . '/' . $this->image;
            if (is_readable($generated_file_name)) {
                @unlink($generated_file_name);
            }

        }
    }


    /**
     * Add users
     *
     * @param LoveThatFit\UserBundle\Entity\User $users
     * @return ProductItem
     */
    public function addUser(\LoveThatFit\UserBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param LoveThatFit\UserBundle\Entity\User $users
     */
    public function removeUser(\LoveThatFit\UserBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }


    /**
     * Add user_item_try_history
     *
     * @param LoveThatFit\SiteBundle\Entity\UserItemTryHistory $userItemTryHistory
     * @return ProductItem
     */
    public function addUserItemTryHistory(\LoveThatFit\SiteBundle\Entity\UserItemTryHistory $userItemTryHistory)
    {
        $this->user_item_try_history[] = $userItemTryHistory;

        return $this;
    }

    /**
     * Remove user_item_try_history
     *
     * @param LoveThatFit\SiteBundle\Entity\UserItemTryHistory $userItemTryHistory
     */
    public function removeUserItemTryHistory(\LoveThatFit\SiteBundle\Entity\UserItemTryHistory $userItemTryHistory)
    {
        $this->user_item_try_history->removeElement($userItemTryHistory);
    }

    /**
     * Get user_item_try_history
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getUserItemTryHistory()
    {
        return $this->user_item_try_history;
    }

    /**
     * Set raw_image
     *
     * @param string $rawImage
     * @return ProductItem
     */
    public function setRawImage($rawImage)
    {
        $this->raw_image = $rawImage;
        return $this;
    }

    /**
     * Get raw_image
     *
     * @return string
     */
    public function getRawImage()
    {
        return $this->raw_image;
    }


    /**
     * Add product_item_piece
     *
     * @param \LoveThatFit\AdminBundle\Entity\ProductItemPiece $product_item_piece
     * @return ProductItem
     */
    public function addProductItemPiece(\LoveThatFit\AdminBundle\Entity\ProductItemPiece $product_item_piece)
    {
        $this->product_item_pieces[] = $product_item_piece;

        return $this;
    }

    /**
     * Remove product_item_piece
     *
     * @param \LoveThatFit\AdminBundle\Entity\ProductItemPiece $product_item_piece
     */
    public function removeProductItemPiece(\LoveThatFit\AdminBundle\Entity\ProductItemPiece $product_item_piece)
    {
        $this->product_item_pieces->removeElement($product_item_piece);
    }

    /**
     * Get productitempiece
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductItemPieces()
    {
        return $this->product_item_pieces;
    }

#-----------------------------------------------------------------
    public function getProductColorViewByTitle($title)
    {
        foreach ($this->product_color->getProductColorViews() as $pcv) {
            if (strtolower($pcv->getTitle()) == strtolower($title)) {
                return $pcv;
            }
        }
        return;
    }

    #-----------------------------------------------------------------
    public function getProductPieceDetailArray()
    {

        $pda[0] = array('product_color_view_type' => 'default',
            'product_color_view_url' => $this->product_color->getWebPath(),
            'product_item_piece_url' => $this->getWebPath(),
        );

        foreach ($this->product_item_pieces as $pip) {
            $pcv = $pip->getProductColorView();
            if ($pcv) {
                $pda[$pip->getId()] = array('product_color_view_type' => $pcv->getTitle(),
                    'product_color_view_url' => $pcv->getWebPath(),
                    'product_item_piece_url' => $pip->getWebPath(),
                );
            }
        }
        return $pda;
    }

    /**
     * Set sku
     *
     * @param string $sku
     * @return ProductItem
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Add user_fitting_room_ittem
     *
     * @param \LoveThatFit\SiteBundle\Entity\UserFittingRoomItem $userFittingRoomIttem
     * @return ProductItem
     */
    public function addUserFittingRoomIttem(\LoveThatFit\SiteBundle\Entity\UserFittingRoomItem $userFittingRoomIttem)
    {
        $this->user_fitting_room_ittem[] = $userFittingRoomIttem;

        return $this;
    }

    /**
     * Remove user_fitting_room_ittem
     *
     * @param \LoveThatFit\SiteBundle\Entity\UserFittingRoomItem $userFittingRoomIttem
     */
    public function removeUserFittingRoomIttem(\LoveThatFit\SiteBundle\Entity\UserFittingRoomItem $userFittingRoomIttem)
    {
        $this->user_fitting_room_ittem->removeElement($userFittingRoomIttem);
    }

    /**
     * Get user_fitting_room_ittem
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserFittingRoomIttem()
    {
        return $this->user_fitting_room_ittem;
    }
#---------------------------------------------
    /**
     * Add selfieshare_item
     *
     * @param \LoveThatFit\UserBundle\Entity\SelfieshareItem $selfieshare_item
     * @return ProductItem
     */
    public function addSelfieshareItem(\LoveThatFit\UserBundle\Entity\SelfieshareItem $selfieshare_item)
    {
        $this->selfieshare_item[] = $selfieshare_item;

        return $this;
    }

    /**
     * Remove selfieshare_item
     *
     * @param \LoveThatFit\UserBundle\Entity\SelfieshareItem $selfieshare_item
     */
    public function removeSelfieshareItem(\LoveThatFit\UserBundle\Entity\SelfieshareItem $selfieshare_item)
    {
        $this->selfieshare_item->removeElement($selfieshare_item);
    }

    /**
     * Get selfieshare_item
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSelfieshareItem()
    {
        return $this->selfieshare_item;
    }

#----------------------------------------------    


    /**
     * Add cart
     *
     * @param \LoveThatFit\CartBundle\Entity\Cart $cart
     * @return ProductItem
     */
    public function addCart(\LoveThatFit\CartBundle\Entity\Cart $cart)
    {
        $this->cart[] = $cart;
        return $this;
    }

    /**
     * Remove cart
     *
     * @param \LoveThatFit\CartBundle\Entity\Cart $cart
     */
    public function removeCart(\LoveThatFit\CartBundle\Entity\Cart $cart)
    {
        $this->cart->removeElement($cart);
    }

    /**
     * Get cart
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCart()
    {
        return $this->cart;
    }


#----------------------------------------------
	/**
     * Add user_item_fav_history
     *
     * @param \LoveThatFit\SiteBundle\Entity\UserItemFavHistory $userItemFavHistory
     * @return ProductItem
     */
    public function addUserItemFavHistory(\LoveThatFit\SiteBundle\Entity\UserItemFavHistory $userItemFavHistory)
    {
        $this->user_item_fav_history[] = $userItemFavHistory;
        return $this;
    }
	
     /*
	 * Remove user_item_fav_history
     *
     * @param \LoveThatFit\SiteBundle\Entity\UserItemFavHistory $userItemFavHistory
     */
    public function removeUserItemFavHistory(\LoveThatFit\SiteBundle\Entity\UserItemFavHistory $userItemFavHistory)
    {
        $this->user_item_fav_history->removeElement($userItemFavHistory);
    }
	
	/**
     * Get user_item_fav_history
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserItemFavHistory()
    {
        return $this->user_item_fav_history;
    }

#----------------------------------------------


    /**
     * Add wishlist
     *
     * @param \LoveThatFit\CartBundle\Entity\Wishlist $wishlist
     * @return ProductItem
     */
    public function addWishlist(\LoveThatFit\CartBundle\Entity\Wishlist $wishlist)
    {
        $this->wishlist[] = $wishlist;

        return $this;
    }

    /**
     * Remove wishlist
     *
     * @param \LoveThatFit\CartBundle\Entity\Wishlist $wishlist
     */
    public function removeWishlist(\LoveThatFit\CartBundle\Entity\Wishlist $wishlist)
    {
        $this->wishlist->removeElement($wishlist);
    }

    /**
     * Get wishlist
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWishlist()
    {
        return $this->wishlist;
    }

#------------------------------------

    /**
     * Add user_order_detail
     *
     * @param \LoveThatFit\CartBundle\Entity\UserOrderDetail $userOrderDetail
     * @return ProductItem
     */
    public function addUserOrderDetail(\LoveThatFit\CartBundle\Entity\UserOrderDetail $userOrderDetail)
    {
        $this->user_order_detail[] = $userOrderDetail;

        return $this;
    }

    /**
     * Remove user_order_detail
     *
     * @param \LoveThatFit\CartBundle\Entity\UserOrderDetail $userOrderDetail
     */
    public function removeUserOrderDetail(\LoveThatFit\CartBundle\Entity\UserOrderDetail $userOrderDetail)
    {
        $this->user_order_detail->removeElement($userOrderDetail);
    }

    /**
     * Get user_order_detail
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserOrderDetail()
    {
        return $this->user_order_detail;
    }

    /**
     * Set save_look_item
     *
     * @param \LoveThatFit\AdminBundle\Entity\SaveLookItem $saveLookItem
     * @return ProductItem
     */
    public function setSaveLookItem(\LoveThatFit\AdminBundle\Entity\SaveLookItem $saveLookItem = null)
    {
        $this->save_look_item = $saveLookItem;

        return $this;
    }

    /**
     * Get save_look_item
     *
     * @return \LoveThatFit\AdminBundle\Entity\SaveLookItem
     */
    public function getSaveLookItem()
    {
        return $this->save_look_item;
    }

    /**
     * Add save_look_item
     *
     * @param \LoveThatFit\AdminBundle\Entity\SaveLookItem $saveLookItem
     * @return ProductItem
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
}