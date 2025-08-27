<?php

namespace App\Http\Controllers;

use App\Enums\AttachmentStage;
use App\Enums\TicketStatus;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Ticket;
use App\Models\User;
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
                'status'      => TicketStatus::OPEN,
                'created_by'  => $user->id,
            ]);

            $files = $request->file('problem_attachments', []);
            foreach ($files as $file) {
                $this->attachmentService->storeUploadedFile(
                    $ticket,
                    $file,
                    AttachmentStage::PROBLEM,
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

        $assignees = User::role(['Admin','Technician'])
            ->select('id','name','email')
            ->orderBy('name')
            ->get();

        return view('tickets.show', compact('ticket'), compact('assignees'));
    }

    public function edit(Ticket $ticket)
    {
        $assigneesQuery = User::role(['Admin', 'Technician'])
            ->select('id', 'name', 'email')
            ->orderBy('name');

        if (auth()->user()->hasRole('Technician') && !auth()->user()->hasRole('Admin')) {
            $assigneesQuery->where('id', auth()->id());
        }

        $assignees = $assigneesQuery->get();

        return view('tickets.edit', [
            'ticket' => $ticket->load('assignee'),
            'assignees' => $assignees,
        ]);
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
