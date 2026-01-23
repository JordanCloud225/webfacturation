<?php

namespace App\Entity;

use App\Repository\TypeserviceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeserviceRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
      
    ],
    normalizationContext: ['groups' => ['typeservice:read']],
    denormalizationContext: ['groups' => ['typeservice:write']]
)]
class Typeservice extends EntityBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['typeservice:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['typeservice:read', 'typeservice:write'])]
    private ?string $libellefr = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['typeservice:read', 'typeservice:write'])]
    private ?string $libelleen = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['typeservice:read', 'typeservice:write'])]
    private ?int $identreprise = null;

    /**
     * @var Collection<int, Service>
     */
    #[ORM\OneToMany(targetEntity: Service::class, mappedBy: 'typeservice')]
    #[Groups(['typeservice:read', 'typeservice:write'])]
    private Collection $services;

    public function __construct()
    { 
        $this->services = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibellefr(): ?string
    {
        return $this->libellefr;
    }

    public function setLibellefr(string $libellefr): static
    {
        $this->libellefr = $libellefr;

        return $this;
    }

    public function getLibelleen(): ?string
    {
        return $this->libelleen;
    }

    public function setLibelleen(?string $libelleen): static
    {
        $this->libelleen = $libelleen;

        return $this;
    }

    public function getIdentreprise(): ?int
    {
        return $this->identreprise;
    }

    public function setIdentreprise(?int $identreprise): static
    {
        $this->identreprise = $identreprise;

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): static
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->setTypeservice($this);
        }

        return $this;
    }

    public function removeService(Service $service): static
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getTypeservice() === $this) {
                $service->setTypeservice(null);
            }
        }

        return $this;
    }
}
