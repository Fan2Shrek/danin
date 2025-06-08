<?php

declare(strict_types=1);

namespace App\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final class AddLocaleSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => ['onKernelRequest', 150],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->attributes->has('_locale')) {
            return;
        }

        $locale ??= $request->headers->has('Accept-Language')
            ? $request->getPreferredLanguage()
            : $request->getLocale()
        ;

        if ($locale) {
            $request->attributes->set('_locale', $locale);
        }
    }
}
