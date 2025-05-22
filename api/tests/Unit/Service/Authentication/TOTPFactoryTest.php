<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Authentication;

use App\Service\Authentication\TOTP;
use App\Service\Authentication\TOTPFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

final class TOTPFactoryTest extends TestCase
{
    #[DataProvider('totpProvider')]
    public function testGetQRCodeURL(string $expected, TOTP $totp)
    {
        $factory = new TOTPFactory(
            'aaa',
            'test_app',
            new \Symfony\Component\Clock\NativeClock(),
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
                self::getFakeUser(),
            ),
        ];
        yield [
            'otpauth://totp/test_app:test_user?secret=ooo&issuer=test_app',
            new TOTP(
                'ooo',
                30,
                self::getFakeUser(),
            ),
        ];
    }

    private static function getFakeUser(): UserInterface
    {
        return new class() implements UserInterface {
            public function getUserIdentifier(): string
            {
                return 'test_user';
            }

            public function getRoles(): array
            {
                return ['ROLE_USER'];
            }

            public function getPassword(): ?string
            {
                return null;
            }

            public function getSalt(): ?string
            {
                return null;
            }

            public function eraseCredentials(): void
            {
                // No-op
            }
        };
    }
}
