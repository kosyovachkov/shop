<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\ORM\Mapping as ORM;

/**
 * OrderedProduct
 *
 * @ORM\Table(name="ordered_products")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrderedProductRepository")
 */
class OrderedProduct
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @var ArrayCollection
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cart", inversedBy="orderedProducts")
     * @ORM\JoinColumn(name="cart_id", referencedColumnName="id")
     */
    private $cart;

    /**
     * @var integer
     * @ORM\Column(name="product_id", type="integer")
     */
    private $productId;

    /**
     * @var UserOrder
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserOrder", inversedBy="orderedProducts")
     * @ORM\JoinColumn(name="userOrder_id", referencedColumnName="id")
     */
    private $userOrder;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return OrderedProduct
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return OrderedProduct
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return OrderedProduct
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return ArrayCollection
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @param ArrayCollection $cart
     */
    public function setCart(ArrayCollection $cart)
    {
        $this->cart = $cart;
    }

    public function addCart(Cart $cart)
    {
        $this->cart = $cart;
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @param mixed $productId
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }


    public function getUserOrder()
    {
        return $this->userOrder;
    }

    /**
     * @param UserOrder $userOrder
     */
    public function setUserOrder(UserOrder $userOrder)
    {
        $this->userOrder = $userOrder;
    }

}

