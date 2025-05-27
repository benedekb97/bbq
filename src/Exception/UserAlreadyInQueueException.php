<?php

declare(strict_types=1);

namespace App\Exception;

use App\Entity\Queue;
use Exception;

class UserAlreadyInQueueException extends Exception
{
    public function __construct(Queue $queue)
    {
        parent::__construct(sprintf(
            'DOH! You\'re already in the \'%s\' queue.',
            $queue->name,
        ));
    }
}