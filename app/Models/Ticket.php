<?php

namespace App\Models;

use chillerlan\QRCode\QRCode;
use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $event_id
 * @property int $user_id
 * @property string $ticket_code
 * @property bool $reminder_sent
 * @property Carbon|null $checked_in_at
 * @property Carbon|null $cancelled_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['event_id', 'user_id', 'ticket_code', 'reminder_sent', 'checked_in_at', 'cancelled_at'])]
class Ticket extends Model
{
    /** @use HasFactory<TicketFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'reminder_sent' => 'boolean',
            'checked_in_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * Boot the model and register event listeners.
     */
    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket): void {
            $ticket->ticket_code = (string) Str::uuid();
        });

        static::deleting(function (Ticket $ticket): void {
            if ($ticket->event_id) {
                DB::transaction(function () use ($ticket): void {
                    $event = Event::lockForUpdate()->find($ticket->event_id);
                    if ($event && $event->tickets_sold > 0) {
                        $event->decrement('tickets_sold');
                    }
                });
            }
        });
    }

    /**
     * Get the event this ticket belongs to.
     *
     * @return BelongsTo<Event, $this>
     */
    public function event(): BelongsTo
    {
        /** @var BelongsTo<Event, $this> */
        return $this->belongsTo(Event::class)->withTrashed();
    }

    /**
     * Get the attendee who owns this ticket.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Determine if the ticket has been cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->cancelled_at !== null || ($this->event && $this->event->trashed());
    }

    /**
     * Determine if the ticket has been checked in.
     */
    public function isCheckedIn(): bool
    {
        return $this->checked_in_at !== null;
    }

    /**
     * Mark the ticket as checked in.
     */
    public function checkIn(): void
    {
        $this->update([
            'checked_in_at' => now(),
        ]);
    }

    /**
     * Undo check in for this ticket.
     */
    public function undoCheckIn(): void
    {
        $this->update([
            'checked_in_at' => null,
        ]);
    }

    /**
     * Generate an SVG QR code Data URI for this ticket code.
     */
    public function getQrCodeDataUri(): string
    {
        return (new QRCode)->render($this->ticket_code);
    }
}
