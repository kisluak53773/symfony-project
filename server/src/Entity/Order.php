<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Constants\PaymnetConstants;
use App\Constants\OrderConstatns;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['orders'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Customer should be present')]
    private ?User $customer = null;

    /**
     * @var Collection<int, OrderProduct>
     */
    #[ORM\OneToMany(targetEntity: OrderProduct::class, mappedBy: 'orderEntity')]
    #[Groups(['orders'])]
    private Collection $orderProducts;

    #[ORM\Column(length: 20)]
    #[Assert\Choice([PaymnetConstants::PAYMENT_CASH, PaymnetConstants::PAYMENT_CARD])]
    #[Groups(['orders'])]
    private ?string $paymentMethod = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['orders'])]
    private ?\DateTimeInterface $deliveryTime = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Comment should not be so long',
    )]
    #[Groups(['orders'])]
    private ?string $comment = null;

    #[ORM\Column(length: 20)]
    #[Groups(['orders'])]
    #[Assert\Choice([
        OrderConstatns::ORDER_PROCESSED,
        OrderConstatns::ORDER_ON_THE_WAY,
        OrderConstatns::ORDER_DELIVERED,
        OrderConstatns::ORDER_CANCELED
    ])]
    private ?string $orderStatus = null;

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
        return $this->getCustomer()->getPhone();
    }

    #[Groups(['orders_admin'])]
    public function getCustomerId(): ?string
    {
        return $this->getCustomer()->getId();
    }
}
