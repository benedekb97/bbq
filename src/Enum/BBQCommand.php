<?php

declare(strict_types=1);

namespace App\Enum;

enum BBQCommand: string
{
    case JOIN = 'join';
    case LEAVE = 'leave';
}