<?php

namespace App\Policies;

use App\Models\TicketAttachment;
use App\Models\User;

class TicketAttachmentPolicy
{
    public function view(User $user, TicketAttachment $attachment): bool
    {
        $ticket = $attachment->ticket;
        return $user->hasRole('Admin')
            || $ticket->assigned_to === $user->id
            || $ticket->created_by === $user->id;
    }

    public function delete(User $user, TicketAttachment $attachment): bool
    {
        $ticket = $attachment->ticket;
        return $user->hasRole('Admin') || $ticket->assigned_to === $user->id;
    }
}
