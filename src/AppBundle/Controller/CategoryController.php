<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 * @package AppBundle\Controller
 * @Route("/category")
 */
class CategoryController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    /**
     * @param int $id
     * @Route("/{id}", name="category")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllProductsFromCategory(int $id){

        $products = $this->getDoctrine()->getRepository(Product::class)->findBy(["category"=>$id]);
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(["id"=>$id]);

        return $this->render('category/all.html.twig', ["products"=>$products, "category"=>$category]);
    }
}
