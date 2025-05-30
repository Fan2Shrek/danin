<?php

namespace App\DataFixtures;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends AbstractFixtures
{
    private const USER_PASSWORD = 'aaa';

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function getEntityClass(): string
    {
        return User::class;
    }

    public function getData(): iterable
    {
        yield [
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => self::USER_PASSWORD,
            'roles' => ['ROLE_ADMIN'],
        ];

        yield [
            'username' => '2FA',
            'email' => 'big@gmail.com',
            'password' => self::USER_PASSWORD,
            'roles' => ['ROLE_USER'],
            'totpSecret' => 'JBSWY3DPEHPK3PXP',
        ];
    }

    protected function postInstantiate($entity): void
    {
        $entity->setPassword($this->passwordHasher->hashPassword($entity, $entity->getPassword()));
    }
}
