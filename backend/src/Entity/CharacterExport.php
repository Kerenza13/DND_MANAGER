<?php

namespace App\Entity;

use App\Repository\CharacterExportRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CharacterExportRepository::class)]
class CharacterExport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'exports')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?CharacterSheet $character = null;

    #[ORM\Column(nullable: true)]
    private ?string $filePath = null;

    #[ORM\Column(length: 20)]
    private string $type = 'pdf';

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $snapshot = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // ---------------- GETTERS ----------------

    public function getId(): ?int { return $this->id; }

    public function getCharacter(): ?CharacterSheet
    {
        return $this->character;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSnapshot(): ?array
    {
        return $this->snapshot;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    // ---------------- SETTERS ----------------

    public function setCharacter(?CharacterSheet $character): self
    {
        $this->character = $character;
        return $this;
    }

    public function setFilePath(?string $filePath): self
    {
        $this->filePath = $filePath;
        return $this;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function setSnapshot(?array $snapshot): self
    {
        $this->snapshot = $snapshot;
        return $this;
    }
}
