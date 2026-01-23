<?php

namespace App\Entity;

use App\Repository\ConditionoffreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConditionoffreRepository::class)]
class Conditionoffre extends EntityBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $libelle = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $valeurlibelle = null;

    /**
     * @var Collection<int, Boncommande>
     */
    #[ORM\OneToMany(targetEntity: Boncommande::class, mappedBy: 'conditionoffre')]
    private Collection $boncommandes;

    public function __construct()
    {
        $this->boncommandes = new ArrayCollection();
    }

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

    public function getValeurlibelle(): ?string
    {
        return $this->valeurlibelle;
    }

    public function setValeurlibelle(?string $valeurlibelle): static
    {
        $this->valeurlibelle = $valeurlibelle;

        return $this;
    }

    /**
     * @return Collection<int, Boncommande>
     */
    public function getBoncommandes(): Collection
    {
        return $this->boncommandes;
    }

    public function addBoncommande(Boncommande $boncommande): static
    {
        if (!$this->boncommandes->contains($boncommande)) {
            $this->boncommandes->add($boncommande);
            $boncommande->setConditionoffre($this);
        }

        return $this;
    }

    public function removeBoncommande(Boncommande $boncommande): static
    {
        if ($this->boncommandes->removeElement($boncommande)) {
            // set the owning side to null (unless already changed)
            if ($boncommande->getConditionoffre() === $this) {
                $boncommande->setConditionoffre(null);
            }
        }

        return $this;
    }
}
