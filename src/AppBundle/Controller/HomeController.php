<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', ["products"=>$products]);
    }
}
