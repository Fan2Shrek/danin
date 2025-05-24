<?php

declare(strict_types=1);

namespace App\Domain\Command\Security;

use App\Entity\User;
use App\Security\TOTPFactory;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CheckTOTPHandler
{
    public function __construct(
        private Security $security,
        private TOTPFactory $totpFactory,
        private JWTTokenManagerInterface $jwtManager,
    ) {
    }

    public function __invoke(CheckTOTPCommand $command): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        $totp = $this->totpFactory->create($user);
        if ($totp->verifyCode($command->code)) {
            return new JsonResponse([
                'valid' => true,
                'token' => $this->jwtManager->create($user),
            ]);
        }

        return new JsonResponse([
            'valid' => false,
            'error' => 'Invalid TOTP code',
        ], 401);
    }
}
