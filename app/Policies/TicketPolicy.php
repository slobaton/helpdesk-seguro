<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Technician', 'Requester']);
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('Admin')
            || $ticket->assigned_to === $user->id
            || $ticket->created_by === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Technician', 'Requester']);
    }

    public function update(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('Admin')
            || $ticket->assigned_to === $user->id;
    }

    public function assign(User $user, Ticket $ticket): bool
    {
        if ($user->hasRole('Admin')) return true;

        if ($user->hasRole('Technician')) {
            return is_null($ticket->assigned_to) || $ticket->assigned_to === $user->id;
        }

        return false;
    }

    public function close(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('Admin') || $ticket->assigned_to === $user->id;
    }

    public function changeStatus(User $user, Ticket $ticket): bool
    {
        return $this->update($user, $ticket);
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('Admin');
    }

    public function restore(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('Admin');
    }

    public function forceDelete(User $user, Ticket $ticket): bool
    {
        return false;
    }
}
