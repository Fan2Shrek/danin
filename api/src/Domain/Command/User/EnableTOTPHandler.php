<?php

declare(strict_types=1);

namespace App\Domain\Command\User;

use App\Entity\User;
use App\Security\TOTPFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class EnableTOTPHandler
{
    public function __construct(
        private Security $security,
        private TOTPFactory $totpFactory,
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(EnableTOTPCommand $command): array
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new \RuntimeException('User not authenticated');
        }

        $totp = $this->totpFactory->activateForUser($user);
        $this->em->flush();

        return [
            'qrCode' => $this->totpFactory->getQRCodeURL($totp),
        ];
    }
}
