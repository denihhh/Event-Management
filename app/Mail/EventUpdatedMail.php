<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventUpdatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public function backoff(): array
    {
        return [10, 60, 300];
    }

    public function __construct(public Ticket $ticket)
    {
        $this->ticket->loadMissing(['event', 'user']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Event Schedule/Location Update: '.$this->ticket->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.events.updated',
            with: [
                'ticket' => $this->ticket,
                'event' => $this->ticket->event,
                'user' => $this->ticket->user,
            ],
        );
    }
}
