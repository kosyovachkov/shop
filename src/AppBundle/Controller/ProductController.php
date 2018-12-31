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
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/create", name="create_product")
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function createAction(Request $request)
    {
        /**
         * @var User $user
         */
        $roles = $this->getUser()->getRoles();

        if (in_array("ROLE_ADMIN", $roles)) {
            $product = new Product();

            $form = $this->createForm(ProductType::class, $product);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /**
                 * @var UploadedFile $file
                 */
                $file = $product->getImage();
                $fileName = md5(uniqid()) . "." . $file->guessExtension();

                try {
                    $file->move($this->getParameter("product_directory"), $fileName);
                } catch (FileException $ex) {

                }

                $product->setImage($fileName);
                $em = $this->getDoctrine()->getManager();
                $em->persist($product);
                $em->flush();

                return $this->redirectToRoute("homepage");
            }

            return $this->render("product/create.html.twig", ["productForm" => $form->createView()]);
        } else {
            return $this->redirectToRoute("homepage");
        }
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/edit/{id}", name="edit_product")
     */
    public function editAction(Request $request, int $id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        /**
         * @var User $user
         */
        $roles = $this->getUser()->getRoles();

        if (in_array("ROLE_ADMIN", $roles)) {

            $image = $product->getImage();

            $form = $this->createForm(ProductType::class, $product);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /**
                 * @var UploadedFile $file
                 */
                $file = $product->getImage();

                if ($file == null) {
                    $product->setImage($image);
                } else {
                    $fileName = md5(uniqid()) . "." . $file->guessExtension();

                    try {
                        $file->move($this->getParameter("product_directory"), $fileName);
                    } catch (FileException $ex) {

                    }
                    $product->setImage($fileName);
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($product);
                $em->flush();

                return $this->redirectToRoute("homepage");
            }

            return $this->render("product/edit.html.twig", ["productForm" => $form->createView(), "product" => $product]);
        } else {
            return $this->render('product/product.html.twig', ["product" => $product]);
        }
    }

    /**
     * @Route("/delete/{id}", name="delete_product")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteProduct($id)
    {
        /**
         * @var User $user
         */
        $roles = $this->getUser()->getRoles();

        if (in_array("ROLE_ADMIN", $roles)) {
            $em = $this->getDoctrine()->getManager();
            $product = $em->getRepository(Product::class)->find($id);
            $em->remove($product);
            $em->flush();

            return $this->redirectToRoute("homepage");
        } else {
            return $this->redirectToRoute("homepage");
        }
    }

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
