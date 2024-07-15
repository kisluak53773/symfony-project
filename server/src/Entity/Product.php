<?php

namespace App\Entity;

use App\Repository\ProductRepository;
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

    #[ORM\Column(length: 255)]
    #[Groups(['product_list'])]
    private ?string $type = null;

    #[ORM\Column(length: 40)]
    #[Groups(['product_list'])]
    private ?string $weight = null;

    #[ORM\Column]
    #[Groups(['product_list'])]
    private ?int $price = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product_list'])]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['product_list'])]
    private ?Producer $producer = null;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

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
}
