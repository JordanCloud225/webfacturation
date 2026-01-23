<?php

namespace App\Entity;

use App\Repository\FournisseurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FournisseurRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['fournisseur:read']],
    denormalizationContext: ['groups' => ['fournisseur:write']]
)]
class Fournisseur extends EntityBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fournisseur:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['fournisseur:read', 'fournisseur:write'])]
    private ?string $libelle = null;

    #[ORM\Column(length: 128, nullable: true)]
    #[Groups(['fournisseur:read', 'fournisseur:write'])]
    private ?string $contact = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['fournisseur:read', 'fournisseur:write'])]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['fournisseur:read', 'fournisseur:write'])]
    private ?string $adresse = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['fournisseur:read', 'fournisseur:write'])]
    private ?string $langue = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $login = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['fournisseur:read', 'fournisseur:write'])]
    private ?int $identreprise = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['fournisseur:read', 'fournisseur:write'])]
    private ?bool $etat = null;

    /**
     * @var Collection<int, Approvisionnement>
     */
    #[ORM\OneToMany(targetEntity: Approvisionnement::class, mappedBy: 'fournisseur')]
    #[Groups(['fournisseur:read', 'fournisseur:write'])]
    private Collection $approvisionnements;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['fournisseur:read', 'fournisseur:write'])]
    private ?string $brochureFilename = null;

    /**
     * @var Collection<int, Boncommandefour>
     */
    #[ORM\OneToMany(targetEntity: Boncommandefour::class, mappedBy: 'fournisseur')]
    #[Groups(['fournisseur:read', 'fournisseur:write'])]
    private Collection $boncommandefours;

    public function __construct()
    {
        $this->approvisionnements = new ArrayCollection();
        $this->boncommandefours = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(?string $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getLangue(): ?string
    {
        return $this->langue;
    }

    public function setLangue(?string $langue): static
    {
        $this->langue = $langue;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(?string $login): static
    {
        $this->login = $login;

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

    public function isEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(?bool $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection<int, Approvisionnement>
     */
    public function getApprovisionnements(): Collection
    {
        return $this->approvisionnements;
    }

    public function addApprovisionnement(Approvisionnement $approvisionnement): static
    {
        if (!$this->approvisionnements->contains($approvisionnement)) {
            $this->approvisionnements->add($approvisionnement);
            $approvisionnement->setFournisseur($this);
        }

        return $this;
    }

    public function removeApprovisionnement(Approvisionnement $approvisionnement): static
    {
        if ($this->approvisionnements->removeElement($approvisionnement)) {
            // set the owning side to null (unless already changed)
            if ($approvisionnement->getFournisseur() === $this) {
                $approvisionnement->setFournisseur(null);
            }
        }

        return $this;
    }

    public function getBrochureFilename(): ?string
    {
        return $this->brochureFilename;
    }

    public function setBrochureFilename(?string $brochureFilename): static
    {
        $this->brochureFilename = $brochureFilename;

        return $this;
    }

    /**
     * @return Collection<int, Boncommandefour>
     */
    public function getBoncommandefours(): Collection
    {
        return $this->boncommandefours;
    }

    public function addBoncommandefour(Boncommandefour $boncommandefour): static
    {
        if (!$this->boncommandefours->contains($boncommandefour)) {
            $this->boncommandefours->add($boncommandefour);
            $boncommandefour->setFournisseur($this);
        }

        return $this;
    }

    public function removeBoncommandefour(Boncommandefour $boncommandefour): static
    {
        if ($this->boncommandefours->removeElement($boncommandefour)) {
            // set the owning side to null (unless already changed)
            if ($boncommandefour->getFournisseur() === $this) {
                $boncommandefour->setFournisseur(null);
            }
        }

        return $this;
    }
}
