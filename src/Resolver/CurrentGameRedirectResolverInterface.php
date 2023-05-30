<?php

declare(strict_types=1);

namespace App\Resolver;

use App\Entity\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

interface CurrentGameRedirectResolverInterface
{
    public function resolve(UserInterface $user): RedirectResponse;
}