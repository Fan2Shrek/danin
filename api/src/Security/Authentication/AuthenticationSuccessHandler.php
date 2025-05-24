<?php

declare(strict_types=1);

namespace App\Security\Authentication;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler as LexikAuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

final class AuthenticationSuccessHandler extends LexikAuthenticationSuccessHandler
{
    public function handleAuthenticationSuccess(UserInterface $user, $jwt = null): Response
    {
        if ($user instanceof User && $user->hasTotp()) {
            $jwt = $this->jwtManager->createFromPayload($user, [
                JwtAuthenticator::TOTP_CLAIM => true,
            ]);
        }

        return parent::handleAuthenticationSuccess($user, $jwt);
    }
}
