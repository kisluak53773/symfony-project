<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProducerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProducerRepository::class)]
class Producer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product_list', 'vendor_producer', 'vendor_does_not_sell', 'elastica'])]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    #[Groups(['product_list', 'vendor_producer', 'vendor_does_not_sell', 'elastica'])]
    private ?string $title = null;

    #[ORM\Column(length: 40)]
    #[Groups(['product_list', 'vendor_producer', 'vendor_does_not_sell', 'elastica'])]
    private ?string $country = null;

    #[ORM\Column(length: 100)]
    #[Groups(['product_list', 'vendor_producer', 'vendor_does_not_sell', 'elastica'])]
    private ?string $address = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'producer', orphanRemoval: true)]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setProducer($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getProducer() === $this) {
                $product->setProducer(null);
            }
        }

        return $this;
    }
}
