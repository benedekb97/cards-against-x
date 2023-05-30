<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }

    #[Route('/disclaimer', name: 'disclaimer')]
    public function disclaimer(): Response
    {

    }

    #[Route('/help', name: 'help')]
    public function help(): Response
    {

    }
}