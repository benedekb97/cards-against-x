<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Deck;
use App\Entity\DeckInterface;
use App\Entity\Enum\DeckType;
use App\Entity\GameInterface;
use App\Entity\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DeckRepository extends ServiceEntityRepository implements DeckRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Deck::class);
    }

    public function getDecksForUser(UserInterface $user): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.type = :type')
            ->orWhere('d.createdBy = :user')
            ->setParameter('type', DeckType::PUBLIC)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function getDecksForGame(GameInterface $game): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.type = :type')
            ->orWhere('d.createdBy = :user')
            ->setParameter('type', DeckType::PUBLIC)
            ->setParameter('user', $game->getCreatedBy())
            ->getQuery()
            ->getResult();
    }

    public function getDeckForGame(GameInterface $game, mixed $id): ?DeckInterface
    {
        return $this->createQueryBuilder('d')
            ->where('d.createdBy = :user')
            ->orWhere('d.type = :type')
            ->andWhere('d.id = :id')
            ->setParameter('user', $game->getCreatedBy())
            ->setParameter('type', DeckType::PUBLIC)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}