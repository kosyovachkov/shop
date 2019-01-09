<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cart;
use AppBundle\Entity\Category;
use AppBundle\Entity\Message;
use AppBundle\Entity\OrderedProduct;
use AppBundle\Entity\Product;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Entity\UserOrder;
use AppBundle\Form\ProductType;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @package AppBundle\Controller
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/category/add", name="add_category")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addCategory(Request $request)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($user->isAdmin()) {
            $category = new Category();

            $form = $this->createFormBuilder($category)
                ->add("name", TextType::class, [
                    "label" => "Име на категория",
                    "attr" => [
                        "class" => "form-control"
                    ]])
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($category);
                $em->flush();

                return $this->redirectToRoute("categories_all");
            }

            return $this->render("admin/add-category.html.twig", ["form" => $form->createView()]);
        }
    }

    /**
     * @param int $id
     * @Route("/{id}", name="remove_category")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeCategory(int $id)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($user->isAdmin()) {
            $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

            $productsInCategory = $category->getProducts();

            if (count($productsInCategory) > 0) {
                $this->addFlash("deleteInfo", "В категорията има добавени продукти. За да я изтриете първо трябва да премахнете тях");
                return $this->redirectToRoute("categories_all");
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();

            return $this->redirectToRoute("categories_all");
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/category/all", name="categories_all")
     */
    public function viewAllCategories(Request $request)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($user->isAdmin()) {
            $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

            $category = new Category();

            $form = $this->createFormBuilder($category)
                ->add("name", TextType::class, [
                    "label" => "Име на категория",
                    "attr" => [
                        "class" => "form-control"
                    ]])
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($category);
                $em->flush();

                return $this->redirectToRoute("categories_all");
            }

            return $this->render('admin/all-categories.html.twig', ["categories" => $categories, "form" => $form->createView()]);
        }
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/category/edit/{id}", name="edit_category")
     */
    public function editCategory(Request $request, int $id)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($user->isAdmin()) {
            $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

            $form = $this->createFormBuilder($category)
                ->add("name", TextType::class, [
                    "label" => "Име на категория",
                    "attr" => [
                        "class" => "form-control"
                    ]])
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($category);
                $em->flush();

                return $this->redirectToRoute("categories_all");
            }

            return $this->render("admin/edit-category.html.twig", ["form" => $form->createView()]);
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/user/all", name="users_all")
     */
    public function getAllUsers(Request $request)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($user->isAdmin()) {
            $users = $this->getDoctrine()->getRepository(User::class)->findAll();

            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $users, /* query NOT result */
                $request->query->getInt('page', 1)/*page number*/,
                3/*limit per page*/
            );

            return $this->render("admin/users-all.html.twig", ["users" => $pagination]);
        }
    }

    /**
     * @param int $id
     * @Route("/user/delete/{id}", name="user-delete")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteUser(int $id)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($user->isAdmin()) {
            $user = $this->getDoctrine()->getRepository(User::class)->find($id);

            $userCart = $this->getDoctrine()->getRepository(Cart::class)->findOneBy(["user" => $user]);

            /*$ordered = $this->getDoctrine()->getRepository(OrderedProduct::class)->findBy(["cart"=>$userCart]);*/
            /*        $userOrder = $this->getDoctrine()->getRepository(UserOrder::class)->findOneBy(["user"=>$user]);*/

            $em = $this->getDoctrine()->getManager();
            $em->remove($userCart);
            $em->remove($user);
            $em->flush();

            return $this->redirectToRoute("users_all");
        }
    }


    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/view/user/{id}", name="view-user")
     */
    public function viewUserProfile(int $id)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($user->isAdmin()) {
            $user = $this->getDoctrine()->getRepository(User::class)->find($id);

            return $this->render("admin/view-user.html.twig", ["user" => $user]);
        }
    }

    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/user/edit/{id}", name="user-adminEdit")
     */
    public function makeUserAdmin(int $id)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($user->isAdmin()) {
            $user = $this->getDoctrine()->getRepository(User::class)->find($id);

            $role = $this->getDoctrine()->getRepository(Role::class)->find(2);
            $user->addRole($role);
            $role->setUsers($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->persist($role);
            $em->flush();

            return $this->redirectToRoute("users_all");
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/product/create", name="create_product")
     */
    public function createProduct(Request $request)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($user->isAdmin()) {
            $product = new Product();

            $image = "no-image.jpg";

            $form = $this->createForm(ProductType::class, $product);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /**
                 * @var UploadedFile $file
                 */
                $file = $product->getImage();

                if ($file === "noSelectedImage") {
                    $product->setImage($image);
                } else {
                    $fileName = md5(uniqid()) . "." . $file->guessExtension();

                    try {
                        $file->move($this->getParameter("product_directory"), $fileName);
                    } catch (FileException $ex) {
                        echo $ex->getMessage();
                    }

                    $product->setImage($fileName);
                }

                $catId = $form["category"]->getData()->getId();

                $em = $this->getDoctrine()->getManager();
                $em->persist($product);
                $em->flush();

                return $this->redirectToRoute("products_in_category", ["id" => $catId]);
            }

            return $this->render("product/create.html.twig", ["productForm" => $form->createView()]);
        } else {

            return $this->redirectToRoute("products_in_category");
        }
    }


    /**
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/product/edit/{id}", name="edit_product")
     */
    public function editProduct(Request $request, int $id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $catId = $product->getCategory()->getId();

        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($user->isAdmin()) {

            $image = $product->getImage();

            $form = $this->createForm(ProductType::class, $product);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /**
                 * @var UploadedFile $file
                 */
                $file = $form->getData()->getImage();

                if ($file === "noSelectedImage") {
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

                return $this->redirectToRoute("products_in_category", ["id" => $catId]);
            }

            return $this->render("product/edit.html.twig", ["productForm" => $form->createView(), "product" => $product]);
        } else {

            return $this->render('product/product.html.twig', ["product" => $product]);
        }
    }

    /**
     * @Route("/product/delete/{id}", name="delete_product_from_category")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteProductFromCategory(int $id)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($user->isAdmin()) {
            $em = $this->getDoctrine()->getManager();

            $product = $em->getRepository(Product::class)->find($id);
            $catId = $product->getCategory()->getId();

            $em->remove($product);
            $em->flush();

            return $this->redirectToRoute("products_in_category", ["id" => $catId]);
        }
    }

    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/user/orders/all/{id}", name="user_orders")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function allUserOrders(int $id)
    {
        $currentUser = $this->getUser();

        if ($currentUser->isAdmin()) {

            $user = $this->getDoctrine()->getRepository(User::class)->find($id);

            $orders = $this->getDoctrine()->getRepository(UserOrder::class)->findBy(["user" => $user]);

            return $this->render("order/all.html.twig", ["orders" => $orders]);
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/category/show/all", name="category_show_all")
     */
    public function showAllCategories()
    {
        $currentUser = $this->getUser();

        if ($currentUser->isAdmin()) {
            $categories = $this->getDoctrine()->getRepository(Category::class)->findBy([], ["id" => "ASC"]);

            return $this->render("admin/show-categories.html.twig", ["categories" => $categories]);
        }
    }

    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/category/{id}/products", name="products_in_category")
     */
    public function showAllProductsInCategory(Request $request, int $id)
    {
        $currentUser = $this->getUser();

        if ($currentUser->isAdmin()) {

            $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
            $products = $this->getDoctrine()->getRepository(Product::class)->findBy(["category" => $category]);

            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $products, /* query NOT result */
                $request->query->getInt('page', 1)/*page number*/,
                6/*limit per page*/
            );

            return $this->render("admin/view-products-in-category.html.twig", ["products" => $pagination, "category" => $category]);

        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/message/all", name="all_messages")
     */
    public function getAllMessages(Request $request)
    {
        $currentUser = $this->getUser();

        if ($currentUser->isAdmin()) {

            $messages = $this->getDoctrine()->getRepository(Message::class)->findBy([], ["dateAdded" => "DESC"]);

            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $messages, /* query NOT result */
                $request->query->getInt('page', 1)/*page number*/,
                3/*limit per page*/
            );


            return $this->render("admin/view-all-messages.html.twig", ["messages" => $pagination]);

        }
    }

    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/message/{id}", name="view_one_message")
     */
    public function getMessage(int $id)
    {
        $currentUser = $this->getUser();

        if ($currentUser->isAdmin()) {

            $message = $this->getDoctrine()->getRepository(Message::class)->find($id);

            $message->setIsNew(false);

            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            return $this->render("admin/view-message.html.twig", ["message" => $message]);
        }
    }

    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/message/delete/{id}", name="delete_message")
     */
    public function deleteMessage(int $id)
    {
        $currentUser = $this->getUser();

        if ($currentUser->isAdmin()) {
            $message = $this->getDoctrine()->getRepository(Message::class)->find($id);

            $em = $this->getDoctrine()->getManager();
            $em->remove($message);
            $em->flush();

            $this->addFlash("messageDeleteInfo", "Съобщението беше успешно изтрито.");

            return $this->redirectToRoute("all_messages");

        }
    }
}
