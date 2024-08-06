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
    #[Groups(['order_product', 'vendor_order'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orderProducts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Order should be present')]
    private ?Order $orderEntity = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Quanti should be present')]
    #[Groups(['order_product', 'vendor_order'])]
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

    #[Groups(['order_product', 'vendor_order'])]
    public function getVendorProductId(): ?int
    {
        return $this->getVendorProduct()->getId();
    }

    #[Groups(['order_product', 'vendor_order'])]
    public function getPrice(): ?string
    {
        return $this->getVendorProduct()->getPrice();
    }

    #[Groups(['order_product', 'vendor_order'])]
    public function getProductId(): ?int
    {
        return $this->getVendorProduct()->getProduct()->getId();
    }

    #[Groups(['order_product', 'vendor_order'])]
    public function getProductImage(): ?string
    {
        return $this->getVendorProduct()->getProduct()->getImage();
    }

    #[Groups(['order_product', 'vendor_order'])]
    public function getProductWeight(): ?string
    {
        return $this->getVendorProduct()->getProduct()->getWeight();
    }

    #[Groups(['order_product', 'vendor_order'])]
    public function getProductTitle(): ?string
    {
        return $this->getVendorProduct()->getProduct()->getTitle();
    }
}
