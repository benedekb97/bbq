<?php

declare(strict_types=1);

namespace App\Scheduler;

use App\Message\QueueClearMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('queued-user')]
class QueueClearTaskProvider implements ScheduleProviderInterface
{
    private Schedule $schedule;

    public function getSchedule(): Schedule
    {
        return $this->schedule ??= (new Schedule())->with(
            RecurringMessage::every('1 minute', new QueueClearMessage())
        );
    }
}