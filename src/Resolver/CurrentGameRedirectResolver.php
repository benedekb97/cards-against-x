<?php

declare(strict_types=1);

namespace App\Resolver;

use App\Entity\Enum\GameStatus;
use App\Entity\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

readonly class CurrentGameRedirectResolver implements CurrentGameRedirectResolverInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {}

    public function resolve(UserInterface $user): RedirectResponse
    {
        if (!$user->hasPlayer()) {
            return new RedirectResponse(
                $this->urlGenerator->generate('index')
            );
        }

        if ($user->getPlayer()->getGame()->getStatus() === GameStatus::LOBBY) {
            return new RedirectResponse(
                $this->urlGenerator->generate('lobby', ['slug' => $user->getPlayer()->getGame()->getSlug()])
            );
        }
    }
}