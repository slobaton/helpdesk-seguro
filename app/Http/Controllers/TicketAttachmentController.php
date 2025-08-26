<?php

namespace App\Http\Controllers;

use App\Enums\AttachmentStage;
use App\Http\Requests\StoreTicketAttachmentRequest;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Services\AttachmentService;
use Illuminate\Http\Request;

class TicketAttachmentController extends Controller
{
    public function __construct(private AttachmentService $attachmentService)
    {
        $this->middleware(['auth', 'verified']);
    }

    public function store(StoreTicketAttachmentRequest $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        foreach ($request->file('files') as $file) {
            $this->attachmentService->storeUploadedFile(
                $ticket,
                $file,
                AttachmentStage::from($request->validated()['stage']),
                $request->user()
            );
        }

        activity()->performedOn($ticket)->causedBy($request->user())
            ->withProperties(['stage' => $request->validated()['stage']])
            ->log('ticket.attachment_uploaded');

        return back()->with('status', 'Attachments uploaded.');
    }

    public function show(Request $request, TicketAttachment $attachment)
    {
        $this->authorize('view', $attachment);
        return $this->attachmentService->streamAttachment($attachment);
    }
}
