<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AddTOTPSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
        ];
    }

    public function onAuthenticationSuccess($event): void
    {
        $user = $event->getUser();

        if (!$user instanceof User || !$user->hasTotp()) {
            return;
        }

        $data = $event->getData();
        $data['need_totp'] = true;

        $event->setData($data);
    }
}
