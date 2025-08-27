<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class TicketStatusController extends Controller
{
    public function update(UpdateTicketStatusRequest $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('changeStatus', $ticket);

        $newStatus = TicketStatus::from($request->validated()['status']);

        DB::transaction(function () use ($ticket, $newStatus, $request) {
            $ticket->status = $newStatus;

            if ($newStatus === TicketStatus::CLOSED) {
                $ticket->resolved_by = $request->user()->id;
                $ticket->resolved_at = now();
            } else {
                $ticket->resolved_by = null;
                $ticket->resolved_at = null;
            }

            $ticket->save();

            activity()->performedOn($ticket)->causedBy($request->user())
                ->withProperties(['status' => $newStatus->value])
                ->log('ticket.status_changed');
        });

        return back()->with('status', 'Status updated.');
    }
}
