<?php

declare(strict_types=1);

namespace App\Security\Authentication;

use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\JWTAuthenticator as LexikJWTAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

final class JwtAuthenticator extends LexikJWTAuthenticator
{
    public const string TOTP_CLAIM = 'TOTP_REQUIRED';

    public function authenticate(Request $request): Passport
    {
        $token = $this->getTokenExtractor()->extract($request);

        try {
            if (!$payload = $this->getJwtManager()->parse($token)) {
                throw new InvalidTokenException('Invalid JWT Token');
            }
        } catch (JWTDecodeFailureException $e) {
        }

        if (isset($payload[self::TOTP_CLAIM]) && true === $payload[self::TOTP_CLAIM]) {
            throw new InvalidTokenException('TOTP authentication is required for this user.');
        }

        return parent::authenticate($request);
    }
}
