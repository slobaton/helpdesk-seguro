<?php

namespace App\Models;

use App\Enums\AttachmentStage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_id', 'stage', 'disk', 'path', 'original_name',
        'mime_type', 'size', 'checksum_sha256', 'uploaded_by',
    ];

    protected $casts = [
        'stage' => AttachmentStage::class,
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
