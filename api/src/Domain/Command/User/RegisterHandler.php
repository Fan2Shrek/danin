<?php

declare(strict_types=1);

namespace App\Domain\Command\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
final class RegisterHandler
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(RegisterCommand $command): JsonResponse
    {
        // Check if the user already exists
        if ($this->userRepository->findOneBy(['username' => $command->username])) {
            return new JsonResponse('Username already exists', 400);
        }

        $user = (new User($command->username, $command->email))
            ->setRoles(['ROLE_USER']);

        $user->setPassword($this->passwordHasher->hashPassword($user, $command->password));

        $this->userRepository->save($user);

        return new JsonResponse([
            'id' => $user->getId(),
        ]);
    }
}
