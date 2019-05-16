<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/order", name="order")
     */
    public function index()
    {
        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
        ]);
    }

    /**
     * @Route("/order", name="order", methods={"GET","POST"})
     */
    public function edit(Request $request, SessionInterface $session): Response
    {
        $order = new Order();

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Produkty, které přijdou do objednávky načteme z session
            $products = $session->get('basket');

            // Pro každý produkt v košíku:
            foreach ($products as $product) {
                // Nová entita - produkt v objednávce:
                $product = new OrderProduct;

                // Entitu naplníme daty ze session
                $product->setId($product['id']);
                $product->setName($product['name']);
                $product->setPrice($product['price']);
                $product->setAmount($product['amount']);

                // Produkt necháme ukládat do databáze
                $this->getDoctrine()->getManager()->persist($product);

                // A přidáme do právě vytvářené objednávky
                $order->addProduct($product);
            }

            $order->setTotalPrice($product->getPrice());

            // Uložení objednávky do databáze
            $this->getDoctrine()->getManager()->persist($order);
            $this->getDoctrine()->getManager()->flush();

            // Nyní můžeme košík smazat - zákazník už má objednáno:
            $session->set('basket', []);
            // Případně session daného uživatele smazat komplet:
            $session->clear();

            // Redirect na děkovnou stránku
            return $this->redirectToRoute('thankyou');
        }

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'products' => $session->get('basket', [])
        ]);
    }
}
