<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class TOTPFactory
{
    public function __construct(
        private string $secret,
        private string $appName,
        private ClockInterface $clock,
        private int $timeStep = 30,
    ) {}

    public function create(UserInterface $user): TOTP
    {
        return new TOTP(
            $this->secret,
            $this->timeStep,
            $user,
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
