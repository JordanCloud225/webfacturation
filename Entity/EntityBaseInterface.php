<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;


interface EntityBaseInterface
{
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updatedTimestamps(): void;



    public function getCreatedAt(): ?DateTime;

    public function setCreatedAt(DateTime $createdAt): self;

    public function getUpdatedAt(): ?DateTime;

    public function setUpdatedAt(DateTime $updatedAt): self;

    public function getDeletedAt(): ?DateTime;

    public function setDeletedAt(DateTime $deletedAt): self;

    public  function getCreatedBy(): ?int;
    public  function setCreatedBy(?int $createdBy): self;
    public  function getUpdatedBy(): ?int;
    public  function setUpdatedBy(?int $updatedBy): self;
    public  function getDeletedBy(): ?int;
    public  function setDeletedBy(?int $deletedBy): self;



  
}
