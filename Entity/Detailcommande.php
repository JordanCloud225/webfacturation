<?php

namespace App\Entity;

use App\Repository\DetailcommandeRepository;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
 
#[ORM\Entity(repositoryClass: DetailcommandeRepository::class)]
class Detailcommande extends EntityBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["show:liste"])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(["show:liste"])]
    private ?\DateTimeInterface $datedetail = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $soustotal = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $prixunitaire = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["show:liste"])]
    private ?int $quantite = 1;

    #[ORM\ManyToOne(inversedBy: 'detailcommandes')]
    #[Groups(["show:liste"])]
    private ?Service $service = null;

    #[ORM\ManyToOne(inversedBy: 'detailcommandes')]
    #[Groups(["show:liste"])]
    private ?Article $article = null;

    #[ORM\Column(nullable: true)]
    private ?int $identreprise = null;

    #[ORM\ManyToOne(inversedBy: 'detailcommandes')]
    #[Groups(["show:liste"])]
    private ?Boncommande $boncommande = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatedetail(): ?\DateTimeInterface
    {
        return $this->datedetail;
    }

    public function setDatedetail(?\DateTimeInterface $datedetail): static
    {
        $this->datedetail = $datedetail;

        return $this;
    }

    public function getSoustotal(): ?string
    {

        if ($this->getArticle() && $this->getQuantite()) {
            return $this->getQuantite() * $this->getArticle()->getPrixunitaire();
        }
        return 0.0;
        
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

    public function getQuantite(): ?string
    {
        return $this->quantite;
    }

    public function setQuantite(?string $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): static
    {
        $this->service = $service;

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

    public function getIdentreprise(): ?int
    {
        return $this->identreprise;
    }

    public function setIdentreprise(?int $identreprise): static
    {
        $this->identreprise = $identreprise;

        return $this;
    }

    public function getBoncommande(): ?Boncommande
    {
        return $this->boncommande;
    }

    public function setBoncommande(?Boncommande $boncommande): static
    {
        $this->boncommande = $boncommande;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    // Méthode utilitaire pour obtenir le libellé
    public function getLibelle(): string
    {
        return $this->type === 'service' 
            ? ($this->service ? $this->service->getLibellefr() : '') 
            : ($this->article ? $this->article->getLibellefr() : '');
    }

 
}
