<?php

namespace App\Models;

use Database\Factories\OrganizerApplicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $organization_name
 * @property string|null $business_reg_number
 * @property string|null $contact_phone
 * @property string|null $bank_name
 * @property string|null $bank_account_number
 * @property string $event_title
 * @property string $event_description
 * @property Carbon $proposed_starts_at
 * @property int $requested_duration_days
 * @property string $status
 * @property string|null $admin_notes
 * @property Carbon|null $approved_until
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'user_id',
    'organization_name',
    'business_reg_number',
    'contact_phone',
    'bank_name',
    'bank_account_number',
    'event_title',
    'event_description',
    'proposed_starts_at',
    'requested_duration_days',
    'status',
    'admin_notes',
    'approved_until',
])]
class OrganizerApplication extends Model
{
    /** @use HasFactory<OrganizerApplicationFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'proposed_starts_at' => 'datetime',
            'approved_until' => 'datetime',
            'requested_duration_days' => 'integer',
        ];
    }

    /**
     * Get the applicant user who submitted this application.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the application is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the application was approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the application was rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
