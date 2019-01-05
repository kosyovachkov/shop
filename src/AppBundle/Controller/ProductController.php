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
    public function viewAllProducts()
    {

        $allProducts = $this->getDoctrine()->getRepository(Product::class)->getAllProducts();

        return $this->render('product/all.html.twig', ["products" => $allProducts]);
    }


}
