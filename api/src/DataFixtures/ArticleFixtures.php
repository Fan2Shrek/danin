<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Article;

final class ArticleFixtures extends AbstractFixtures implements TranslatableFixtureInterface
{
    public function getEntityClass(): string
    {
        return Article::class;
    }

    public function getData(): iterable
    {
        yield [
            'title' => 'Premier Article',
            'content' => 'Ceci est le contenu du premier article.',
        ];

        yield [
            'title' => 'Deuxième Article',
            'content' => 'Ceci est le contenu du deuxième article.',
        ];

        yield [
            'title' => 'Troisième Article',
            'content' => 'Ceci est le contenu du troisième article.',
        ];
    }

    public function getDataForLocale(string $locale): iterable
    {
        if ('en' !== $locale) {
            return [];
        }

        yield [
            'title' => 'First Article',
            'content' => 'This is the content of the first article.',
        ];

        yield [
            'title' => 'Second Article',
            'content' => 'This is the content of the second article.',
        ];

        yield [
            'title' => 'Third Article',
            'content' => 'This is the content of the third article.',
        ];
    }

    protected function postInstantiate(object $entity): void
    {
        $entity->setTranslatableLocale('fr');
    }
}
