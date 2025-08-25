<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;

class StoreResolutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');
        return $this->user()->can('update', $ticket);
    }

    public function rules(): array
    {
        return [
            'resolution_text' => ['required','string','max:5000'],
            'resolution_attachments' => ['sometimes','array','max:10'],
            'resolution_attachments.*' => ['file','mimetypes:image/jpeg,image/png,image/webp','max:4096'],
        ];
    }
}
