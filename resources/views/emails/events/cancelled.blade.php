<x-mail::message>
# Important Notice: Event Cancelled

We regret to inform you that the following event has been **cancelled** by the organizer:

**Event:** {{ $eventTitle }}<br>
**Original Scheduled Date:** {{ $eventStartsAt }}<br>
**Location:** {{ $eventLocation }}

Your ticket for this event is no longer active. We apologize for any inconvenience caused.

<x-mail::button :url="route('events.index')">
Browse Other Events
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
