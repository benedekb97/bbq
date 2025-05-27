<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\QueueUpdatedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: QueueUpdatedEvent::class, method: 'onUpdate')]
readonly class QueueListener
{
    public function onUpdate(QueueUpdatedEvent $event): void
    {
        // Send current queue status to slack
        // Ping first in queue
    }
}