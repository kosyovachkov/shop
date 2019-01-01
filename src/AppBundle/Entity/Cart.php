<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use function PHPSTORM_META\map;

/**
 * Cart
 *
 * @ORM\Table(name="carts")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CartRepository")
 */
class Cart
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
     * @var User
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User", mappedBy="cart")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var ArrayCollection|OrderedProduct[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\OrderedProduct", mappedBy="cart")
     */
    private $orderedProducts;


    public function __construct()
    {
        $this->orderedProducts = new ArrayCollection();
    }


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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }


    public function getOrderedProducts()
    {
        return $this->orderedProducts;
    }


    public function setOrderedProducts(ArrayCollection $orderedProducts)
    {
        $this->orderedProducts = $orderedProducts;
    }

    public function addOrderedProduct(OrderedProduct $orderedProduct)
    {
        $this->orderedProducts[] = $orderedProduct;
    }

    public function getCurrentProduct(int $id)
    {
        $productsInCart = $this->getOrderedProducts();

        foreach ($productsInCart as $item) {
            if ($item->getProductId() === $id && !$item->getUserOrder()) {
                return $item;
            }
        }
        return null;
    }

    public function getTotal()
    {
        $total = 0;
        $productsInCart = $this->getOrderedProducts();

        foreach ($productsInCart as $item) {
            if(!$item->getUserOrder()){
                $q = $item->getQuantity();
                $p = $item->getPrice();
                $total += ($q * $p);
            }
        }

        return $total;
    }

    public function dropProducts(){
        $this->orderedProducts=[];
    }

    public function getProductById(int $id)
    {
        $productsInCart = $this->getOrderedProducts();

        foreach ($productsInCart as $item) {
            if ($item->getId() === $id) {
                return $item;
            }
        }
        return null;
    }
}