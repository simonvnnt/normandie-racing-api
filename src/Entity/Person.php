<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('person')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('person')]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Groups('person')]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('person')]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('person')]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['person'])]
    private ?string $address = null;

    #[ORM\Column(length: 10, nullable: true)]
    #[Groups(['person'])]
    private ?string $zipCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['person'])]
    private ?string $city = null;

    #[ORM\Column(length: 5, nullable: true)]
    #[Groups(['person'])]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['person'])]
    private ?string $nationality = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('person')]
    private ?string $role = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups('person')]
    private ?string $comment = null;

    #[ORM\Column]
    #[Groups('person')]
    private ?int $warnings = 0;

    /**
     * @var Collection<int, Link>
     */
    #[ORM\ManyToMany(targetEntity: Link::class, inversedBy: 'people')]
    #[Groups('personLinks')]
    private Collection $links;

    #[ORM\Column(length: 255)]
    private ?string $uniqueId = null;

    /**
     * @var Collection<int, Sponsor>
     */
    #[ORM\OneToMany(targetEntity: Sponsor::class, mappedBy: 'contact')]
    private Collection $sponsors;

    public function __construct()
    {
        $this->links = new ArrayCollection();
        $this->sponsors = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function updatedTimestamps(): void
    {
        if ($this->uniqueId === null) {
            $this->uniqueId = uniqid('', true);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    public function setNationality(?string $nationality): static
    {
        $this->nationality = $nationality;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getWarnings(): ?int
    {
        return $this->warnings;
    }

    public function setWarnings(int $warnings): static
    {
        $this->warnings = $warnings;

        return $this;
    }

    /**
     * @return Collection<int, Link>
     */
    public function getLinks(): Collection
    {
        return $this->links;
    }

    public function addLink(Link $link): static
    {
        if (!$this->links->contains($link)) {
            $this->links->add($link);
        }

        return $this;
    }

    public function removeLink(Link $link): static
    {
        $this->links->removeElement($link);

        return $this;
    }

    public function getUniqueId(): ?string
    {
        return $this->uniqueId;
    }

    public function setUniqueId(string $uniqueId): static
    {
        $this->uniqueId = $uniqueId;

        return $this;
    }

    /**
     * @return Collection<int, Sponsor>
     */
    public function getSponsors(): Collection
    {
        return $this->sponsors;
    }

    public function addSponsor(Sponsor $sponsor): static
    {
        if (!$this->sponsors->contains($sponsor)) {
            $this->sponsors->add($sponsor);
            $sponsor->setContact($this);
        }

        return $this;
    }

    public function removeSponsor(Sponsor $sponsor): static
    {
        if ($this->sponsors->removeElement($sponsor)) {
            // set the owning side to null (unless already changed)
            if ($sponsor->getContact() === $this) {
                $sponsor->setContact(null);
            }
        }

        return $this;
    }
}
