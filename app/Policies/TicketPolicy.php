<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Determine whether the user can view or download the ticket pass.
     * Allowed if the user is the ticket owner or the organizer hosting the event.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->user_id
            || ($ticket->event !== null && $user->id === $ticket->event->user_id);
    }

    /**
     * Determine whether the user can download the PDF ticket pass.
     * Allowed only for active passes (not cancelled, not checked-in, not past, and event not soft-deleted).
     */
    public function download(User $user, Ticket $ticket): bool
    {
        $canAccess = $user->id === $ticket->user_id
            || ($ticket->event !== null && $user->id === $ticket->event->user_id);

        if (! $canAccess) {
            return false;
        }

        return ! $ticket->isCancelled()
            && ! $ticket->isCheckedIn()
            && ! $ticket->event?->hasStarted()
            && ! $ticket->event?->trashed();
    }
}
