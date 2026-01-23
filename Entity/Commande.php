<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codecommande = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodecommande(): ?string
    {
        return $this->codecommande;
    }

    public function setCodecommande(?string $codecommande): static
    {
        $this->codecommande = $codecommande;

        return $this;
    }
}
