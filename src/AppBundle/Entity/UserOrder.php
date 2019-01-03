<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\DecimalType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\ORM\Mapping as ORM;

/**
 * UserOrder
 *
 * @ORM\Table(name="user_orders")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserOrderRepository")
 */
class UserOrder
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
     * @var \DateTime
     * @ORM\Column(name="date_ordered", type="datetime")
     */
    private $dateOrdered;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\OrderedProduct", mappedBy="userOrder")
     */
    private $orderedProducts;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="orders")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;


    public function getNumberOfProducts()
    {
        $number=0;
        foreach ($this->getOrderedProducts() as $product){
            $number+=$product->getQuantity();
        }
        return $number;
    }


    public function __construct()
    {
        $this->orderedProducts = new ArrayCollection();
        $this->dateOrdered = new \DateTime("now");
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


    public function getDateOrdered()
    {
        return $this->dateOrdered;
    }


    public function setDateOrdered(\DateTime $dateOrdered)
    {
        $this->dateOrdered = $dateOrdered;
    }

    public function getFormatedDate()
    {
        $date = $this->getDateOrdered();
        return date_format($date, 'Y-m-d H:i:s');
    }


    /**
     * @return ArrayCollection
     */
    public function getOrderedProducts()
    {
        return $this->orderedProducts;
    }

    /**
     * @param ArrayCollection $orderedProducts
     */
    public function setOrderedProducts(ArrayCollection $orderedProducts)
    {
        $this->orderedProducts = $orderedProducts;
    }

    public function addProduct(OrderedProduct $orderedProduct)
    {
        $this->orderedProducts[] = $orderedProduct;
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

    public function getTotal()
    {
        $total = 0;
        $productsInCart = $this->getOrderedProducts();

        foreach ($productsInCart as $item) {

                $q = $item->getQuantity();
                $p = $item->getPrice();
                $total += ($q * $p);
        }

        return $total;
    }

}

