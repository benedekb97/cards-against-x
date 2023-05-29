<?php

declare(strict_types=1);

namespace App\Security\OAuth;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class AuthSchAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface
{
    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly EntityManagerInterface $entityManager,
        private readonly RouterInterface $router
    ) {}


    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse(
            $this->router->generate('auth_sch_redirect')
        );
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'auth_sch_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('auth_sch_oauth');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge(
                $accessToken->getToken(),
                function () use ($accessToken, $client) {
                    /** @var AuthSchUser $user */
                    $user = $client->fetchUserFromToken($accessToken);

                    /** @var UserRepositoryInterface $repository */
                    $repository = $this->entityManager->getRepository(User::class);

                    $existingUser = $repository->findOneByEmail($user->getEmail());

                    if ($existingUser !== null) {
                        $existingUser->setInternalId($user->getInternalId());
                        $existingUser->setName($user->getDisplayName());

                        $this->entityManager->persist($existingUser);
                        $this->entityManager->flush();

                        return $existingUser;
                    }

                    $newUser = new User();

                    $newUser->setEmail($user->getEmail());
                    $newUser->setName($user->getDisplayName());
                    $newUser->setInternalId($user->getInternalId());

                    $this->entityManager->persist($newUser);
                    $this->entityManager->flush();

                    return $newUser;
                }
            )
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetUrl = $this->router->generate('index');

        return new RedirectResponse($targetUrl);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }
}