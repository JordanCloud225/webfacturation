<?php

namespace App\Entity;

use App\Repository\ReglementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
 
#[ORM\Entity(repositoryClass: ReglementRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['reglement:read']],
    denormalizationContext: ['groups' => ['reglement:write']]
)]
class Reglement extends EntityBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['reglement:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['reglement:read', 'reglement:write'])]
    private ?string $montantpayee = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['reglement:read', 'reglement:write'])]
    private ?string $reste = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['reglement:read', 'reglement:write'])]
    private ?int $identreprise = null;

    #[ORM\ManyToOne(inversedBy: 'reglements')]
    #[Groups(['reglement:read', 'reglement:write'])]
    private ?Facture $facture = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['reglement:read', 'reglement:write'])]
    private ?string $montant = null;

    // #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'reglements')]
    // 
    // private ?self $reglement = null;

    /**
     * @var Collection<int, Detailreglement>
     */
    #[ORM\OneToMany(targetEntity: Detailreglement::class, mappedBy: 'reglement')]
    #[Groups(['reglement:read', 'reglement:write'])]
    private Collection $detailreglements;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['reglement:read', 'reglement:write'])]
    
    private ?\DateTimeInterface $datereglement = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['reglement:read', 'reglement:write'])]
    private ?string $mode = null;


    public function __construct()
    {
        $this->detailreglements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontantpayee(): ?string
    {
        return $this->montantpayee;
    }

    public function setMontantpayee(?string $montantpayee): static
    {
        $this->montantpayee = $montantpayee;

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

    public function getIdentreprise(): ?int
    {
        return $this->identreprise;
    }

    public function setIdentreprise(?int $identreprise): static
    {
        $this->identreprise = $identreprise;

        return $this;
    }


    public function getFacture(): ?Facture
    {
        return $this->facture;
    }

    public function setFacture(?Facture $facture): static
    {
        $this->facture = $facture;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(?string $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getReglement(): ?self
    {
        return $this->reglement;
    }

    public function setReglement(?self $reglement): static
    {
        $this->reglement = $reglement;

        return $this;
    }

    /**
     * @return Collection<int, Detailreglement>
     */
    public function getDetailreglements(): Collection
    {
        return $this->detailreglements;
    }

    public function addDetailreglement(Detailreglement $detailreglement): static
    {
        if (!$this->detailreglements->contains($detailreglement)) {
            $this->detailreglements->add($detailreglement);
            $detailreglement->setReglement($this);
        }

        return $this;
    }

    public function removeDetailreglement(Detailreglement $detailreglement): static
    {
        if ($this->detailreglements->removeElement($detailreglement)) {
            // set the owning side to null (unless already changed)
            if ($detailreglement->getReglement() === $this) {
                $detailreglement->setReglement(null);
            }
        }

        return $this;
    }

    public function getDatereglement(): ?\DateTimeInterface
    {
        return $this->datereglement;
    }

    public function setDatereglement(?\DateTimeInterface $datereglement): static
    {
        $this->datereglement = $datereglement;

        return $this;
    }

    public function getMode(): ?string
    {
        return $this->mode;
    }

    public function setMode(?string $mode): static
    {
        $this->mode = $mode;

        return $this;
    }

 
}
