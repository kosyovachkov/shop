<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cart;
use AppBundle\Entity\Category;
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
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

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
    public function getAllUsers()
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($user->isAdmin()) {
            $users = $this->getDoctrine()->getRepository(User::class)->findAll();

            return $this->render("admin/users-all.html.twig", ["users" => $users]);
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

                return $this->redirectToRoute("products_in_category", ["id"=>$catId]);
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

            return $this->redirectToRoute("products_in_category", ["id"=>$catId]);
        }
    }

    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/user/orders/all/{id}", name="user_orders")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function allUserOrders(int $id){

        $currentUser = $this->getUser();

        if($currentUser->isAdmin()){

            $user = $this->getDoctrine()->getRepository(User::class)->find($id);

            $orders = $this->getDoctrine()->getRepository(UserOrder::class)->findBy(["user"=>$user]);

            return $this->render("order/all.html.twig", ["orders"=>$orders]);
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
            $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

            return $this->render("admin/show-categories.html.twig", ["categories" => $categories]);
        }
    }

    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/category/{id}/products", name="products_in_category")
     */
    public function showAllProductsInCategory(int $id){

        $currentUser = $this->getUser();

        if ($currentUser->isAdmin()) {

            $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
            $products = $this->getDoctrine()->getRepository(Product::class)->findBy(["category"=>$category]);

            return $this->render("admin/view-products-in-category.html.twig", ["products"=>$products, "category"=>$category]);

        }
    }
}
