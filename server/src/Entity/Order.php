<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use DateTime;
use DateTimeImmutable;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
#[ORM\HasLifecycleCallbacks]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['orders'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $customer = null;

    /**
     * @var Collection<int, OrderProduct>
     */
    #[ORM\OneToMany(targetEntity: OrderProduct::class, mappedBy: 'orderEntity')]
    #[Groups(['order_product'])]
    private Collection $orderProducts;

    #[ORM\Column(length: 20)]
    #[Groups(['orders'])]
    private ?string $paymentMethod = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['orders'])]
    private ?\DateTimeInterface $deliveryTime = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['orders'])]
    private ?string $comment = null;

    #[ORM\Column(length: 20)]
    #[Groups(['orders'])]
    private ?string $orderStatus = null;

    #[ORM\Column]
    #[Groups(['orders'])]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['orders'])]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->orderProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Collection<int, OrderProduct>
     */
    public function getOrderProducts(): Collection
    {
        return $this->orderProducts;
    }

    public function addOrderProduct(OrderProduct $orderProduct): static
    {
        if (!$this->orderProducts->contains($orderProduct)) {
            $this->orderProducts->add($orderProduct);
            $orderProduct->setOrderEntity($this);
        }

        return $this;
    }

    public function removeOrderProduct(OrderProduct $orderProduct): static
    {
        if ($this->orderProducts->removeElement($orderProduct)) {
            // set the owning side to null (unless already changed)
            if ($orderProduct->getOrderEntity() === $this) {
                $orderProduct->setOrderEntity(null);
            }
        }

        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getDeliveryTime(): ?\DateTimeInterface
    {
        return $this->deliveryTime;
    }

    public function setDeliveryTime(\DateTimeInterface $deliveryTime): static
    {
        $this->deliveryTime = $deliveryTime;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getOrderStatus(): ?string
    {
        return $this->orderStatus;
    }

    public function setOrderStatus(string $orderStatus): static
    {
        $this->orderStatus = $orderStatus;

        return $this;
    }

    #[Groups(['orders_admin'])]
    public function getCustomerPhone(): ?string
    {
        return $this->getCustomer()?->getPhone();
    }

    #[Groups(['orders_admin'])]
    public function getCustomerId(): ?int
    {
        return $this->getCustomer()?->getId();
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new DateTimeImmutable('now');
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new DateTime('now');
    }

    #[Groups(['orders'])]
    public function getTotalPrice(): ?float
    {
        $products = $this->getOrderProducts()->getValues();

        return array_reduce($products, function ($sum, $item) {
            $quantity = $item->getQuantity() ?? 0;

            $price = is_numeric($item->getVendorProduct()?->getPrice()) ? (float) $item->getVendorProduct()->getPrice() : 0.0;

            return $sum + $quantity * $price;
        }, 0.0);
    }

    #[Groups(['orders'])]
    public function getDeliveryAddress(): ?string
    {
        return $this->getCustomer()?->getAddress();
    }
}
