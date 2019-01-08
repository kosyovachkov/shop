<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cart;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use AppBundle\Form\ProductType;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductController
 * @package AppBundle\Controller
 * @Route("/product")
 */
class ProductController extends Controller
{


    /**
     * @Route("/{id}", name="view_product")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewProduct($id)
    {

        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        return $this->render('product/product.html.twig', ["product" => $product]);
    }

    /**
     * @Route("/all/", name="all_products")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAllProducts(Request $request)
    {

        $allProducts = $this->getDoctrine()->getRepository(Product::class)->getAllProducts();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $allProducts, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            15/*limit per page*/
        );


        return $this->render('product/all.html.twig', ["allProducts" => $pagination]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/promo/all", name="promo_products")
     */
    public function viewPromoProducts(Request $request){

        $promoProducts = $this->getDoctrine()->getRepository(Product::class)->getPromoAllProducts();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $promoProducts, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            6/*limit per page*/
        );

        return $this->render("product/promoProducts.html.twig", ["promoProducts"=>$pagination]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/featured/all", name="featured_products")
     */
    public function viewFeaturedProducts(Request $request){

        $featuredProducts = $this->getDoctrine()->getRepository(Product::class)->findBy(["featured"=>true], ["id"=>"DESC"]);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $featuredProducts, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            6/*limit per page*/
        );

        return $this->render("product/featuredProducts.html.twig", ["featuredProducts"=>$pagination]);
    }


}
