<?php

namespace App\Factory;

use App\Entity\DeckInterface;
use App\Entity\UserInterface;

interface DeckFactoryInterface
{
    public function createForUser(UserInterface $user): DeckInterface;
}