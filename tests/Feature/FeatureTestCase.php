<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use App\DataFixtures\UserFixtures;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\HttpUtils;

class FeatureTestCase extends KernelTestCase
{
    protected const SESSION_COOKIE_NAME = 'MOCKSESSID';

    private UrlGeneratorInterface $urlGenerator;

    private HttpUtils $httpUtils;

    private Request $request;

    private ?Response $response = null;

    private ?Cookie $sessionCookie = null;

    public function setUp(): void
    {
        $this->urlGenerator = $this->getContainer()->get('router')->getGenerator();
        $this->httpUtils = $this->getContainer()->get('security.http_utils');
    }

    protected function sendGet(string $routeName, array $routeParameters = [], array $queryParameters = []): void
    {
        $this->response = static::$kernel->handle(
            $this->prepareRequest(
                routeName: $routeName,
                query: $queryParameters,
                routeParameters: $routeParameters
            )
        );
    }

    protected function sendGetToUrl(string $url): void
    {
        $this->response = static::$kernel->handle(
            $this->prepareRequest(
                url: $url
            )
        );
    }

    protected function sendJsonPost(
        string $routeName,
        array $data,
        array $routeParameters = [],
        array $queryParameters = []
    ): void {
        $this->response = static::$kernel->handle(
            $this->prepareRequest(
                routeName: $routeName,
                data: $data,
                query: $queryParameters,
                method: Request::METHOD_POST,
                routeParameters: $routeParameters
            )
        );
    }

    protected function assertResponseOk(): void
    {
        $this->assertEquals(Response::HTTP_OK, $this->response->getStatusCode());
    }

    protected function assertResponseNotFound(): void
    {
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->response->getStatusCode());
    }

    protected function assertResponseRedirect(): void
    {
        $this->assertEquals(Response::HTTP_FOUND, $this->response->getStatusCode());
    }

    protected function assertSecurityRedirectLocation(string $route, array $routeParameters = []): void
    {
        $this->assertEquals(
            $this->httpUtils->generateUri($this->request, $this->urlGenerator->generate($route, $routeParameters)),
            $this->response->headers->get('Location')
        );
    }

    protected function assertRedirectLocation(string $route, array $routeParameters = []): void
    {
        $this->assertEquals(
            $this->urlGenerator->generate($route, $routeParameters),
            $this->response->headers->get('Location')
        );
    }

    private function prepareRequest(
        ?string $routeName = null,
        ?string $url = null,
        array $data = [],
        array $query = [],
        string $method = Request::METHOD_GET,
        array $routeParameters = []
    ): Request {
        if (null === $url) {
            $url = $this->urlGenerator->generate($routeName, $routeParameters);
        }

        return $this->request = new TestRequest($url, $method, $data, $query, $this->sessionCookie);
    }

    protected function getResponse(): Response
    {
        return $this->response;
    }

    protected function getRequest(): Request
    {
        return $this->request;
    }

    protected function getSessionCookie(): ?Cookie
    {
        return $this->sessionCookie;
    }

    protected function loginAdmin(): void
    {
        $this->sendJsonPost(
            routeName: 'login',
            data: [
                'login' => [
                    'email' => UserFixtures::ADMIN_USER_EMAIL,
                    'password' => 'password123',
                ],
            ]
        );

        $this->setSessionCookie();
    }

    protected function loginUser(): void
    {
        $this->sendJsonPost(
            routeName: 'login',
            data: [
                'login' => [
                    'email' => UserFixtures::NORMAL_USER_EMAIL,
                    'password'=> 'password123',
                ],
            ]
        );

        $this->setSessionCookie();
    }

    private function setSessionCookie(): void
    {
        $cookies = $this->response->headers->getCookies();

        foreach ($cookies as $cookie) {
            if (self::SESSION_COOKIE_NAME === $cookie->getName()) {
                $this->sessionCookie = $cookie;

                return;
            }
        }
    }

    protected function getRedirectLocation(): ?string
    {
        return $this->response?->headers?->get('Location');
    }
}