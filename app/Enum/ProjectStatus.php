<?php

namespace App\Enum;

enum ProjectStatus: string
{
    case PENDING = 'pending';
    case INPROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case DELAYED = 'delayed';
}
