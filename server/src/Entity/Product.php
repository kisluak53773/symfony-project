<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product_list'])]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    #[Groups(['product_list'])]
    private ?string $title = null;

    #[ORM\Column(length: 1000)]
    #[Groups(['product_list'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product_list'])]
    private ?string $compound = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product_list'])]
    private ?string $storageConditions = null;

    #[ORM\Column(length: 40)]
    #[Groups(['product_list'])]
    private ?string $weight = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product_list'])]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['product_list'])]
    private ?Producer $producer = null;

    /**
     * @var Collection<int, VendorProduct>
     */
    #[ORM\OneToMany(targetEntity: VendorProduct::class, mappedBy: 'product', orphanRemoval: true)]
    private Collection $vendorProducts;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Type $type = null;

    public function __construct()
    {
        $this->vendorProducts = new ArrayCollection();
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
}
