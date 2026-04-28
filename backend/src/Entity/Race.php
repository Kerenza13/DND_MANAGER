<?php

namespace App\Entity;

use App\Repository\RaceRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: RaceRepository::class)]
class Race
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $statBonuses = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $traits = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    #[ORM\OneToMany(mappedBy: 'race', targetEntity: CharacterSheet::class)]
    private Collection $characters;

    public function __construct()
    {
        $this->characters = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    // ---------------- GETTERS ----------------

    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }

    public function getDescription(): ?string { return $this->description; }

    public function getStatBonuses(): ?array { return $this->statBonuses; }

    public function getTraits(): ?array { return $this->traits; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    public function getCharacters(): Collection { return $this->characters; }

    // ---------------- SETTERS ----------------

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setStatBonuses(?array $statBonuses): self
    {
        $this->statBonuses = $statBonuses;
        return $this;
    }

    public function setTraits(?array $traits): self
    {
        $this->traits = $traits;
        return $this;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    // ---------------- RELATION HELPERS ----------------

    public function addCharacter(CharacterSheet $character): self
    {
        if (!$this->characters->contains($character)) {
            $this->characters[] = $character;
            $character->setRace($this);
        }

        return $this;
    }

    public function removeCharacter(CharacterSheet $character): self
    {
        if ($this->characters->removeElement($character)) {
            if ($character->getRace() === $this) {
                $character->setRace(null);
            }
        }

        return $this;
    }
}
