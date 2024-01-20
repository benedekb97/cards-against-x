<?php

declare(strict_types=1);

namespace App\Tests\Feature\Lobby;

use App\DataFixtures\UserFixtures;
use App\Entity\User;
use App\Tests\Feature\FeatureTestCase;
use Doctrine\ORM\EntityManagerInterface;

class LobbyTest extends FeatureTestCase
{
    public function testCreateLobby(): void
    {
        $this->loginUser();

        $this->sendGet('create');

        $this->assertResponseRedirect();
        $this->assertRedirectLocation('lobby', ['slug' => $this->getLobbySlug()]);

        $this->sendGetToUrl($this->getRedirectLocation());

        $this->assertResponseOk();
    }

    public function testCreateLobbyAlreadyCreated(): void
    {
        $this->loginUser();

        $this->sendGet('create');
    }

    private function getLobbySlug(): string
    {
        return $this->getContainer()
            ->get(EntityManagerInterface::class)
            ?->getRepository(User::class)
            ?->findOneByEmail(UserFixtures::NORMAL_USER_EMAIL)
            ?->getPlayer()
            ?->getGame()
            ?->getSlug();
    }
}