<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['article:read']],
    denormalizationContext: ['groups' => ['article:write']]
)]
class Article extends EntityBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['article:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['article:read', 'article:write'])]
    private ?string $libellefr = null;
  

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['article:read', 'article:write'])]
    private ?string $reference = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['article:read', 'article:write'])]
    private ?int $identreprise = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[Groups(['article:read', 'article:write'])]
    private ?Marque $marque = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[Groups(['article:read', 'article:write'])]
    private ?Fabricant $fabricant = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[Groups(['article:read', 'article:write'])]
    private ?Typearticle $typearticle = null;

    /**
     * @var Collection<int, Detailcommande>
     */
    #[ORM\OneToMany(targetEntity: Detailcommande::class, mappedBy: 'article')]
    #[Groups(['article:read', 'article:write'])]
    private Collection $detailcommandes;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['article:read', 'article:write'])]
    private ?string $brochureFilename = null;

    /**
     * @var Collection<int, Approvisionnement>
     */
    #[ORM\OneToMany(targetEntity: Approvisionnement::class, mappedBy: 'article')]
    #[Groups(['article:read', 'article:write'])]
    private Collection $approvisionnements;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['article:read', 'article:write'])]
    private ?string $quantitestock = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['article:read', 'article:write'])]
    private ?string $quantitelimite = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['article:read', 'article:write'])]
    private ?string $prixunitaire = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['article:read', 'article:write'])]
    private ?string $quantiteappro = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['article:read', 'article:write'])]
    private ?string $quantitevente = null;

    #[ORM\Column(length: 512, nullable: true)]
    #[Groups(['article:read', 'article:write'])]
    private ?string $detailarticle = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['article:read', 'article:write'])]
    private ?string $usefor = null;

    public function __construct()
    {
        $this->detailcommandes = new ArrayCollection();
        $this->approvisionnements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibellefr(): ?string
    {
        return $this->libellefr;
    }

    public function setLibellefr(string $libellefr): static
    {
        $this->libellefr = $libellefr;

        return $this;
    }


    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): static
    {
        $this->reference = $reference;

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

    public function getMarque(): ?Marque
    {
        return $this->marque;
    }

    public function setMarque(?Marque $marque): static
    {
        $this->marque = $marque;

        return $this;
    }

    public function getFabricant(): ?Fabricant
    {
        return $this->fabricant;
    }

    public function setFabricant(?Fabricant $fabricant): static
    {
        $this->fabricant = $fabricant;

        return $this;
    }

    public function getTypearticle(): ?Typearticle
    {
        return $this->typearticle;
    }

    public function setTypearticle(?Typearticle $typearticle): static
    {
        $this->typearticle = $typearticle;

        return $this;
    }

 

    /**
     * @return Collection<int, Detailcommande>
     */
    public function getDetailcommandes(): Collection
    {
        return $this->detailcommandes;
    }

    public function addDetailcommande(Detailcommande $detailcommande): static
    {
        if (!$this->detailcommandes->contains($detailcommande)) {
            $this->detailcommandes->add($detailcommande);
            $detailcommande->setArticle($this);
        }

        return $this;
    }

    public function removeDetailcommande(Detailcommande $detailcommande): static
    {
        if ($this->detailcommandes->removeElement($detailcommande)) {
            // set the owning side to null (unless already changed)
            if ($detailcommande->getArticle() === $this) {
                $detailcommande->setArticle(null);
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
            $approvisionnement->setArticle($this);
        }

        return $this;
    }

    public function removeApprovisionnement(Approvisionnement $approvisionnement): static
    {
        if ($this->approvisionnements->removeElement($approvisionnement)) {
            // set the owning side to null (unless already changed)
            if ($approvisionnement->getArticle() === $this) {
                $approvisionnement->setArticle(null);
            }
        }

        return $this;
    }

    public function getQuantitestock(): ?string
    {
        return $this->quantitestock;
    }

    public function setQuantitestock(?string $quantitestock): static
    {
        $this->quantitestock = $quantitestock;

        return $this;
    }

    public function getQuantitelimite(): ?string
    {
        return $this->quantitelimite;
    }

    public function setQuantitelimite(?string $quantitelimite): static
    {
        $this->quantitelimite = $quantitelimite;

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

    public function getQuantiteappro(): ?string
    {
        return $this->quantiteappro;
    }

    public function setQuantiteappro(?string $quantiteappro): static
    {
        $this->quantiteappro = $quantiteappro;

        return $this;
    }

    public function getQuantitevente(): ?string
    {
        return $this->quantitevente;
    }

    public function setQuantitevente(?string $quantitevente): static
    {
        $this->quantitevente = $quantitevente;

        return $this;
    }

    public function getDetailarticle(): ?string
    {
        return $this->detailarticle;
    }

    public function setDetailarticle(?string $detailarticle): static
    {
        $this->detailarticle = $detailarticle;

        return $this;
    }

    public function getUsefor(): ?string
    {
        return $this->usefor;
    }

    public function setUsefor(?string $usefor): static
    {
        $this->usefor = $usefor;

        return $this;
    }
}
