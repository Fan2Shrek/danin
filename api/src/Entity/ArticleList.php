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

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Article $setupGame = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Article $setupTchat = null;

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

	#[Ignore]
    public function getSetupGame(): ?Article
    {
        return $this->setupGame;
    }

    public function getSetupGameArticleSlug(): ?string
    {
        return $this->setupGame?->getSlug();
    }

    public function setSetupGame(Article $setupGame): static
    {
        $this->setupGame = $setupGame;

        return $this;
    }

	#[Ignore]
	public function getSetupTchat(): ?Article
	{
		return $this->setupTchat;
	}

	public function getSetupTchatArticleSlug(): ?string
	{
		return $this->setupTchat?->getSlug();
	}

	public function setSetupTchat(Article $setupTchat): static
	{
		$this->setupTchat = $setupTchat;

		return $this;
	}
}
