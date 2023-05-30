<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

interface UserHostIneligibilityHandlerInterface
{
    public function handle(UserInterface $user): RedirectResponse;
}