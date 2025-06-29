<?php

namespace App\Enum;

enum TaskStatus: string
{
    case PENDING = 'pending';
    case INPROGRESS = 'in_progress';
    case DONE = 'done';
    case DELAYED = 'delayed';
}
