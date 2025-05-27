<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\Timestamps;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity]
class QueuedUser
{
    use Timestamps;

    #[Id, Column(type: Types::INTEGER), GeneratedValue]
    public ?int $id = null;

    #[Column(type: Types::STRING)]
    public ?string $userId = null;

    #[ManyToOne(targetEntity: Queue::class)]
    public ?Queue $queue = null;

    #[Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    public ?DateTimeInterface $expiresAt = null;

    #[Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    public ?DateTimeInterface $deletedAt = null;

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }
}