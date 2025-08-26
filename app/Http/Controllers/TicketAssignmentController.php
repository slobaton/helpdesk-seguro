<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignTicketRequest;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;

class TicketAssignmentController extends Controller
{
    public function store(AssignTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('assign', $ticket);

        $ticket->update([
            'assigned_to' => $request->validated()['assigned_to'],
        ]);

        activity()->performedOn($ticket)->causedBy($request->user())
            ->withProperties(['assigned_to' => $ticket->assigned_to])
            ->log('ticket.assigned');

        return back()->with('status', 'Ticket assigned.');
    }
}
