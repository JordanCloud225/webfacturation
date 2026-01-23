<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
 
#[ORM\Entity(repositoryClass: FactureRepository::class)]
class Facture extends EntityBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["show:liste"])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(["show:liste"])]
    private ?\DateTimeInterface $datefacture = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $montantfacture = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $netpayee = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $montantremise = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $tauxremise = null;

   

    #[ORM\Column(nullable: true)]
    #[Groups(["show:liste"])]
    private ?bool $etat = null;

    #[ORM\ManyToOne(inversedBy: 'factures')]
    #[Groups(["show:liste"])]
    private ?Boncommande $boncommande = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["show:liste"])]
    private ?int $identreprise = null;

    /**
     * @var Collection<int, Bonlivraison>
     */
    #[ORM\OneToMany(targetEntity: Bonlivraison::class, mappedBy: 'facture')]
   
    private Collection $bonlivraisons;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $numfacture = null;

    /**
     * @var Collection<int, Reglement>
     */
    #[ORM\OneToMany(targetEntity: Reglement::class, mappedBy: 'facture')]
    private Collection $reglements;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $tva = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $montantht = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $montantpaye = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $reste = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["show:liste"])]
    private ?bool $typefacture = null;

    #[ORM\Column(length: 128, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $jobtitle = null;

    #[ORM\Column(length: 128, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $sitelocation = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["show:liste"])]
    
    private ?string $po = null;

    public function __construct()
    {
        $this->bonlivraisons = new ArrayCollection();
        $this->reglements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatefacture(): ?\DateTimeInterface
    {
        return $this->datefacture;
    }

    public function setDatefacture(?\DateTimeInterface $datefacture): static
    {
        $this->datefacture = $datefacture;

        return $this;
    }

    public function getMontantfacture(): ?string
    {
        return $this->montantfacture;
    }

    public function setMontantfacture(?string $montantfacture): static
    {
        $this->montantfacture = $montantfacture;

        return $this;
    }

    public function getNetpayee(): ?string
    {
        return $this->netpayee;
    }

    public function setNetpayee(?string $netpayee): static
    {
        $this->netpayee = $netpayee;

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

    public function getTauxremise(): ?string
    {
        return $this->tauxremise;
    }

    public function setTauxremise(?string $tauxremise): static
    {
        $this->tauxremise = $tauxremise;

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

    public function getBoncommande(): ?Boncommande
    {
        return $this->boncommande;
    }

    public function setBoncommande(?Boncommande $boncommande): static
    {
        $this->boncommande = $boncommande;

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
     * @return Collection<int, Bonlivraison>
     */
    public function getBonlivraisons(): Collection
    {
        return $this->bonlivraisons;
    }

    public function addBonlivraison(Bonlivraison $bonlivraison): static
    {
        if (!$this->bonlivraisons->contains($bonlivraison)) {
            $this->bonlivraisons->add($bonlivraison);
            $bonlivraison->setFacture($this);
        }

        return $this;
    }

    public function removeBonlivraison(Bonlivraison $bonlivraison): static
    {
        if ($this->bonlivraisons->removeElement($bonlivraison)) {
            // set the owning side to null (unless already changed)
            if ($bonlivraison->getFacture() === $this) {
                $bonlivraison->setFacture(null);
            }
        }

        return $this;
    }

    public function getNumfacture(): ?string
    {
        return $this->numfacture;
    }

    public function setNumfacture(?string $numfacture): static
    {
        $this->numfacture = $numfacture;

        return $this;
    }

    /**
     * @return Collection<int, Reglement>
     */
    public function getReglements(): Collection
    {
        return $this->reglements;
    }

    public function addReglement(Reglement $reglement): static
    {
        if (!$this->reglements->contains($reglement)) {
            $this->reglements->add($reglement);
            $reglement->setFacture($this);
        }

        return $this;
    }

    public function removeReglement(Reglement $reglement): static
    {
        if ($this->reglements->removeElement($reglement)) {
            // set the owning side to null (unless already changed)
            if ($reglement->getFacture() === $this) {
                $reglement->setFacture(null);
            }
        }

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

    public function isTypefacture(): ?bool
    {
        return $this->typefacture;
    }

    public function setTypefacture(?bool $typefacture): static
    {
        $this->typefacture = $typefacture;

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

    public function getPo(): ?string
    {
        return $this->po;
    }

    public function setPo(?string $po): static
    {
        $this->po = $po;

        return $this;
    }
}
