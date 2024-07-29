<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product_list', 'vendor_products', 'vendor_does_not_sell', 'elastica'])]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    #[Groups(['product_list', 'vendor_products', 'vendor_does_not_sell', 'elastica'])]
    #[Assert\Length(
        min: 2,
        max: 40,
        minMessage: 'Title must not be so short',
        maxMessage: 'Title should not be so long',
    )]
    private ?string $title = null;

    #[ORM\Column(length: 1000)]
    #[Groups(['product_list', 'vendor_products', 'vendor_does_not_sell', 'elastica'])]
    #[Assert\Length(
        min: 1,
        max: 1000,
        minMessage: 'Description must not be so short',
        maxMessage: 'Description should not be so long',
    )]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product_list', 'vendor_products', 'vendor_does_not_sell', 'elastica'])]
    #[Assert\Length(
        min: 1,
        max: 1000,
        minMessage: 'Compound must not be so short',
        maxMessage: 'Compound should not be so long',
    )]
    private ?string $compound = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product_list', 'vendor_products', 'vendor_does_not_sell', 'elastica'])]
    #[Assert\Length(
        min: 1,
        max: 255,
        minMessage: 'Storage conditions must not be so short',
        maxMessage: 'Storage conditions should not be so long',
    )]
    private ?string $storageConditions = null;

    #[ORM\Column(length: 40)]
    #[Groups(['product_list', 'vendor_products', 'vendor_does_not_sell', 'elastica'])]
    #[Assert\Length(
        min: 1,
        max: 40,
        minMessage: 'Weight conditions must not be so short',
        maxMessage: 'Weightshould not be so long',
    )]
    private ?string $weight = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product_list', 'vendor_products', 'vendor_does_not_sell', 'elastica'])]
    #[Assert\NotBlank]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vendor_does_not_sell'])]
    #[Assert\NotBlank]
    private ?Producer $producer = null;

    /**
     * @var Collection<int, VendorProduct>
     */
    #[ORM\OneToMany(targetEntity: VendorProduct::class, mappedBy: 'product', orphanRemoval: true)]
    #[Groups(['product_list', 'elastica'])]
    private Collection $vendorProducts;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vendor_does_not_sell'])]
    #[Assert\NotBlank]
    private ?Type $type = null;

    /**
     * @var Collection<int, CartProduct>
     */
    #[ORM\OneToMany(targetEntity: CartProduct::class, mappedBy: 'product', orphanRemoval: true)]
    private Collection $cartProducts;

    /**
     * @var Collection<int, OrderProduct>
     */
    #[ORM\OneToMany(targetEntity: OrderProduct::class, mappedBy: 'product', orphanRemoval: true)]
    private Collection $orderProducts;

    public function __construct()
    {
        $this->vendorProducts = new ArrayCollection();
        $this->cartProducts = new ArrayCollection();
        $this->orderProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCompound(): ?string
    {
        return $this->compound;
    }

    public function setCompound(string $compound): static
    {
        $this->compound = $compound;

        return $this;
    }

    public function getStorageConditions(): ?string
    {
        return $this->storageConditions;
    }

    public function setStorageConditions(string $storageConditions): static
    {
        $this->storageConditions = $storageConditions;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getProducer(): ?Producer
    {
        return $this->producer;
    }

    public function setProducer(?Producer $producer): static
    {
        $this->producer = $producer;

        return $this;
    }

    /**
     * @return Collection<int, VendorProduct>
     */
    public function getVendorProducts(): Collection
    {
        return $this->vendorProducts;
    }

    public function addVendorProduct(VendorProduct $vendorProduct): static
    {
        if (!$this->vendorProducts->contains($vendorProduct)) {
            $this->vendorProducts->add($vendorProduct);
            $vendorProduct->setProduct($this);
        }

        return $this;
    }

    public function removeVendorProduct(VendorProduct $vendorProduct): static
    {
        if ($this->vendorProducts->removeElement($vendorProduct)) {
            // set the owning side to null (unless already changed)
            if ($vendorProduct->getProduct() === $this) {
                $vendorProduct->setProduct(null);
            }
        }

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): static
    {
        $this->type = $type;

        return $this;
    }

    #[Groups(['product_list', 'vendor_products', 'elastica'])]
    public function getTypeId(): ?int
    {
        return $this->type->getId();
    }

    #[Groups(['product_list', 'vendor_products', 'elastica'])]
    public function getProducerId(): ?int
    {
        return $this->producer->getId();
    }

    /**
     * @return Collection<int, CartProduct>
     */
    public function getCartProducts(): Collection
    {
        return $this->cartProducts;
    }

    public function addCartProduct(CartProduct $cartProduct): static
    {
        if (!$this->cartProducts->contains($cartProduct)) {
            $this->cartProducts->add($cartProduct);
            $cartProduct->setProduct($this);
        }

        return $this;
    }

    public function removeCartProduct(CartProduct $cartProduct): static
    {
        if ($this->cartProducts->removeElement($cartProduct)) {
            // set the owning side to null (unless already changed)
            if ($cartProduct->getProduct() === $this) {
                $cartProduct->setProduct(null);
            }
        }

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
            $orderProduct->setProduct($this);
        }

        return $this;
    }

    public function removeOrderProduct(OrderProduct $orderProduct): static
    {
        if ($this->orderProducts->removeElement($orderProduct)) {
            // set the owning side to null (unless already changed)
            if ($orderProduct->getProduct() === $this) {
                $orderProduct->setProduct(null);
            }
        }

        return $this;
    }
}
