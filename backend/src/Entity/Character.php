<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '`character`')]
class Character
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $experience = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $background = null;

    #[ORM\Column(length: 50)]
    private ?string $alignment = null;

    #[ORM\Column]
    private array $stats = [];

    #[ORM\Column(nullable: true)]
    private ?array $inventory = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $lore = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\ManyToOne(inversedBy: 'characters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'characters')]
    private ?Race $race = null;

    #[ORM\ManyToOne(inversedBy: 'characters')]
    private ?CharacterClass $characterClass = null;

    #[ORM\OneToMany(mappedBy: 'charRelation', targetEntity: CharacterExport::class, orphanRemoval: true)]
    private Collection $characterExports;

    public function __construct()
    {
        $this->characterExports = new ArrayCollection();
    }

    // --- Getters and Setters ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getExperience(): ?int
    {
        return $this->experience;
    }

    public function setExperience(int $experience): static
    {
        $this->experience = $experience;
        return $this;
    }

    public function getBackground(): ?string
    {
        return $this->background;
    }

    public function setBackground(?string $background): static
    {
        $this->background = $background;
        return $this;
    }

    public function getAlignment(): ?string
    {
        return $this->alignment;
    }

    public function setAlignment(string $alignment): static
    {
        $this->alignment = $alignment;
        return $this;
    }

    public function getStats(): array
    {
        return $this->stats;
    }

    public function setStats(array $stats): static
    {
        $this->stats = $stats;
        return $this;
    }

    public function getInventory(): ?array
    {
        return $this->inventory;
    }

    public function setInventory(?array $inventory): static
    {
        $this->inventory = $inventory;
        return $this;
    }

    public function getLore(): ?string
    {
        return $this->lore;
    }

    public function setLore(?string $lore): static
    {
        $this->lore = $lore;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
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

    public function getRace(): ?Race
    {
        return $this->race;
    }

    public function setRace(?Race $race): static
    {
        $this->race = $race;
        return $this;
    }

    public function getCharacterClass(): ?CharacterClass
    {
        return $this->characterClass;
    }

    public function setCharacterClass(?CharacterClass $characterClass): static
    {
        $this->characterClass = $characterClass;
        return $this;
    }

    public function getCharacterExports(): Collection
    {
        return $this->characterExports;
    }
}