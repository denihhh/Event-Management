<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Lab404\Impersonate\Models\Impersonate;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property bool $is_organizer
 * @property Carbon|null $organizer_expires_at
 * @property bool $is_admin
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'is_organizer', 'organizer_expires_at', 'is_admin'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Impersonate, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_organizer' => 'boolean',
            'organizer_expires_at' => 'datetime',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Boot the model and register event listeners.
     */
    protected static function booted(): void
    {
        static::deleting(function (User $user): void {
            $user->tickets->each(function (Ticket $ticket): void {
                $ticket->delete();
            });
        });
    }

    /**
     * Determine if this user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Determine if this user is currently an active organizer.
     */
    public function isOrganizer(): bool
    {
        return $this->is_organizer;
    }

    /**
     * Determine if this user is a regular attendee (not an active organizer or admin).
     */
    public function isAttendee(): bool
    {
        return ! $this->isOrganizer() && ! $this->is_admin;
    }

    /**
     * Only admins may impersonate other users.
     */
    public function canImpersonate(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Admins cannot be impersonated to prevent privilege-escalation loops.
     */
    public function canBeImpersonated(): bool
    {
        return ! $this->isAdmin();
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    /**
     * Get the events organized by this user.
     */
    public function organizedEvents(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get the tickets owned by this user.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get all organizer applications submitted by this user.
     */
    public function organizerApplications(): HasMany
    {
        return $this->hasMany(OrganizerApplication::class);
    }

    /**
     * Get the user's latest organizer application.
     */
    public function latestOrganizerApplication(): HasOne
    {
        return $this->hasOne(OrganizerApplication::class)->latestOfMany();
    }
}
