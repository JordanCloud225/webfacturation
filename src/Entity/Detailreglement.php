<?php

namespace App\Entity;

use App\Repository\DetailreglementRepository;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
 
#[ORM\Entity(repositoryClass: DetailreglementRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['detailreglement:read']],
    denormalizationContext: ['groups' => ['detailreglement:write']]
)]
class Detailreglement extends EntityBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['detailreglement:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['detailreglement:read', 'detailreglement:write'])]
    private ?string $montantpaye = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['detailreglement:read', 'detailreglement:write'])]
    private ?\DateTimeInterface $datepaiement = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['detailreglement:read', 'detailreglement:write'])]
    private ?int $identreprise = null;

    #[ORM\ManyToOne(inversedBy: 'detailreglements')]
    #[Groups(['detailreglement:read', 'detailreglement:write'])]
    private ?Reglement $reglement = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['detailreglement:read', 'detailreglement:write'])]
    private ?string $reste = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontantpaye(): ?string
    {
        return $this->montantpaye;
    }

    public function setMontantpaye(?string $montantpaye): static
    {
        $this->montantpaye = $montantpaye;

        return $this;
    }

    public function getDatepaiement(): ?\DateTimeInterface
    {
        return $this->datepaiement;
    }

    public function setDatepaiement(?\DateTimeInterface $datepaiement): static
    {
        $this->datepaiement = $datepaiement;

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


  
    public function getReglement(): ?Reglement
    {
        return $this->reglement;
    }

    public function setReglement(?Reglement $reglement): static
    {
        $this->reglement = $reglement;

        return $this;
    }

    public function getReste(): ?string
    {
        return $this->reste;
    }

    public function setReste(?string $reste): static
    {
        $this->reste = $reste;

        return $this;
    }
}
