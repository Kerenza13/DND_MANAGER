<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class CharacterExport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $filePath = null;

    #[ORM\ManyToOne(inversedBy: 'characterExports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Character $charRelation = null;

    #[ORM\ManyToOne(inversedBy: 'characterExports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // --- Getters and Setters ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): static
    {
        $this->filePath = $filePath;
        return $this;
    }

    public function getCharRelation(): ?Character
    {
        return $this->charRelation;
    }

    public function setCharRelation(?Character $charRelation): static
    {
        $this->charRelation = $charRelation;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }
}