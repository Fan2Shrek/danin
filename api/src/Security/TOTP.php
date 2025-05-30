<?php

declare(strict_types=1);

namespace App\Security;

final class TOTP
{
    private const string BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    public function __construct(
        private readonly string $secret,
        private readonly int $timeStep,
        private readonly string $user,
    ) {
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function getUsername(): string
    {
        return $this->user;
    }

    public function getTOTPCode(): string
    {
        $timeSlice = floor(time() / $this->timeStep);

        $time = pack('N*', 0).pack('N*', $timeSlice);

        $secretKey = $this->base32Decode($this->secret);
        $hash = hash_hmac('sha1', $time, $secretKey, true);

        $offset = \ord($hash[19]) & 0xF;
        $truncatedHash = unpack('N', substr($hash, $offset, 4))[1] & 0x7FFFFFFF;

        return str_pad(\strval($truncatedHash % 1000000), 6, '0', STR_PAD_LEFT);
    }

    public function verifyCode(string $input, int $window = 1): bool
    {
        $currentSlice = floor(time() / $this->timeStep);
        for ($i = -$window; $i <= $window; ++$i) {
            if ($this->getTOTPCode($currentSlice + $i) === $input) {
                return true;
            }
        }

        return false;
    }

    public static function generateSecret(): string
    {
        $secret = '';
        for ($i = 0; $i < 16; ++$i) {
            $secret .= self::BASE32_ALPHABET[random_int(0, \strlen(self::BASE32_ALPHABET) - 1)];
        }

        return $secret;
    }

    private function base32Decode(string $b32): string
    {
        $b32 = strtoupper($b32);
        $binaryString = '';

        foreach (str_split($b32) as $char) {
            $val = strpos(self::BASE32_ALPHABET, $char);
            if (false === $val) {
                continue;
            }
            $binaryString .= str_pad(decbin($val), 5, '0', STR_PAD_LEFT);
        }

        $bytes = '';
        foreach (str_split($binaryString, 8) as $byte) {
            if (8 === \strlen($byte)) {
                $bytes .= \chr(bindec($byte));
            }
        }

        return $bytes;
    }
}
