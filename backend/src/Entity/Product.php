<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column]
    private ?bool $isAvalible = null;

    /**
     * @var Collection<int, OrderLine>
     */
    #[ORM\OneToMany(targetEntity: OrderLine::class, mappedBy: 'product')]
    private Collection $orderLines;

    /**
     * @var Collection<int, orderLine>
     */
    #[ORM\OneToMany(targetEntity: orderLine::class, mappedBy: 'productRef')]
    private Collection $orderLinesRelation;

    public function __construct()
    {
        $this->orderLines = new ArrayCollection();
        $this->orderLinesRelation = new ArrayCollection();
    }

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function isAvalible(): ?bool
    {
        return $this->isAvalible;
    }

    public function setIsAvalible(bool $isAvalible): static
    {
        $this->isAvalible = $isAvalible;

        return $this;
    }

    /**
     * @return Collection<int, OrderLine>
     */
    public function getOrderLines(): Collection
    {
        return $this->orderLines;
    }

    public function addOrderLine(OrderLine $orderLine): static
    {
        if (!$this->orderLines->contains($orderLine)) {
            $this->orderLines->add($orderLine);
            $orderLine->setProduct($this);
        }

        return $this;
    }

    public function removeOrderLine(OrderLine $orderLine): static
    {
        if ($this->orderLines->removeElement($orderLine)) {
            // set the owning side to null (unless already changed)
            if ($orderLine->getProduct() === $this) {
                $orderLine->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, orderLine>
     */
    public function getOrderLinesRelation(): Collection
    {
        return $this->orderLinesRelation;
    }

    public function addOrderLinesRelation(orderLine $orderLinesRelation): static
    {
        if (!$this->orderLinesRelation->contains($orderLinesRelation)) {
            $this->orderLinesRelation->add($orderLinesRelation);
            $orderLinesRelation->setProductRef($this);
        }

        return $this;
    }

    public function removeOrderLinesRelation(orderLine $orderLinesRelation): static
    {
        if ($this->orderLinesRelation->removeElement($orderLinesRelation)) {
            // set the owning side to null (unless already changed)
            if ($orderLinesRelation->getProductRef() === $this) {
                $orderLinesRelation->setProductRef(null);
            }
        }

        return $this;
    }
}
