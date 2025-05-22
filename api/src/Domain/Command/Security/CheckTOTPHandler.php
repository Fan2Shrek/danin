<?php

declare(strict_types=1);

namespace App\Domain\Command\Security;

use App\Entity\User;
use App\Service\Authentication\TOTPFactory;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessagehandler]
final class CheckTOTPHandler
{
    public function __construct(
        private Security $security,
        private TOTPFactory $totpFactory,
    ) {
    }

    public function __invoke(CheckTOTPCommand $command): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        $totp = $this->totpFactory->create($user);
        $isValid = $totp->verifyCode($command->code);

        return new JsonResponse(['valid' => $isValid]);
    }
}
