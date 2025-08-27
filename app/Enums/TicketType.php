<?php

namespace App\Enums;

enum TicketType: string
{
    case SERVICE = 'service';
    case HARDWARE = 'hardware';
    case SOFTWARE = 'software';
}
