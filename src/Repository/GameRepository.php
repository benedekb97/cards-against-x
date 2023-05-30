<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Game;
use App\Entity\GameInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class GameRepository extends ServiceEntityRepository implements GameRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function findOneBySlug(string $slug, bool $withDeleted = true): ?GameInterface
    {
        $queryBuilder = $this->createQueryBuilder('g')
            ->where('g.slug = :slug');

        if (!$withDeleted) {
            $queryBuilder->andWhere('g.deletedAt IS NULL');
        }

        return $queryBuilder
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }
}