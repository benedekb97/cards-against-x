<?php

declare(strict_types=1);

namespace App\Security\OAuth;

use App\Entity\UserInterface;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class AuthSchProvider extends AbstractProvider
{
    public function getBaseAuthorizationUrl(): string
    {
        return 'https://auth.sch.bme.hu/site/login';
    }

    public function getBaseAccessTokenUrl(array $params): string
    {
        return 'https://auth.sch.bme.hu/oauth2/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return 'https://auth.sch.bme.hu/api/profile/?access_token=' . $token->getToken();
    }

    protected function getDefaultScopes(): array
    {
        return [];
    }

    protected function checkResponse(ResponseInterface $response, $data): bool
    {
        return $response->getStatusCode() === Response::HTTP_OK;
    }

    protected function createResourceOwner(array $response, AccessToken $token): AuthSchUser
    {
        return new AuthSchUser($response['internal_id'], $response['displayName'], $response['mail']);
    }

    public function getScopeSeparator(): string
    {
        return '+';
    }

    public function getAuthorizationUrl(array $options = []): string
    {
        $base = $this->getBaseAuthorizationUrl();
        $params = $this->getAuthorizationParameters($options);
        $query = $this->getAuthorizationQuery($params);

        // AuthSCH doesn't like URL encoded + signs in the scope :)
        $query = str_replace('%2B', '+', $query);

        return $this->appendQuery($base, $query);
    }
}