<?php

namespace App\Enum;

enum ClientStatus: string
{
    case INTERESTED = 'interested';
    case NEGOTIATING = 'negotiating';
    case ACTIVE = 'active';
    case FINISHED = 'finished';
    case PAUSED = 'paused';
}
