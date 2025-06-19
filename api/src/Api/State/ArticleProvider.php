<?php

declare(strict_types=1);

namespace App\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\ArticleRepository;

final class ArticleProvider implements ProviderInterface
{
    public function __construct(
        private ArticleRepository $articleRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->articleRepository->findOneBySlugForLocale($uriVariables['slug'], $context['request']->getLocale());
    }
}
