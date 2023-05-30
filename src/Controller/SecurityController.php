<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly Security $security
    ) {}

    #[Route('/auth/redirect', name: 'auth_sch_redirect')]
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

    #[Route('/auth/check', name: 'auth_sch_check')]
    public function check(ClientRegistry $clientRegistry): Response
    {
        return new RedirectResponse('/');
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): RedirectResponse
    {
        return new RedirectResponse('/');
    }

    #[Route('/login', name: 'login')]
    public function login(
        UrlGeneratorInterface $urlGenerator,
        Session $session
    ): Response
    {
        if ($this->security->getUser() !== null) {
            return new RedirectResponse($urlGenerator->generate('index'));
        }

        $error = $session->get('_security.last_error');

        $session->remove('_security.last_error');

        $form = $this->createForm(LoginType::class);

        return $this->render(
            'login.html.twig',
            [
                'loginForm' => $form,
                'error' => $error,
            ]
        );
    }
}