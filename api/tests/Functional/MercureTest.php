<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Functional\User\UserTrait;

final class MercureTest extends FunctionalTestCase
{
    use UserTrait;

    protected const URI = '/api/mercure';

    public function testMercure(): void
    {
        $user = $this->createUser();
        $this->client->loginUser($user);

        $response = $this->client->request('POST', static::URI, [
            'json' => [
                'topics' => ['test'],
            ],
        ]);

        $this->assertResponseIsSuccessful();
        self::assertArrayHasKey('set-cookie', $response->getHeaders(false));
        self::assertMatchesRegularExpression(
            '/^mercureAuthorization=.+; .+/',
            $response->getHeaders(false)['set-cookie'][0]
        );
    }
}
