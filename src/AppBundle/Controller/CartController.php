<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cart;
use AppBundle\Entity\OrderedProduct;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use AppBundle\Entity\UserOrder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CartController
 * @package AppBundle\Controller
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 * @Route("/cart")
 */
class CartController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    /**
     * @param Request $request
     * @param int $id
     * @Route("/add/{id}", name="add_to_cart")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function quickAddToCart(Request $request, int $id)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($user == null) {
            return $this->redirectToRoute("login");
        }

        $userCart = $user->getCart();
        $product = $this->getDoctrine()->getRepository(Product::class)->findOneBy(["id" => $id]);

        $newProduct = $userCart->getCurrentProduct($product->getId());

        if ($newProduct === null) {
            $newProduct = new OrderedProduct();
            $newProduct->setName($product->getName());
            $newProduct->setQuantity(1);
            $newProduct->setPrice($product->getPrice());
            $newProduct->setProductId($id);
            $newProduct->addCart($userCart);
        } else {
            $newProduct->setQuantity($newProduct->getQuantity() + 1);
        }

        $userCart->addOrderedProduct($newProduct);

        $product->setQuantity($product->getQuantity() - 1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->persist($newProduct);
        $em->persist($userCart);
        $em->flush();

        return $this->redirectToRoute("view_cart");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/view", name="view_cart")
     */
    public function showCart()
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $products = [];
           foreach($user->getCart()->getOrderedProducts() as $product){
               if(!$product->getUserOrder()){
                   $products[]=$product;
               }
           };

        $total = $user->getCart()->getTotal();

        return $this->render("cart/view.html.twig", ["products" => $products, "total" => $total]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("", name="order_products")
     */
    public function PurchaseProductsInCart()
    {
        /**
         * @var User $user
         */
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $userCart = $user->getCart();
        $productsInCart = $userCart->getOrderedProducts();

        $order = new UserOrder();
        $order->setUser($user);
        foreach ($productsInCart as $product) {
            if(!$product->getUserOrder()){
                $product->setUserOrder($order);
                $order->addProduct($product);
            }
        }

       /* foreach ($productsInCart as $product) {
            $product->setIsActive(false);
        }*/

        $userCart->dropProducts();

        $em->persist($userCart);
        $em->persist($order);
        $em->flush();

        return $this->redirectToRoute("homepage");
    }

    /**
     * @param int $id
     * @Route("/delete/{id}", name="remove_product")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeProductsFromCart(int $id)
    {

        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        /**
         * @var Cart $userCart
         */
        $userCart = $user->getCart();
        $currentProduct = $userCart->getProductById($id);
        $currentProductQuantity = $currentProduct->getQuantity();

        $product = $this->getDoctrine()->getRepository(Product::class)->findOneBy(["id" => $currentProduct->getProductId()]);

        $product->setQuantity($product->getQuantity() + $currentProductQuantity);

        $em->persist($product);
        $em->remove($currentProduct);
        $em->flush();

        return $this->redirectToRoute("view_cart");
    }

}