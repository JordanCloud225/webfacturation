<?php

namespace App\Entity;

use App\Repository\ApprovisionnementRepository;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApprovisionnementRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['approvisionnement:read']],
    denormalizationContext: ['groups' => ['approvisionnement:write']]
)]
class Approvisionnement extends EntityBase
{
    #[ORM\Id] 
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['approvisionnement:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
     #[Groups(['approvisionnement:read', 'approvisionnement:write'])]
    private ?\DateTimeInterface $dateappro = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
     #[Groups(['approvisionnement:read', 'approvisionnement:write'])]
    private ?string $quantiteappro = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
     #[Groups(['approvisionnement:read', 'approvisionnement:write'])]
    private ?string $soustotal = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
     #[Groups(['approvisionnement:read', 'approvisionnement:write'])]
    private ?string $prixunitaire = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
     #[Groups(['approvisionnement:read', 'approvisionnement:write'])]
    private ?string $total = null;

    #[ORM\Column(nullable: true)]
     #[Groups(['approvisionnement:read', 'approvisionnement:write'])]
    private ?int $identreprise = null;

    #[ORM\ManyToOne(inversedBy: 'approvisionnements')]
    #[Groups(['approvisionnement:read', 'approvisionnement:write'])]
    private ?Fournisseur $fournisseur = null;

    #[ORM\ManyToOne(inversedBy: 'approvisionnements')]
    #[Groups(['approvisionnement:read', 'approvisionnement:write'])]
    private ?Article $article = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateappro(): ?\DateTimeInterface
    {
        return $this->dateappro;
    }

    public function setDateappro(?\DateTimeInterface $dateappro): static
    {
        $this->dateappro = $dateappro;

        return $this;
    }

    public function getQuantiteappro(): ?string
    {
        return $this->quantiteappro;
    }

    public function setQuantiteappro(?string $quantiteappro): static
    {
        $this->quantiteappro = $quantiteappro;

        return $this;
    }

    public function getSoustotal(): ?string
    {
        return $this->soustotal;
    }

    public function setSoustotal(?string $soustotal): static
    {
        $this->soustotal = $soustotal;

        return $this;
    }

    public function getPrixunitaire(): ?string
    {
        return $this->prixunitaire;
    }

    public function setPrixunitaire(?string $prixunitaire): static
    {
        $this->prixunitaire = $prixunitaire;

        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(?string $total): static
    {
        $this->total = $total;

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

    public function getFournisseur(): ?Fournisseur
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?Fournisseur $fournisseur): static
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): static
    {
        $this->article = $article;

        return $this;
    }
}
