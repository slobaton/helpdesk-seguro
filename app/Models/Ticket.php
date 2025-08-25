<?php

namespace App\Models;

use App\Enums\TicketStatus;
use App\Enums\TicketType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'description', 'type', 'status',
        'assigned_to', 'resolution_text', 'resolved_by', 'resolved_at',
    ];

    protected $casts = [
        'type' => TicketType::class,
        'status' => TicketStatus::class,
        'resolved_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function problemAttachments(): HasMany
    {
        return $this->attachments()->where('stage', 'problem');
    }

    public function resolutionAttachments(): HasMany
    {
        return $this->attachments()->where('stage', 'resolution');
    }

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->hasRole('Admin')) {
            return $query;
        }
        if ($user->hasRole('Technician')) {
            return $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                    ->orWhereNull('assigned_to');
            });
        }

        return $query->where('created_by', $user->id);
    }


    protected function isPending(): Attribute
    {
        return Attribute::get(fn () => $this->status !== TicketStatus::Closed);
    }
}
