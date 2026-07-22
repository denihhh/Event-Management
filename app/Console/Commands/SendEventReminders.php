<?php

namespace App\Console\Commands;

use App\Mail\EventReminderMail;
use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails for events starting within the next 24 hours';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tickets = Ticket::query()
            ->where('reminder_sent', false)
            ->whereHas('event', function ($query): void {
                $query->whereBetween('starts_at', [now(), now()->addHours(24)]);
            })
            ->with(['event', 'user'])
            ->cursor();

        $count = 0;

        foreach ($tickets as $ticket) {
            if ($ticket->user) {
                Mail::to($ticket->user)->queue(new EventReminderMail($ticket));
                $ticket->update(['reminder_sent' => true]);
                $count++;
            }
        }

        $this->info("Dispatched {$count} reminder(s).");

        return Command::SUCCESS;
    }
}
