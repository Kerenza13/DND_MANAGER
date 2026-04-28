<?php

namespace App\Entity;

use App\Repository\ClassesRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: ClassesRepository::class)]
#[ORM\Table(name: "classes")]
class Classes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $hitDie = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $primaryAbility = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $savingThrows = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $features = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    #[ORM\OneToMany(mappedBy: 'gameClass', targetEntity: CharacterSheet::class)]
    private Collection $characters;

    public function __construct()
    {
        $this->characters = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getHitDie(): ?int { return $this->hitDie; }
    public function setHitDie(?int $hitDie): self { $this->hitDie = $hitDie; return $this; }

    public function getPrimaryAbility(): ?string { return $this->primaryAbility; }
    public function setPrimaryAbility(?string $primaryAbility): self { $this->primaryAbility = $primaryAbility; return $this; }

    public function getSavingThrows(): ?array { return $this->savingThrows; }
    public function setSavingThrows(?array $savingThrows): self { $this->savingThrows = $savingThrows; return $this; }

    public function getFeatures(): ?array { return $this->features; }
    public function setFeatures(?array $features): self { $this->features = $features; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    public function getCharacters(): Collection { return $this->characters; }

    public function addCharacter(CharacterSheet $character): self
    {
        if (!$this->characters->contains($character)) {
            $this->characters[] = $character;
            $character->setGameClass($this);
        }
        return $this;
    }

    public function removeCharacter(CharacterSheet $character): self
    {
        if ($this->characters->removeElement($character)) {
            if ($character->getGameClass() === $this) {
                $character->setGameClass(null);
            }
        }
        return $this;
    }
}
