<x-mail::message>
# Ticket Registration Confirmed!

Hi {{ $user->name }},

You're all set! Your ticket for **{{ $event->title }}** has been registered successfully.

## Event Details
- **Date & Time:** {{ $event->starts_at->format('F j, Y \a\t g:i A') }}
- **Location:** {{ $event->location }}

## Your Ticket Code
<x-mail::panel>
**{{ $ticket->ticket_code }}**
</x-mail::panel>

Please keep this ticket code handy when attending the event.

<x-mail::button :url="route('tickets.index')">
View My Tickets
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
