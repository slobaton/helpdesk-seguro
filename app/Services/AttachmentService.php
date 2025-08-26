<?php

namespace App\Services;

use App\Enums\AttachmentStage;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentService
{
    private array $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];

    private array $extByMime = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    public function storeUploadedFile(
        Ticket $ticket,
        UploadedFile $file,
        AttachmentStage $stage,
        User $uploader,
        string $disk = 'local'
    ): TicketAttachment {
        $realMime = $file->getMimeType();
        if (!in_array($realMime, $this->allowedMimes, true)) {
            abort(422, 'Unsupported file type.');
        }

        $ext = $this->extByMime[$realMime] ?? 'bin';
        $dir = "tickets/{$ticket->id}/{$stage->value}";
        $filename = Str::uuid()->toString() . '.' . $ext;
        $relativePath = "$dir/$filename";

        $checksum = hash_file('sha256', $file->getRealPath());

        Storage::disk($disk)->put($relativePath, file_get_contents($file->getRealPath()));

        return DB::transaction(function () use ($ticket, $disk, $relativePath, $file, $realMime, $uploader, $checksum, $stage) {
            return TicketAttachment::create([
                'ticket_id'       => $ticket->id,
                'stage'           => $stage,
                'disk'            => $disk,
                'path'            => $relativePath,
                'original_name'   => $this->safeOriginalName($file),
                'mime_type'       => $realMime,
                'size'            => $file->getSize(),
                'checksum_sha256' => $checksum,
                'uploaded_by'     => $uploader->id,
            ]);
        });
    }

    private function safeOriginalName(UploadedFile $file): string
    {
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $name = Str::of($name)->ascii()->replaceMatches('/[^A-Za-z0-9_\-\.]/', '_')->limit(64, '');
        $ext  = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        $ext  = Str::of($ext)->ascii()->replaceMatches('/[^A-Za-z0-9]/', '')->limit(8, '');
        $ext  = $ext ?: 'file';
        return "{$name}.{$ext}";
    }

    public function streamAttachment(TicketAttachment $attachment)
    {
        $this->assertExists($attachment);

        $headers = [
            'Content-Type' => $attachment->mime_type,
            'X-Content-Type-Options' => 'nosniff',
            'Content-Disposition' => 'inline; filename="' . addslashes($attachment->original_name) . '"',
            'Cache-Control' => 'private, max-age=3600',
        ];

        return response()->streamDownload(function () use ($attachment) {
            echo Storage::disk($attachment->disk)->get($attachment->path);
        }, $attachment->original_name, $headers);
    }

    private function assertExists(TicketAttachment $attachment): void
    {
        if (!Storage::disk($attachment->disk)->exists($attachment->path)) {
            abort(404, 'Attachment not found.');
        }
    }
}
