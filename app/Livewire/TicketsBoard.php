<?php

namespace App\Livewire;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class TicketsBoard extends Component
{
    use AuthorizesRequests;

    public array $open = [];
    public array $in_progress = [];
    public array $closed = [];

    public function mount(): void
    {
        $user = Auth::user();
        if (!$user->hasAnyRole(['Admin', 'Technician'])) {
            abort(403);
        }

        $this->reloadColumns();
    }

    public function render()
    {
        return view('livewire.tickets-board');
    }

    #[On('kanban:moved')]
    public function moveCard(int $ticketId, string $newStatus): void
    {
        $user = Auth::user();

        $allowed = ['open','in_progress','closed'];
        if (!in_array($newStatus, $allowed, true)) {
            return;
        }

        $ticket = Ticket::query()->findOrFail($ticketId);

        $this->authorize('changeStatus', $ticket);

        $ticket->status = TicketStatus::from($newStatus);
        if ($ticket->status === TicketStatus::Closed) {
            $ticket->resolved_by = $user->id;
            $ticket->resolved_at = now();
        } else {
            $ticket->resolved_by = null;
            $ticket->resolved_at = null;
        }
        $ticket->save();

        activity()->performedOn($ticket)->causedBy($user)
            ->withProperties(['status' => $newStatus])
            ->log('ticket.status_changed');

        $this->reloadColumns();
    }

    private function reloadColumns(): void
    {
        $user = Auth::user();

        $base = Ticket::query()->visibleTo($user)->with(['creator','assignee']);

        $this->open = $base->clone()
            ->where('status', TicketStatus::Open)
            ->latest()
            ->take(50)
            ->get()
            ->toArray();
        $this->in_progress = $base->clone()
            ->where('status', TicketStatus::InProgress)
            ->latest()
            ->take(50)
            ->get()
            ->toArray();
        $this->closed = $base->clone()
            ->where('status', TicketStatus::Closed)
            ->latest()
            ->take(50)
            ->get()
            ->toArray();
    }
}
