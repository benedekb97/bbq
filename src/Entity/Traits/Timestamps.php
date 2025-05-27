<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;

trait Timestamps
{
    #[Column(type: Types::DATETIME_IMMUTABLE)]
    public ?DateTimeInterface $createdAt = null;

    #[Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    public ?DateTimeInterface $updatedAt = null;

    #[PrePersist]
    public function setCreatedAtNow(): void
    {
        $this->createdAt = new DateTimeImmutable();
    }

    #[PreUpdate]
    public function setUpdatedAtNow(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}