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
        $alreadyExists = $this->userRepository->checkUsernameAndEmail($command->username, $command->email);

        if ($alreadyExists['username']) {
            return new JsonResponse([
                'message' => 'register.error.username.alreadyExists',
            ], 400);
        }

        if ($alreadyExists['email']) {
            return new JsonResponse([
                'message' => 'register.error.email.alreadyExists',
            ], 400);
        }

        $user = new User($command->username, $command->email)
            ->setRoles(['ROLE_USER'])
        ;

        $user->setPassword($this->passwordHasher->hashPassword($user, $command->password));

        $this->userRepository->save($user);

        return new JsonResponse([
            'username' => $user->getUsername(),
        ]);
    }
}
