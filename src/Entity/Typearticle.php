<?php

namespace App\Entity;

use App\Repository\TypearticleRepository;
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

#[ORM\Entity(repositoryClass: TypearticleRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
      
    ],
    normalizationContext: ['groups' => ['typearticle:read']],
    denormalizationContext: ['groups' => ['typearticle:write']]
)]
class Typearticle extends EntityBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['typearticle:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['typearticle:read', 'typearticle:write'])]
    private ?string $libellefr = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['typearticle:read', 'typearticle:write'])]
    private ?string $libelleen = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['typearticle:read', 'typearticle:write'])]
    private ?int $identreprise = null;

    /**
     * @var Collection<int, Article>
     */
    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'typearticle')]
    #[Groups(['typearticle:read', 'typearticle:write'])]
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
