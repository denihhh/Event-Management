<?php

namespace App\Livewire\Tickets;

use App\Mail\EventTicketMail;
use App\Models\Event;
use App\Models\Ticket;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;

class RegisterTicket extends Component
{
    #[Locked]
    public Event $event;

    public bool $showModal = false;

    public function mount(Event $event): void
    {
        $this->event = $event;
    }

    public function openModal(): void
    {
        if (! Auth::check()) {
            $this->redirect(route('login'), navigate: true);

            return;
        }

        $this->showModal = true;
    }

    public function register(): void
    {
        if (! Auth::check()) {
            $this->redirect(route('login'), navigate: true);

            return;
        }

        try {
            /** @var Ticket $ticket */
            $ticket = DB::transaction(function (): Ticket {
                $lockedEvent = Event::lockForUpdate()->findOrFail($this->event->id);

                if ($lockedEvent->hasStarted()) {
                    throw ValidationException::withMessages([
                        'event' => __('This event has already started or taken place.'),
                    ]);
                }

                if ($lockedEvent->isSoldOut()) {
                    throw ValidationException::withMessages([
                        'event' => __('This event is sold out.'),
                    ]);
                }

                if (! Gate::allows('register', $lockedEvent)) {
                    throw ValidationException::withMessages([
                        'event' => __('You cannot register for your own event.'),
                    ]);
                }

                /** @var Ticket|null $existingTicket */
                $existingTicket = $lockedEvent->tickets()
                    ->where('user_id', Auth::id())
                    ->first();

                if ($existingTicket) {
                    if (! $existingTicket->isCancelled()) {
                        throw ValidationException::withMessages([
                            'event' => __('You already have an active ticket for this event.'),
                        ]);
                    }

                    // Reactivate the cancelled ticket with a fresh UUID pass code
                    $existingTicket->update([
                        'cancelled_at' => null,
                        'ticket_code' => (string) Str::uuid(),
                        'reminder_sent' => false,
                        'checked_in_at' => null,
                    ]);

                    $lockedEvent->increment('tickets_sold');

                    return $existingTicket;
                }

                /** @var Ticket $ticket */
                $ticket = $lockedEvent->tickets()->create([
                    'user_id' => Auth::id(),
                ]);

                $lockedEvent->increment('tickets_sold');

                return $ticket;
            });

            Mail::to(Auth::user())->queue(new EventTicketMail($ticket));

            $this->showModal = false;
            $this->event->refresh();
            $this->dispatch('ticket-registered');

            Flux::toast(variant: 'success', text: __('Ticket registered successfully! Check your email.'));
        } catch (ValidationException $e) {
            $message = $e->validator->errors()->first('event') ?: $e->getMessage();
            Flux::toast(variant: 'danger', text: $message);
        } catch (\Throwable $e) {
            report($e);
            Flux::toast(variant: 'danger', text: __('An unexpected error occurred. Please try again.'));
        }
    }

    public function render(): View
    {
        $hasTicket = Auth::check() && $this->event->tickets()->whereNull('cancelled_at')->where('user_id', Auth::id())->exists();
        $isOrganizer = Auth::check() && Auth::id() === $this->event->user_id;

        return view('livewire.tickets.register-ticket', [
            'hasTicket' => $hasTicket,
            'isOrganizer' => $isOrganizer,
        ]);
    }
}
