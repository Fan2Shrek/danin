<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Clock\ClockInterface;

final class TOTPFactory
{
    public function __construct(
        private string $appName,
        private ClockInterface $clock,
        private int $timeStep = 30,
    ) {
    }

    public function activateForUser(User $user): TOTP
    {
        if ($user->hasTotp()) {
            throw new \RuntimeException('User already has TOTP enabled');
        }

        $user->setTotpSecret(TOTP::generateSecret());

        return $this->create($user);
    }

    public function deactivateForUser(User $user): void
    {
        if (!$user->hasTotp()) {
            throw new \RuntimeException('User does not have TOTP enabled');
        }

        $user->setTotpSecret(null);
    }

    public function create(User $user): TOTP
    {
        if (!$user->hasTotp()) {
            throw new \RuntimeException('User does not have TOTP enabled');
        }

        return new TOTP(
            $user->getTotpSecret(),
            $this->timeStep,
            $user->getUsername(),
        );
    }

    public function getQRCodeURL(TOTP $totp): string
    {
        return \sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s',
            urlencode($this->appName),
            urlencode($totp->getUsername()),
            $totp->getSecret(),
            urlencode($this->appName)
        );
    }
}
