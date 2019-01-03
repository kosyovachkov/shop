<?php

namespace AppBundle\Controller;

use AppBundle\Entity\UserOrder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserOrderController
 * @package AppBundle\Controller
 * @Route("/order")
 */
class UserOrderController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/all", name="orders_all")
     */
    public function allOrders(){

        $orders = $this->getDoctrine()->getRepository(UserOrder::class)->findAll();

        return $this->render("order/all.html.twig", ["orders"=>$orders]);
    }

    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/view/{id}", name="view_order")
     */
    public function viewOrder(int $id){

        $order = $this->getDoctrine()->getRepository(UserOrder::class)->find($id);

        $total = $order->getTotal();

        $orderProducts = $order->getOrderedProducts();
        return $this->render("order/view.html.twig", ["products"=>$orderProducts, "total"=>$total]);
    }
}
