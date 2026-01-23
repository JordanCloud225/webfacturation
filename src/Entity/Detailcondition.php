<?php

namespace App\Entity;

use App\Repository\DetailconditionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailconditionRepository::class)]
class Detailcondition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'detailconditions')]
    private ?Conditionoffre $conditionoffre = null;

    #[ORM\ManyToOne(inversedBy: 'detailconditions')]
    private ?Boncommande $boncommande = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConditionoffre(): ?Conditionoffre
    {
        return $this->conditionoffre;
    }

    public function setConditionoffre(?Conditionoffre $conditionoffre): static
    {
        $this->conditionoffre = $conditionoffre;

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
}
