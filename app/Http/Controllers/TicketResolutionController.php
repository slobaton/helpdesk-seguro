<?php

namespace App\Http\Controllers;

use App\Enums\AttachmentStage;
use App\Http\Requests\StoreResolutionRequest;
use App\Models\Ticket;
use App\Services\AttachmentService;
use Illuminate\Support\Facades\DB;

class TicketResolutionController extends Controller
{
    public function __construct(private AttachmentService $attachmentService)
    {
        $this->middleware(['auth', 'verified']);
    }

    public function store(StoreResolutionRequest $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        DB::transaction(function () use ($request, $ticket) {
            $ticket->update([
                'resolution_text' => $request->validated()['resolution_text'],
            ]);

            foreach ($request->file('resolution_attachments', []) as $file) {
                $this->attachmentService->storeUploadedFile(
                    $ticket,
                    $file,
                    AttachmentStage::Resolution,
                    $request->user()
                );
            }

            activity()->performedOn($ticket)->causedBy($request->user())
                ->log('ticket.resolution_updated');
        });

        return back()->with('status', 'Resolution saved.');
    }
}
