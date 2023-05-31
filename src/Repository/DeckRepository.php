<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Deck;
use App\Entity\DeckInterface;
use App\Entity\GameInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DeckRepository extends ServiceEntityRepository implements DeckRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Deck::class);
    }

    public function getDecksForGame(GameInterface $game): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.public = :public')
            ->orWhere('d.createdBy = :user')
            ->setParameter('public', true)
            ->setParameter('user', $game->getCreatedBy())
            ->getQuery()
            ->getResult();
    }

    public function getDeckForGame(GameInterface $game, mixed $id): ?DeckInterface
    {
        return $this->createQueryBuilder('d')
            ->where('d.createdBy = :user')
            ->orWhere('d.public = :public')
            ->andWhere('d.id = :id')
            ->setParameter('user', $game->getCreatedBy())
            ->setParameter('public', true)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}