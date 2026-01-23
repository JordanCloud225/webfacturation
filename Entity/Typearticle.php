<?php

namespace App\Entity;

use App\Repository\TypearticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypearticleRepository::class)]
class Typearticle extends EntityBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["show:liste"])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $libellefr = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["show:liste"])]
    private ?string $libelleen = null;

    #[ORM\Column(nullable: true)]
   
    private ?int $identreprise = null;

    /**
     * @var Collection<int, Article>
     */
    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'typearticle')]
    private Collection $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibellefr(): ?string
    {
        return $this->libellefr;
    }

    public function setLibellefr(?string $libellefr): static
    {
        $this->libellefr = $libellefr;

        return $this;
    }

    public function getLibelleen(): ?string
    {
        return $this->libelleen;
    }

    public function setLibelleen(?string $libelleen): static
    {
        $this->libelleen = $libelleen;

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
            $article->setTypearticle($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): static
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getTypearticle() === $this) {
                $article->setTypearticle(null);
            }
        }

        return $this;
    }
}
