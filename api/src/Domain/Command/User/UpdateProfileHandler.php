<?php

declare(strict_types=1);

namespace App\Domain\Command\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
final class UpdateProfileHandler
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function __invoke(UpdateProfileCommand $command): JsonResponse
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new \RuntimeException('User not found');
        }

        if (null !== $command->username) {
            $user->setUsername($command->username);
        }

        if (null !== $command->email) {
            $user->setEmail($command->email);
        }

        if (null !== $command->password) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $command->password));
        }

        $this->em->flush();

        return new JsonResponse([
            'username' => $user->getUsername(),
        ]);
    }
}
