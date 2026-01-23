<?php

namespace App\Entity;

use App\Repository\ApprovisionnementRepository;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApprovisionnementRepository::class)]
class Approvisionnement extends EntityBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["show:liste"])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(["show:liste"])]
    private ?\DateTimeInterface $dateappro = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $quantiteappro = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $soustotal = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $prixunitaire = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $total = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["show:liste"])]
    private ?int $identreprise = null;

    #[ORM\ManyToOne(inversedBy: 'approvisionnements')]
    private ?Fournisseur $fournisseur = null;

    #[ORM\ManyToOne(inversedBy: 'approvisionnements')]
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
