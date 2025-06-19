<?php

namespace App\Tests\Functional\Articles;

use App\Entity\Article;
use App\Tests\Functional\FunctionalTestCase;

class GetArticleTest extends FunctionalTestCase
{
    protected const URI = '/api/articles/%s';

    public function testGetArticle(): void
    {
        $article = new Article(
            'Test Article',
            'This is a test article content.'
        );
        $article->setSlug('test-article');
        $this->getEM()->persist($article);
        $this->getEM()->flush();
        $this->client->request('GET', \sprintf(self::URI, 'test-article'));

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'title' => 'Test Article',
            'slug' => 'test-article',
            'content' => 'This is a test article content.',
        ]);
    }

    public function testGetNonExistentArticle(): void
    {
        $this->client->request('GET', '/articles/non-existent-article');

        $this->assertResponseStatusCodeSame(404);
    }
}
