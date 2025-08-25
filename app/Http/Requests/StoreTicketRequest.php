<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Ticket::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['required','string','min:3','max:120'],
            'description' => ['required','string','max:5000'],
            'type' => ['required','in:service,hardware,software'],
            'problem_attachments' => ['sometimes','array','max:5'],
            'problem_attachments.*' => ['file','mimetypes:image/jpeg,image/png,image/webp','max:2048'],
        ];
    }
}
