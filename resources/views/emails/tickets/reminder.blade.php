<x-mail::message>
# Reminder: Your Event is Coming Up Soon!

Hi {{ $user->name }},

This is a quick reminder that **{{ $event->title }}** will take place within the next 24 hours!

## Event Details
- **Date & Time:** {{ $event->starts_at->format('F j, Y \a\t g:i A') }}
- **Location:** {{ $event->location }}

## Your Ticket Pass Code
<x-mail::panel>
**{{ $ticket->ticket_code }}**
</x-mail::panel>

Please have your ticket code ready when arriving at the venue.

<x-mail::button :url="route('tickets.index')">
View My Tickets
</x-mail::button>

We look forward to seeing you there!

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
