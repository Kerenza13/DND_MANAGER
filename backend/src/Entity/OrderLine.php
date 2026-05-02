<?php

namespace App\Entity;
// Product
use App\Repository\OrderLineRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderLineRepository::class)]
class OrderLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\ManyToOne(inversedBy: 'orderLines')]
    private ?Order $orderRef = null;

    #[ORM\ManyToOne(inversedBy: 'orderLinesRef')]
    #[ORM\JoinColumn(nullable: false)]
    private ?order $orderRelation = null;

    #[ORM\ManyToOne(inversedBy: 'orderLines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $Product = null;

    #[ORM\ManyToOne(inversedBy: 'orderLinesRelation')]
    private ?Product $ProductRef = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

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

    public function getOrderRef(): ?Order
    {
        return $this->orderRef;
    }

    public function setOrderRef(?Order $orderRef): static
    {
        $this->orderRef = $orderRef;

        return $this;
    }

    public function getOrderRelation(): ?order
    {
        return $this->orderRelation;
    }

    public function setOrderRelation(?order $orderRelation): static
    {
        $this->orderRelation = $orderRelation;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->Product;
    }

    public function setProduct(?Product $Product): static
    {
        $this->Product = $Product;

        return $this;
    }

    public function getProductRef(): ?Product
    {
        return $this->ProductRef;
    }

    public function setProductRef(?Product $ProductRef): static
    {
        $this->ProductRef = $ProductRef;

        return $this;
    }
}
