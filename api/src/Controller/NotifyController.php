<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class NotifyController extends AbstractController
{
    public function __invoke(Request $r): JsonResponse
    {
        $payload = $r->getPayload();

        var_dump($payload);

        return new JsonResponse([
            'status' => 'ok',
        ]);
    }
}
