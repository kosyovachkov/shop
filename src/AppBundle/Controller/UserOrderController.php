<?php

namespace AppBundle\Controller;

use AppBundle\Entity\UserOrder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserOrderController
 * @package AppBundle\Controller
 * @Route("/order")
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class UserOrderController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/all", name="orders_all")
     */
    public function allMyOrders(){

        $user = $this->getUser();

        $orders = $this->getDoctrine()->getRepository(UserOrder::class)->findBy(["user"=>$user]);

        return $this->render("order/all.html.twig", ["orders"=>$orders]);
    }

    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/view/{id}", name="view_order")
     */
    public function viewMyOrder(int $id){

        $order = $this->getDoctrine()->getRepository(UserOrder::class)->find($id);

        $total = $order->getTotal();

        $orderProducts = $order->getOrderedProducts();
        return $this->render("order/view.html.twig", ["products"=>$orderProducts, "total"=>$total]);
    }
}
