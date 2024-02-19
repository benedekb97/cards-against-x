<?php

namespace App\DataFixtures;

use App\Entity\Enum\DeckType;
use App\Entity\UserInterface;
use App\Factory\DeckFactoryInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DeckFixture extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly DeckFactoryInterface    $deckFactory,
    ) {}

    public function load(ObjectManager $manager): void
    {
        $deck = $this->deckFactory->createForUser($this->loadUser());

        $deck->setName('Test deck');
        $deck->setType(DeckType::PUBLIC);

        $manager->persist($deck);
        $manager->flush();

        $this->addReference('deck', $deck);
    }

    private function loadUser(): UserInterface
    {
        return $this->getReference(UserFixtures::ADMIN_USER_EMAIL);
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}