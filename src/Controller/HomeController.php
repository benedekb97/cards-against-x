<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\UserInterface;
use App\Service\LocaleServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly LocaleServiceInterface $localeService
    ) {}

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        return $this->render(
            'index.html.twig',
            [
                'user' => $user,
                'game' => $user?->getPlayer()?->getGame(),
            ]
        );
    }

    #[Route('/disclaimer', name: 'disclaimer')]
    public function disclaimer(): Response
    {

    }

    #[Route('/help', name: 'help')]
    public function help(): Response
    {

    }

    #[Route('/locale/{localeCode}', name: 'change_locale')]
    public function changeLocale(Request $request, string $localeCode): RedirectResponse
    {
        $this->localeService->changeLocale($localeCode);

        return $this->redirect($request->headers->get('referer'));
    }
}