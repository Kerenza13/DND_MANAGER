<?php

namespace App\Entity;

use App\Repository\RestaurantTableRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RestaurantTableRepository::class)]
class RestaurantTable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $number = null;

    #[ORM\Column]
    private ?int $capacity = null;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'RestaurantTable')]
    private Collection $orders;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'restaurantTable')]
    private Collection $OrderRelation;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->OrderRelation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setRestaurantTable($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getRestaurantTable() === $this) {
                $order->setRestaurantTable(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrderRelation(): Collection
    {
        return $this->OrderRelation;
    }

    public function addOrderRelation(Order $orderRelation): static
    {
        if (!$this->OrderRelation->contains($orderRelation)) {
            $this->OrderRelation->add($orderRelation);
            $orderRelation->setRestaurantTable($this);
        }

        return $this;
    }

    public function removeOrderRelation(Order $orderRelation): static
    {
        if ($this->OrderRelation->removeElement($orderRelation)) {
            // set the owning side to null (unless already changed)
            if ($orderRelation->getRestaurantTable() === $this) {
                $orderRelation->setRestaurantTable(null);
            }
        }

        return $this;
    }
}
