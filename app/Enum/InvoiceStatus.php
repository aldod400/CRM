<?php

namespace App\Enum;

enum InvoiceStatus: string
{
    case UNPAID = 'unpaid';
    case PARTIALLYPAID = 'partially_paid';
    case PAID = 'paid';
}
