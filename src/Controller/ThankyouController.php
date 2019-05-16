<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThankyouController extends AbstractController
{
    /**
     * Děkovná stránka po odeslání objednávky.
     *
     * @Route("/thankyou", name="thankyou", methods={"GET"})
     */
    public function thank(): Response
    {
        return $this->render('thankyou/index.html.twig');
    }
}
