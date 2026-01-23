<?php

namespace App\Entity;

use App\Repository\BoncommandefourRepository;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: BoncommandefourRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['boncommandefour:read']],
    denormalizationContext: ['groups' => ['boncommandefour:write']]
)]
class Boncommandefour extends EntityBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['boncommandefour:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'boncommandefours')]
    #[Groups(['boncommandefour:read', 'boncommandefour:write'])]
    private ?Fournisseur $fournisseur = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['boncommandefour:read', 'boncommandefour:write'])]
    private ?string $brochure = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['boncommandefour:read', 'boncommandefour:write'])]
    private ?\DateTime $datebdc = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['boncommandefour:read', 'boncommandefour:write'])]
    private ?string $identreprise = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFournisseur(): ?Fournisseur
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?Fournisseur $fournisseur): static
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    public function getBrochure(): ?string
    {
        return $this->brochure;
    }

    public function setBrochure(?string $brochure): static
    {
        $this->brochure = $brochure;

        return $this;
    }

    public function getDatebdc(): ?\DateTime
    {
        return $this->datebdc;
    }

    public function setDatebdc(?\DateTime $datebdc): static
    {
        $this->datebdc = $datebdc;

        return $this;
    }

    public function getIdentreprise(): ?string
    {
        return $this->identreprise;
    }

    public function setIdentreprise(?string $identreprise): static
    {
        $this->identreprise = $identreprise;

        return $this;
    }
}
