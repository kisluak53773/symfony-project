<?php

declare(strict_types=1);

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
    #[Groups(['product_list', 'vendor_products', 'vendor_does_not_sell', 'elastica'])]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    #[Groups(['product_list', 'vendor_products', 'vendor_does_not_sell', 'elastica'])]
    private ?string $title = null;

    #[ORM\Column(length: 1000)]
    #[Groups(['product_list', 'vendor_products', 'vendor_does_not_sell', 'elastica'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product_list', 'vendor_products', 'vendor_does_not_sell', 'elastica'])]
    private ?string $compound = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product_list', 'vendor_products', 'vendor_does_not_sell', 'elastica'])]
    private ?string $storageConditions = null;

    #[ORM\Column(length: 40)]
    #[Groups(['product_list', 'vendor_products', 'vendor_does_not_sell', 'elastica'])]
    private ?string $weight = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product_list', 'vendor_products', 'vendor_does_not_sell', 'elastica'])]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vendor_does_not_sell'])]
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
    private ?Type $type = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'favorite')]
    private Collection $users;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'product', orphanRemoval: true)]
    private Collection $reviews;

    public function __construct()
    {
        $this->vendorProducts = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->reviews = new ArrayCollection();
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
        return $this->type?->getId();
    }

    #[Groups(['product_list', 'vendor_products', 'elastica'])]
    public function getProducerId(): ?int
    {
        return $this->producer?->getId();
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addFavorite($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeFavorite($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setProduct($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getProduct() === $this) {
                $review->setProduct(null);
            }
        }

        return $this;
    }
}
