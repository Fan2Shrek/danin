<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Repository\ArticleListRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ApiResource(operations: [
    new Get(
        uriTemplate: 'articles_list',
    ),
])]
#[ORM\Entity(repositoryClass: ArticleListRepository::class)]
class ArticleList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Article $suggestGame = null;

    public function getId(): int
    {
        return $this->id;
    }

    #[Ignore]
    public function getSuggestGame(): ?Article
    {
        return $this->suggestGame;
    }

    public function getSuggestGameArticleSlug(): ?string
    {
        return $this->suggestGame?->getSlug();
    }

    public function setSuggestGame(Article $suggestGame): static
    {
        $this->suggestGame = $suggestGame;

        return $this;
    }
}
