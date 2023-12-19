<?php

namespace App\Factory;

use App\Entity\Deck;
use App\Entity\DeckInterface;
use App\Entity\UserInterface;

class DeckFactory implements DeckFactoryInterface
{
    public function createForUser(UserInterface $user): DeckInterface
    {
        $deck = new Deck();

        $deck->setCreatedBy($user);

        return $deck;
    }
}