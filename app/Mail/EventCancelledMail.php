<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventCancelledMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [10, 60, 300];
    }

    public function __construct(
        public string $eventTitle,
        public string $eventStartsAt,
        public string $eventLocation
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Event Cancelled: '.$this->eventTitle,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.events.cancelled',
            with: [
                'eventTitle' => $this->eventTitle,
                'eventStartsAt' => $this->eventStartsAt,
                'eventLocation' => $this->eventLocation,
            ],
        );
    }
}
