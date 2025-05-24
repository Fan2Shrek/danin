<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

final class TOTP
{
    public function __construct(
        private readonly string $secret,
        private readonly int $timeStep,
        private readonly UserInterface $user,
    ) {
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function getUsername(): string
    {
        return $this->user->getUserIdentifier();
    }

    public function getTOTPCode()
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
            dump($this->getTOTPCode($currentSlice + $i), $currentSlice + $i);
            if ($this->getTOTPCode($currentSlice + $i) === $input) {
                return true;
            }
        }

        return false;
    }

    private function base32Decode(string $b32): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $b32 = strtoupper($b32);
        $binaryString = '';

        foreach (str_split($b32) as $char) {
            $val = strpos($alphabet, $char);
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
