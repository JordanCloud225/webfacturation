<?php

namespace App\Entity;

use App\Repository\BonlivraisonRepository;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BonlivraisonRepository::class)]
class Bonlivraison extends EntityBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["show:liste"])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $moyen = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(["show:liste"])]
    private ?\DateTimeInterface $datebon = null;

    #[ORM\ManyToOne(inversedBy: 'bonlivraisons')]
    private ?Facture $facture = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["show:liste"])]
    private ?int $identreprise = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMoyen(): ?string
    {
        return $this->moyen;
    }

    public function setMoyen(?string $moyen): static
    {
        $this->moyen = $moyen;

        return $this;
    }

    public function getDatebon(): ?\DateTimeInterface
    {
        return $this->datebon;
    }

    public function setDatebon(?\DateTimeInterface $datebon): static
    {
        $this->datebon = $datebon;

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

    public function getIdentreprise(): ?int
    {
        return $this->identreprise;
    }

    public function setIdentreprise(?int $identreprise): static
    {
        $this->identreprise = $identreprise;

        return $this;
    }
}
