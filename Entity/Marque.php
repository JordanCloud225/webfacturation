<?php

namespace App\Entity;

use App\Repository\MarqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarqueRepository::class)]
class Marque extends EntityBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["show:liste"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["show:liste"])]
    private ?string $libellefr = null;
    
    #[Groups(["show:liste"])]
    public string $test_visibilite = "OK JE SUIS LA";



    #[ORM\Column(nullable: true)]
   
    private ?int $identreprise = null;

    /**
     * @var Collection<int, Article>
     */
    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'marque')]
    private Collection $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }
    
    #[Groups(["show:liste"])]
    public function getId(): ?int
    {
        return $this->id;
    }
    
    #[Groups(["show:liste"])]
    public function getLibellefr(): ?string
    {
        return $this->libellefr;
    }

    public function setLibellefr(string $libellefr): static
    {
        $this->libellefr = $libellefr;

        return $this;
    }

   

    public function getIdentrepprise(): ?int
    {
        return $this->identreprise;
    }

    public function setIdentrepprise(?int $identreprise): static
    {
        $this->identreprise = $identreprise;

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setMarque($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): static
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getMarque() === $this) {
                $article->setMarque(null);
            }
        }

        return $this;
    }
}
