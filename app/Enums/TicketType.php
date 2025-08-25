<?php

namespace App\Enums;

enum TicketType: string
{
    case Service = 'service';
    case Hardware = 'hardware';
    case Software = 'software';
}
