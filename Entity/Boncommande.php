<?php

namespace App\Entity;

use App\Repository\BoncommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BoncommandeRepository::class)]
class Boncommande extends EntityBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["show:liste"])]
    private ?int $id = null;
    
  
    #[ORM\Column(nullable: true)]
    #[Groups(["show:liste"])]
    private ?int $typecommande = null;
 
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $po = null;
 

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $quantite = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $prixunitaire = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $tauxremise = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $tva = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $soustotal = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $ttc = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["show:liste"])]
    private ?bool $etat = null;

    #[ORM\Column(nullable: true)]
   
    private ?int $identreprise = null;
  
   

    /**
     * @var Collection<int, Facture>
     */
    #[ORM\OneToMany(targetEntity: Facture::class, mappedBy: 'boncommande', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $factures;

    /**
     * @var Collection<int, Detailcommande>
     */ 
    #[ORM\OneToMany(targetEntity: Detailcommande::class, mappedBy: 'boncommande', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $detailcommandes;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $montantremise = null;

    #[ORM\ManyToOne(inversedBy: 'boncommandes')]
    #[Groups(["show:liste"])]
    private ?Client $client = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $montantht = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $montantpaye = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $reste = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(["show:liste"])]
    private ?\DateTimeInterface $datedevis = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(["show:liste"])]
    private ?\DateTimeInterface $datelivraison = null;

    #[Groups(["show:liste"])]
    #[ORM\Column(nullable: true)]
    private ?int $devis = null;

    #[Groups(["show:liste"])]
    #[ORM\Column(nullable: true)]
    private ?int $facturee = null;



    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $codecommande = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $codedevis = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(["show:liste"])]
    private ?\DateTimeInterface $dateproforma = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $codeproforma = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(["show:liste"])]
    private ?\DateTimeInterface $datecommande = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $jobtitle = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $sitelocation = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["show:liste"])]

    private ?string $delivraydelai = null;

    #[ORM\ManyToOne(inversedBy: 'boncommandes')]
    private ?Conditionoffre $conditionoffre = null;



  

    public function __construct()
    {
        $this->factures = new ArrayCollection();
        $this->detailcommandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
   

    public function getPo(): ?string
    {
        return $this->po;
    }

    public function setPo(?string $po): static
    {
        $this->po = $po;

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

    public function getPrixunitaire(): ?string
    {
        return $this->prixunitaire;
    }

    public function setPrixunitaire(?string $prixunitaire): static
    {
        $this->prixunitaire = $prixunitaire;

        return $this;
    }

    public function getTauxremise(): ?string
    {
        return $this->tauxremise;
    }

    public function setTauxremise(string $tauxremise): static
    {
        $this->tauxremise = $tauxremise;

        return $this;
    }

    public function getTva(): ?string
    {
        return $this->tva;
    }

    public function setTva(?string $tva): static
    {
        $this->tva = $tva;

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

    public function getTtc(): ?string
    {
        return $this->ttc;
    }

    public function setTtc(?string $ttc): static
    {
        $this->ttc = $ttc;

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

    public function getIdentreprise(): ?int
    {
        return $this->identreprise;
    }

    public function setIdentreprise(?int $identreprise): static
    {
        $this->identreprise = $identreprise;

        return $this;
    }

 

    /**
     * @return Collection<int, Facture>
     */
    public function getFactures(): Collection
    {
        return $this->factures;
    }

    public function addFacture(Facture $facture): static
    {
        if (!$this->factures->contains($facture)) {
            $this->factures->add($facture);
            $facture->setBoncommande($this);
        }

        return $this;
    }

    public function removeFacture(Facture $facture): static
    {
        if ($this->factures->removeElement($facture)) {
            // set the owning side to null (unless already changed)
            if ($facture->getBoncommande() === $this) {
                $facture->setBoncommande(null);
            }
        }

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
            $detailcommande->setBoncommande($this);
        }

        return $this;
    }

    public function removeDetailcommande(Detailcommande $detailcommande): static
    {
        if ($this->detailcommandes->removeElement($detailcommande)) {
            // set the owning side to null (unless already changed)
            if ($detailcommande->getBoncommande() === $this) {
                $detailcommande->setBoncommande(null);
            }
        }

        return $this;
    }

    public function getMontantremise(): ?string
    {
        return $this->montantremise;
    }

    public function setMontantremise(?string $montantremise): static
    {
        $this->montantremise = $montantremise;

        return $this;
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

    public function getMontantht(): ?string
    {
        return $this->montantht;
    }

    public function setMontantht(?string $montantht): static
    {
        $this->montantht = $montantht;

        return $this;
    }

    public function getMontantpaye(): ?string
    {
        return $this->montantpaye;
    }

    public function setMontantpaye(?string $montantpaye): static
    {
        $this->montantpaye = $montantpaye;

        return $this;
    }

    public function getReste(): ?string
    {
        return $this->reste;
    }

    public function setReste(?string $reste): static
    {
        $this->reste = $reste;

        return $this;
    }

    public function getDatedevis(): ?\DateTimeInterface
    {
        return $this->datedevis;
    }

    public function setDatedevis(?\DateTimeInterface $datedevis): static
    {
        $this->datedevis = $datedevis;

        return $this;
    }

    public function getDatelivraison(): ?\DateTimeInterface
    {
        return $this->datelivraison;
    }

    public function setDatelivraison(?\DateTimeInterface $datelivraison): static
    {
        $this->datelivraison = $datelivraison;

        return $this;
    }

    public function getDevis(): ?int
    {
        return $this->devis;
    }

    public function setDevis(?int $devis): static
    {
        $this->devis = $devis;

        return $this;
    }

    public function getFacturee(): ?int
    {
        return $this->facturee;
    }

    public function setFacturee(?int $facturee): static
    {
        $this->facturee = $facturee;

        return $this;
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

    public function getCodedevis(): ?string
    {
        return $this->codedevis;
    }

    public function setCodedevis(?string $codedevis): static
    {
        $this->codedevis = $codedevis;

        return $this;
    }

    public function getDateproforma(): ?\DateTimeInterface
    {
        return $this->dateproforma;
    }

    public function setDateproforma(?\DateTimeInterface $dateproforma): static
    {
        $this->dateproforma = $dateproforma;

        return $this;
    }

    public function getCodeproforma(): ?string
    {
        return $this->codeproforma;
    }

    public function setCodeproforma(?string $codeproforma): static
    {
        $this->codeproforma = $codeproforma;

        return $this;
    }

    public function getDatecommande(): ?\DateTimeInterface
    {
        return $this->datecommande;
    }

    public function setDatecommande(?\DateTimeInterface $datecommande): static
    {
        $this->datecommande = $datecommande;

        return $this;
    }

    public function getJobtitle(): ?string
    {
        return $this->jobtitle;
    }

    public function setJobtitle(?string $jobtitle): static
    {
        $this->jobtitle = $jobtitle;

        return $this;
    }

    public function getSitelocation(): ?string
    {
        return $this->sitelocation;
    }

    public function setSitelocation(?string $sitelocation): static
    {
        $this->sitelocation = $sitelocation;

        return $this;
    }

    public function getDelivraydelai(): ?string
    {
        return $this->delivraydelai;
    }

    public function setDelivraydelai(?string $delivraydelai): static
    {
        $this->delivraydelai = $delivraydelai;

        return $this;
    }

    public function getTotal(): float
{
    $total = 0;
    foreach ($this->getDetailcommandes() as $detail) {
        $total += $detail->getSoustotal();
    }
    return $total;
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


}
                                                                                             