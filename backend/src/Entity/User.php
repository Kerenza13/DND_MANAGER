<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private array $role = [];

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Character::class, orphanRemoval: true)]
    private Collection $characters;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: CharacterExport::class, orphanRemoval: true)]
    private Collection $characterExports;

    public function __construct()
    {
        $this->characters = new ArrayCollection();
        $this->characterExports = new ArrayCollection();
    }

    // --- Getters and Setters ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getRole(): array
    {
        return $this->role;
    }

    public function setRole(array $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getCharacters(): Collection
    {
        return $this->characters;
    }

    public function getCharacterExports(): Collection
    {
        return $this->characterExports;
    }
}