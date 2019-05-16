<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * Detail produktu.
     *
     * @Route("/product/{id}", name="product")
     */
    public function index(Product $product)
    {
        return $this->render('product/index.html.twig', ['product' => $product]);
    }
}
