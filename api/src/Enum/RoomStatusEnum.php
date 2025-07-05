<?php

declare(strict_types=1);

namespace App\Enum;

enum RoomStatusEnum: string
{
    case CREATED = 'created';
    case STARTED = 'started';
}
