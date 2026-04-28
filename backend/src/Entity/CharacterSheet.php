<?php

namespace App\Entity;

use App\Repository\CharacterSheetRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: CharacterSheetRepository::class)]
#[ORM\Table(name: "character_sheets")]
class CharacterSheet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'characterSheets')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(length: 100)]
    private string $name;

    #[ORM\ManyToOne(inversedBy: 'characters')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Race $race = null;

    #[ORM\ManyToOne(inversedBy: 'characters')]
    #[ORM\JoinColumn(name: "class_id", referencedColumnName: "id", onDelete: 'SET NULL')]
    private ?Classes $gameClass = null;

    #[ORM\Column]
    private int $level = 1;

    #[ORM\Column(nullable: true)]
    private ?int $experience = 0;

    #[ORM\Column(type: 'json')]
    private array $stats = [];

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $inventory = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $items = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $classSnapshot = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $raceSnapshot = null;

    #[ORM\Column(nullable: true)]
    private ?string $avatarUrl = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    #[ORM\OneToMany(mappedBy: 'character', targetEntity: CharacterExport::class)]
    private Collection $exports;

    public function __construct()
    {
        $this->exports = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    // ---------------- GETTERS ----------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getRace(): ?Race
    {
        return $this->race;
    }
    public function getGameClass(): ?Classes
    {
        return $this->gameClass;
    }
    public function getLevel(): int
    {
        return $this->level;
    }
    public function getExperience(): ?int
    {
        return $this->experience;
    }
    public function getStats(): array
    {
        return $this->stats;
    }
    public function getInventory(): ?array
    {
        return $this->inventory;
    }
    public function getItems(): ?array
    {
        return $this->items;
    }
    public function getClassSnapshot(): ?array
    {
        return $this->classSnapshot;
    }
    public function getRaceSnapshot(): ?array
    {
        return $this->raceSnapshot;
    }
    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }
    public function getNotes(): ?string
    {
        return $this->notes;
    }
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
    public function getExports(): Collection
    {
        return $this->exports;
    }

    // ---------------- SETTERS ----------------

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function setRace(?Race $race): self
    {
        $this->race = $race;
        return $this;
    }
    public function setGameClass(?Classes $gameClass): self
    {
        $this->gameClass = $gameClass;
        return $this;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function setExperience(?int $experience): self
    {
        $this->experience = $experience;
        return $this;
    }

    public function setStats(array $stats): self
    {
        $this->stats = $stats;
        return $this;
    }

    public function setInventory(?array $inventory): self
    {
        $this->inventory = $inventory;
        return $this;
    }

    public function setItems(?array $items): self
    {
        $this->items = $items;
        return $this;
    }

    public function setClassSnapshot(?array $classSnapshot): self
    {
        $this->classSnapshot = $classSnapshot;
        return $this;
    }

    public function setRaceSnapshot(?array $raceSnapshot): self
    {
        $this->raceSnapshot = $raceSnapshot;
        return $this;
    }

    public function setAvatarUrl(?string $avatarUrl): self
    {
        $this->avatarUrl = $avatarUrl;
        return $this;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

    // ---------------- RELATION HELPERS ----------------

    public function addExport(CharacterExport $export): self
    {
        if (!$this->exports->contains($export)) {
            $this->exports[] = $export;
            $export->setCharacter($this);
        }
        return $this;
    }

    public function removeExport(CharacterExport $export): self
    {
        if ($this->exports->removeElement($export)) {
            if ($export->getCharacter() === $this) {
                $export->setCharacter(null);
            }
        }
        return $this;
    }
}
