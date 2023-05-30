<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Enum\GameStatus;
use App\Entity\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

readonly class UserHostIneligibilityHandler implements UserHostIneligibilityHandlerInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {}

    public function handle(UserInterface $user): RedirectResponse
    {
        if ($user->getPlayer()->getGame()->getStatus() === GameStatus::LOBBY) {
            return new RedirectResponse(
                $this->urlGenerator->generate('lobby', ['slug' => $user->getPlayer()->getGame()->getSlug()])
            );
        }

        return new RedirectResponse(
            $this->urlGenerator->generate('play', ['slug' => $user->getPlayer()->getGame()->getSlug()])
        );
    }
}