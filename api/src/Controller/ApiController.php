<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\Model\Connection;
use App\Service\Transport\GameTransportInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * This is a class test that help send request to the game server
 * todo: remove this class when the game server is ready.
 */
final class ApiController extends AbstractController
{
    #[Route('/api/msg', name: 'api')]
    public function sendRequest(GameTransportInterface $gameTransport): Response
    {
        $connection = new Connection('172.17.0.1', 12345);
        $gameTransport->send($connection, json_encode([
            'type' => 'spawn',
            'content' => 4,
        ]));

        return new JsonResponse([
            'status' => 'ok',
        ]);
    }
}
