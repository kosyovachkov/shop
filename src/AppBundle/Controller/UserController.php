<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cart;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {

        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $newEmail = $form->getData()->getEmail();
            $existingUser = $this->getDoctrine()->getRepository(User::class)->findOneBy(["email"=>$newEmail]);

            if($existingUser){
                $this->addFlash("registerInfo", "User with this email already registered.");
                return $this->render("user/register.html.twig", ["registerForm"=>$form->createView()]);
            }

            $password = $this->get("security.password_encoder")->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $role = $this->getDoctrine()->getRepository(Role::class)->find(1);
            $user->addRole($role);

            $userCart = new Cart();
            $user->setCart($userCart);
            $userCart->setUser($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->persist($userCart);
            $em->flush();

            return $this->redirect("/login");
        }

        return $this->render('user/register.html.twig', ["registerForm"=>$form->createView()]);
    }

    /**
     * @param Request $request
     * @Route("/user/profile", name="user_profile")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function viewProfileAction(Request $request)
    {
        /**
         * @var User $loggedInUser
         */
        $loggedInUser = $this->getUser();

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(["email" => $loggedInUser->getEmail()]);

        return $this->render("user/profile.html.twig", ["user" => $user]);
    }


}
