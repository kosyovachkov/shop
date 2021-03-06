<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cart;
use AppBundle\Entity\OrderedProduct;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use AppBundle\Entity\UserOrder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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

        $productPrice = $product->getPrice();

        if ($product->getPromoPrice()) {
            $productPrice = $product->getPromoPrice();
        }

        $newProduct = $userCart->getCurrentProduct($product->getId());

        if ($newProduct === null) {
            $newProduct = new OrderedProduct();
            $newProduct->setName($product->getName());
            $newProduct->setQuantity(1);
            $newProduct->setPrice($productPrice);
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
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/product/add/{id}", name="add_product_to_cart")
     */
    public function addToCart(Request $request, int $id)
    {
        $user = $this->getUser();

        if ($user == null) {
            return $this->redirectToRoute("login");
        }

        $userCart = $user->getCart();
        $product = $this->getDoctrine()->getRepository(Product::class)->findOneBy(["id" => $id]);

        $productPrice = $product->getPrice();
        $productQuantity = $product->getQuantity();

        if ($product->getPromoPrice()) {
            $productPrice = $product->getPromoPrice();
        }

        $newOrderedProduct = $userCart->getCurrentProduct($product->getId());

        $oldQuantity = 0;
        if ($newOrderedProduct) {
            $oldQuantity = $newOrderedProduct->getQuantity();
        }

        $form = $this->createFormBuilder($newOrderedProduct)
            ->add("quantity", NumberType::class, [
                "label" => "Желано количество",
                "invalid_message"=>"Въведете число от 1 до 200",
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()&&$form->isValid()) {

            if($productQuantity<$form["quantity"]->getData()){
                $this->addFlash("quantityInfo", "Няма достатъчна наличност от продукта.");

                return $this->render("product/product.html.twig", ["product" => $product, "form" => $form->createView()]);
            }

            if (!$newOrderedProduct) {
                $newOrderedProduct = new OrderedProduct();
                $newOrderedProduct->setName($product->getName());
                $newOrderedProduct->setQuantity($form["quantity"]->getData());
                $newOrderedProduct->setPrice($productPrice);
                $newOrderedProduct->setProductId($id);
                $newOrderedProduct->addCart($userCart);
            } else {
                $newOrderedProduct->setQuantity($oldQuantity + $form["quantity"]->getData());
            }
            $userCart->addOrderedProduct($newOrderedProduct);

            $product->setQuantity($product->getQuantity() - $form["quantity"]->getData());

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->persist($newOrderedProduct);
            $em->persist($userCart);
            $em->flush();

            return $this->redirectToRoute("view_cart");
        }

        return $this->render("product/product.html.twig", ["product" => $product, "form" => $form->createView()]);
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

        if ($user == null) {
            return $this->redirectToRoute("login");
        }

        $products = [];

        foreach ($user->getCart()->getOrderedProducts() as $product) {
            if (!$product->getUserOrder()) {
                $products[] = $product;
            }
        };

        $total = $user->getCart()->getTotal();

        $wallet = $user->getWallet();

        return $this->render("cart/view.html.twig", ["products" => $products, "total" => $total, "wallet" => $wallet]);
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
        $user = $this->getUser();

        if ($user == null) {
            return $this->redirectToRoute("login");
        }

        $em = $this->getDoctrine()->getManager();

        $userCart = $user->getCart();
        $productsInCart = $userCart->getOrderedProducts();
        $total = $userCart->getTotal();
        $wallet = $user->getWallet();

        if ($total > $wallet) {
            $this->addFlash("noMoneyInfo", "Нямате достатъчна наличност за да извършите покупката.");
            return $this->redirectToRoute("order_products");
        }

        $order = new UserOrder();

        $order->setUser($user);

        foreach ($productsInCart as $product) {
            if (!$product->getUserOrder()) {
                $product->setUserOrder($order);
                $order->addProduct($product);
            }
        }

        $user->setWallet($user->getWallet() - $total);

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
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($user == null) {
            return $this->redirectToRoute("login");
        }

        $em = $this->getDoctrine()->getManager();

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