<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\Timestamps;
use App\Repository\QueuedUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;

#[Entity(repositoryClass: QueuedUserRepository::class), HasLifecycleCallbacks]
class Queue
{
    use Timestamps;

    #[Id, Column(type: Types::INTEGER), GeneratedValue]
    public ?int $id = null;

    #[Column(type: Types::STRING)]
    public ?string $name = null;

    #[Column(name: '`default`', type: Types::BOOLEAN)]
    public bool $default = false;

    #[OneToMany(targetEntity: QueuedUser::class, mappedBy: 'queue')]
    private Collection $users;

    #[Column(type: Types::INTEGER, nullable: true)]
    public ?int $expiryInMinutes = null;

    #[Column(type: Types::STRING, nullable: true)]
    public ?string $domain = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getQueuedUsers(): Collection
    {
        return $this->users->filter(fn (QueuedUser $u) => !$u->isDeleted());
    }
}