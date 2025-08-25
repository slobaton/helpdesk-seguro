<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;

class StoreTicketAttachmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');
        return $this->user()->can('update', $ticket);
    }

    public function rules(): array
    {
        return [
            'stage' => ['required','in:problem,resolution'],
            'files' => ['required','array','min:1','max:10'],
            'files.*' => ['file','mimetypes:image/jpeg,image/png,image/webp','max:4096'],
        ];
    }
}
