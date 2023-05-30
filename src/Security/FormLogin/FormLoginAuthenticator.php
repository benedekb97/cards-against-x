<?php

declare(strict_types=1);

namespace App\Security\FormLogin;

use App\Entity\UserInterface;
use App\Security\FormLogin\Exception\UserLoggedInWithOauthException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator as SymfonyFormLoginAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\HttpUtils;

class FormLoginAuthenticator extends SymfonyFormLoginAuthenticator implements AuthenticatorInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        HttpUtils $httpUtils,
        UserProviderInterface $userProvider,
        AuthenticationSuccessHandlerInterface $successHandler,
        AuthenticationFailureHandlerInterface $failureHandler,
        array $options,
        UrlGeneratorInterface $urlGenerator
    )
    {
        parent::__construct($httpUtils, $userProvider, $successHandler, $failureHandler, $options);

        $this->urlGenerator = $urlGenerator;
    }

    public function authenticate(Request $request): Passport
    {
        $passport = parent::authenticate($request);

        $user = $passport->getUser();

        if (
            $user instanceof UserInterface &&
            $user->getInternalId() !== null &&
            $user->getPassword() === null
        ) {
            throw new UserLoggedInWithOauthException();
        }

        return $passport;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        if ($exception instanceof UserLoggedInWithOauthException) {
            return new RedirectResponse($this->urlGenerator->generate('auth_sch_redirect'));
        }

        return parent::onAuthenticationFailure($request, $exception);
    }
}