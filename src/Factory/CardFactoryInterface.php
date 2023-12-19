<?php

namespace App\Factory;

use App\Entity\CardInterface;

interface CardFactoryInterface
{
    public function createWhiteCard(): CardInterface;

    public function createBlackCard(): CardInterface;
}