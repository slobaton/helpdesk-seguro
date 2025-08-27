<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');
        return $this->user()->can('changeStatus', $ticket);
    }

    public function rules(): array
    {
        return [
            'status' => ['required','in:open,in_progress,closed'],
        ];
    }
}
