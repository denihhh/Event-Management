<?php

namespace App\Models;

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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['event_id', 'user_id', 'reminder_sent'])]
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
}
