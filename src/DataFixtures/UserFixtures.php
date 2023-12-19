<?php

namespace App\DataFixtures;

use App\Entity\Enum\Role;
use App\Entity\User;
use App\Entity\UserInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const ADMIN_USER_EMAIL = 'admin@cards-against-x.com';
    public const NORMAL_USER_EMAIL = 'user@cards-against-x.com';

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $manager->persist($this->createAdminUser());
        $manager->persist($this->createNormalUser());

        $manager->flush();
    }

    private function createAdminUser(): UserInterface
    {
        $user = new User();

        $user->setEmail(self::ADMIN_USER_EMAIL);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'password123')
        );
        $user->addRole(Role::ROLE_ADMINISTRATOR->value);

        $user->setName('Admin User');
        $user->setActivated(true);
        $user->setNickname('Admin');

        $this->addReference(self::ADMIN_USER_EMAIL, $user);

        return $user;
    }

    private function createNormalUser(): UserInterface
    {
        $user = new User();

        $user->setEmail(self::NORMAL_USER_EMAIL);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'password123')
        );
        $user->setName('Normal User');
        $user->setActivated(true);
        $user->setNickname('User');

        $this->addReference(self::NORMAL_USER_EMAIL, $user);

        return $user;
    }
}