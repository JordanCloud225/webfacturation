<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
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

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['entreprise:read']],
    denormalizationContext: ['groups' => ['entreprise:write']]
)]
class Entreprise extends EntityBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
   #[Groups(['entreprise:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
   #[Groups(['entreprise:read', 'entreprise:write'])]
    private ?string $libelle = null;

    #[ORM\Column(length: 255, nullable: true)]
   #[Groups(['entreprise:read', 'entreprise:write'])]
    private ?string $siren = null;

    #[ORM\Column(length: 255, nullable: true)]
   #[Groups(['entreprise:read', 'entreprise:write'])]
    private ?string $codenaf = null;

    #[ORM\Column(length: 255, nullable: true)]
   #[Groups(['entreprise:read', 'entreprise:write'])]
    private ?string $numtva = null;

    #[ORM\Column(length: 255, nullable: true)]
   #[Groups(['entreprise:read', 'entreprise:write'])]
    private ?string $adresse = null;

    #[ORM\Column(length: 255, nullable: true)]
   #[Groups(['entreprise:read', 'entreprise:write'])]
    private ?string $complementadresse = null;

    #[ORM\Column(length: 255, nullable: true)]
   #[Groups(['entreprise:read', 'entreprise:write'])]
    private ?string $ville = null;

    #[ORM\Column(length: 255, nullable: true)]
   #[Groups(['entreprise:read', 'entreprise:write'])]
    private ?string $pays = null;

    #[ORM\Column(length: 255, nullable: true)]
   #[Groups(['entreprise:read', 'entreprise:write'])]
    private ?string $contact1 = null;

    #[ORM\Column(length: 255, nullable: true)]
   #[Groups(['entreprise:read', 'entreprise:write'])]
    private ?string $contact2 = null;

    #[ORM\Column(length: 255)]
   #[Groups(['entreprise:read', 'entreprise:write'])]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
   #[Groups(['entreprise:read', 'entreprise:write'])]
    private ?string $codepostal = null;

    #[ORM\Column(length: 255, nullable: true)]
   #[Groups(['entreprise:read', 'entreprise:write'])]
    private ?string $langue = null;

    #[ORM\Column(length: 255, nullable: true)]
   #[Groups(['entreprise:read', 'entreprise:write'])]
    private ?string $siteweb = null;

    #[ORM\Column(length: 255, nullable: true)]
   #[Groups(['entreprise:read', 'entreprise:write'])]
    private ?string $brochureFilename = null;

    #[ORM\Column(nullable: true)]
   #[Groups(['entreprise:read', 'entreprise:write'])]
    private ?bool $etat = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'entreprise')]
    #[Groups(['entreprise:read', 'entreprise:write'])]
    private Collection $users;

    /**
     * @var Collection<int, Solde>
     */
    #[ORM\OneToMany(targetEntity: Solde::class, mappedBy: 'entreprise')]
    #[Groups(['entreprise:read', 'entreprise:write'])]
    private Collection $soldes;

    #[ORM\Column(length: 128, nullable: true)]
   #[Groups(['entreprise:read', 'entreprise:write'])]
   
    private ?string $sigle = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['entreprise:read', 'entreprise:write'])]
    private ?string $competance = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->soldes = new ArrayCollection();
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

    public function getSiren(): ?string
    {
        return $this->siren;
    }

    public function setSiren(?string $siren): static
    {
        $this->siren = $siren;

        return $this;
    }

    public function getCodenaf(): ?string
    {
        return $this->codenaf;
    }

    public function setCodenaf(?string $codenaf): static
    {
        $this->codenaf = $codenaf;

        return $this;
    }

    public function getNumtva(): ?string
    {
        return $this->numtva;
    }

    public function setNumtva(?string $numtva): static
    {
        $this->numtva = $numtva;

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

    public function getComplementadresse(): ?string
    {
        return $this->complementadresse;
    }

    public function setComplementadresse(?string $complementadresse): static
    {
        $this->complementadresse = $complementadresse;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(?string $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    public function getContact1(): ?string
    {
        return $this->contact1;
    }

    public function setContact1(?string $contact1): static
    {
        $this->contact1 = $contact1;

        return $this;
    }

    public function getContact2(): ?string
    {
        return $this->contact2;
    }

    public function setContact2(?string $contact2): static
    {
        $this->contact2 = $contact2;

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

    public function getCodepostal(): ?string
    {
        return $this->codepostal;
    }

    public function setCodepostal(?string $codepostal): static
    {
        $this->codepostal = $codepostal;

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

    public function getSiteweb(): ?string
    {
        return $this->siteweb;
    }

    public function setSiteweb(?string $siteweb): static
    {
        $this->siteweb = $siteweb;

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
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setEntreprise($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getEntreprise() === $this) {
                $user->setEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Solde>
     */
    public function getSoldes(): Collection
    {
        return $this->soldes;
    }

    public function addSolde(Solde $solde): static
    {
        if (!$this->soldes->contains($solde)) {
            $this->soldes->add($solde);
            $solde->setEntreprise($this);
        }

        return $this;
    }

    public function removeSolde(Solde $solde): static
    {
        if ($this->soldes->removeElement($solde)) {
            // set the owning side to null (unless already changed)
            if ($solde->getEntreprise() === $this) {
                $solde->setEntreprise(null);
            }
        }

        return $this;
    }

    public function getSigle(): ?string
    {
        return $this->sigle;
    }

    public function setSigle(?string $sigle): static
    {
        $this->sigle = $sigle;

        return $this;
    }

    public function getCompetance(): ?string
    {
        return $this->competance;
    }

    public function setCompetance(?string $competance): static
    {
        $this->competance = $competance;

        return $this;
    }
}
