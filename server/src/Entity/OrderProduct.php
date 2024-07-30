<?php

namespace App\Entity;

use App\Repository\OrderProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderProductRepository::class)]
class OrderProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orderProducts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Order should be present')]
    private ?Order $orderEntity = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Quanti should be present')]
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
}
