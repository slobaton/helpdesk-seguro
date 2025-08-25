<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;

class AssignTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');
        return $this->user()->can('assign', $ticket);
    }

    public function rules(): array
    {
        return [
            'assigned_to' => ['required','integer','exists:users,id'],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();
        if ($this->user()->hasRole('Technician')) {
            $data['assigned_to'] = $this->user()->id;
        }
        return $data;
    }
}
