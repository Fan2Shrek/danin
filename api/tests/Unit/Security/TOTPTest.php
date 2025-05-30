<?php

declare(strict_types=1);

namespace App\Tests\Unit\Security;

use App\Security\TOTP;
use PHPUnit\Framework\TestCase;

final class TOTPTest extends TestCase
{
    public function testTOTPGenerateSecretFormat()
    {
        self::assertMatchesRegularExpression(
            '/^[A-Z2-7]{16}$/',
            TOTP::generateSecret()
        );
    }

    public function testTOTPGenerateSecretRandom()
    {
        $secrets = [];
        for ($i = 0; $i < 100; ++$i) {
            $secrets[] = TOTP::generateSecret();
        }

        self::assertCount(\count(array_unique($secrets)), $secrets);
    }

    public function testVerifyCode()
    {
        $totp = new TOTP(
            'JBSWY3DPEHPK3PXP',
            30,
            'test_user',
        );

        self::assertTrue($totp->verifyCode($totp->getTOTPCode()));
    }
}
