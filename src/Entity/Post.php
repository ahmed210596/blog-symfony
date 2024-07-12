<?php

namespace App\Entity;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use  ApiPlatform\Metadata\Put;

use ApiPlatform\Metadata\Delete;

#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => 'post:item']),
        new GetCollection(normalizationContext: ['groups' => 'post:list']),
        new Put(normalizationContext: ['groups' => 'post:update']),
        new Delete(),
    ],
    order: ['createdAt' => 'DESC', 'title' => 'ASC'],
    paginationEnabled: false,
)]
#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['post:list', 'post:item', 'post:create', 'post:update'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['post:list', 'post:item', 'post:create', 'post:update'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(['post:list', 'post:item', 'post:create', 'post:update'])]
    private ?string $content = null;

    #[ORM\Column]
    #[Groups(['post:list', 'post:item', 'post:create', 'post:update'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['post:list', 'post:item', 'post:create', 'post:update'])]
    private ?Category $category = null;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}