<?php

namespace App\Entity;

use App\Repository\CommuneRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommuneRepository::class)]
class Commune extends EntityBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libelle = null;

    #[ORM\Column(length: 48, nullable: true)]
    private ?string $createdFromIp = null;

    #[ORM\Column(length: 48, nullable: true)]
    private ?string $updatedFromIp = null;

    #[ORM\Column(length: 48, nullable: true)]
    private ?string $deletedFromIp = null;

    #[ORM\Column(nullable: true)]
    private ?int $typecommande = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(?string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getCreatedFromIp(): ?string
    {
        return $this->createdFromIp;
    }

    public function setCreatedFromIp(?string $createdFromIp): static
    {
        $this->createdFromIp = $createdFromIp;

        return $this;
    }

    public function getUpdatedFromIp(): ?string
    {
        return $this->updatedFromIp;
    }

    public function setUpdatedFromIp(?string $updatedFromIp): static
    {
        $this->updatedFromIp = $updatedFromIp;

        return $this;
    }

    public function getDeletedFromIp(): ?string
    {
        return $this->deletedFromIp;
    }

    public function setDeletedFromIp(?string $deletedFromIp): static
    {
        $this->deletedFromIp = $deletedFromIp;

        return $this;
    }

    public function getTypecommande(): ?int
    {
        return $this->typecommande;
    }

    public function setTypecommande(?int $typecommande): static
    {
        $this->typecommande = $typecommande;

        return $this;
    }
}
