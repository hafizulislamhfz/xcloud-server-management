<?php

namespace App\Enums\Server;

use App\Traits\Enumerrayble;

enum Provider: string
{
    use Enumerrayble;

    case AWS = 'aws';

    case DIGITALOCEAN = 'digitalocean';

    case VULTR = 'vultr';

    case OTHER = 'other';
}
