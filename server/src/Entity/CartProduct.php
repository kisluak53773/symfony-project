<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CartProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CartProductRepository::class)]
class CartProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['cart_product'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'cartProducts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cart $cart = null;

    #[ORM\Column]
    #[Groups(['cart_product'])]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'cartProducts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?VendorProduct $vendorProduct = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): static
    {
        $this->cart = $cart;

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

    public function increaseQuantity(int $quantity): void
    {
        $this->quantity += $quantity;
    }

    public function decreaseQuantity(int $quantity): void
    {
        $this->quantity -= $quantity;
    }

    #[Groups(['cart_product'])]
    public function getVendorProductId(): ?int
    {
        return $this->getVendorProduct()->getId();
    }

    #[Groups(['cart_product'])]
    public function getPrice(): ?string
    {
        return $this->getVendorProduct()->getPrice();
    }

    #[Groups(['cart_product'])]
    public function getProductId(): ?int
    {
        return $this->getVendorProduct()->getProduct()->getId();
    }

    #[Groups(['cart_product'])]
    public function getProductImage(): ?string
    {
        return $this->getVendorProduct()->getProduct()->getImage();
    }

    #[Groups(['cart_product'])]
    public function getProductWeight(): ?string
    {
        return $this->getVendorProduct()->getProduct()->getWeight();
    }

    #[Groups(['cart_product'])]
    public function getProductTitle(): ?string
    {
        return $this->getVendorProduct()->getProduct()->getTitle();
    }

    #[Groups(['cart_product'])]
    public function getInStock(): ?int
    {
        return $this->getVendorProduct()->getQuantity();
    }
}
