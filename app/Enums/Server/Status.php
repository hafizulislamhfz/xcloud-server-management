<?php

namespace App\Enums\Server;

use App\Traits\Enumerrayble;

enum Status: string
{
    use Enumerrayble;

    case ACTIVE = 'active';

    case INACTIVE = 'inactive';

    case MAINTENANCE = 'maintenance';
}
