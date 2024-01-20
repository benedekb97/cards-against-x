<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

class TestRequest extends Request
{
    public function __construct(
        string $url,
        string $method,
        array $requestData = [],
        array $queryParameters = [],
        ?Cookie $sessionCookie = null
    )
    {
        parent::__construct(
            query: $queryParameters,
            request: $requestData,
            cookies: null !== $sessionCookie ? [
                $sessionCookie->getName() => $sessionCookie->getValue(),
            ] : [],
            server: [
                'REMOTE_ADDR' => '127.0.0.1',
                'SERVER_PORT' => '80',
                'SERVER_ADDR' => 'localhost',
            ]
        );

        $this->requestUri = $url;
        $this->method = $method;
    }
}