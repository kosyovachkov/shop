<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Entity\Product;
use AppBundle\Form\MessageType;
use AppBundle\Service\Category\CategoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @param CategoryInterface $category
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, CategoryInterface $category)
    {

        $products = $this->getDoctrine()->getRepository(Product::class)->getLastThreeProducts();
        $featuredProducts = $this->getDoctrine()->getRepository(Product::class)->findBy(["featured"=>true], ["id"=>"DESC"], 3);


        /*$this->get("twig")->addGlobal("count", count($products));*/
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', ["products" => $products, "featured"=>$featuredProducts]);
    }

    /**
     * @Route("/contact", name="contact_form")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function getContactForm(Request $request)
    {

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            $this->addFlash("messageInfo", "Съобщението е изпратено успешно.");

            return $this->redirectToRoute("contact_form");
        }

        return $this->render("contact/contact.html.twig", ["contactForm" => $form->createView()]);

    }

}
