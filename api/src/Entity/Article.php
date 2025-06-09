<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Get;
use App\Repository\ArticleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ApiProperty(operations: [
    new Get(
        uriTemplate: '/articles/{title}',
    ),
])]
#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    #[Gedmo\Translatable]
    private string $title;

    #[ORM\Column(type: Types::TEXT)]
    #[Gedmo\Translatable]
    private string $content;

    #[Gedmo\Locale]
    private ?string $locale = null;

    public function __construct(string $title, string $content = '')
    {
        $this->title = $title;
        $this->content = $content;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function setTranslatableLocale(string $locale)
    {
        $this->locale = $locale;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): static
    {
        $this->setTranslatableLocale($locale);

        return $this;
    }
}
