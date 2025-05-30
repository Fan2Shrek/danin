<?php

declare(strict_types=1);

namespace App\Tests\Unit\Security;

use App\Entity\User;
use App\Security\TOTP;
use App\Security\TOTPFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\NativeClock;

final class TOTPFactoryTest extends TestCase
{
    #[DataProvider('totpProvider')]
    public function testGetQRCodeURL(string $expected, TOTP $totp)
    {
        $factory = new TOTPFactory(
            'test_app',
            new NativeClock(),
        );

        $this->assertSame($expected, $factory->getQRCodeURL($totp));
    }

    public static function totpProvider(): iterable
    {
        yield [
            'otpauth://totp/test_app:test_user?secret=aaa&issuer=test_app',
            new TOTP(
                'aaa',
                30,
                'test_user',
            ),
        ];
        yield [
            'otpauth://totp/test_app:test_user?secret=ooo&issuer=test_app',
            new TOTP(
                'ooo',
                30,
                'test_user',
            ),
        ];
    }

    public function testActivateForUser()
    {
        $factory = new TOTPFactory(
            'test_app',
            new NativeClock(),
        );

        $user = self::getFakeUser();
        $totp = $factory->activateForUser($user);

        $this->assertNotEmpty($totp->getSecret());
    }

    public function testDeactivateForUser()
    {
        $factory = new TOTPFactory(
            'test_app',
            new NativeClock(),
        );

        $user = self::getFakeUser();
        $factory->activateForUser($user);
        $factory->deactivateForUser($user);

        $this->assertNull($user->getTotpSecret());
    }

    private static function getFakeUser(): User
    {
        return new User(
            'test_user',
            'test@test.com',
        );
    }
}
