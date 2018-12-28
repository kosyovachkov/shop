<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Form\ProductType;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/product/create", name="create_product")
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function createAction(Request $request)
    {
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
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/product/edit/{id}", name="edit_product")
     */
    public function editAction(Request $request, int $id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

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

        return $this->render("product/edit.html.twig", ["productForm" => $form->createView(), "product" => $product]);
    }

    /**
     * @Route("/product/{id}", name="product_view")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewProduct($id)
    {

        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        return $this->render('product/product.html.twig', ["product" => $product]);
    }
}
