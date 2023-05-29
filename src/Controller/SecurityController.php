<?php

declare(strict_types=1);

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route('/connect', name: 'auth_sch_redirect')]
    public function redirectToAuthSch(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry
            ->getClient('auth_sch_oauth')
            ->redirect(
                [
                    'basic',
                    'displayName',
                    'mail',
                ]
            );
    }

    #[Route('/callback', name: 'auth_sch_check')]
    public function check(ClientRegistry $clientRegistry): Response
    {
        return new RedirectResponse('/');
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): RedirectResponse
    {
        return new RedirectResponse('/');
    }
}