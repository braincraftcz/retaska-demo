<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductListController extends AbstractController
{
    /**
     * Výpis produktů vyfiltrovaný dle jedné kategorie (ID kategorie je v argumentu jako {id}).
     *
     * @Route("prodducts-list/{id}", name="homepage")
     */
    public function index(ProductRepository $repo, $id)
    {
        return $this->render('product_list/index.html.twig', [
            'products' => $repo->findBy(['category' => $id]), // <- zde se použije findBy() místo findAll()
        ]);
    }

    /**
     * Vyhledání produktu podle parametru v URL, napriklad "/search?search=taska" (Předpokládá formulář odeslaný metodou GET).
     *
     * @Route("/search", name="product_list_search")
     */
    public function search(EntityManagerInterface $entityManager, Request $request): Response
    {
        $search = $request->query->get('search');

        $products = $entityManager
            ->createQuery("SELECT p FROM " . Product::class . " p WHERE p.name LIKE :search")
            ->setParameter('search', "%$search%")
            ->getResult();
        // Vygeneruje SQL: SELECT * FROM product p WHERE p.name LIKE '%taska%'

        return $this->render('product_list/index.html.twig', [
            'products' => $products // <- $products jse ve stejném tvaru, jako vrací metoda findAll()
        ]);
    }
}
