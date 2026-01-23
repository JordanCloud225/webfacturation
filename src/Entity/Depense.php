<?php

namespace App\Entity;

use App\Repository\DepenseRepository;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepenseRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['depense:read']],
    denormalizationContext: ['groups' => ['depense:write']]
)]
class Depense extends EntityBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['depense:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['depense:read', 'depense:write'])]
    private ?\DateTimeInterface $datedepense = null;

    #[ORM\Column]
    #[Groups(['depense:read', 'depense:write'])]
    private ?int $montant = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['depense:read', 'depense:write'])]
    private ?string $detail = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['depense:read', 'depense:write'])]
    private ?string $brochureFilename = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['depense:read', 'depense:write'])]
    private ?int $identreprise = null;

  
    #[ORM\ManyToOne(inversedBy: 'depenses')]
    #[Groups(['depense:read', 'depense:write'])]
    private ?Objetdepense $objetdepense = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['depense:read', 'depense:write'])]
    private ?string $beneficiaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['depense:read', 'depense:write'])]
    private ?string $typedepense = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatedepense(): ?\DateTimeInterface
    {
        
        return $this->datedepense;
        
    }

    public function setDatedepense(?\DateTimeInterface $datedepense): static
    {
        $this->datedepense = $datedepense;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(?string $detail): static
    {
        $this->detail = $detail;

        return $this;
    }

    public function getBrochureFilename(): ?string
    {
        return $this->brochureFilename;
    }

    public function setBrochureFilename(?string $brochureFilename): static
    {
        $this->brochureFilename = $brochureFilename;

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

    public function getObjetdepense(): ?Objetdepense
    {
        return $this->objetdepense;
    }

    public function setObjetdepense(?Objetdepense $objetdepense): static
    {
        $this->objetdepense = $objetdepense;

        return $this;
    }

    public function getBeneficiaire(): ?string
    {
        return $this->beneficiaire;
    }

    public function setBeneficiaire(?string $beneficiaire): static
    {
        $this->beneficiaire = $beneficiaire;

        return $this;
    }

    public function getTypedepense(): ?string
    {
        return $this->typedepense;
    }

    public function setTypedepense(?string $typedepense): static
    {
        $this->typedepense = $typedepense;

        return $this;
    }
}
