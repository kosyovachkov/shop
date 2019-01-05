<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/{id}", name="category")
     */
    public function getAllProductsFromCategory(Request $request, int $id){

        $products = $this->getDoctrine()->getRepository(Product::class)->findBy(["category"=>$id]);
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(["id"=>$id]);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $products, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            6/*limit per page*/
        );

        return $this->render('category/all.html.twig', ["pagination"=>$pagination, "category"=>$category]);
    }
}
