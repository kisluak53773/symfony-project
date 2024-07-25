<?php

namespace App\Entity;

use App\Repository\VendorProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VendorProductRepository::class)]
#[ORM\HasLifecycleCallbacks]
class VendorProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product_list', 'vendor_products', 'elastica'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['product_list', 'vendor_products', 'elastica'])]
    #[Assert\NotBlank(message: 'Price should not be blank')]
    private ?string $price = null;

    #[ORM\Column(
        nullable: false,
        options: ["default" => 0]
    )]
    #[Groups(['product_list', 'vendor_products', 'elastica'])]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'vendorProducts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Vendor should be present')]
    private ?Vendor $vendor = null;

    #[ORM\ManyToOne(inversedBy: 'vendorProducts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vendor_products'])]
    #[Assert\NotBlank(message: 'Product should be present')]
    private ?Product $product = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

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

    public function getVendor(): ?Vendor
    {
        return $this->vendor;
    }

    public function setVendor(?Vendor $vendor): static
    {
        $this->vendor = $vendor;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    #[ORM\PrePersist]
    public function setDefaultQuantity(): void
    {
        if (!isset($this->quantity)) {
            $this->quantity = 0;
        }
    }

    #[Groups(['product_list', 'elastica'])]
    public function getVendorId(): ?int
    {
        return $this->vendor->getId();
    }
}
