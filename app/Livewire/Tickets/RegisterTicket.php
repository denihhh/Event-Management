<?php

namespace App\Livewire\Tickets;

use App\Mail\EventTicketMail;
use App\Models\Event;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
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
            $ticket = DB::transaction(function () {
                $lockedEvent = Event::lockForUpdate()->findOrFail($this->event->id);

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

                if ($lockedEvent->tickets()->where('user_id', Auth::id())->exists()) {
                    throw ValidationException::withMessages([
                        'event' => __('You already have a ticket for this event.'),
                    ]);
                }

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
        $hasTicket = Auth::check() && $this->event->tickets()->where('user_id', Auth::id())->exists();
        $isOrganizer = Auth::check() && Auth::id() === $this->event->user_id;

        return view('livewire.tickets.register-ticket', [
            'hasTicket' => $hasTicket,
            'isOrganizer' => $isOrganizer,
        ]);
    }
}
