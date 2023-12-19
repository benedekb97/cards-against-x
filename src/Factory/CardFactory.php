<?php

namespace App\Factory;

use App\Entity\Card;
use App\Entity\CardInterface;
use App\Entity\Enum\CardType;

class CardFactory implements CardFactoryInterface
{
    public function createWhiteCard(): CardInterface
    {
        return new Card(type: CardType::WHITE);
    }

    public function createBlackCard(): CardInterface
    {
        return new Card(type: CardType::BLACK);
    }
}