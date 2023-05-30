<?php

declare(strict_types=1);

namespace App\Security\FormLogin\Handlers;

use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;

class FailureHandler extends DefaultAuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
}