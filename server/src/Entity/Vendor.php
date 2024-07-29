<?php

namespace App\Entity;

use App\Repository\VendorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VendorRepository::class)]
class Vendor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['current_vendor'])]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    #[Groups(['current_vendor'])]
    #[Assert\Length(
        min: 2,
        max: 40,
        minMessage: 'Title must not be so short',
        maxMessage: 'Title should not be so long',
    )]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(['current_vendor'])]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Address must not be so short',
        maxMessage: 'Address should not be so long',
    )]
    private ?string $address = null;

    #[ORM\Column(length: 10)]
    #[Groups(['current_vendor'])]
    #[Assert\Length(
        min: 2,
        max: 10,
        minMessage: 'INN must not be so short',
        maxMessage: 'INN should not be so long',
    )]
    private ?string $inn = null;

    #[ORM\Column(length: 100)]
    #[Groups(['current_vendor'])]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: 'Registration authority must not be so short',
        maxMessage: 'Registration authority should not be so long',
    )]
    private ?string $registrationAuthority = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['current_vendor'])]
    #[Assert\Date]
    private ?\DateTimeInterface $registrationDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['current_vendor'])]
    #[Assert\Date]
    private ?\DateTimeInterface $registrationCertificateDate = null;

    #[ORM\OneToOne(inversedBy: 'vendor', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?User $user = null;

    /**
     * @var Collection<int, VendorProduct>
     */
    #[ORM\OneToMany(targetEntity: VendorProduct::class, mappedBy: 'vendor', orphanRemoval: true)]
    private Collection $vendorProducts;

    /**
     * @var Collection<int, CartProduct>
     */
    #[ORM\OneToMany(targetEntity: CartProduct::class, mappedBy: 'vendor')]
    private Collection $cartProducts;

    /**
     * @var Collection<int, OrderProduct>
     */
    #[ORM\OneToMany(targetEntity: OrderProduct::class, mappedBy: 'vendor')]
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getInn(): ?string
    {
        return $this->inn;
    }

    public function setInn(string $inn): static
    {
        $this->inn = $inn;

        return $this;
    }

    public function getRegistrationAuthority(): ?string
    {
        return $this->registrationAuthority;
    }

    public function setRegistrationAuthority(string $registrationAuthority): static
    {
        $this->registrationAuthority = $registrationAuthority;

        return $this;
    }

    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(\DateTimeInterface $registrationDate): static
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    public function getRegistrationCertificateDate(): ?\DateTimeInterface
    {
        return $this->registrationCertificateDate;
    }

    public function setRegistrationCertificateDate(\DateTimeInterface $registrationCertificateDate): static
    {
        $this->registrationCertificateDate = $registrationCertificateDate;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

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
            $vendorProduct->setVendor($this);
        }

        return $this;
    }

    public function removeVendorProduct(VendorProduct $vendorProduct): static
    {
        if ($this->vendorProducts->removeElement($vendorProduct)) {
            // set the owning side to null (unless already changed)
            if ($vendorProduct->getVendor() === $this) {
                $vendorProduct->setVendor(null);
            }
        }

        return $this;
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
            $cartProduct->setVendor($this);
        }

        return $this;
    }

    public function removeCartProduct(CartProduct $cartProduct): static
    {
        if ($this->cartProducts->removeElement($cartProduct)) {
            // set the owning side to null (unless already changed)
            if ($cartProduct->getVendor() === $this) {
                $cartProduct->setVendor(null);
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
            $orderProduct->setVendor($this);
        }

        return $this;
    }

    public function removeOrderProduct(OrderProduct $orderProduct): static
    {
        if ($this->orderProducts->removeElement($orderProduct)) {
            // set the owning side to null (unless already changed)
            if ($orderProduct->getVendor() === $this) {
                $orderProduct->setVendor(null);
            }
        }

        return $this;
    }
}
