<?php

namespace App\DataFixtures;

use App\Entity\DeckInterface;
use App\Entity\UserInterface;
use App\Factory\CardFactoryInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CardFixture extends Fixture implements DependentFixtureInterface
{
    private const WHITE_CARD_COUNT = 450;
    private const BLACK_CARD_COUNT = 150;

    private UserInterface $user;

    public function __construct(
        private readonly CardFactoryInterface $cardFactory
    ) {}

    public function load(ObjectManager $manager): void
    {
        $this->user = $this->getReference(UserFixtures::ADMIN_USER_EMAIL);

        $deck = $this->getReference('deck');

        $this->loadWhiteCards($deck);
        $this->loadBlackCards($deck);

        $manager->persist($deck);
        $manager->flush();
    }

    private function loadWhiteCards(DeckInterface $deck): void
    {
        for ($i = 1; $i <= self::WHITE_CARD_COUNT; $i++) {
            $card = $this->cardFactory->createWhiteCard();

            $card->setText(['Card ' . $i]);
            $card->setCreatedBy($this->user);

            $deck->addCard($card);
        }
    }

    private function loadBlackCards(DeckInterface $deck): void
    {
        for ($i = 1; $i<= self::BLACK_CARD_COUNT; $i++) {
            $card = $this->cardFactory->createBlackCard();

            $blankCount = random_int(1, 3);

            $text = [];

            for ($j = 0; $j <= $blankCount; $j++) {
                $text[] = 'Text ' . $j;
            }

            $card->setText($text);
            $card->setCreatedBy($this->user);

            $deck->addCard($card);
        }
    }

    public function getDependencies(): array
    {
        return [
            DeckFixture::class,
        ];
    }
}