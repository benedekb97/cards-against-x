<?php

declare(strict_types=1);

namespace App\Security\FormLogin;

use App\Entity\UserInterface;
use App\Security\FormLogin\Exception\UserLoggedInWithOauthException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordUpgradeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\ParameterBagUtils;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

class FormLoginAuthenticator extends AbstractLoginFormAuthenticator implements AuthenticatorInterface
{
    public function __construct(
        private readonly HttpUtils $httpUtils,
        private readonly UserProviderInterface $userProvider,
        private readonly AuthenticationSuccessHandlerInterface $successHandler,
        private readonly AuthenticationFailureHandlerInterface $failureHandler,
        private array $options,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly HttpKernelInterface $httpKernel
    ) {
        $this->options = array_merge([
            'username_parameter' => '_username',
            'password_parameter' => '_password',
            'check_path' => '/login_check',
            'post_only' => true,
            'form_only' => false,
            'enable_csrf' => false,
            'csrf_parameter' => '_csrf_token',
            'csrf_token_id' => 'authenticate',
        ], $options);
    }

    public function authenticate(Request $request): Passport
    {
        $passport = $this->getPassport($request);

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

        return $this->failureHandler->onAuthenticationFailure($request, $exception);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->httpUtils->generateUri($request, $this->options['login_path']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return $this->successHandler->onAuthenticationSuccess($request, $token);
    }

    public function getPassport(Request $request): Passport
    {
        $credentials = $this->getCredentials($request);

        $userBadge = new UserBadge($credentials['username'], $this->userProvider->loadUserByIdentifier(...));
        $passport = new Passport($userBadge, new PasswordCredentials($credentials['password']), [new RememberMeBadge()]);

        if ($this->options['enable_csrf']) {
            $passport->addBadge(new CsrfTokenBadge($this->options['csrf_token_id'], $credentials['csrf_token']));
        }

        if ($this->userProvider instanceof PasswordUpgraderInterface) {
            $passport->addBadge(new PasswordUpgradeBadge($credentials['password'], $this->userProvider));
        }

        return $passport;
    }

    private function getCredentials(Request $request): array
    {
        $credentials = [];

        $credentials['csrf_token'] = ParameterBagUtils::getRequestParameterValue($request, $this->options['csrf_parameter']);

        if ($this->options['post_only']) {
            $credentials['username'] = ParameterBagUtils::getParameterBagValue($request->request, $this->options['username_parameter']);
            $credentials['password'] = ParameterBagUtils::getParameterBagValue($request->request, $this->options['password_parameter']) ?? '';
        } else {
            $credentials['username'] = ParameterBagUtils::getRequestParameterValue($request, $this->options['username_parameter']);
            $credentials['password'] = ParameterBagUtils::getRequestParameterValue($request, $this->options['password_parameter']) ?? '';
        }

        if (!\is_string($credentials['username']) && !$credentials['username'] instanceof \Stringable) {
            throw new BadRequestHttpException(sprintf('The key "%s" must be a string, "%s" given.', $this->options['username_parameter'], \gettype($credentials['username'])));
        }

        $credentials['username'] = trim($credentials['username']);

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $credentials['username']);

        return $credentials;
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        if (!$this->options['use_forward']) {
            return parent::start($request, $authException);
        }

        $subRequest = $this->httpUtils->createRequest($request, $this->options['login_path']);
        $response = $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        if (200 === $response->getStatusCode()) {
            $response->setStatusCode(401);
        }

        return $response;
    }

    public function createToken(Passport $passport, string $firewallName): TokenInterface
    {
        return new UsernamePasswordToken($passport->getUser(), $firewallName, $passport->getUser()->getRoles());
    }

    public function supports(Request $request): bool
    {
        return ($this->options['post_only'] ? $request->isMethod('POST') : true)
            && $this->httpUtils->checkRequestPath($request, $this->options['check_path'])
            && ($this->options['form_only'] ? 'form' === (method_exists(Request::class, 'getContentTypeFormat') ? $request->getContentTypeFormat() : $request->getContentType()) : true);
    }
}