<?php

namespace App\Mail;

use App\Models\OrganizerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrganizerApplicationApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public function backoff(): array
    {
        return [10, 60, 300];
    }

    public function __construct(
        public OrganizerApplication $application
    ) {
        $this->application->loadMissing('user');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Congratulations! Your Organizer Application has been Approved',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.applications.approved',
            with: [
                'application' => $this->application,
                'user' => $this->application->user,
            ],
        );
    }
}
