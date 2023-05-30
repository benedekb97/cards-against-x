<?php

declare(strict_types=1);

namespace App\Security\FormLogin\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class UserLoggedInWithOauthException extends AuthenticationException
{
}