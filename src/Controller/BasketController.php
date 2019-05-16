<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class BasketController extends AbstractController
{
    /**
     * Přidat produkt do košíku.
     * -> Odkaz sem je v templates/product/index.html.twig
     *
     * @Route("/basket-add/{id}", name="basket_add")
     */
    public function add(Product $product, SessionInterface $session)
    {
        // Načtu celý košík ze session (pokud neexistuje, použije se prázdné pole (viz druhý argument))
        $basket = $session->get('basket', []);

        // Do pole se přidá následující pole, jako klíč se použije ID produktu. Pokud už produkt v košíku je
        // (v poli existuje klíč odpovídající ID produktu), nic se nestane.
        if (!isset($basket[$product->getId()])) {
            $basket[$product->getId()] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'amount' => 1
            ];
        }

        // Uloží změněné pole reprezentující košík do session (přepíše stávající).
        $session->set('basket', $basket);

        // Přesměruje na routu 'basket' (metodu 'index()' v této třídě)
        return $this->redirectToRoute('basket');
    }

    /**
     * Změna počtu produktů v košíku. Pokud je mode=1 -> zvýšit počet, pokud je 0 -> snížit.
     *
     * @Route("/basket-change-amount/{id}/{mode}", name="basket_change_amount")
     */
    public function changeAmount(Product $product, SessionInterface $session, $mode)
    {
        // Načtu celý košík z databáze
        $basket = $session->get('basket', []);

        // pokud je mode 1..
        if ($mode == 1) {
            // ..zvýšit počet produktů o 1
            $basket[$product->getId()]['amount']++;
        } else {
            // ..jinak snížit o 1
            $basket[$product->getId()]['amount']--;
        }

        // Pokud po úpravě počet = 0 -> kompletně odebrat produkt z košíku
        if ($basket[$product->getId()]['amount'] === 0) {
            unset($basket[$product->getId()]);
        }

        // Uložím celý košík do databáze
        $session->set('basket', $basket);

        // Přesměruju na routu 'basket' (neboli metodu 'index' v této třídě)
        return $this->redirectToRoute('basket');
    }

    public function removeProduct()
    {
        
    }

    /**
     * Vypsání košíku.
     *
     * @Route("/basket", name="basket")
     */
    public function index(SessionInterface $session)
    {
        // Do šablony předám pouze to, co je v session
        return $this->render('basket/index.html.twig', [
            'basket' => $session->get('basket', [])
        ]);
    }
}
