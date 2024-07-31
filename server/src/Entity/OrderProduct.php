<?php

namespace App\Entity;

use App\Repository\OrderProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OrderProductRepository::class)]
class OrderProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['orders'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orderProducts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Order should be present')]
    private ?Order $orderEntity = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Quanti should be present')]
    #[Groups(['orders'])]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'orderProducts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'VendorProduct should be present')]
    private ?VendorProduct $vendorProduct = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderEntity(): ?Order
    {
        return $this->orderEntity;
    }

    public function setOrderEntity(?Order $orderEntity): static
    {
        $this->orderEntity = $orderEntity;

        return $this;
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

    public function getVendorProduct(): ?VendorProduct
    {
        return $this->vendorProduct;
    }

    public function setVendorProduct(?VendorProduct $vendorProduct): static
    {
        $this->vendorProduct = $vendorProduct;

        return $this;
    }

    #[Groups(['orders'])]
    public function getVendorProductId(): ?int
    {
        return $this->getVendorProduct()->getId();
    }

    #[Groups(['orders'])]
    public function getPrice(): ?string
    {
        return $this->getVendorProduct()->getPrice();
    }

    #[Groups(['orders'])]
    public function getProductId(): ?int
    {
        return $this->getVendorProduct()->getProduct()->getId();
    }

    #[Groups(['orders'])]
    public function getProductImage(): ?string
    {
        return $this->getVendorProduct()->getProduct()->getImage();
    }

    #[Groups(['orders'])]
    public function getProductWeight(): ?string
    {
        return $this->getVendorProduct()->getProduct()->getWeight();
    }

    #[Groups(['orders'])]
    public function getProductTitle(): ?string
    {
        return $this->getVendorProduct()->getProduct()->getTitle();
    }
}
