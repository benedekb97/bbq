<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\QueueClearMessage;
use App\Repository\QueuedUserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class QueueClearMessageHandler
{
    public function __construct(
        private QueuedUserRepository $queuedUserRepository,
    ) {}

    public function __invoke(QueueClearMessage $message): void
    {
        $this->queuedUserRepository->deleteAllExpired();
    }
}