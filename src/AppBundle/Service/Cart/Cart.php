<?php
/**
 * Created by PhpStorm.
 * User: morgan
 * Date: 1/4/2019
 * Time: 8:06 PM
 */

namespace AppBundle\Service\Cart;


use AppBundle\Entity\User;
use AppBundle\Entity\UserOrder;
use AppBundle\Repository\CartRepository;
use AppBundle\Repository\OrderedProductRepository;
use AppBundle\Repository\UserOrderRepository;
use AppBundle\Repository\UserRepository;
use http\Env\Request;
use Symfony\Component\Security\Core\Security;

class Cart implements CartInterface
{
    private $cartRepository;

    private $user;

    private $orderedProductRepository;

    public function __construct(CartRepository $cartRepository, Security $security, OrderedProductRepository $orderedProductRepository)
    {
        $this->cartRepository = $cartRepository;
        $this->user = $security->getUser();
        $this->orderedProductRepository=$orderedProductRepository;
    }


    public function numberOfProductsInCart()
    {
        $cart = $this->cartRepository->findOneBy(["user"=>$this->user]);

        $products = $this->orderedProductRepository->findBy(["cart"=>$cart, "userOrder"=>null]);

        $numberOfProducts=0;
        foreach ($products as $product) {
            $numberOfProducts+=$product->getQuantity();
        }

        return $numberOfProducts;
    }
}