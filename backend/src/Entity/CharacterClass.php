<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class CharacterClass
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $hitDie = null;

    #[ORM\Column(length: 50)]
    private ?string $primaryAbility = null;

    #[ORM\Column(nullable: true)]
    private ?array $savingThrow = null;

    #[ORM\Column(nullable: true)]
    private ?array $features = null;

    #[ORM\OneToMany(mappedBy: 'characterClass', targetEntity: Character::class)]
    private Collection $characters;

    public function __construct()
    {
        $this->characters = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getHitDie(): ?int
    {
        return $this->hitDie;
    }

    public function setHitDie(int $hitDie): static
    {
        $this->hitDie = $hitDie;
        return $this;
    }

    public function getPrimaryAbility(): ?string
    {
        return $this->primaryAbility;
    }

    public function setPrimaryAbility(string $primaryAbility): static
    {
        $this->primaryAbility = $primaryAbility;
        return $this;
    }

    public function getSavingThrow(): ?array
    {
        return $this->savingThrow;
    }

    public function setSavingThrow(?array $savingThrow): static
    {
        $this->savingThrow = $savingThrow;
        return $this;
    }

    public function getFeatures(): ?array
    {
        return $this->features;
    }

    public function setFeatures(?array $features): static
    {
        $this->features = $features;
        return $this;
    }

    public function getCharacters(): Collection
    {
        return $this->characters;
    }
}
