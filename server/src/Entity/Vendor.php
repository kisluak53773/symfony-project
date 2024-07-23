<?php

namespace App\Entity;

use App\Repository\VendorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

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
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(['current_vendor'])]
    private ?string $address = null;

    #[ORM\Column(length: 10)]
    #[Groups(['current_vendor'])]
    private ?string $inn = null;

    #[ORM\Column(length: 100)]
    #[Groups(['current_vendor'])]
    private ?string $registrationAuthority = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['current_vendor'])]
    private ?\DateTimeInterface $registrationDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['current_vendor'])]
    private ?\DateTimeInterface $registrationCertificateDate = null;

    #[ORM\OneToOne(inversedBy: 'vendor', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, VendorProduct>
     */
    #[ORM\OneToMany(targetEntity: VendorProduct::class, mappedBy: 'vendor', orphanRemoval: true)]
    private Collection $vendorProducts;

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
}
