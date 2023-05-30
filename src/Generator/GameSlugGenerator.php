<?php

declare(strict_types=1);

namespace App\Generator;

use App\Repository\GameRepositoryInterface;

readonly class GameSlugGenerator implements GameSlugGeneratorInterface
{
    public function __construct(
        private GameRepositoryInterface $gameRepository
    ) {}

    public function generate(): string
    {
        do {
            $slug = $this->generateSlug();
        } while ($this->gameRepository->findOneBySlug($slug) !== null);

        return $slug;
    }

    private function generateSlug(): string
    {
        $slug = '';

        for ($i = 0; $i < self::SLUG_LENGTH; $i++) {
            $slug .= self::SLUG_CHARACTERS[array_rand(self::SLUG_CHARACTERS)];
        }

        return $slug;
    }


}