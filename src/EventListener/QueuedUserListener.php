<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\QueuedUser;
use App\Event\QueueUpdatedEvent;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Psr\EventDispatcher\EventDispatcherInterface;

#[AsEntityListener(event: Events::preRemove, method: 'preDelete', entity: QueuedUser::class)]
#[AsEntityListener(event: Events::prePersist, method: 'preCreate', entity: QueuedUser::class)]
readonly class QueuedUserListener
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function preCreate(QueuedUser $user): void
    {
        $this->eventDispatcher->dispatch(new QueueUpdatedEvent($user->queue));
    }

    public function preDelete(QueuedUser $user): void
    {
        $this->eventDispatcher->dispatch(new QueueUpdatedEvent($user->queue));
    }
}