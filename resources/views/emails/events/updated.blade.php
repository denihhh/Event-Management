<x-mail::message>
# Event Details Updated

Hi {{ $user->name }},

Please be advised that the organizer has updated the details for **{{ $event->title }}**:

## Updated Event Details
- **New Date & Time:** {{ $event->starts_at->format('F j, Y \a\t g:i A') }}
- **Location:** {{ $event->location }}

Your ticket code remains valid:
<x-mail::panel>
**{{ $ticket->ticket_code }}**
</x-mail::panel>

<x-mail::button :url="route('events.show', $event)">
View Event Page
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
