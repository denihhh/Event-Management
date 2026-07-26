<?php

namespace App\Models;

use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $description
 * @property Carbon $starts_at
 * @property string $location
 * @property int $capacity
 * @property int $tickets_sold
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable(['title', 'description', 'starts_at', 'location', 'capacity'])]
class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'capacity' => 'integer',
            'tickets_sold' => 'integer',
        ];
    }

    /**
     * Get the organizer who created this event.
     *
     * @return BelongsTo<User, $this>
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all tickets for this event.
     *
     * @return HasMany<Ticket, $this>
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Determine if the event has reached its ticket capacity.
     */
    public function isSoldOut(): bool
    {
        return $this->tickets_sold >= $this->capacity;
    }

    /**
     * Get the number of remaining tickets.
     */
    public function remainingTickets(): int
    {
        return max(0, $this->capacity - $this->tickets_sold);
    }

    /**
     * Determine if the event has already started.
     */
    public function hasStarted(): bool
    {
        return $this->starts_at->isPast();
    }

    /**
     * Determine if the event has concluded (24 hours after start time).
     */
    public function hasEnded(): bool
    {
        return now()->greaterThan($this->starts_at->copy()->addHours(24));
    }
}
