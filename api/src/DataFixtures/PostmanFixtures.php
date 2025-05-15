<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Loads the data fixtures for Postman.
 */
#[When('test')]
class PostmanFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->createUser($manager);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['postman'];
    }

    private function createUser(ObjectManager $manager): void
    {
        $user = new User('test', 'test_user@yopmail.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'test1234'));
        $user->setRoles(['ROLE_USER']);

        $manager->persist($user);
    }
}
