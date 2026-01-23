<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

#[ORM\HasLifecycleCallbacks]
class EntityBase implements EntityBaseInterface
{

    #[ORM\Column(name:"created_at", type:"datetime", nullable:true)]
    #[Groups(["show:liste"])]
    protected $createdAt;

 
    #[ORM\Column(name:"updated_at", type:"datetime", nullable:true)]
    #[Groups(["show:liste"])]
    protected $updatedAt;

    #[ORM\Column(name:"deleted_at", type:"datetime", nullable:true)]
    #[Groups(["show:liste"])]
    protected $deletedAt;

    #[ORM\Column(name:"created_by", type: 'integer', nullable:true)]
    protected ?int $createdBy;


    #[ORM\Column(name:"updated_by", type: 'integer',nullable:true)]

    protected ?int $updatedBy;

    #[ORM\Column(name:"deleted_by", type: 'integer', nullable:true)]


 
    protected ?int $deletedBy;

   

    #[ORM\PrePersist] 
    #[ORM\PreUpdate]
    public function updatedTimestamps(): void
    {
        $dateTimeNow = new DateTime('now');

        $this->setUpdatedAt($dateTimeNow);

        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt($dateTimeNow);
        }
    }

    public function getCreatedAt() :?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt() :?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    public function getDeletedAt() :?DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy(): ?int
    {
        return $this->createdBy;
    }

    /**
     * @param mixed $createdBy
     */
    public function setCreatedBy(?int $createdBy): self
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedBy(): ?int
    {
        return $this->updatedBy;
    }

    /**
     * @param mixed $updatedBy
     */
    public function setUpdatedBy(?int $updatedBy): self
    {
        $this->updatedBy = $updatedBy;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeletedBy(): ?int
    {
        return $this->deletedBy;
    }

    /**
     * @param mixed $deletedBy
     */
    public function setDeletedBy(?int $deletedBy): self
    {
        $this->deletedBy = $deletedBy;
        return $this;
    }

   

}
