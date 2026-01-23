<?php

namespace App\Entity;

use App\Repository\BoncommandeclientRepository;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BoncommandeclientRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['boncommandeclient:read']],
    denormalizationContext: ['groups' => ['boncommandeclient:write']]
)]
class Boncommandeclient extends EntityBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['boncommandeclient:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'boncommandeclients')]
    #[Groups(['boncommandeclient:read', 'boncommandeclient:write'])]
    private ?Client $client = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['boncommandeclient:read', 'boncommandeclient:write'])]
    private ?\DateTime $datebdccli = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['boncommandeclient:read', 'boncommandeclient:write'])]
    private ?string $numdevis = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['boncommandeclient:read', 'boncommandeclient:write'])]
    private ?string $brochure = null;

    #[ORM\Column]
    #[Groups(['boncommandeclient:read', 'boncommandeclient:write'])]
    private ?int $identreprise = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getDatebdccli(): ?\DateTime
    {
        return $this->datebdccli;
    }

    public function setDatebdccli(?\DateTime $datebdccli): static
    {
        $this->datebdccli = $datebdccli;

        return $this;
    }

    public function getNumdevis(): ?string
    {
        return $this->numdevis;
    }

    public function setNumdevis(?string $numdevis): static
    {
        $this->numdevis = $numdevis;

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

    public function getIdentreprise(): ?int
    {
        return $this->identreprise;
    }

    public function setIdentreprise(int $identreprise): static
    {
        $this->identreprise = $identreprise;

        return $this;
    }
}
