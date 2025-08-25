<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');
        return $this->user()->can('update', $ticket);
    }

    public function rules(): array
    {
        return [
            'title' => ['required','string','min:3','max:120'],
            'description' => ['required','string','max:5000'],
            'type' => ['required','in:service,hardware,software'],
        ];
    }
}
