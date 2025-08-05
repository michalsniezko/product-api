<?php

declare(strict_types=1);

namespace App\Enum;

enum ProductStatus: string
{
    case SAVED = 'saved';
    case UPDATED = 'updated';
}
