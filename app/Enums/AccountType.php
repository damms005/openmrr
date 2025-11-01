<?php

declare(strict_types=1);

namespace App\Enums;

enum AccountType: string
{
    case POLAR = 'polar';
    case STRIPE = 'stripe';
}
