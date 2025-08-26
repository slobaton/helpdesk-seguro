<?php

namespace App\Http\Controllers;

use App\Enums\AttachmentStage;
use App\Enums\TicketStatus;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Ticket;
use App\Services\AttachmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function __construct(private AttachmentService $attachmentService)
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Ticket::class);

        $user = $request->user();

        $tickets = Ticket::query()
            ->visibleTo($user)
            ->latest()
            ->paginate(12);

        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        $this->authorize('create', Ticket::class);
        return view('tickets.create');
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $user = $request->user();

        $ticket = DB::transaction(function () use ($request, $user) {

            $ticket = Ticket::create([
                'title'       => $request->validated()['title'],
                'description' => $request->validated()['description'],
                'type'        => $request->validated()['type'],
                'status'      => TicketStatus::Open,
                'created_by'  => $user->id,
            ]);

            // Optional multiple problem attachments on creation
            $files = $request->file('problem_attachments', []);
            foreach ($files as $file) {
                $this->attachmentService->storeUploadedFile(
                    $ticket,
                    $file,
                    AttachmentStage::Problem,
                    $user
                );
            }

            activity()->performedOn($ticket)->causedBy($user)
                ->withProperties(['title' => $ticket->title])
                ->log('ticket.created');

            return $ticket;
        });

        return redirect()->route('tickets.show', $ticket)->with('status', 'Ticket created.');
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);
        $ticket->load(['creator', 'assignee', 'resolver', 'attachments']);
        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        $this->authorize('update', $ticket);
        return view('tickets.edit', compact('ticket'));
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('update', $ticket);

        $ticket->update($request->validated());

        activity()->performedOn($ticket)->causedBy($request->user())
            ->withProperties(['status' => $ticket->status->value])
            ->log('ticket.updated');

        return redirect()->route('tickets.show', $ticket)->with('status', 'Ticket updated.');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $this->authorize('delete', $ticket);
        $ticket->delete();

        activity()->performedOn($ticket)->causedBy(Auth::user())
            ->log('ticket.deleted');

        return redirect()->route('tickets.index')->with('status', 'Ticket deleted.');
    }
}
